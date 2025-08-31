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
        <h4><i class="bi bi-arrow-repeat me-2"></i> {{ trans('subscriptions') }}</h4>
    </div>

    <div class="dashboard-card  mb-5 mb-xl-10" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-body p-0 p-md-4">
            <h4>{{ trans('you_are_on') }} {{ Auth::user()->plan->name }}</h4>
            <div class="price-price mb-2">
                <span><b>{{ get_setting('currency_symbol') }}{{ Auth::user()->plan->price }}</b> /{{ Auth::user()->plan->duration }}</span>
            </div>
            <a href="{{ route('user.pricing') }}" class="d-block mb-2">{{ trans('plan_details') }}</a>
            <div class="border-top mt-2 mt-md-4 pt-2 pt-md-4">
            <div class="row mt-1 align-items-center">
                <div class="col-md-4 col-xxl-6">
                <span class="text-muted mb-4">{{ trans('auto_renews_on') }}: </span>
                <p>{{ \Carbon\Carbon::parse(Auth::user()->subscription()->will_expire)->isoFormat('D MMMM YYYY') }}</p>
                </div>
                <div class="col-md-8 col-xxl-6 text-md-end">
                <a  href="javascript:void(0)" onclick="cancel_sub('{{ route('user.subscriptions.cancel') }}','{{ Auth::user()->plan->id }}','{{ trans('cancel_subscription') }}?');" class="btn btn-outline-secondary me-3 mt-2">{{ trans('cancel_subscription') }}</a>
                <a href="{{ route('user.pricing') }}" class="btn btn-mint mt-2">{{ trans('change_plan') }}</a>
                </div>
            </div>
            </div>
        </div>
    </div><!--/dashboard-card-->

    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-header">
            <h4 class="m-0">{{ trans('subscription_history') }}</h4>
        </div>
        <div class="dashboard-body">
            <div class="table-responsive">
                <!--begin::Table-->
                <table id="datatable_cms" class="table align-middle table-row-dashed gy-4 mb-0">
                    <thead>
                        <tr class="border-bottom border-gray-200 gs-0">
                            <th class="min-w-150px">{{ trans('name') }}</th>
                            <th class="min-w-125px">{{ trans('amount') }}</th>
                            <th class="min-w-125px">{{ trans('expiry_date') }}</th>
                            <th class="min-w-125px">{{ trans('status') }}</th>
                            <th class="min-w-70px">{{ trans('invoice') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (Auth::user()->subscriptions() as $subscription)
                            <tr>
                                <td>{{ $subscription->plan->name }}</td>
                                <td>{{ get_setting('currency_symbol') }}{{ $subscription->price }}</td>
                                <td>{{ \Carbon\Carbon::parse($subscription->will_expire)->isoFormat('D MMMM YYYY') }}</td>
                                @if ($subscription->status === 1)
                                    <td><span class="badge bg-green p-2">{{ trans('active') }}</span></td>
                                @else
                                    <td><span class="badge bg-danger p-2">{{ trans('expired') }}</span></td>
                                @endif
                                <td class="text-right"><a href="{{ route('user.subscriptions.invoice', ['id' => $subscription->id]) }}" class="btn btn-sm btn-light">{{ trans('view') }} {{ trans('invoice') }}</a></td>
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

<script>

    $('#datatable_cms').DataTable();

</script>

@endsection
