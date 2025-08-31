<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eForum API Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --sidebar-width: 320px;
        }

        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .main-container {
            display: flex;
            min-height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .sidebar { 
            position: fixed; 
            top: 0; 
            left: 0; 
            height: 100vh; 
            width: var(--sidebar-width); 
            overflow-y: auto; 
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 1000;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            background: rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h4 {
            color: white;
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-header .logo {
            width: 32px;
            height: 32px;
            background: var(--primary-color);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 2rem; 
            flex: 1;
            background: white;
        }

        .nav-link { 
            color: rgba(255, 255, 255, 0.8); 
            text-decoration: none; 
            padding: 0.75rem 1.5rem; 
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-radius: 0.5rem; 
            margin: 0.25rem 1rem; 
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover { 
            background: rgba(255, 255, 255, 0.1); 
            color: white; 
            transform: translateX(5px);
        }

        .nav-link.active { 
            background: var(--primary-color); 
            color: white; 
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .nav-section {
            margin: 1rem 0;
        }

        .nav-section-title {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.5rem 1.5rem;
            margin: 0;
        }

        .endpoint { 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6; 
            border-radius: 1rem; 
            padding: 2rem; 
            margin: 1.5rem 0; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .endpoint:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .method { 
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem; 
            border-radius: 0.5rem; 
            font-weight: 600; 
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .method.get { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; }
        .method.post { background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); color: #0c5460; }
        .method.put { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); color: #856404; }
        .method.delete { background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24; }

        .url { 
            font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace; 
            background: #2d3748; 
            color: #e2e8f0;
            padding: 1rem; 
            border-radius: 0.5rem; 
            margin: 1rem 0; 
            font-size: 0.9rem;
            border-left: 4px solid var(--primary-color);
        }

        pre { 
            background: #1a202c; 
            border: 1px solid #2d3748; 
            border-radius: 0.5rem; 
            padding: 1.5rem; 
            overflow-x: auto; 
            font-size: 0.875rem;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .badge { 
            font-size: 0.75rem; 
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 600;
        }

        .auth-required { background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24; }
        .public { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; }

        .response-example { 
            background: linear-gradient(135deg, #e7f3ff 0%, #d1ecf1 100%); 
            border-left: 4px solid var(--primary-color); 
            padding: 1.5rem; 
            margin: 1rem 0; 
            border-radius: 0.5rem;
        }

        .error-example { 
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%); 
            border-left: 4px solid var(--danger-color); 
            padding: 1.5rem; 
            margin: 1rem 0; 
            border-radius: 0.5rem;
        }

        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin: -2rem -2rem 2rem -2rem;
            border-radius: 0 0 2rem 2rem;
        }

        .hero-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .hero-section .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-card .label {
            color: var(--secondary-color);
            font-weight: 500;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-card .icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .feature-card h4 {
            color: var(--dark-color);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .feature-card p {
            color: var(--secondary-color);
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .sidebar { 
                position: static; 
                width: 100%; 
                height: auto; 
                border-radius: 0 0 1rem 1rem;
            }
            .main-content { 
                margin-left: 0; 
                padding: 1rem;
            }
            .hero-section {
                margin: -1rem -1rem 1rem -1rem;
                padding: 2rem 1rem;
            }
            .hero-section h1 {
                font-size: 2rem;
            }
        }

        /* Custom scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Code highlighting improvements */
        .language-json {
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* Animation for endpoints */
        .endpoint {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h4>
                    <div class="logo">eF</div>
                    eForum API
                </h4>
            </div>
            
            <nav>
                <div class="nav-section">
                    <h6 class="nav-section-title">Getting Started</h6>
                    <a href="#overview" class="nav-link">
                        <i class="fas fa-rocket"></i>
                        Overview
                    </a>
                    <a href="#authentication" class="nav-link">
                        <i class="fas fa-key"></i>
                        Authentication
                    </a>
                    <a href="#api-keys" class="nav-link">
                        <i class="fas fa-shield-alt"></i>
                        API Keys
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">Core Features</h6>
                    <a href="#users" class="nav-link">
                        <i class="fas fa-users"></i>
                        User Management
                    </a>
                    <a href="#jobs" class="nav-link">
                        <i class="fas fa-briefcase"></i>
                        Job Board
                    </a>
                    <a href="#visa" class="nav-link">
                        <i class="fas fa-plane"></i>
                        Visa Tracking
                    </a>
                    <a href="#posts" class="nav-link">
                        <i class="fas fa-comments"></i>
                        Forum Posts
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">Advanced</h6>
                    <a href="#errors" class="nav-link">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error Handling
                    </a>
                    <a href="#rate-limiting" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Rate Limiting
                    </a>
                    <a href="#sdks" class="nav-link">
                        <i class="fas fa-code"></i>
                        SDKs & Libraries
                    </a>
                </div>
            </nav>
        </div>

        <div class="main-content">
            <div class="hero-section">
                <div class="container-fluid">
                    <h1>eForum API Documentation</h1>
                    <p class="subtitle">Comprehensive API for the Nigerian Professional Community Platform</p>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="number">50+</div>
                            <div class="label">Endpoints</div>
                        </div>
                        <div class="stat-card">
                            <div class="number">4</div>
                            <div class="label">Core Modules</div>
                        </div>
                        <div class="stat-card">
                            <div class="number">99.9%</div>
                            <div class="label">Uptime</div>
                        </div>
                        <div class="stat-card">
                            <div class="number">24/7</div>
                            <div class="label">Support</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="alert alert-info" style="background: linear-gradient(135deg, #e7f3ff 0%, #d1ecf1 100%); border: none; border-radius: 1rem; padding: 1.5rem;">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-info-circle fa-2x text-primary"></i>
                        <div>
                            <strong>Base URL:</strong> <code>https://eforum.ng/api/v1/</code><br>
                            <strong>Version:</strong> 1.0.0<br>
                            <strong>Authentication:</strong> Bearer Token (Sanctum) or API Keys
                        </div>
                    </div>
                </div>

                <section id="overview">
                    <h2><i class="fas fa-rocket text-primary me-2"></i>Overview</h2>
                    <p>The eForum API provides comprehensive access to all platform features including user management, job postings, visa tracking, and more. All responses are in JSON format and follow RESTful conventions.</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4>User Management</h4>
                            <p>Complete user registration, authentication, profiles, and verification system with Nigerian-specific features.</p>
                        </div>
                        <div class="feature-card">
                            <div class="icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <h4>Job Board</h4>
                            <p>Comprehensive job posting and application system with visa sponsorship tracking and Nigerian payment integration.</p>
                        </div>
                        <div class="feature-card">
                            <div class="icon">
                                <i class="fas fa-plane"></i>
                            </div>
                            <h4>Visa Tracking</h4>
                            <p>Advanced visa application tracking with timeline management, checklist features, and community sharing.</p>
                        </div>
                        <div class="feature-card">
                            <div class="icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h4>Forum System</h4>
                            <p>Full-featured forum with posts, comments, reactions, moderation, and reputation system.</p>
                        </div>
                    </div>

                    <h3>Quick Start</h3>
                    <ol>
                        <li>Register for an account using <code>POST /api/v1/register</code></li>
                        <li>Login to get your authentication token</li>
                        <li>Request an API key for programmatic access</li>
                        <li>Include the token in the Authorization header for protected endpoints</li>
                        <li>Start building your integration!</li>
                    </ol>
                </section>

                <section id="authentication">
                    <h2><i class="fas fa-key text-primary me-2"></i>Authentication</h2>
                    
                    <div class="endpoint">
                        <h4>Register</h4>
                        <span class="method post">POST</span>
                        <span class="badge public">Public</span>
                        <div class="url">/api/v1/register</div>
                        
                        <h5>Request Body:</h5>
                        <pre><code class="language-json">{
    "name": "John Doe",
    "username": "johndoe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "gender": "Male"
}</code></pre>
                        
                        <h5>Response:</h5>
                        <div class="response-example">
                            <pre><code class="language-json">{
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
}</code></pre>
                        </div>
                    </div>

                    <div class="endpoint">
                        <h4>Login</h4>
                        <span class="method post">POST</span>
                        <span class="badge public">Public</span>
                        <div class="url">/api/v1/login</div>
                        
                        <h5>Request Body:</h5>
                        <pre><code class="language-json">{
    "email": "john@example.com",
    "password": "password123"
}</code></pre>
                    </div>

                    <div class="endpoint">
                        <h4>Logout</h4>
                        <span class="method post">POST</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/logout</div>
                        <p>Include <code>Authorization: Bearer {token}</code> header</p>
                    </div>
                </section>

                <section id="users">
                    <h2><i class="fas fa-users text-primary me-2"></i>User Management</h2>
                    
                    <div class="endpoint">
                        <h4>Get Profile</h4>
                        <span class="method get">GET</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/profile</div>
                    </div>

                    <div class="endpoint">
                        <h4>Update Profile</h4>
                        <span class="method put">PUT</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/profile</div>
                        
                        <h5>Request Body:</h5>
                        <pre><code class="language-json">{
    "name": "John Doe Updated",
    "bio": "Updated bio",
    "phone_number": "+2348012345678"
}</code></pre>
                    </div>

                    <div class="endpoint">
                        <h4>Change Password</h4>
                        <span class="method put">PUT</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/password</div>
                        
                        <h5>Request Body:</h5>
                        <pre><code class="language-json">{
    "current_password": "oldpassword",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}</code></pre>
                    </div>
                </section>

                <section id="jobs">
                    <h2><i class="fas fa-briefcase text-primary me-2"></i>Job Board</h2>
                    
                    <div class="endpoint">
                        <h4>Get Jobs</h4>
                        <span class="method get">GET</span>
                        <span class="badge public">Public</span>
                        <div class="url">/api/v1/jobs?page=1&per_page=20&search=developer&category_id=1&job_type=full_time&location=Lagos&is_remote=true&has_visa_sponsorship=true&min_salary=50000&max_salary=100000</div>
                        
                        <h5>Query Parameters:</h5>
                        <ul>
                            <li><code>page</code> - Page number (default: 1)</li>
                            <li><code>per_page</code> - Items per page (default: 20)</li>
                            <li><code>search</code> - Search term</li>
                            <li><code>category_id</code> - Filter by category</li>
                            <li><code>job_type</code> - full_time, part_time, contract, internship</li>
                            <li><code>location</code> - Job location</li>
                            <li><code>is_remote</code> - true/false</li>
                            <li><code>has_visa_sponsorship</code> - true/false</li>
                            <li><code>min_salary</code> - Minimum salary</li>
                            <li><code>max_salary</code> - Maximum salary</li>
                        </ul>
                    </div>

                    <div class="endpoint">
                        <h4>Get Job Details</h4>
                        <span class="method get">GET</span>
                        <span class="badge public">Public</span>
                        <div class="url">/api/v1/jobs/{slug}</div>
                    </div>

                    <div class="endpoint">
                        <h4>Create Job</h4>
                        <span class="method post">POST</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/jobs</div>
                        
                        <h5>Request Body:</h5>
                        <pre><code class="language-json">{
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
}</code></pre>
                    </div>

                    <div class="endpoint">
                        <h4>Apply for Job</h4>
                        <span class="method post">POST</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/jobs/{id}/apply</div>
                        
                        <h5>Request Body:</h5>
                        <pre><code class="language-json">{
    "cover_letter": "I am interested in this position...",
    "resume": "base64_encoded_resume_or_url"
}</code></pre>
                    </div>

                    <div class="endpoint">
                        <h4>Save/Unsave Job</h4>
                        <span class="method post">POST</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/jobs/{id}/save</div>
                    </div>

                    <div class="endpoint">
                        <h4>Get My Jobs</h4>
                        <span class="method get">GET</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/my/jobs</div>
                    </div>

                    <div class="endpoint">
                        <h4>Get My Applications</h4>
                        <span class="method get">GET</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/my/job-applications</div>
                    </div>

                    <div class="endpoint">
                        <h4>Get Saved Jobs</h4>
                        <span class="method get">GET</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/my/saved-jobs</div>
                    </div>

                    <div class="endpoint">
                        <h4>Get Job Categories</h4>
                        <span class="method get">GET</span>
                        <span class="badge public">Public</span>
                        <div class="url">/api/v1/jobs/categories</div>
                    </div>
                </section>

                <section id="visa">
                    <h2><i class="fas fa-plane text-primary me-2"></i>Visa Tracking</h2>
                    
                    <div class="endpoint">
                        <h4>Get Visa Trackings</h4>
                        <span class="method get">GET</span>
                        <span class="badge public">Public</span>
                        <div class="url">/api/v1/visa-trackings?page=1&per_page=20&country=USA&visa_type=H1B&status=approved&year=2024&sort_by=created_at&sort_order=desc</div>
                        
                        <h5>Query Parameters:</h5>
                        <ul>
                            <li><code>page</code> - Page number</li>
                            <li><code>per_page</code> - Items per page</li>
                            <li><code>country</code> - Filter by country</li>
                            <li><code>visa_type</code> - Filter by visa type</li>
                            <li><code>status</code> - planning, preparing, submitted, biometrics, interview_scheduled, interview_completed, approved, rejected, on_hold</li>
                            <li><code>year</code> - Filter by year</li>
                            <li><code>sort_by</code> - created_at, updated_at, application_date</li>
                            <li><code>sort_order</code> - asc, desc</li>
                        </ul>
                    </div>

                    <div class="endpoint">
                        <h4>Get Visa Tracking Details</h4>
                        <span class="method get">GET</span>
                        <span class="badge public">Public</span>
                        <div class="url">/api/v1/visa-trackings/{id}</div>
                    </div>

                    <div class="endpoint">
                        <h4>Create Visa Tracking</h4>
                        <span class="method post">POST</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/visa-trackings</div>
                        
                        <h5>Request Body:</h5>
                        <pre><code class="language-json">{
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
}</code></pre>
                    </div>

                    <div class="endpoint">
                        <h4>Update Visa Tracking</h4>
                        <span class="method put">PUT</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/visa-trackings/{id}</div>
                    </div>

                    <div class="endpoint">
                        <h4>Delete Visa Tracking</h4>
                        <span class="method delete">DELETE</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/visa-trackings/{id}</div>
                    </div>

                    <div class="endpoint">
                        <h4>Get My Visa Trackings</h4>
                        <span class="method get">GET</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/my/visa-trackings</div>
                    </div>

                    <div class="endpoint">
                        <h4>Add Timeline Event</h4>
                        <span class="method post">POST</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/visa-trackings/{id}/timeline</div>
                        
                        <h5>Request Body:</h5>
                        <pre><code class="language-json">{
    "event": "Documents submitted",
    "date": "2024-01-25",
    "notes": "All required documents submitted to embassy"
}</code></pre>
                    </div>

                    <div class="endpoint">
                        <h4>Update Checklist</h4>
                        <span class="method put">PUT</span>
                        <span class="badge auth-required">Auth Required</span>
                        <div class="url">/api/v1/visa-trackings/{id}/checklist</div>
                        
                        <h5>Request Body:</h5>
                        <pre><code class="language-json">{
    "documents_prepared": true,
    "forms_completed": true,
    "fees_paid": false
}</code></pre>
                    </div>

                    <div class="endpoint">
                        <h4>Get Visa Statistics</h4>
                        <span class="method get">GET</span>
                        <span class="badge public">Public</span>
                        <div class="url">/api/v1/visa-trackings/statistics</div>
                    </div>
                </section>

                <section id="errors">
                    <h2><i class="fas fa-exclamation-triangle text-primary me-2"></i>Error Handling</h2>
                    
                    <div class="endpoint">
                        <h4>Validation Error (422)</h4>
                        <div class="error-example">
                            <pre><code class="language-json">{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}</code></pre>
                        </div>
                    </div>

                    <div class="endpoint">
                        <h4>Unauthorized (401)</h4>
                        <div class="error-example">
                            <pre><code class="language-json">{
    "success": false,
    "message": "Unauthenticated",
    "errors": []
}</code></pre>
                        </div>
                    </div>

                    <div class="endpoint">
                        <h4>Not Found (404)</h4>
                        <div class="error-example">
                            <pre><code class="language-json">{
    "success": false,
    "message": "Resource not found",
    "errors": []
}</code></pre>
                        </div>
                    </div>

                    <div class="endpoint">
                        <h4>Server Error (500)</h4>
                        <div class="error-example">
                            <pre><code class="language-json">{
    "success": false,
    "message": "Server error",
    "errors": []
}</code></pre>
                        </div>
                    </div>
                </section>

                <section id="rate-limiting">
                    <h2><i class="fas fa-tachometer-alt text-primary me-2"></i>Rate Limiting</h2>
                    <ul>
                        <li><strong>Public endpoints:</strong> 60 requests per minute</li>
                        <li><strong>Authenticated endpoints:</strong> 120 requests per minute</li>
                        <li><strong>File uploads:</strong> 10 requests per minute</li>
                    </ul>
                </section>

                <section id="sdks">
                    <h2><i class="fas fa-code text-primary me-2"></i>SDKs & Libraries</h2>
                    
                    <h3>JavaScript/TypeScript</h3>
                    <pre><code class="language-bash">npm install eforum-api-client</code></pre>
                    
                    <pre><code class="language-javascript">import { EForumAPI } from 'eforum-api-client';

const api = new EForumAPI({
    baseURL: 'https://eforum.ng/api/v1',
    token: 'your-auth-token'
});

// Get jobs
const jobs = await api.jobs.list({
    search: 'developer',
    location: 'Lagos'
});</code></pre>

                    <h3>PHP</h3>
                    <pre><code class="language-bash">composer require eforum/php-sdk</code></pre>
                    
                    <pre><code class="language-php">use EForum\API\Client;

$client = new Client([
    'base_url' => 'https://eforum.ng/api/v1',
    'token' => 'your-auth-token'
]);

$jobs = $client->jobs()->list([
    'search' => 'developer',
    'location' => 'Lagos'
]);</code></pre>
                </section>

                <section class="mt-5">
                    <h2>Support</h2>
                    <ul>
                        <li><strong>Documentation:</strong> <a href="https://eforum.ng/docs/api" target="_blank">https://eforum.ng/docs/api</a></li>
                        <li><strong>Support Email:</strong> <a href="mailto:api-support@eforum.ng">api-support@eforum.ng</a></li>
                        <li><strong>Developer Portal:</strong> <a href="https://developers.eforum.ng" target="_blank">https://developers.eforum.ng</a></li>
                        <li><strong>Status Page:</strong> <a href="https://status.eforum.ng" target="_blank">https://status.eforum.ng</a></li>
                    </ul>
                </section>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Active navigation highlighting
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 60) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
