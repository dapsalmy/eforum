<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eForum API Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 300px; overflow-y: auto; background: #f8f9fa; border-right: 1px solid #dee2e6; }
        .main-content { margin-left: 300px; padding: 2rem; }
        .nav-link { color: #495057; text-decoration: none; padding: 0.5rem 1rem; display: block; border-radius: 0.25rem; margin: 0.25rem 0; }
        .nav-link:hover { background: #e9ecef; color: #212529; }
        .nav-link.active { background: #007bff; color: white; }
        .endpoint { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 1.5rem; margin: 1rem 0; }
        .method { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-weight: bold; font-size: 0.875rem; }
        .method.get { background: #d4edda; color: #155724; }
        .method.post { background: #d1ecf1; color: #0c5460; }
        .method.put { background: #fff3cd; color: #856404; }
        .method.delete { background: #f8d7da; color: #721c24; }
        .url { font-family: 'Courier New', monospace; background: #e9ecef; padding: 0.5rem; border-radius: 0.25rem; margin: 0.5rem 0; }
        pre { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.25rem; padding: 1rem; overflow-x: auto; }
        .badge { font-size: 0.75rem; }
        .auth-required { background: #f8d7da; color: #721c24; }
        .public { background: #d4edda; color: #155724; }
        .response-example { background: #e7f3ff; border-left: 4px solid #007bff; padding: 1rem; margin: 1rem 0; }
        .error-example { background: #fff5f5; border-left: 4px solid #dc3545; padding: 1rem; margin: 1rem 0; }
        @media (max-width: 768px) {
            .sidebar { position: static; width: 100%; height: auto; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-3">
            <h4 class="mb-3">eForum API</h4>
            <nav>
                <a href="#overview" class="nav-link">Overview</a>
                <a href="#authentication" class="nav-link">Authentication</a>
                <a href="#users" class="nav-link">User Management</a>
                <a href="#jobs" class="nav-link">Job Board</a>
                <a href="#visa" class="nav-link">Visa Tracking</a>
                <a href="#errors" class="nav-link">Error Handling</a>
                <a href="#rate-limiting" class="nav-link">Rate Limiting</a>
                <a href="#sdks" class="nav-link">SDKs & Libraries</a>
            </nav>
        </div>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h1 class="mb-4">eForum API Documentation</h1>
            
            <div class="alert alert-info">
                <strong>Base URL:</strong> <code>https://eforum.ng/api/v1/</code><br>
                <strong>Version:</strong> 1.0.0<br>
                <strong>Authentication:</strong> Bearer Token (Sanctum)
            </div>

            <section id="overview">
                <h2>Overview</h2>
                <p>The eForum API provides comprehensive access to all platform features including user management, job postings, visa tracking, and more. All responses are in JSON format and follow RESTful conventions.</p>
                
                <h3>Quick Start</h3>
                <ol>
                    <li>Register for an account using <code>POST /api/v1/register</code></li>
                    <li>Login to get your authentication token</li>
                    <li>Include the token in the Authorization header for protected endpoints</li>
                    <li>Start building your integration!</li>
                </ol>
            </section>

            <section id="authentication">
                <h2>Authentication</h2>
                
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
                <h2>User Management</h2>
                
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
                <h2>Job Board</h2>
                
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
                <h2>Visa Tracking</h2>
                
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
                <h2>Error Handling</h2>
                
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
                <h2>Rate Limiting</h2>
                <ul>
                    <li><strong>Public endpoints:</strong> 60 requests per minute</li>
                    <li><strong>Authenticated endpoints:</strong> 120 requests per minute</li>
                    <li><strong>File uploads:</strong> 10 requests per minute</li>
                </ul>
            </section>

            <section id="sdks">
                <h2>SDKs & Libraries</h2>
                
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
