@extends('layouts.admin')

@section('styles')

<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/ui/trumbowyg.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/colors/ui/trumbowyg.colors.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/emoji/ui/trumbowyg.emoji.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/giphy/ui/trumbowyg.giphy.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/prism/prism.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/highlight/ui/trumbowyg.highlight.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/bootstrap-taginput/bootstrap-tagsinput.css') }}">

@endsection

@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">
      <div class="row mt-50">

        <div class="col-lg-12">
            @if(Route::is('admin.reports.posts'))
                <div class="card">
                    <div class="card-header">

                        <div class="d-md-flex justify-content-between align-items-center mb-10">
                            <h5 class="h4 mb-0">{{ trans('posts_reported') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable_cms" class="table table-bordered table-reload">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('user') }}</th>
                                    <th>{{ trans('title') }}</th>
                                    <th>{{ trans('date') }}</th>
                                    <th class="text-right">{{ trans('options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $key => $report)
                                    <tr>
                                        <td>{{ ($key+1) }}</td>
                                        <td><img src="{{ my_asset('uploads/users/'.App\Models\User::find($report->user_id)->image) }}" class="img-fluid avatar avatar-rounded me-2" width="40px" height="40px" alt="Image">
                                         {{ App\Models\User::find($report->user_id)->name }}</td>
                                        <td><a href="{{ route('home.post', ['post_id' => App\Models\Posts::find($report->report_id)->post_id, 'slug' => App\Models\Posts::find($report->report_id)->slug]) }}" target="_blank">
                                            {{ App\Models\Posts::find($report->report_id)->title }}</a></td>

                                        <td>{{ \Carbon\Carbon::parse($report->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="text-right">
                                            <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="Delete"
                                            onclick="delete_item('{{ route('admin.posts.destroy') }}','{{ $report->report_id }}','{{ trans('delete_this_post') }}?');">
                                                <i class="align-middle" data-feather="trash"></i>
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    </div>
                </div>
            @elseif (Route::is('admin.reports.comments'))
                <div class="card">
                    <div class="card-header">

                        <div class="d-md-flex justify-content-between align-items-center mb-10">
                            <h5 class="h4 mb-0">{{ trans('comments_reported') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable_cms" class="table table-bordered table-reload">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('user') }}</th>
                                    <th>{{ trans('comment') }}</th>
                                    <th>{{ trans('date') }}</th>
                                    <th class="text-right">{{ trans('options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $key => $report)
                                    <tr>
                                        <td>{{ ($key+1) }}</td>
                                        <td><img src="{{ my_asset('uploads/users/'.App\Models\User::find($report->user_id)->image) }}" class="img-fluid avatar avatar-rounded me-2" width="40px" height="40px" alt="Image">
                                        {{ App\Models\User::find($report->user_id)->name }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.comments.edit', ['id' => $report->report_id]) }}" class="btn btn-soft-success btn-icon btn-circle btn-sm btn icon" title="Edit">
                                                <i class="align-middle" data-feather="eye"></i>
                                            </a>
                                        </td>

                                        <td>{{ \Carbon\Carbon::parse($report->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="text-right">

                                            <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="Delete"
                                            onclick="delete_item('{{ route('admin.comments.destroy') }}','{{ $report->report_id }}','{{ trans('delete_this_comment') }}?');">
                                                <i class="align-middle" data-feather="trash"></i>
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    </div>
                </div>
            @elseif (Route::is('admin.reports.replies'))
                <div class="card">
                    <div class="card-header">

                        <div class="d-md-flex justify-content-between align-items-center mb-10">
                            <h5 class="h4 mb-0">{{ trans('replies_reported') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable_cms" class="table table-bordered table-reload">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('user') }}</th>
                                    <th>{{ trans('reply') }}</th>
                                    <th>{{ trans('date') }}</th>
                                    <th class="text-right">{{ trans('options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $key => $report)
                                    <tr>
                                        <td>{{ ($key+1) }}</td>
                                        <td><img src="{{ my_asset('uploads/users/'.App\Models\User::find($report->user_id)->image) }}" class="img-fluid avatar avatar-rounded me-2" width="40px" height="40px" alt="Image">
                                        {{ App\Models\User::find($report->user_id)->name }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.replies.edit', ['id' => $report->report_id]) }}" class="btn btn-soft-success btn-icon btn-circle btn-sm btn icon" title="Edit">
                                                <i class="align-middle" data-feather="eye"></i>
                                            </a>
                                        </td>

                                        <td>{{ \Carbon\Carbon::parse($report->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="text-right">

                                            <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="Delete"
                                            onclick="delete_item('{{ route('admin.replies.destroy') }}','{{ $report->report_id }}','{{ trans('delete_this_comment') }}?');">
                                                <i class="align-middle" data-feather="trash"></i>
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    </div>
                </div>
            @endif
        </div>


      </div><!-- row -->
     </div><!-- container -->
    </section>

</main>
@endsection
