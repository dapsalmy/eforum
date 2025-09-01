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
        <h4><i class="bi bi-piggy-bank me-2"></i> {{ trans('withdrawals') }}</h4>
    </div>

    <div class="dashboard-card mb-5 mb-xl-10" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-body">
            <form id="set_form" method="POST" data-validate-form>
                @csrf
                <div class="row align-items-end pb-3">
                    <div class="col-lg-6 col-sm-7">
                        <label class="form-label fs-base">{{ trans('your') }} PayPal {{ trans('email') }}</label>
                        <input type="email" name="paypal_email" id="paypal_email" value="{{ Auth::user()->paypal_email }}" data-validate="required|email">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-mint" id="set_btn">Submit</button>
            </form>
        </div>
    </div><!--/dashboard-card-->

    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-header">
            <h4 class="m-0">{{ trans('withdrawal_history') }}</h4>
        </div>
        <div class="dashboard-body">
            <div class="table-responsive">
                <!--begin::Table-->
                <table id="datatable_cms" class="table align-middle table-row-dashed gy-4 mb-0">
                    <thead>
                        <tr class="border-bottom border-gray-200 gs-0">
                            <th class="min-w-125px">{{ trans('amount') }}</th>
                            <th class="min-w-125px">PayPal</th>
                            <th class="min-w-125px">{{ trans('date_requested') }}</th>
                            <th class="min-w-125px">{{ trans('date_to_be_paid') }}</th>
                            <th class="min-w-125px">{{ trans('status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($withdrawals as $withdraw)
                            <tr>
                                <td>{{ get_setting('currency_symbol') }}{{ $withdraw->amount }}</td>
                                <td>{{ $withdraw->paypal_email }}</td>
                                <td>{{ \Carbon\Carbon::parse($withdraw->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                <td>{{ \Carbon\Carbon::parse($withdraw->process_date)->isoFormat('D MMMM YYYY') }}</td>
                                @if ($withdraw->status === 1)
                                    <td><span class="badge bg-green p-2">{{ trans('paid') }}</span></td>
                                @else
                                    <td><span class="badge bg-danger p-2">{{ trans('waiting_for_admin_to_pay_you') }}.</span></td>
                                @endif
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

        $('#datatable_cms, #datatable_cms_2').DataTable();


        // Set
        $(document).on('submit', '#set_form', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#set_btn").text('{{ trans('sending') }}...');
            $.ajax({
                url: '{{ route('user.withdrawals.set') }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {

                    end_load();

                    if (response.status == 400) {

                        showError('paypal_email', response.messages.paypal_email);
                        $("#set_btn").text('Submit');

                    }else if (response.status == 200) {

                        tata.success("Success", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        window.location.reload();

                    }else if(response.status == 401){

                        tata.error("Error", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        window.location.reload();

                    }

                }
            });
        });

    </script>

@endsection
