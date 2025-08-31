<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Job Application</title>
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
            background: #008751;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .applicant-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
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
        .badge {
            display: inline-block;
            padding: 4px 8px;
            background: #e9ecef;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 10px;
        }
        .badge.verified {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 New Job Application!</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $job->user->name }},</p>
            
            <p>Good news! You have received a new application for your job posting:</p>
            
            <h3 style="color: #008751;">{{ $job->title }}</h3>
            <p style="color: #666;">{{ $job->company_name }} • {{ $job->location ?: 'Remote' }}</p>
            
            <div class="applicant-info">
                <h4 style="margin-top: 0;">Applicant Details</h4>
                <p>
                    <strong>Name:</strong> {{ $applicant->name }}
                    @if($applicant->isVerifiedProfessional())
                        <span class="badge verified">✓ Verified Professional</span>
                    @endif
                </p>
                <p><strong>Email:</strong> {{ $applicant->email }}</p>
                @if($applicant->phone_number)
                    <p><strong>Phone:</strong> {{ $applicant->formatted_phone }}</p>
                @endif
                @if($applicant->state)
                    <p><strong>Location:</strong> {{ $applicant->state->name }}, Nigeria</p>
                @endif
                @if($applicant->tagline)
                    <p><strong>Professional Summary:</strong><br>{{ $applicant->tagline }}</p>
                @endif
            </div>
            
            <p>This brings your total applications to <strong>{{ $job->applications }}</strong> for this position.</p>
            
            <center>
                <a href="{{ route('jobs.applicants', $job->id) }}" class="button">
                    View All Applicants
                </a>
            </center>
            
            <p style="margin-top: 30px;">
                <strong>Quick Actions:</strong><br>
                • View applicant's profile: <a href="{{ route('user', $applicant->username) }}">{{ route('user', $applicant->username) }}</a><br>
                • Contact via email: <a href="mailto:{{ $applicant->email }}">{{ $applicant->email }}</a>
            </p>
        </div>
        
        <div class="footer">
            <p>You're receiving this because you posted a job on <a href="{{ config('app.url') }}">eForum</a>.</p>
            <p>To stop receiving these notifications, update your <a href="{{ route('user.email.notifications') }}">email preferences</a>.</p>
            <p>&copy; {{ date('Y') }} eForum Nigeria. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
