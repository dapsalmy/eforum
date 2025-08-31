<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to eForum Nigeria!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #008751 0%, #00a65d 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            margin: 0;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .feature-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #008751;
        }
        .feature-box h3 {
            margin-top: 0;
            color: #008751;
        }
        .button {
            display: inline-block;
            padding: 14px 35px;
            background: #008751;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: #006940;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .footer a {
            color: #008751;
            text-decoration: none;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #008751;
            text-decoration: none;
        }
        .welcome-emoji {
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="welcome-emoji">🇳🇬 👋</div>
            <h1>Welcome to eForum, {{ $user->name }}!</h1>
            <p>Nigeria's Premier Community for Visa, Jobs & Life Discussions</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>We're thrilled to have you join our growing community of Nigerians helping each other navigate visa applications, find remote job opportunities, and share life experiences!</p>
            
            <p>Your account has been successfully created, and you're now part of a community that's making a difference in the lives of fellow Nigerians worldwide.</p>
            
            <div class="feature-box">
                <h3>🛂 Visa & Immigration Support</h3>
                <p>Connect with others who've successfully navigated visa applications. Share your timeline, learn from others' experiences, and get answers to your visa questions.</p>
            </div>
            
            <div class="feature-box">
                <h3>💼 Remote Job Opportunities</h3>
                <p>Discover remote and sponsorship job opportunities posted by verified employers. Get tips on landing international roles and growing your career.</p>
            </div>
            
            <div class="feature-box">
                <h3>❤️ Relationships & Life</h3>
                <p>Join discussions about relationships, dating, and everyday life challenges. Get advice from a supportive community that understands your perspective.</p>
            </div>
            
            <center>
                <a href="{{ route('home') }}" class="button">
                    Start Exploring eForum
                </a>
            </center>
            
            <h3>🚀 Quick Start Guide:</h3>
            <ol>
                <li><strong>Complete Your Profile:</strong> Add your location, bio, and interests to connect with like-minded people.</li>
                <li><strong>Introduce Yourself:</strong> Say hello in the General Discussion forum.</li>
                <li><strong>Track Your Visa:</strong> If you're applying for a visa, start tracking your journey to help others.</li>
                <li><strong>Browse Jobs:</strong> Check out the latest remote job opportunities.</li>
                <li><strong>Earn Reputation:</strong> Help others by answering questions and sharing your experiences.</li>
            </ol>
            
            <p style="margin-top: 30px;">
                <strong>Need Help?</strong><br>
                • Visit our <a href="{{ route('faq') }}">FAQ section</a><br>
                • Read the <a href="{{ route('page', 'community-rules') }}">Community Guidelines</a><br>
                • Contact us at <a href="mailto:{{ get_setting('contact_email') }}">{{ get_setting('contact_email') }}</a>
            </p>
        </div>
        
        <div class="footer">
            <p><strong>Connect with us:</strong></p>
            <div class="social-links">
                <a href="#">Twitter</a> •
                <a href="#">Facebook</a> •
                <a href="#">Instagram</a> •
                <a href="#">WhatsApp</a>
            </div>
            <p>You're receiving this because you signed up at <a href="{{ config('app.url') }}">eForum.ng</a></p>
            <p>&copy; {{ date('Y') }} eForum Nigeria. Empowering Nigerians Worldwide.</p>
        </div>
    </div>
</body>
</html>
