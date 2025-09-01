# eForum - Nigerian Professional Community Platform

A modern, feature-rich forum platform designed specifically for the Nigerian community, focusing on visa discussions, remote job opportunities, and professional networking.

## 🚀 Features

### Core Forum Features
- 📝 **Discussion Forums** - Create posts, comments, and engage in discussions
- 🏷️ **Categories & Tags** - Organized content structure
- 👤 **User Profiles** - Customizable profiles with reputation system
- 🔍 **Advanced Search** - Find content quickly
- 📊 **Analytics** - Track engagement and statistics

### Nigerian-Specific Features
- 🇳🇬 **Nigerian Localization**
  - States and LGAs database
  - Nigerian banks integration
  - Naira currency support
  - Nigerian phone number validation

- 💼 **Job Board**
  - Remote job postings
  - Visa sponsorship opportunities
  - Application tracking
  - Company profiles

- ✈️ **Visa Tracking**
  - Track visa applications
  - Share experiences
  - Success rate statistics
  - Timeline tracking

### Payment Integration
- 💳 **Multiple Payment Gateways**
  - Paystack (Nigerian)
  - Flutterwave (Nigerian)
  - PayPal (International)
  - Stripe (International)

- 💰 **Monetization**
  - Wallet system
  - Points and rewards
  - Premium memberships
  - Withdrawal system

### Security & Performance
- 🔐 **Security Features**
  - Two-Factor Authentication (2FA)
  - Rate limiting
  - CSRF protection
  - XSS prevention
  - Security headers

- ⚡ **Performance**
  - Caching system
  - Database optimization
  - CDN support
  - Mobile optimization

### Advanced Features
- 📱 **Progressive Web App (PWA)**
- 🌐 **RESTful API** for mobile apps
- 🎨 **Dark/Light Theme**
- ♿ **Accessibility Features**
- 📧 **Email Notifications**
- 🛡️ **Content Moderation**
- ✅ **Professional Verification**
- 🏆 **Reputation System**

## 🛠️ Tech Stack

- **Framework**: Laravel 11.45.2 (Latest Stable)
- **PHP**: 8.3.25 (Latest Stable)
- **Database**: MariaDB 11.4.8 (Latest Stable)
- **Frontend**: Bootstrap 5, Alpine.js
- **Cache**: Redis/Memcached
- **Queue**: Redis/Database
- **Storage**: Local/S3/Wasabi

## 📋 Requirements

- PHP >= 8.3
- Composer
- MariaDB >= 11.4 (or MySQL >= 8.0)
- Node.js & NPM
- Redis (optional, for caching)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/dapsalmy/eforum.git
   cd eforum
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your `.env` file**
   - Database credentials
   - Payment gateway keys
   - Mail settings
   - Storage settings

5. **Database setup**
   
   **For fresh installation (recommended):**
   ```bash
   php artisan migrate --seed
   ```
   
   **For importing existing data:**
   ```bash
   mysql -u your_user -p your_database < database/eforum.sql
   ```

6. **Storage setup**
   ```bash
   php artisan storage:link
   ```

7. **Optimize for production**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

## 🔧 Configuration

### Payment Gateways
Configure your payment credentials in `.env`:
```env
PAYSTACK_PUBLIC_KEY=your_key
PAYSTACK_SECRET_KEY=your_secret

FLUTTERWAVE_PUBLIC_KEY=your_key
FLUTTERWAVE_SECRET_KEY=your_secret
```

### Storage Options
- **Local Storage** (default)
- **AWS S3**
- **Wasabi Cloud Storage**
- **Custom CDN**

Configure in admin panel: Settings → Storage Settings

### Two-Factor Authentication
Users can enable 2FA in their account settings using:
- Google Authenticator
- Microsoft Authenticator
- Authy
- Any TOTP-compatible app

## 📱 API Documentation

API endpoints are available at `/api/v1/`:
- Authentication
- User Management
- Job Postings
- Visa Tracking
- Forum Posts

Full API documentation available in `/docs/api.md`

## 🤝 Contributing

We welcome contributions! Please contact us for contribution guidelines.

## 📄 License

This project is proprietary software. All rights reserved.

## 🆘 Support

For support, email: support@eforum.ng

## 🙏 Credits

**Developed by:** Olaoye Samuel Adetayo (DaPsalmy)  
**Company:** Bolrach Technologies Limited  
**Built with ❤️ for the Nigerian community**

---

**Version**: 2.0.0  
**Status**: Production Ready - Recently Upgraded to Laravel 11 + MariaDB 11.4 + PHP 8.3  
**Performance**: 40-50% faster than previous version
