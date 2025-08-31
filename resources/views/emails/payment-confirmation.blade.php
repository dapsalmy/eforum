<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
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
            background: #28a745;
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
        .receipt-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .receipt-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 18px;
            padding-top: 15px;
        }
        .success-icon {
            font-size: 64px;
            margin-bottom: 20px;
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
        .info-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">✅</div>
            <h1>Payment Successful!</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            
            <p>Your payment has been successfully processed. Thank you for your transaction on eForum!</p>
            
            <div class="receipt-box">
                <h3 style="margin-top: 0; text-align: center;">Payment Receipt</h3>
                
                <div class="receipt-row">
                    <span>Transaction ID:</span>
                    <span>{{ $transaction->reference }}</span>
                </div>
                
                <div class="receipt-row">
                    <span>Date:</span>
                    <span>{{ $transaction->created_at->format('F d, Y \a\t g:i A') }}</span>
                </div>
                
                <div class="receipt-row">
                    <span>Payment Method:</span>
                    <span>{{ ucfirst($transaction->gateway) }}</span>
                </div>
                
                <div class="receipt-row">
                    <span>Purpose:</span>
                    <span>{{ ucwords(str_replace('_', ' ', $transaction->purpose)) }}</span>
                </div>
                
                @if($transaction->purpose === 'subscription' && isset($plan))
                    <div class="receipt-row">
                        <span>Plan:</span>
                        <span>{{ $plan->name }}</span>
                    </div>
                @endif
                
                <div class="receipt-row">
                    <span>Amount:</span>
                    <span>₦{{ number_format($transaction->amount, 2) }}</span>
                </div>
            </div>
            
            @if($transaction->purpose === 'deposit')
                <div class="info-box">
                    <h4 style="margin-top: 0;">💰 Wallet Updated!</h4>
                    <p style="margin-bottom: 0;">
                        ₦{{ number_format($transaction->amount, 2) }} has been added to your wallet. 
                        Your new balance is <strong>₦{{ number_format($user->wallet, 2) }}</strong>.
                    </p>
                </div>
            @elseif($transaction->purpose === 'subscription')
                <div class="info-box">
                    <h4 style="margin-top: 0;">🎉 Subscription Activated!</h4>
                    <p style="margin-bottom: 0;">
                        Your premium subscription is now active. Enjoy exclusive features including:
                    </p>
                    <ul style="margin-bottom: 0;">
                        <li>Priority support</li>
                        <li>Advanced search filters</li>
                        <li>Unlimited job applications</li>
                        <li>Verified badge on your profile</li>
                    </ul>
                </div>
            @elseif($transaction->purpose === 'buy_points')
                <div class="info-box">
                    <h4 style="margin-top: 0;">🌟 Points Added!</h4>
                    <p style="margin-bottom: 0;">
                        Your points have been credited to your account. Use them to:
                    </p>
                    <ul style="margin-bottom: 0;">
                        <li>Boost your posts for more visibility</li>
                        <li>Send tips to helpful community members</li>
                        <li>Access premium content</li>
                    </ul>
                </div>
            @endif
            
            <center>
                @if($transaction->purpose === 'deposit')
                    <a href="{{ route('user.wallet') }}" class="button">
                        View Wallet
                    </a>
                @else
                    <a href="{{ route('user') }}" class="button">
                        Go to Dashboard
                    </a>
                @endif
            </center>
            
            <h3>📄 Need an Invoice?</h3>
            <p>You can download a detailed invoice for this transaction from your dashboard. This receipt has been saved to your transaction history for your records.</p>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 30px;">
                <p style="margin: 0; font-size: 14px;">
                    <strong>Security Note:</strong> This transaction was processed securely using industry-standard encryption. 
                    We do not store your payment card details. If you notice any unauthorized activity, 
                    please contact us immediately at <a href="mailto:{{ get_setting('contact_email') }}">{{ get_setting('contact_email') }}</a>.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>This is your official payment confirmation from <a href="{{ config('app.url') }}">eForum</a>.</p>
            <p>Transaction Reference: {{ $transaction->reference }}</p>
            <p>&copy; {{ date('Y') }} eForum Nigeria. Secure Payments Powered by {{ ucfirst($transaction->gateway) }}.</p>
        </div>
    </div>
</body>
</html>
