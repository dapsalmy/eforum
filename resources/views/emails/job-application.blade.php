<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Job Application</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
        .job-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .applicant-details { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Job Application Received</h2>
            <p>You have received a new application for your job posting.</p>
        </div>

        <div class="content">
            <div class="job-details">
                <h3>Job Details</h3>
                <p><strong>Position:</strong> {{ $job->title }}</p>
                <p><strong>Company:</strong> {{ $job->company_name }}</p>
                <p><strong>Location:</strong> {{ $job->location }}</p>
            </div>

            <div class="applicant-details">
                <h3>Applicant Details</h3>
                <p><strong>Name:</strong> {{ $applicant->name }}</p>
                <p><strong>Email:</strong> {{ $applicant->email }}</p>
                <p><strong>Username:</strong> {{ $applicant->username }}</p>
                @if($applicant->bio)
                    <p><strong>Bio:</strong> {{ $applicant->bio }}</p>
                @endif
            </div>

            @if($cover_letter)
                <div class="cover-letter">
                    <h3>Cover Letter</h3>
                    <p>{{ $cover_letter }}</p>
                </div>
            @endif

            <p style="margin-top: 20px;">
                <a href="{{ route('admin.jobs.applications', $job->id) }}" class="btn">View All Applications</a>
            </p>
        </div>

        <div class="footer">
            <p>This email was sent from {{ config('app.name') }}</p>
            <p>You can manage your job postings from your dashboard.</p>
        </div>
    </div>
</body>
</html>
