@extends('layouts.admin')

@section('content')

<main class="content">
    <a href="{{ route('admin.users.list') }}" class="btn btn-success"><i class="align-middle" data-feather="arrow-left"></i> {{ trans('users') }}</a>
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">
        <div class="row mt-50">


            <div class="col-xl-4 col-lg-4 col-md-12">
                <div class="author-wrap-ngh">
                    @if(Cache::has('user-is-online-' . $user->id))
                    <div class="online">{{ trans('online') }}</div>
                    @endif
                    <div class="author-wrap-head-ngh">
                        <div class="author-wrap-ngh-thumb">
                            <img src="{{ my_asset('uploads/users/'.$user->image) }}" class="img-fluid circle" alt="avatar">
                        </div>
                        <div class="author-wrap-ngh-info">
                            <h5>{{ $user->name }}</h5>
                            <div class="Goodup-location"><i class="align-middle" data-feather="map-pin"></i>{{ $user->location }},

                        @if($user->country != NULL)
                        {{ App\Models\Admin\Country::where('code',$user->country)->first()->name }}
                        @endif
                </div>
                        </div>
                    </div>

                    <div class="author-wrap-caption-ngh">
                        <div class="author-wrap-yuio-ngh">
                            <a href="{{ route('user', ['username' => $user->username]) }}" class="btn btn-success full-width" target="_blank">{{ trans('view_profile') }}</a>
                        </div>
                    </div>

                    <div class="author-wrap-footer-ngh">
                        <ul>
                            <li>
                                <div class="jhk-list-inf">
                                    <div class="jhk-list-inf-ico"><i class="align-middle" data-feather="eye"></i></div>
                                    <div class="jhk-list-inf-caption"><h5>{{ trans('last_seen') }}</h5><p>{{ \Carbon\Carbon::parse($user->last_seen)->diffForHumans() }}</p></div>
                                </div>
                            </li>
                            <li>
                                <div class="jhk-list-inf">
                                    <div class="jhk-list-inf-ico"><i class="align-middle" data-feather="log-in"></i></div>
                                    <div class="jhk-list-inf-caption"><h5>{{ trans('joined') }}</h5><p>{{ \Carbon\Carbon::parse($user->created_at)->isoFormat('D MMMM YYYY') }}</p></div>
                                </div>
                            </li>
                            <li>
                                <div class="jhk-list-inf">
                                    <div class="jhk-list-inf-ico"><i class="align-middle" data-feather="droplet"></i></div>
                                    <div class="jhk-list-inf-caption"><h5>{{ trans('profession') }}</h5><p>{{ $user->profession }}</p></div>
                                </div>
                            </li>
                            <li>
                                <div class="jhk-list-inf">
                                    <div class="jhk-list-inf-ico"><i class="align-middle" data-feather="layout"></i></div>
                                    <div class="jhk-list-inf-caption"><h5>{{ trans('website') }}</h5><p>{{ $user->website }}</p></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

            <div class="col-xl-8 col-lg-8 col-md-12">
              <div class="row">
            @if(get_setting('payments_on_site') == 'Yes')
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm icon-with-bg rounded-2 me-3">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="mt-0 mb-0"><span class="counter">{{ get_setting('currency_symbol') }}{{ $earnings }}</span></h3>
                                    <p class="text-muted mb-0">{{ trans('earnings') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm icon-with-bg rounded-2 me-3">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="mt-0 mb-0"><span class="counter">{{ get_setting('currency_symbol') }}{{ $user->wallet }}</span></h3>
                                    <p class="text-muted mb-0">{{ trans('wallet') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm icon-with-bg rounded-2 me-3">
                                    <i class="bi bi-journals"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="mt-0 mb-0"><span class="counter">{{ $posts }}</span> </h3>
                                    <p class="text-muted mb-0">{{ trans('posts') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm icon-with-bg rounded-2 me-3 flex-shrink-0">
                                 <i class="bi bi-chat-left-dots"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="mt-0 mb-0"><span class="counter">{{ $comments }}</span></h3>
                                    <p class="text-muted mb-0">{{ trans('comments') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm icon-with-bg rounded-2 me-3">
                                    <i class="bi bi-chat-dots"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="mt-0 mb-0"><span class="counter">{{ $replies }}</span></h3>
                                    <p class="text-muted mb-0">{{ trans('replies') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm icon-with-bg rounded-2 me-3">
                                    <i class="bi bi-eye"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="mt-0 mb-0"><span class="counter">{{ $viewers }}</span></h3>
                                    <p class="text-muted mb-0">{{ trans('views') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm icon-with-bg rounded-2 me-3 flex-shrink-0">
                                 <i class="bi bi-person-plus"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="mt-0 mb-0"><span class="counter">{{ $user->followings->count() }}</span></h3>
                                    <p class="text-muted mb-0">{{ trans('following') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm icon-with-bg rounded-2 me-3 flex-shrink-0">
                                 <i class="bi bi-people"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="mt-0 mb-0"><span class="counter">{{ $user->followers->count() }}</span></h3>
                                    <p class="text-muted mb-0">{{ trans('followers') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>

        </div><!-- row -->
      </div><!-- container -->
  </section>

</main>
@endsection
