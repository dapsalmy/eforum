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
        <h4><i class="bi bi-x-octagon me-2"></i> {{ trans('user_blocks') }}</h4>
    </div>

    @if(get_setting('payments_on_site') == 'Yes')
        @if (Auth::user()->subscription()->users === 1)
            <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                <div class="dashboard-header">
                    <h4 class="m-0">{{ trans('users') }}</h4>
                </div>
                <div class="dashboard-body">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table id="datatable_cms" class="table align-middle table-row-dashed gy-4 mb-0">
                            <thead>
                                <tr class="border-bottom border-gray-200 gs-0">
                                    <th class="min-w-150px">{{ trans('user') }}</th>
                                    <th class="min-w-125px">{{ trans('date') }}</th>
                                    <th class="min-w-125px">{{ trans('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($blocks as $block)
                                    <tr>
                                        <td><img src="{{ my_asset('uploads/users/'.App\Models\User::find($block->block_id)->image) }}" class="img-fluid avatar avatar-rounded me-2" width="40px" height="40px" alt="Image">
                                        {{ App\Models\User::find($block->block_id)->name }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($block->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                        <td><a href="javascript:void(0)" onclick="block('{{ route('unblock') }}','{{ $block->id }}','{{ trans('unblock_user') }}?');" class="btn btn-red">{{ trans('unblock') }}</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        @else

            <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                <div class="dashboard-body">
                    <div class="upload-image my-3">
                        <h4 class="mb-3">{{ trans('to_view_blocked_users') }}, {{ trans('you_need_to_purchase_a_new_pricing_plan') }}.</h4>
                    </div>
                </div>
            </div><!--/dashboard-card-->

        @endif
    @else
        <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
            <div class="dashboard-header">
                <h4 class="m-0">{{ trans('users') }}</h4>
            </div>
            <div class="dashboard-body">
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table id="datatable_cms" class="table align-middle table-row-dashed gy-4 mb-0">
                        <thead>
                            <tr class="border-bottom border-gray-200 gs-0">
                                <th class="min-w-150px">{{ trans('user') }}</th>
                                <th class="min-w-125px">{{ trans('date') }}</th>
                                <th class="min-w-125px">{{ trans('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($blocks as $block)
                                <tr>
                                    <td><img src="{{ my_asset('uploads/users/'.App\Models\User::find($block->block_id)->image) }}" class="img-fluid avatar avatar-rounded me-2" width="40px" height="40px" alt="Image">
                                    {{ App\Models\User::find($block->block_id)->name }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($block->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                    <td><a href="javascript:void(0)" onclick="block('{{ route('unblock') }}','{{ $block->id }}','{{ trans('unblock_user') }}?');" class="btn btn-red">{{ trans('unblock') }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script src="{{ my_asset('assets/vendors/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ my_asset('assets/vendors/datatables/dataTables.bootstrap5.min.js') }}"></script>

    <script>

        $('#datatable_cms').DataTable();

    </script>

@endsection
