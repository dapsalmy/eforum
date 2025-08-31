<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa Status Update</title>
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
        .status-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            border: 2px solid #dee2e6;
        }
        .status-box.approved {
            border-color: #28a745;
            background-color: #d4edda;
        }
        .status-box.rejected {
            border-color: #dc3545;
            background-color: #f8d7da;
        }
        .status-box.interview {
            border-color: #ffc107;
            background-color: #fff3cd;
        }
        .status-icon {
            font-size: 48px;
            margin-bottom: 15px;
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
        .timeline-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .timeline-item:last-child {
            border-bottom: none;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Visa Status Update</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $tracking->user->name }},</p>
            
            <p>Your visa application status has been updated:</p>
            
            <div class="status-box {{ $tracking->status === 'approved' ? 'approved' : ($tracking->status === 'rejected' ? 'rejected' : 'interview') }}">
                <div class="status-icon">
                    @if($tracking->status === 'approved')
                        🎉
                    @elseif($tracking->status === 'rejected')
                        😔
                    @elseif($tracking->status === 'interview_scheduled')
                        📅
                    @elseif($tracking->status === 'submitted')
                        ✉️
                    @else
                        📋
                    @endif
                </div>
                <h3 style="margin: 10px 0;">{{ ucwords(str_replace('_', ' ', $tracking->status)) }}</h3>
                <p style="margin: 0;">
                    <strong>{{ $tracking->country }}</strong> - {{ $tracking->visa_type }}
                </p>
            </div>
            
            @if($tracking->status === 'approved')
                <h3 style="color: #28a745;">🎊 Congratulations!</h3>
                <p>Your visa application has been approved! This is fantastic news, and we're thrilled for you.</p>
                <p><strong>Next Steps:</strong></p>
                <ul>
                    <li>Collect your passport from the visa application center</li>
                    <li>Double-check your visa details for accuracy</li>
                    <li>Start planning your travel</li>
                    <li>Share your success story to help others in the community</li>
                </ul>
            @elseif($tracking->status === 'rejected')
                <h3>We're Sorry</h3>
                <p>Your visa application was not approved this time. We know this is disappointing, but don't give up!</p>
                <p><strong>What You Can Do:</strong></p>
                <ul>
                    <li>Review the rejection reason (if provided)</li>
                    <li>Consult with immigration experts in our community</li>
                    <li>Consider reapplying after addressing the issues</li>
                    <li>Share your experience to help others avoid similar issues</li>
                </ul>
            @elseif($tracking->status === 'interview_scheduled')
                <h3 style="color: #ffc107;">Interview Scheduled!</h3>
                <p>Your visa interview has been scheduled. This is an important step in your application process.</p>
                @if($tracking->interview_date)
                    <p><strong>Interview Date:</strong> {{ $tracking->interview_date->format('F d, Y') }}</p>
                @endif
                <p><strong>Preparation Tips:</strong></p>
                <ul>
                    <li>Review all your documents</li>
                    <li>Practice common interview questions</li>
                    <li>Dress professionally</li>
                    <li>Arrive early at the consulate</li>
                    <li>Check our forum for interview experiences</li>
                </ul>
            @endif
            
            @if($tracking->timeline && count($tracking->timeline) > 0)
                <h3>Your Timeline Updates:</h3>
                @foreach(array_slice($tracking->timeline, -3) as $event)
                    <div class="timeline-item">
                        <strong>{{ $event['date'] ?? '' }}</strong><br>
                        {{ $event['event'] ?? '' }}
                        @if(isset($event['description']) && $event['description'])
                            <br><small style="color: #666;">{{ $event['description'] }}</small>
                        @endif
                    </div>
                @endforeach
            @endif
            
            <center>
                <a href="{{ route('visa.show', $tracking->id) }}" class="button">
                    View Full Timeline
                </a>
            </center>
            
            <p style="margin-top: 30px;">
                <strong>Need Support?</strong><br>
                • Join our <a href="{{ route('home') }}">visa discussion forums</a><br>
                • Connect with others going through the same process<br>
                • Get tips from successful applicants
            </p>
        </div>
        
        <div class="footer">
            <p>You're receiving this because you're tracking a visa application on <a href="{{ config('app.url') }}">eForum</a>.</p>
            <p>To manage notifications, visit your <a href="{{ route('user.email.notifications') }}">email preferences</a>.</p>
            <p>&copy; {{ date('Y') }} eForum Nigeria. Supporting Your Journey.</p>
        </div>
    </div>
</body>
</html>
