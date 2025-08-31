@extends('layouts.admin')

@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">
      <div class="row mt-50">

        <div class="col-12">
            <div class="card">
                <div class="card-header">

                    <div class="d-md-flex justify-content-between align-items-center mb-10">
                        <h5 class="h4 mb-0">{{ trans('banned_users') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable_cms" class="table table-bordered table-reload">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('user') }}</th>
                                <th>{{ trans('reason') }}</th>
                                <th>{{ trans('date') }}</th>
                                <th>{{ trans('expiring') }}</th>
                                <th class="text-right">{{ trans('options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td>{{ ($key+1) }}</td>
                                    <td><img src="{{ my_asset('uploads/users/'.$user->image) }}" class="img-fluid avatar avatar-rounded me-2" width="40px" height="40px" alt="Image">
                                     {{ $user->name }}
                                    </td>
                                    <td>{{ $user->user_ban($user->id)->comment }}</td>
                                    <td>{{ \Carbon\Carbon::parse($user->user_ban($user->id)->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($user->user_ban($user->id)->expired_at)->isoFormat('D MMMM YYYY') }}</td>
                                    <td class="text-right">
                                        @if (App\Models\User::find($user->id)->isBanned())
                                            <a href="javascript:void(0)" onclick="remove_ban('{{ route('remove.ban') }}','{{ $user->id }}','{{ trans('remove_ban') }}')" class="btn btn-danger">
                                                <i class="align-middle" data-feather="x"></i> {{ trans('remove_ban') }}
                                            </a>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                </div>
            </div>
        </div>

    </div><!-- row -->
   </div><!-- container -->
  </section>

</main>
@endsection
