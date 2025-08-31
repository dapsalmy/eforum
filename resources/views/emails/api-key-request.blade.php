<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New API Key Request</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
        .key-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .user-details { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New API Key Request</h2>
            <p>A user has requested a new API key for the eForum platform.</p>
        </div>

        <div class="content">
            <div class="user-details">
                <h3>User Details</h3>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Username:</strong> {{ $user->username }}</p>
                <p><strong>Member Since:</strong> {{ $user->created_at->format('M d, Y') }}</p>
            </div>

            <div class="key-details">
                <h3>API Key Request Details</h3>
                <p><strong>Key Name:</strong> {{ $apiKey->name }}</p>
                <p><strong>Permissions:</strong> {{ implode(', ', $apiKey->permissions ?? ['read']) }}</p>
                <p><strong>Rate Limit:</strong> {{ $apiKey->rate_limit }} requests per minute</p>
                @if($apiKey->expires_at)
                    <p><strong>Expires:</strong> {{ $apiKey->expires_at->format('M d, Y H:i') }}</p>
                @endif
                @if($apiKey->notes)
                    <p><strong>Notes:</strong> {{ $apiKey->notes }}</p>
                @endif
                <p><strong>Requested:</strong> {{ $apiKey->created_at->format('M d, Y H:i') }}</p>
            </div>

            <p style="margin-top: 20px;">
                <a href="{{ route('admin.api-keys.show', $apiKey->id) }}" class="btn">Review Request</a>
            </p>
        </div>

        <div class="footer">
            <p>This email was sent from {{ config('app.name') }}</p>
            <p>You can manage API key requests from the admin panel.</p>
        </div>
    </div>
</body>
</html>
