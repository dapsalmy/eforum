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
        <h4><i class="bi bi-file-ppt me-2"></i> {{ trans('points') }}</h4>
    </div>


    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-header">
            <h4 class="m-0">{{ trans('points') }}</h4>
        </div>
        <div class="dashboard-body">
            <div class="table-responsive">
                <!--begin::Table-->
                <table id="datatable_cms" class="table align-middle table-row-dashed gy-4 mb-0">
                    <thead>
                        <tr class="border-bottom border-gray-200 gs-0">
                            <th class="min-w-150px">{{ trans('score') }}</th>
                            <th class="min-w-125px">{{ trans('type') }}</th>
                            <th class="min-w-125px">{{ trans('date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($points as $point)
                            <tr>
                                <td>{{ $point->score }}</td>
                                @if($point->type == 1)
                                    <td><span class="badge bg-green p-2">{{ trans('login') }} {{ trans('points') }}</span></td>
                                @elseif($point->type == 2)
                                    <td><span class="badge bg-green p-2">{{ trans('register') }} {{ trans('points') }}</span></td>
                                @elseif($point->type == 3)
                                    <td><span class="badge bg-green p-2">{{ trans('post') }} {{ trans('points') }}</span></td>
                                @elseif($point->type == 4)
                                    <td><span class="badge bg-green p-2">{{ trans('comment') }} {{ trans('points') }}</span></td>
                                @elseif($point->type == 5)
                                    <td><span class="badge bg-green p-2">{{ trans('reply') }} {{ trans('points') }}</span></td>
                                @elseif($point->type == 6)
                                    <td><span class="badge bg-green p-2">{{ trans('like') }} {{ trans('points') }}</span></td>
                                @elseif($point->type == 7)
                                    <td><span class="badge bg-green p-2">{{ trans('reaction') }} {{ trans('points') }}</span></td>
                                @elseif($point->type == 8)
                                    <td><span class="badge bg-green p-2">{{ trans('share') }} {{ trans('points') }}</span></td>
                                @elseif($point->type == 9)
                                    <td><span class="badge bg-green p-2">{{ trans('subscription') }} {{ trans('points') }}</span></td>
                                @elseif($point->type == 10)
                                    <td><span class="badge bg-green p-2">{{ trans('buy_points') }}</span></td>
                                @endif

                                <td>{{ \Carbon\Carbon::parse($point->created_at)->isoFormat('D MMMM YYYY') }}</td>
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
