@extends('layouts.front')

@section('content')

    <div class="vine-header mb-4" data-aos="fade-down" data-aos-easing="linear">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('home') }}"><span class="bi bi-house me-1"></span>{{ trans('home') }}</a></li>
                        <li>{{ trans('stats') }}</li>
                    </ul>
                    <h2 class="mb-2">{{ trans('stats') }}</h2>
                </div>
            </div>
        </div>
    </div><!--/vine-header-->

    <div class="stats">
        <div class="container">
            <div class="row">
                    <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                        <div class="counter-box">
                            <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                            <div class="number-counter"><span class="number"><span class="counter">{{ $users }}</span></span></div>
                            <h6 class="title">{{ trans('users') }}</h6>
                        </div>
                    </div>
                    <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                        <div class="counter-box">
                            <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                            <div class="number-counter"><span class="number"><span class="counter">{{ $posts }}</span></span></div>
                            <h6 class="title">{{ trans('posts') }}</h6>
                        </div>
                    </div>
                    <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                        <div class="counter-box">
                            <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                            <div class="number-counter"><span class="number"><span class="counter">{{ $comments }}</span></span></div>
                            <h6 class="title">{{ trans('comments') }}</h6>
                        </div>
                    </div>
                    <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                        <div class="counter-box">
                            <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                            <div class="number-counter"><span class="number"><span class="counter">{{ $replies }}</span></span></div>
                            <h6 class="title">{{ trans('replies') }}</h6>
                        </div>
                    </div>
                    <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                        <div class="counter-box">
                            <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                            <div class="number-counter"><span class="number"><span class="counter">{{ $categories }}</span></span></div>
                            <h6 class="title">{{ trans('categories') }}</h6>
                        </div>
                    </div>
                    <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                        <div class="counter-box">
                            <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                            <div class="number-counter"><span class="number"><span class="counter">{{ $tags }}</span></span></div>
                            <h6 class="title">{{ trans('tags') }}</h6>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div class="online-users">
        <div class="container">
            <div class="row">
                <h2 class="my-4" data-aos="fade-up" data-aos-easing="linear">{{ trans('online_users') }}</h2>
                @forelse($online as $user)
                    <div class="col-lg-2" data-aos="fade-up" data-aos-easing="linear">
                        <div class="online-user text-center">
                            <a href="{{ route('user', ['username' => $user->username]) }}">
                                <img src="{{ my_asset('uploads/users/'.$user->image) }}" alt="user"></a>
                            <div class="name"><a href="{{ route('user', ['username' => $user->username]) }}">{{ $user->name }}</a></div>
                            <div class="info">{{'@'.$user->username }}</div>
                        </div>
                    </div>
                @empty
                    <p data-aos="fade-up" data-aos-easing="linear">{{ trans('no_online_users_currently') }}</p>
                @endforelse
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ my_asset('assets/vendors/waypoints/waypoints.js') }}"></script>
    <script src="{{ my_asset('assets/vendors/counter-up/counter-up.js') }}"></script>
    <script src="{{ my_asset('assets/frontend/js/counterup.js') }}"></script>
@endsection
