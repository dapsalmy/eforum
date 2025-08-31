# eForum API Documentation

**Base URL:** `https://eforum.ng/api/v1/`  
**Version:** 1.0.0  
**Authentication:** Bearer Token (Sanctum)

## 🔐 Authentication

### Register
```http
POST /api/v1/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "username": "johndoe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "gender": "Male"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "username": "johndoe",
            "email": "john@example.com"
        },
        "token": "1|abc123..."
    }
}
```

### Login
```http
POST /api/v1/login
```

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "username": "johndoe",
            "email": "john@example.com"
        },
        "token": "1|abc123..."
    }
}
```

### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

## 👤 User Management

### Get Profile
```http
GET /api/v1/profile
Authorization: Bearer {token}
```

### Update Profile
```http
PUT /api/v1/profile
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "bio": "Updated bio",
    "phone_number": "+2348012345678"
}
```

### Change Password
```http
PUT /api/v1/password
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "current_password": "oldpassword",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

## 💼 Job Board

### Get Jobs
```http
GET /api/v1/jobs?page=1&per_page=20&search=developer&category_id=1&job_type=full_time&location=Lagos&is_remote=true&has_visa_sponsorship=true&min_salary=50000&max_salary=100000
```

**Query Parameters:**
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 20)
- `search` - Search term
- `category_id` - Filter by category
- `job_type` - full_time, part_time, contract, internship
- `location` - Job location
- `is_remote` - true/false
- `has_visa_sponsorship` - true/false
- `min_salary` - Minimum salary
- `max_salary` - Maximum salary

**Response:**
```json
{
    "success": true,
    "message": "Jobs retrieved successfully",
    "data": {
        "data": [
            {
                "id": 1,
                "title": "Senior Developer",
                "slug": "senior-developer",
                "description": "Job description...",
                "company_name": "Tech Corp",
                "location": "Lagos, Nigeria",
                "job_type": "full_time",
                "salary_min": 50000,
                "salary_max": 100000,
                "is_remote": true,
                "visa_sponsorship": true,
                "is_featured": false,
                "applications_count": 5,
                "created_at": "2024-01-20T10:00:00Z",
                "user": {
                    "id": 1,
                    "name": "John Doe",
                    "username": "johndoe"
                },
                "category": {
                    "id": 1,
                    "name": "Technology"
                }
            }
        ],
        "pagination": {
            "total": 100,
            "per_page": 20,
            "current_page": 1,
            "last_page": 5,
            "from": 1,
            "to": 20,
            "has_more": true
        }
    }
}
```

### Get Job Details
```http
GET /api/v1/jobs/{slug}
```

### Create Job
```http
POST /api/v1/jobs
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "title": "Senior Developer",
    "description": "Job description...",
    "company_name": "Tech Corp",
    "location": "Lagos, Nigeria",
    "job_type": "full_time",
    "salary_min": 50000,
    "salary_max": 100000,
    "is_remote": true,
    "visa_sponsorship": true,
    "category_id": 1,
    "skills": "PHP, Laravel, MySQL",
    "requirements": "5+ years experience",
    "benefits": "Health insurance, remote work"
}
```

### Apply for Job
```http
POST /api/v1/jobs/{id}/apply
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "cover_letter": "I am interested in this position...",
    "resume": "base64_encoded_resume_or_url"
}
```

### Save/Unsave Job
```http
POST /api/v1/jobs/{id}/save
Authorization: Bearer {token}
```

### Get My Jobs
```http
GET /api/v1/my/jobs
Authorization: Bearer {token}
```

### Get My Applications
```http
GET /api/v1/my/job-applications
Authorization: Bearer {token}
```

### Get Saved Jobs
```http
GET /api/v1/my/saved-jobs
Authorization: Bearer {token}
```

### Get Job Categories
```http
GET /api/v1/jobs/categories
```

## ✈️ Visa Tracking

### Get Visa Trackings
```http
GET /api/v1/visa-trackings?page=1&per_page=20&country=USA&visa_type=H1B&status=approved&year=2024&sort_by=created_at&sort_order=desc
```

**Query Parameters:**
- `page` - Page number
- `per_page` - Items per page
- `country` - Filter by country
- `visa_type` - Filter by visa type
- `status` - planning, preparing, submitted, biometrics, interview_scheduled, interview_completed, approved, rejected, on_hold
- `year` - Filter by year
- `sort_by` - created_at, updated_at, application_date
- `sort_order` - asc, desc

### Get Visa Tracking Details
```http
GET /api/v1/visa-trackings/{id}
```

### Create Visa Tracking
```http
POST /api/v1/visa-trackings
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "visa_type": "H1B",
    "country": "USA",
    "status": "planning",
    "application_date": "2024-01-20",
    "is_public": true,
    "notes": "Planning to apply for H1B visa",
    "timeline": [
        {
            "event": "Started planning",
            "date": "2024-01-20",
            "notes": "Researching requirements"
        }
    ],
    "checklist": {
        "documents_prepared": false,
        "forms_completed": false,
        "fees_paid": false
    }
}
```

### Update Visa Tracking
```http
PUT /api/v1/visa-trackings/{id}
Authorization: Bearer {token}
```

### Delete Visa Tracking
```http
DELETE /api/v1/visa-trackings/{id}
Authorization: Bearer {token}
```

### Get My Visa Trackings
```http
GET /api/v1/my/visa-trackings
Authorization: Bearer {token}
```

### Add Timeline Event
```http
POST /api/v1/visa-trackings/{id}/timeline
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "event": "Documents submitted",
    "date": "2024-01-25",
    "notes": "All required documents submitted to embassy"
}
```

### Update Checklist
```http
PUT /api/v1/visa-trackings/{id}/checklist
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "documents_prepared": true,
    "forms_completed": true,
    "fees_paid": false
}
```

### Get Visa Statistics
```http
GET /api/v1/visa-trackings/statistics
```

**Response:**
```json
{
    "success": true,
    "message": "Statistics retrieved successfully",
    "data": {
        "total_applications": 1500,
        "approved": 1200,
        "rejected": 200,
        "pending": 100,
        "success_rate": 80.0,
        "by_country": {
            "USA": 800,
            "UK": 400,
            "Canada": 300
        },
        "by_visa_type": {
            "H1B": 600,
            "F1": 400,
            "L1": 500
        }
    }
}
```

## 🔑 API Key Management

### Get API Keys
```http
GET /api/v1/api-keys
Authorization: Bearer {token}
```

### Create API Key Request
```http
POST /api/v1/api-keys
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "name": "My Application",
    "permissions": ["read", "write"],
    "rate_limit": 120,
    "expires_at": "2024-12-31T23:59:59Z",
    "notes": "For my mobile app integration"
}
```

**Response:**
```json
{
    "success": true,
    "message": "API key request submitted successfully. It will be reviewed by administrators.",
    "data": {
        "api_key": {
            "id": 1,
            "name": "My Application",
            "status": "pending",
            "created_at": "2024-01-20T10:00:00Z",
            "notes": "For my mobile app integration"
        }
    }
}
```

### Get API Key Details
```http
GET /api/v1/api-keys/{id}
Authorization: Bearer {token}
```

### Get Actual API Key (Approved Keys Only)
```http
GET /api/v1/api-keys/{id}/key
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "API key retrieved successfully",
    "data": {
        "api_key": {
            "id": 1,
            "name": "My Application",
            "key": "ef_abc123def456ghi789jkl012mno345pqr678stu901vwx234yz",
            "permissions": ["read", "write"],
            "rate_limit": 120,
            "expires_at": "2024-12-31T23:59:59Z"
        }
    }
}
```

### Update API Key (Pending Only)
```http
PUT /api/v1/api-keys/{id}
Authorization: Bearer {token}
```

### Delete API Key
```http
DELETE /api/v1/api-keys/{id}
Authorization: Bearer {token}
```

## 🔐 Using API Keys

Once you have an approved API key, you can use it to authenticate API requests:

```http
GET /api/v1/jobs
X-API-Key: ef_abc123def456ghi789jkl012mno345pqr678stu901vwx234yz
```

Or using the Authorization header:

```http
GET /api/v1/jobs
Authorization: ef_abc123def456ghi789jkl012mno345pqr678stu901vwx234yz
```

## 📊 API Key Permissions

- **read**: Access to read-only endpoints (GET requests)
- **write**: Access to create, update, and delete endpoints (POST, PUT, DELETE requests)
- **admin**: Full administrative access (use with caution)

## ⚡ Rate Limiting

Each API key has its own rate limit:
- **Default**: 120 requests per minute
- **Customizable**: Set during key creation (10-1000 requests per minute)
- **Per-key tracking**: Each key is tracked separately

## 🔄 API Key Lifecycle

1. **Request**: User submits API key request
2. **Review**: Admin reviews the request
3. **Approval/Rejection**: Admin approves or rejects with reason
4. **Active**: Approved keys can be used for API access
5. **Monitoring**: Usage is tracked and monitored
6. **Suspension**: Keys can be suspended for violations
7. **Expiration**: Keys can have expiration dates

## 🔍 Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### Unauthorized (401)
```json
{
    "success": false,
    "message": "Unauthenticated",
    "errors": []
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Resource not found",
    "errors": []
}
```

### Server Error (500)
```json
{
    "success": false,
    "message": "Server error",
    "errors": []
}
```

## 📊 Rate Limiting

- **Public endpoints:** 60 requests per minute
- **Authenticated endpoints:** 120 requests per minute
- **File uploads:** 10 requests per minute

## 🔒 Security

- All API requests must use HTTPS
- Authentication tokens expire after 24 hours
- Passwords must be at least 8 characters
- Rate limiting is enforced on all endpoints
- CORS is configured for cross-origin requests

## 📱 SDKs & Libraries

### JavaScript/TypeScript
```bash
npm install eforum-api-client
```

```javascript
import { EForumAPI } from 'eforum-api-client';

const api = new EForumAPI({
    baseURL: 'https://eforum.ng/api/v1',
    token: 'your-auth-token'
});

// Get jobs
const jobs = await api.jobs.list({
    search: 'developer',
    location: 'Lagos'
});
```

### PHP
```bash
composer require eforum/php-sdk
```

```php
use EForum\API\Client;

$client = new Client([
    'base_url' => 'https://eforum.ng/api/v1',
    'token' => 'your-auth-token'
]);

$jobs = $client->jobs()->list([
    'search' => 'developer',
    'location' => 'Lagos'
]);
```

## 🚀 Getting Started

1. **Register for an account** using the `/register` endpoint
2. **Login** to get your authentication token
3. **Include the token** in the Authorization header for protected endpoints
4. **Start building** your integration!

## 📞 Support

- **Documentation:** https://eforum.ng/docs/api
- **Support Email:** api-support@eforum.ng
- **Developer Portal:** https://developers.eforum.ng
- **Status Page:** https://status.eforum.ng
