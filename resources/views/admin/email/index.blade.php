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
                          <div class="input-style-1">
                            <label>MAIL FROM ADDRESS</label>
                            <input type="text" name="mail_from_address" value="{{env('MAIL_FROM_ADDRESS')}}" placeholder="MAIL FROM ADDRESS" />
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="input-style-1">
                            <label>MAIL FROM NAME</label>
                            <input type="text" name="mail_from_name" value="{{env('MAIL_FROM_NAME')}}" placeholder="MAIL FROM NAME" />
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
                    
                    <h5 class="text-primary my-3">SMTP Configuration</h5>
                    <h6 class="text-muted my-2">For Non-SSL</h6>
                    <ul class="list-group">
                        <li class="list-group-item text-dark">Select sendmail for Mail Driver if you face any issue after configuring smtp as Mail Driver </li>
                        <li class="list-group-item text-dark">Set Mail Host according to your server Mail Client Manual Settings</li>
                        <li class="list-group-item text-dark">Set Mail port as 587</li>
                        <li class="list-group-item text-dark">Set Mail Encryption as ssl if you face issue with tls</li>
                    </ul>
                    <br>
                    <h6 class="text-muted my-2">For SSL</h6>
                    <ul class="list-group">
                        <li class="list-group-item text-dark">Select sendmail for Mail Driver if you face any issue after configuring smtp as Mail Driver</li>
                        <li class="list-group-item text-dark">Set Mail Host according to your server Mail Client Manual Settings</li>
                        <li class="list-group-item text-dark">Set Mail port as 465</li>
                        <li class="list-group-item text-dark">Set Mail Encryption as ssl</li>
                    </ul>
                    
                    <h5 class="text-primary my-3 mt-4">Amazon SES Configuration</h5>
                    <ul class="list-group">
                        <li class="list-group-item text-dark">Select 'Amazon SES' as Mail Mailer</li>
                        <li class="list-group-item text-dark">Create an IAM user with SES send permissions</li>
                        <li class="list-group-item text-dark">Verify your sending domain or email address in SES</li>
                        <li class="list-group-item text-dark">Common regions: us-east-1, eu-west-1, ap-southeast-1</li>
                        <li class="list-group-item text-dark">Leave SMTP fields empty when using SES</li>
                        <li class="list-group-item text-dark">Test with sandbox first before requesting production access</li>
                    </ul>
                </div>
            </div>
        </div>


    </div><!-- row -->
    </div><!-- container -->
    </section>

  </main>

@endsection
