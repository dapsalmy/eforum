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
                        <h5 class="h4 mb-0">{{ trans('subscriptions') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable_cms" class="table table-bordered table-reload">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('user') }}</th>
                                <th>{{ trans('pricing_plan') }}</th>
                                <th>{{ trans('price') }}</th>
                                <th>{{ trans('date_subscribed') }}</th>
                                <th>{{ trans('date_to_expire') }}</th>
                                <th>{{ trans('status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscriptions as $key => $subscription)
                                <tr>
                                    <td>{{ ($key+1) }}</td>
                                    <td><img src="{{ my_asset('uploads/users/'.App\Models\User::find($subscription->user_id)->image) }}" class="img-fluid avatar avatar-rounded me-2" width="40px" height="40px" alt="Image">
                                     {{ App\Models\User::find($subscription->user_id)->name }}
                                    </td>
                                    <td>{{ App\Models\Plans::find($subscription->plan_id)->name }}</td>
                                    <td>{{ get_setting('currency_symbol') }}{{ $subscription->price }}</td>
                                    <td>{{ \Carbon\Carbon::parse($subscription->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($subscription->will_expire)->isoFormat('D MMMM YYYY') }}</td>
                                    @if ($subscription->status === 1)
                                        <td><span class="badge bg-success p-2">{{ trans('active') }}</span></td>
                                    @else
                                        <td><span class="badge bg-danger p-2">{{ trans('expired') }}</span></td>
                                    @endif
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
