# eForum Deployment Guide

## Pre-Deployment Checklist

### 1. Server Requirements
- PHP >= 8.1
- MySQL >= 5.7 or MariaDB >= 10.3
- Nginx or Apache
- Redis (recommended for cache/queues)
- Composer
- Node.js & NPM
- SSL Certificate

### 2. Required PHP Extensions
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- PDO MySQL
- Tokenizer
- XML
- GD or ImageMagick

## Step-by-Step Deployment

### 1. Clone Repository
```bash
cd /var/www
git clone [your-repository-url] eforum
cd eforum
```

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file:
```env
APP_NAME="eForum"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://eforum.ng

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eforum
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@eforum.ng
MAIL_FROM_NAME="eForum Nigeria"

# Nigerian Payment Gateways
PAYSTACK_PUBLIC_KEY=your-paystack-public-key
PAYSTACK_SECRET_KEY=your-paystack-secret-key
FLUTTERWAVE_PUBLIC_KEY=your-flutterwave-public-key
FLUTTERWAVE_SECRET_KEY=your-flutterwave-secret-key

# Storage (optional - defaults to local)
FILESYSTEM_DISK=local
# For Wasabi
WASABI_ACCESS_KEY_ID=
WASABI_SECRET_ACCESS_KEY=
WASABI_DEFAULT_REGION=us-east-1
WASABI_BUCKET=
WASABI_ENDPOINT=https://s3.wasabisys.com

# Queue Driver
QUEUE_CONNECTION=database
# Or for better performance:
# QUEUE_CONNECTION=redis
```

### 4. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE eforum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'eforum_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON eforum.* TO 'eforum_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import database
mysql -u eforum_user -p eforum < "eforum db/eforum.sql"

# Or use migrations (if starting fresh)
php artisan migrate --seed
```

### 5. Set Permissions
```bash
# Storage and cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Uploads directory
mkdir -p public/uploads
chmod -R 775 public/uploads
chown -R www-data:www-data public/uploads

# If using private storage for verifications
mkdir -p storage/app/private/verifications
chmod -R 775 storage/app/private
```

### 6. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
php artisan storage:link
```

### 7. Web Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name eforum.ng www.eforum.ng;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name eforum.ng www.eforum.ng;
    root /var/www/eforum/public;

    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
}
```

### 8. Queue Worker Setup

Create systemd service `/etc/systemd/system/eforum-queue.service`:
```ini
[Unit]
Description=eForum Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/eforum/artisan queue:work --sleep=3 --tries=3
StandardOutput=file:/var/www/eforum/storage/logs/queue.log
StandardError=file:/var/www/eforum/storage/logs/queue-error.log

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
systemctl enable eforum-queue
systemctl start eforum-queue
```

### 9. Cron Job Setup
```bash
crontab -e
# Add this line:
* * * * * cd /var/www/eforum && php artisan schedule:run >> /dev/null 2>&1
```

### 10. Final Steps
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Post-Deployment Tasks

### 1. Create Admin Account
```bash
php artisan tinker
```
```php
$user = \App\Models\User::create([
    'name' => 'Admin Name',
    'username' => 'admin',
    'email' => 'admin@eforum.ng',
    'password' => bcrypt('secure_password'),
    'role' => 1, // Admin role
    'email_verified_at' => now(),
]);
```

### 2. Configure Settings
1. Login to admin panel: `https://eforum.ng/admin`
2. Configure site settings
3. Set up payment gateways
4. Configure storage settings
5. Set up email templates

### 3. Security Hardening
```bash
# Disable directory listing
echo "Options -Indexes" >> public/.htaccess

# Set secure headers in .env
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### 4. Monitoring Setup
- Set up error tracking (Sentry, Bugsnag)
- Configure uptime monitoring
- Set up backup scripts
- Enable Laravel Telescope (dev) or Horizon (production)

### 5. Backup Strategy
Create daily backup script `/usr/local/bin/backup-eforum.sh`:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/eforum"

# Database backup
mysqldump -u eforum_user -p'password' eforum | gzip > "$BACKUP_DIR/db_$DATE.sql.gz"

# Files backup
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" /var/www/eforum/public/uploads /var/www/eforum/storage

# Keep only last 7 days
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete
```

Add to crontab:
```bash
0 2 * * * /usr/local/bin/backup-eforum.sh
```

## Troubleshooting

### Common Issues

1. **500 Error**
   - Check Laravel logs: `tail -f storage/logs/laravel.log`
   - Verify permissions on storage/cache directories
   - Check PHP error logs

2. **Database Connection Error**
   - Verify credentials in `.env`
   - Check MySQL is running
   - Verify database exists

3. **File Upload Issues**
   - Check `upload_max_filesize` in php.ini
   - Verify storage permissions
   - Check disk space

4. **Queue Not Processing**
   - Check queue worker is running
   - Verify queue connection in `.env`
   - Check failed jobs table

5. **Email Not Sending**
   - Verify SMTP credentials
   - Check mail queue
   - Test with `php artisan tinker` and `Mail::raw()`

## Performance Optimization

1. **Enable OPcache**
   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.max_accelerated_files=10000
   opcache.revalidate_freq=0
   opcache.validate_timestamps=0
   ```

2. **Configure Redis**
   ```bash
   apt-get install redis-server
   # Update .env
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

3. **Enable HTTP/2**
   - Already enabled in Nginx config above

4. **CDN Configuration**
   - Configure Cloudflare or similar
   - Update CDN settings in admin panel

## Support

For issues or questions:
- Check logs in `storage/logs/`
- Review error tracking dashboard
- Contact technical support

---

**Last Updated**: January 2025
**Version**: 1.0.0
