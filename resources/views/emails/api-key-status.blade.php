<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Key Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
        .key-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-suspended { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>API Key Status Update</h2>
            <p>Your API key request has been {{ $action }}.</p>
        </div>

        <div class="content">
            <div class="key-details">
                <h3>API Key Details</h3>
                <p><strong>Key Name:</strong> {{ $apiKey->name }}</p>
                <p><strong>Status:</strong> 
                    <span class="status-{{ $action }}">
                        {{ ucfirst($action) }}
                    </span>
                </p>
                <p><strong>Permissions:</strong> {{ implode(', ', $apiKey->permissions ?? ['read']) }}</p>
                <p><strong>Rate Limit:</strong> {{ $apiKey->rate_limit }} requests per minute</p>
                @if($apiKey->expires_at)
                    <p><strong>Expires:</strong> {{ $apiKey->expires_at->format('M d, Y H:i') }}</p>
                @endif
                @if($apiKey->approved_at)
                    <p><strong>Approved:</strong> {{ $apiKey->approved_at->format('M d, Y H:i') }}</p>
                @endif
            </div>

            @if($reason)
                <div class="key-details">
                    <h3>Reason</h3>
                    <p>{{ $reason }}</p>
                </div>
            @endif

            @if($action === 'approved')
                <div class="key-details">
                    <h3>Next Steps</h3>
                    <p>Your API key is now active! You can:</p>
                    <ul>
                        <li>Retrieve your API key from your dashboard</li>
                        <li>Start making API requests</li>
                        <li>View your API usage and limits</li>
                    </ul>
                </div>

                <p style="margin-top: 20px;">
                    <a href="{{ route('user.api-keys') }}" class="btn">View My API Keys</a>
                </p>
            @elseif($action === 'rejected')
                <div class="key-details">
                    <h3>What's Next?</h3>
                    <p>You can:</p>
                    <ul>
                        <li>Review the rejection reason above</li>
                        <li>Submit a new request with updated information</li>
                        <li>Contact support if you have questions</li>
                    </ul>
                </div>
            @elseif($action === 'suspended')
                <div class="key-details">
                    <h3>What's Next?</h3>
                    <p>Your API key has been suspended. You can:</p>
                    <ul>
                        <li>Review the suspension reason above</li>
                        <li>Contact support to appeal the suspension</li>
                        <li>Submit a new request if needed</li>
                    </ul>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>This email was sent from {{ config('app.name') }}</p>
            <p>If you have any questions, please contact support.</p>
        </div>
    </div>
</body>
</html>
