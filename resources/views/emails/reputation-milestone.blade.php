<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reputation Milestone Achieved!</title>
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
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #333;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .trophy {
            font-size: 72px;
            margin-bottom: 20px;
        }
        .content {
            padding: 40px 30px;
        }
        .milestone-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            border: 2px solid #FFD700;
        }
        .milestone-box h2 {
            color: #008751;
            margin: 10px 0;
            font-size: 36px;
        }
        .benefits-box {
            background: #d4edda;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .stat-row {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            text-align: center;
        }
        .stat-item {
            flex: 1;
        }
        .stat-item h3 {
            margin: 0;
            color: #008751;
            font-size: 28px;
        }
        .stat-item p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
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
        .badge-preview {
            display: inline-block;
            padding: 8px 16px;
            background: #FFD700;
            color: #333;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 5px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="trophy">🏆</div>
            <h1>Congratulations, {{ $user->name }}!</h1>
            <p>You've reached a new reputation milestone!</p>
        </div>
        
        <div class="content">
            <div class="milestone-box">
                <p style="margin: 0; color: #666;">Your Reputation Score</p>
                <h2>{{ number_format($user->reputation_score) }}</h2>
                <p style="margin: 10px 0 0 0; color: #666;">
                    You're now in the top {{ $percentile }}% of contributors!
                </p>
            </div>
            
            <p>Your dedication to helping fellow Nigerians navigate visa applications, find job opportunities, and build meaningful relationships has earned you this recognition!</p>
            
            @if($newBadge)
                <h3 style="text-align: center;">🎖️ New Badge Unlocked!</h3>
                <center>
                    <div class="badge-preview">{{ $newBadge }}</div>
                </center>
            @endif
            
            <div class="benefits-box">
                <h3 style="margin-top: 0;">🎁 Your New Benefits:</h3>
                <ul>
                    @if($user->reputation_score >= 1000)
                        <li><strong>Trusted Contributor Badge:</strong> Your content is highlighted as trusted</li>
                    @endif
                    @if($user->reputation_score >= 2500)
                        <li><strong>Expert Status:</strong> Mark yourself as an expert in visa categories</li>
                    @endif
                    @if($user->reputation_score >= 5000)
                        <li><strong>Moderator Privileges:</strong> Help maintain community standards</li>
                    @endif
                    @if($user->reputation_score >= 10000)
                        <li><strong>Community Leader:</strong> Access to exclusive features and direct support</li>
                    @endif
                    <li><strong>Increased Visibility:</strong> Your posts and answers get priority placement</li>
                    <li><strong>Enhanced Trust:</strong> Users are more likely to follow your advice</li>
                </ul>
            </div>
            
            <div class="stat-row">
                <div class="stat-item">
                    <h3>{{ $stats['helpful_answers'] ?? 0 }}</h3>
                    <p>Helpful Answers</p>
                </div>
                <div class="stat-item">
                    <h3>{{ $stats['best_answers'] ?? 0 }}</h3>
                    <p>Best Answers</p>
                </div>
                <div class="stat-item">
                    <h3>{{ $stats['people_helped'] ?? 0 }}</h3>
                    <p>People Helped</p>
                </div>
            </div>
            
            <h3>📈 Your Top Contributions:</h3>
            <ul>
                @foreach($topContributions as $contribution)
                    <li>
                        <strong>{{ $contribution['category'] }}:</strong> 
                        {{ $contribution['points'] }} reputation points
                        @if($contribution['badge'])
                            <span style="color: #008751;">✓ {{ $contribution['badge'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
            
            <center>
                <a href="{{ route('user.reputation') }}" class="button">
                    View Your Full Reputation
                </a>
            </center>
            
            <h3>🚀 Keep Growing!</h3>
            <p>Here's how to continue building your reputation:</p>
            <ul>
                <li>Answer visa questions with detailed, helpful information</li>
                <li>Share your successful visa timeline to guide others</li>
                <li>Post legitimate job opportunities you find</li>
                <li>Provide thoughtful relationship advice</li>
                <li>Report spam and help keep the community clean</li>
            </ul>
            
            <p style="margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 5px;">
                <strong>🌟 Fun Fact:</strong> Your contributions have potentially saved the community over 
                <strong>{{ number_format($stats['time_saved_hours'] ?? 0) }} hours</strong> 
                of research time and helped 
                <strong>{{ number_format($stats['people_helped'] ?? 0) }} people</strong> 
                make better decisions!
            </p>
        </div>
        
        <div class="footer">
            <p><strong>Thank you for making eForum amazing!</strong></p>
            <p>You're receiving this because you achieved a reputation milestone on <a href="{{ config('app.url') }}">eForum</a>.</p>
            <p>Manage notifications in your <a href="{{ route('user.email.notifications') }}">email preferences</a>.</p>
            <p>&copy; {{ date('Y') }} eForum Nigeria. Celebrating Our Community Heroes.</p>
        </div>
    </div>
</body>
</html>
