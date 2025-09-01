@extends('layouts.user')

@section('styles')
<link href="{{ my_asset('assets/vendors/datatables/dataTables.bootstrap5.css') }}" rel="stylesheet">
<link href="{{ my_asset('assets/vendors/datatables/jquery.dataTables_them.css') }}" rel="stylesheet">
<style>
    /* Datatable */
    table.dataTable tbody tr {
        background-color: var(--theme-white) !important;
    }
    .form-select{
        background-color: var(--theme-white) !important;
        color: var(--text-color) !important;
    }
    .form-control{
        background-color: var(--theme-white) !important;
        color: var(--text-color) !important;
    }
</style>
@endsection

@section('content')

    <div class="mb-4" data-aos="fade-down" data-aos-easing="linear">
        <h4><i class="bi bi-wallet2 me-2"></i> {{ trans('wallet') }}</h4>
        <p>{{ trans('add_funds_to_your_wallet_to_use_for_subscriptions_tips_and_more') }}.</p>
    </div>

    <div class="dashboard-card mb-5 mb-xl-10" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-body p-0">
            <h4>{{ trans('available_funds') }}</h4>
            <div class="price-price mb-2">
                <span><b>{{ get_setting('currency_symbol') }}{{ Auth::user()->wallet }}</b></span>
            </div>
        </div>
    </div><!--/dashboard-card-->

    <div class="dashboard-card mb-5 mb-xl-10" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-body p-0">
            <h4 class="mb-3">{{ trans('add_funds') }}</h4>
            <form id="deposit_form" action="{{ route('user.funds.add') }}" method="POST" data-validate-form>
                @csrf

                <div class="form-group">
                    <input id="onlyNumber" name="amount" min="{{ get_setting('min_deposit') }}" autocomplete="off" placeholder="{{ trans('amount') }} (Min - {{ get_setting('min_deposit') }})" type="number" data-validate="required|amount">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="deposit-box mt-3 mb-4">
                    <div class="d-flex justify-content-between">
                        <p>{{ trans('transaction_fee') }}</p>
                        <p>{{ get_setting('currency_symbol') }} <span id="transactionFee">0.00</span></p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p>{{ trans('total') }}</p>
                        <p>{{ get_setting('currency_symbol') }} <span id="total">0.00</span></p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="invalid-feedback" id="payment_gateway_error"></div>
                </div>

                @if(get_setting('paypal_active') == 'Yes')
                    <div class="custom-control custom-radio mb-3">
                        <input name="payment_gateway" value="PayPal" id="PayPal" class="custom-control-input" type="radio" data-validate="required">
                        <label class="custom-control-label" for="PayPal">
                            <span><strong>PayPal</strong></span>
                            <small class="w-100 d-block">{{ get_setting('paypal_fee').'%' }} + {{ get_setting('paypal_fee_cents') }}</small>
                        </label>
                    </div>
                @endif


                @if(get_setting('stripe_active') == 'Yes')
                    <div class="custom-control custom-radio mb-3">
                        <input name="payment_gateway" value="Stripe" id="Stripe" class="custom-control-input" type="radio" data-validate="required">
                        <label class="custom-control-label" for="Stripe">
                            <span><strong>Stripe</strong></span>
                            <small class="w-100 d-block">{{ get_setting('stripe_fee').'%' }} + {{ get_setting('stripe_fee_cents') }}</small>
                        </label>
                    </div>
                @endif

                <button type="submit" class="btn btn-mint w-100 mt-4" id="deposit_btn">{{ trans('add_funds') }}</button>

            </form>
        </div>
    </div><!--/dashboard-card-->


    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-header">
            <h4 class="m-0">{{ trans('deposits_history') }}</h4>
        </div>
        <div class="dashboard-body">
            <div class="table-responsive">
                <!--begin::Table-->
                <table id="datatable_cms" class="table align-middle table-row-dashed gy-4 mb-0">
                    <thead>
                        <tr class="border-bottom border-gray-200 gs-0">
                            <th class="min-w-150px">ID</th>
                            <th class="min-w-125px">{{ trans('amount') }}</th>
                            <th class="min-w-125px">{{ trans('payment_gateway') }}</th>
                            <th class="min-w-125px">{{ trans('date') }}</th>
                            <th class="min-w-125px">{{ trans('status') }}</th>
                            <th class="min-w-70px">{{ trans('invoice') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deposits as $key => $deposit)
                            <tr>
                                <td>{{ ($key+1) }}</td>
                                <td>{{ get_setting('currency_symbol') }}{{ $deposit->amount }}</td>
                                <td>{{ $deposit->gateway }}</td>
                                <td>{{ \Carbon\Carbon::parse($deposit->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                @if ($deposit->status === 1)
                                    <td><span class="badge bg-green p-2">{{ trans('active') }}</span></td>
                                @else
                                    <td><span class="badge bg-danger p-2">{{ trans('pending') }}</span></td>
                                @endif
                                <td class="text-right"><a href="{{ route('user.wallet.invoice', ['id' => $deposit->id]) }}" class="btn btn-sm btn-light">{{ trans('view') }} {{ trans('invoice') }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
<script src="{{ my_asset('assets/vendors/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ my_asset('assets/vendors/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/js/form-validation.js') }}"></script>

<script>

    $('#datatable_cms').DataTable();

    $decimal = 2;

    function toFixed(number, decimals) {
        var x = Math.pow(10, Number(decimals) + 1);
        return (Number(number) + (1 / x)).toFixed(decimals);
    }

    $('input[name=payment_gateway]').on('click', function() {

        var valueOriginal = $('#onlyNumber').val();
        var value = parseFloat($('#onlyNumber').val());
        var element = $(this).val();

        if (element != '' && value >= {{ get_setting('min_deposit') }} && valueOriginal != '' ) {
        // Fees

            if (element == 'PayPal') {
                $fee   = {{ get_setting('paypal_fee') }};
                $cents =  {{ get_setting('paypal_fee_cents') }};
            }else if(element == 'Stripe'){
                $fee   = {{ get_setting('stripe_fee') }};
                $cents =  {{ get_setting('stripe_fee_cents') }};
            }

            var amount = (value * $fee / 100) + $cents;
            var amountFinal = toFixed(amount, $decimal);

            var total = (parseFloat(value) + parseFloat(amountFinal));

            if (valueOriginal.length != 0 || valueOriginal != '' || value >= {{ get_setting('min_deposit') }} ) {
                $('#transactionFee').html(amountFinal);
                $('#total').html(total.toFixed($decimal));
            }else{
                $('#transactionFee, #total').html('0');
                $('#deposit_btn').prop('disabled', true);
            }
        }

    });

    $('#onlyNumber').on('keyup', function() {

        var valueOriginal = $('#onlyNumber').val();
        var value = parseFloat($('#onlyNumber').val());
        var paymentGateway = $('input[name=payment_gateway]:checked').val();

        if (valueOriginal.length == 0) {
            $('#transactionFee').html('0');
            $('#total').html('0');
        }

        if (paymentGateway && value >= {{ get_setting('min_deposit') }} && valueOriginal != '' ) {

            if (paymentGateway == 'PayPal') {
                $fee   = {{ get_setting('paypal_fee') }};
                $cents =  {{ get_setting('paypal_fee_cents') }};
            }else if(paymentGateway == 'Stripe'){
                $fee   = {{ get_setting('stripe_fee') }};
                $cents =  {{ get_setting('stripe_fee_cents') }};
            }

            var amount = (value * $fee / 100) + $cents;
            var amountFinal = toFixed(amount, $decimal);

            var total = (parseFloat(value) + parseFloat(amountFinal));

            if (valueOriginal.length != 0 || valueOriginal != '' || value >= {{ get_setting('min_deposit') }} ) {
                $('#transactionFee').html(amountFinal);
                $('#total').html(total.toFixed($decimal));
            } else {
                $('#transactionFee, #total').html('0');
                $('#deposit_btn').prop('disabled', true);
            }
        }
    });

</script>

@endsection
