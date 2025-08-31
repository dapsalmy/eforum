@extends('layouts.admin')


@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">

      <div class="row mt-50">

        <div class="col-lg-6">

            <div class="card-style settings-card-2 mb-30">
                <h3 class="mb-4">{{ trans('mail_settings') }}</h3>
                <form action="{{ route('admin.settings.mail') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                          <div class="select-style-1">
                            <label>MAIL MAILER</label>
                            <div class="select-position">
                                <select name="mail_mailer" class="light-bg">
                                    <option value="smtp" @if(env('MAIL_MAILER') == 'smtp') selected @endif>SMTP</option>
                                    <option value="ses" @if(env('MAIL_MAILER') == 'ses') selected @endif>Amazon SES</option>
                                    <option value="sendmail" @if(env('MAIL_MAILER') == 'sendmail') selected @endif>Sendmail</option>
                                    <option value="log" @if(env('MAIL_MAILER') == 'log') selected @endif>Log (for testing)</option>
                                </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>MAIL HOST</label>
                            <input type="text" name="mail_host" value="{{env('MAIL_HOST')}}" placeholder="MAIL HOST" />
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>MAIL PORT</label>
                            <input type="text" name="mail_port" value="{{env('MAIL_PORT')}}" placeholder="MAIL PORT" />
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>MAIL USERNAME</label>
                            <input type="text" name="mail_username" value="{{env('MAIL_USERNAME')}}" placeholder="MAIL USERNAME" />
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>MAIL PASSWORD</label>
                            <input type="text" name="mail_password" value="{{env('MAIL_PASSWORD')}}" placeholder="MAIL PASSWORD" />
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>MAIL ENCRYPTION</label>
                            <input type="text" name="mail_encryption" value="{{env('MAIL_ENCRYPTION')}}" placeholder="MAIL ENCRYPTION e.g SSL/TLS" />
                          </div>
                        </div>
                        
                        <div class="col-12">
                          <hr>
                          <h5 class="mb-3">Common Email Settings (SMTP & SES)</h5>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>MAIL FROM ADDRESS</label>
                            <input type="email" name="mail_from_address" value="{{env('MAIL_FROM_ADDRESS')}}" placeholder="noreply@yourdomain.com" />
                            <p class="text-muted small">The email address that will appear in the "From" field</p>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>MAIL FROM NAME</label>
                            <input type="text" name="mail_from_name" value="{{env('MAIL_FROM_NAME')}}" placeholder="{{ get_setting('site_name') }}" />
                            <p class="text-muted small">The name that will appear in the "From" field</p>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>MAIL FROM DOMAIN</label>
                            <input type="text" name="mail_from_domain" value="{{env('MAIL_FROM_DOMAIN')}}" placeholder="yourdomain.com" />
                            <p class="text-muted small">Your verified sending domain (required for SES, recommended for SMTP)</p>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>RETURN PATH / BOUNCE ADDRESS</label>
                            <input type="email" name="mail_return_path" value="{{env('MAIL_RETURN_PATH')}}" placeholder="bounces@yourdomain.com" />
                            <p class="text-muted small">Email address to receive bounce notifications (leave empty to use FROM address)</p>
                          </div>
                        </div>

                        <div class="col-12">
                          <hr>
                          <h5 class="mb-3">Amazon SES Configuration</h5>
                          <p class="text-muted small mb-3">Configure these settings if you're using Amazon SES as your mail driver</p>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>AWS ACCESS KEY ID</label>
                            <input type="text" name="aws_access_key_id" value="{{env('AWS_ACCESS_KEY_ID')}}" placeholder="AWS ACCESS KEY ID" />
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>AWS SECRET ACCESS KEY</label>
                            <input type="password" name="aws_secret_access_key" value="{{env('AWS_SECRET_ACCESS_KEY')}}" placeholder="AWS SECRET ACCESS KEY" />
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>AWS REGION</label>
                            <input type="text" name="aws_default_region" value="{{env('AWS_DEFAULT_REGION', 'us-east-1')}}" placeholder="e.g., us-east-1" />
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>SES REGION (Optional)</label>
                            <input type="text" name="ses_region" value="{{env('AWS_SES_REGION')}}" placeholder="Leave empty to use AWS REGION" />
                            <p class="text-muted small">Only set this if your SES is in a different region than your AWS default region</p>
                          </div>
                        </div>

                        <div class="col-12">
                          <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                          <a href="#" onclick="testEmailConfig(event)" class="main-btn secondary-btn btn-hover ms-2">Test Email Configuration</a>
                        </div>
                      </div>

                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">Instructions</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-danger small mb-4">Please be careful when configuring email settings. Incorrect configuration will cause email sending failures.</h6>
                    
                    <h5 class="text-primary my-3">General Email Setup</h5>
                    <ul class="list-group">
                        <li class="list-group-item text-dark"><strong>From Domain:</strong> Use your verified domain (e.g., yourdomain.com)</li>
                        <li class="list-group-item text-dark"><strong>From Address:</strong> Use an email on your domain (e.g., noreply@yourdomain.com)</li>
                        <li class="list-group-item text-dark"><strong>Return Path:</strong> Set up a bounce handler email (e.g., bounces@yourdomain.com)</li>
                        <li class="list-group-item text-dark"><strong>SPF/DKIM:</strong> Configure DNS records for better deliverability</li>
                    </ul>
                    
                    <h5 class="text-primary my-3 mt-4">SMTP Configuration</h5>
                    <h6 class="text-muted my-2">For Non-SSL (Port 587)</h6>
                    <ul class="list-group">
                        <li class="list-group-item text-dark">Use TLS encryption for port 587</li>
                        <li class="list-group-item text-dark">Common hosts: smtp.gmail.com, smtp.office365.com, smtp.sendgrid.net</li>
                        <li class="list-group-item text-dark">Ensure your SMTP provider allows authentication</li>
                    </ul>
                    <br>
                    <h6 class="text-muted my-2">For SSL (Port 465)</h6>
                    <ul class="list-group">
                        <li class="list-group-item text-dark">Use SSL encryption for port 465</li>
                        <li class="list-group-item text-dark">Some providers require app-specific passwords</li>
                        <li class="list-group-item text-dark">Check firewall settings if connection fails</li>
                    </ul>
                    
                    <h5 class="text-primary my-3 mt-4">Amazon SES Configuration</h5>
                    <ul class="list-group">
                        <li class="list-group-item text-dark"><strong>Step 1:</strong> Select 'Amazon SES' as Mail Mailer</li>
                        <li class="list-group-item text-dark"><strong>Step 2:</strong> Verify your domain in SES console (adds DKIM automatically)</li>
                        <li class="list-group-item text-dark"><strong>Step 3:</strong> Create IAM user with AmazonSESFullAccess policy</li>
                        <li class="list-group-item text-dark"><strong>Step 4:</strong> Configure Return Path (Mail From) domain in SES</li>
                        <li class="list-group-item text-dark"><strong>Regions:</strong> us-east-1, eu-west-1, ap-southeast-1</li>
                        <li class="list-group-item text-dark"><strong>Note:</strong> Start in sandbox mode, request production access later</li>
                        <li class="list-group-item text-dark"><strong>Bounce Handling:</strong> SES can send bounces to Return Path email</li>
                    </ul>
                </div>
            </div>
        </div>


    </div><!-- row -->
    </div><!-- container -->
    </section>

  </main>

@endsection

@section('scripts')
<script>
function testEmailConfig(e) {
    e.preventDefault();
    
    const email = prompt("Enter email address to send test email to:", "{{ auth()->user()->email }}");
    
    if (email) {
        // Show loading
        const btn = e.target;
        const originalText = btn.innerText;
        btn.innerText = "Sending test email...";
        btn.disabled = true;
        
        // Make AJAX request to test email
        fetch("{{ route('admin.settings.mail.test') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
            } else {
                toastr.error(data.message);
            }
        })
        .catch(error => {
            toastr.error('Failed to send test email. Please check your configuration.');
        })
        .finally(() => {
            btn.innerText = originalText;
            btn.disabled = false;
        });
    }
}
</script>
@endsection
