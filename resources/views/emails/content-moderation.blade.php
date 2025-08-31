<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Moderation Notice</title>
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
            background: #dc3545;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header.warning {
            background: #ffc107;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .action-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        .action-box.warning {
            border-left-color: #ffc107;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #008751;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .content-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            margin: 15px 0;
            font-style: italic;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .footer a {
            color: #008751;
            text-decoration: none;
        }
        .rules-list {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header {{ $action === 'warning' ? 'warning' : '' }}">
            <h1>{{ $action === 'warning' ? '⚠️ Content Warning' : '🚫 Content Removed' }}</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            
            @if($action === 'removed')
                <p>We've removed content you posted on eForum because it violated our community guidelines.</p>
            @else
                <p>We're reaching out regarding content you posted on eForum that may violate our community guidelines.</p>
            @endif
            
            <div class="action-box {{ $action === 'warning' ? 'warning' : '' }}">
                <h3 style="margin-top: 0;">{{ $action === 'warning' ? 'Warning Issued' : 'Content Removed' }}</h3>
                <p><strong>Content Type:</strong> {{ ucfirst($contentType) }}</p>
                <p><strong>Violation:</strong> {{ $violation }}</p>
                <p><strong>Date:</strong> {{ now()->format('F d, Y \a\t g:i A') }}</p>
            </div>
            
            @if(isset($contentPreview) && $contentPreview)
                <h4>Content Preview:</h4>
                <div class="content-preview">
                    {{ Str::limit($contentPreview, 200) }}
                </div>
            @endif
            
            <h3>Why This Matters</h3>
            <p>eForum is committed to maintaining a safe, respectful, and helpful environment for all members of our Nigerian community. Content that violates our guidelines can:</p>
            <ul>
                <li>Create a hostile environment for other users</li>
                <li>Spread misinformation that could harm visa or job seekers</li>
                <li>Damage the trust and integrity of our community</li>
            </ul>
            
            <div class="rules-list">
                <h4 style="margin-top: 0;">Common Violations Include:</h4>
                <ul style="margin-bottom: 0;">
                    <li>Hate speech or discrimination</li>
                    <li>Spam or misleading information</li>
                    <li>Harassment or personal attacks</li>
                    <li>Inappropriate content</li>
                    <li>Copyright infringement</li>
                </ul>
            </div>
            
            @if($action === 'warning')
                <p><strong>⚠️ This is a warning.</strong> Please review our community guidelines to avoid future violations. Repeated violations may result in temporary or permanent suspension of your account.</p>
            @else
                <p><strong>🚫 The content has been removed.</strong> Repeated violations may result in temporary or permanent suspension of your account.</p>
            @endif
            
            <center>
                <a href="{{ route('page', 'community-rules') }}" class="button">
                    Review Community Guidelines
                </a>
            </center>
            
            <h3>What You Can Do:</h3>
            <ol>
                <li><strong>Review our guidelines:</strong> Familiarize yourself with what's acceptable in our community</li>
                <li><strong>Edit your content:</strong> If you believe this was a mistake, you can appeal this decision</li>
                <li><strong>Contact us:</strong> If you have questions about this action</li>
            </ol>
            
            @if(isset($moderatorNote) && $moderatorNote)
                <div class="action-box">
                    <h4 style="margin-top: 0;">Moderator Note:</h4>
                    <p>{{ $moderatorNote }}</p>
                </div>
            @endif
            
            <p style="margin-top: 30px;">
                <strong>Need Help?</strong><br>
                • Read our <a href="{{ route('faq') }}">FAQ on content policies</a><br>
                • Contact support at <a href="mailto:{{ get_setting('contact_email') }}">{{ get_setting('contact_email') }}</a><br>
                • Appeal this decision within 7 days
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from <a href="{{ config('app.url') }}">eForum</a> content moderation system.</p>
            <p>Please do not reply to this email. Use the links above to take action.</p>
            <p>&copy; {{ date('Y') }} eForum Nigeria. Building a Better Community Together.</p>
        </div>
    </div>
</body>
</html>
