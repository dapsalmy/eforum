@extends('layouts.admin')


@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
     <div class="container-fluid">
      <div class="row mt-50">

        <div class="col-lg-3">

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills flex-column" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('admin.gateways.paypal') ? 'active' : '' }}" href="{{ route('admin.gateways.paypal') }}">
                            <i class="align-middle me-1" data-feather="compass"></i> PayPal Settings </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('admin.gateways.stripe') ? 'active' : '' }}" href="{{ route('admin.gateways.stripe') }}">
                            <i class="align-middle me-1" data-feather="compass"></i> Stripe Settings </a>
                        </li>
                    </ul>
                </div>
            </div>


        </div>

        <div class="col-lg-9">

            @if(Route::is('admin.gateways.paypal') )

                <div class="card-style settings-card-2 mb-30">
                    <form action="{{ route('admin.gateways.paypal') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                             <div class="select-style-1">
                                <label>Enable PayPal</label>
                                <div class="select-position">
                                    <select name="paypal_active" class="light-bg">
                                        <option @if (get_setting('paypal_active') == 'Yes') selected="selected" @endif value="Yes">Yes</option>
                                        <option @if (get_setting('paypal_active') == 'No') selected="selected" @endif value="No">No</option>
                                    </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-12">
                             <div class="select-style-1">
                                <label>PayPal Mode</label>
                                <div class="select-position">
                                    <select name="paypal_mode" class="light-bg">
                                        <option @if (get_setting('paypal_mode') == 'sandbox') selected="selected" @endif value="sandbox">Sandbox</option>
                                        <option @if (get_setting('paypal_mode') == 'live') selected="selected" @endif value="live">Live</option>
                                    </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label>Client ID</label>
                                    <input type="text" name="paypal_client_id" value="{{ get_setting('paypal_client_id') }}" placeholder="Client ID" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label>Secret ID</label>
                                    <input type="text" name="paypal_secret" value="{{ get_setting('paypal_secret') }}" placeholder="Secret ID" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label>Percentage Fee %</label>
                                    <input type="text" name="paypal_fee" value="{{ get_setting('paypal_fee') }}" placeholder="Percentage Fee %" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label>Fee Cents</label>
                                    <input type="text" name="paypal_fee_cents" value="{{ get_setting('paypal_fee_cents') }}" placeholder="Fee Cents" />
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                <button type="submit" class="main-btn primary-btn btn-hover">Submit</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

            @elseif(Route::is('admin.gateways.stripe') )

                <div class="card-style settings-card-2 mb-30">
                    <form action="{{ route('admin.gateways.stripe') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="select-style-1">
                                <label>Enable Stripe</label>
                                <div class="select-position">
                                    <select name="stripe_active" class="light-bg">
                                        <option @if (get_setting('stripe_active') == 'Yes') selected="selected" @endif value="Yes">Yes</option>
                                        <option @if (get_setting('stripe_active') == 'No') selected="selected" @endif value="No">No</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label>Publishable Key</label>
                                    <input type="text" name="stripe_key" value="{{ get_setting('stripe_key') }}" placeholder="Publishable Key" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label>Secret Key</label>
                                    <input type="text" name="stripe_secret" value="{{ get_setting('stripe_secret') }}" placeholder="Secret ID" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label>Percentage Fee %</label>
                                    <input type="text" name="stripe_fee" value="{{ get_setting('stripe_fee') }}" placeholder="Percentage Fee %" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label>Fee Cents</label>
                                    <input type="text" name="stripe_fee_cents" value="{{ get_setting('stripe_fee_cents') }}" placeholder="Fee Cents" />
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                <button type="submit" class="main-btn primary-btn btn-hover">Submit</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

            @endif

        </div>

      </div>
     </div>
    </section>
</main>

@endsection
