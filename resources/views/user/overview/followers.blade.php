@extends('layouts.user')

@section('content')

    <div class="mb-4" data-aos="fade-down" data-aos-easing="linear">
        <h4><i class="bi bi-people me-2"></i> {{ trans('followers') }} & {{ trans('following') }}</h4>
    </div>

    <div class="row g-4" data-aos="fade-up" data-aos-easing="linear">
        <div class="col-12">
            <div class="vine-tabs pb-0 px-2 px-lg-0 rounded-top">
                <ul class="nav nav-tabs nav-bottom-line nav-responsive border-0 nav-justified" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link mb-0 {{ Route::is('followers') ? 'active' : '' }}" href="{{ route('followers') }}">
                            <i class="bi bi-people fa-fw me-2"></i>{{ trans('followers') }} ({{ Auth::user()->followers->count() }})
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link mb-0 {{ Route::is('following') ? 'active' : '' }}" href="{{ route('following') }}">
                            <i class="bi bi-person-add me-2"></i> {{ trans('following') }} ({{ Auth::user()->followings->count() }})
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="mt-5">
                @if(Route::is('followers') )
                    <div class="row">
                        @forelse(Auth::user()->followers()->with('followers')->get() as $follower)


                            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-easing="linear">
                                <div class="follow-box">
                                    <div class="img">
                                        <a href="{{ route('user', ['username' => $follower->username]) }}">
                                            <img src="{{ my_asset('uploads/users/'.$follower->image) }}" alt="User">
                                        </a>
                                    </div>
                                    <div class="mt10">
                                        <span>
                                            <a class="h5" href="{{ route('user', ['username' => $follower->username]) }}">{{ $follower->name }}</a>
                                        </span>
                                        @if($follower->verified == 1)
                                            <span class="verified-badge" data-bs-toggle="tooltip" aria-label="{{ trans('verified_user') }}" data-bs-original-title="{{ trans('verified_user') }}">
                                                <i class="bi bi-patch-check"></i>
                                            </span>
                                        @endif<br>
                                        <span class="mb-0 small"><i class="bi bi-person-check me-1"></i>
                                            <span id="followers-{{ $follower->id }}">{{ $follower->followers->count() }}</span>
                                            {{ trans('followers') }}
                                        </span>
                                    </div>
                                    <div class="mt10">
                                        @if(Auth::check())
                                            @if (Auth::user()->id != $follower->id)
                                            <a href="javascript:void(0);" id="{{ $follower->id }}" class="btn rounded-pill @if(Auth::user()->isFollowing($follower)) btn-danger @else btn-mint @endif followUser">

                                                @if(Auth::user()->isFollowing($follower))<i class="bi bi-person-check me-1"></i> {{ trans('following') }} @else <i class="bi bi-person-add me-1"></i> {{ trans('follow') }} @endif
                                            </a>
                                            @endif
                                        @else
                                            <a href="#follow-dialog" class="btn btn-mint rounded-pill has-popup"><i class="bi bi-person-add me-1"></i>{{ trans('follow') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        @empty

                            <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                                <div class="dashboard-body">
                                    <div class="upload-image my-3">
                                        <h4 class="mb-3">{{ trans('no_followers_available') }}.</h4>
                                    </div>

                                </div>
                            </div><!--/dashboard-card-->

                        @endforelse
                    </div>

                @elseif (Route::is('following'))
                    <div class="row">
                        @forelse(Auth::user()->followings()->with('followable')->get() as $following)

                            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-easing="linear">
                                <div class="follow-box">
                                    <div class="img">
                                        <a href="{{ route('user', ['username' => $following->followable->username]) }}">
                                            <img src="{{ my_asset('uploads/users/'.$following->followable->image) }}" alt="User">
                                        </a>
                                    </div>
                                    <div class="mt10">
                                        <span>
                                            <a class="h5" href="{{ route('user', ['username' => $following->followable->username]) }}">{{ $following->followable->name }}</a>
                                        </span>
                                        @if($following->followable->verified == 1)
                                            <span class="verified-badge" data-bs-toggle="tooltip" aria-label="{{ trans('verified_user') }}" data-bs-original-title="{{ trans('verified_user') }}">
                                                <i class="bi bi-patch-check"></i>
                                            </span>
                                        @endif<br>
                                        <span class="mb-0 small"><i class="bi bi-person-check me-1"></i>
                                            <span id="followers-{{ $following->followable->id }}">{{ $following->followable->followers->count() }}</span>
                                            {{ trans('followers') }}
                                        </span>
                                    </div>
                                    <div class="mt10">
                                        @if(Auth::check())
                                            @if (Auth::user()->id != $following->followable->id)
                                            <a href="javascript:void(0);" id="{{ $following->followable->id }}" class="btn rounded-pill @if(Auth::user()->isFollowing($following->followable)) btn-danger @else btn-mint @endif followUser">

                                                @if(Auth::user()->isFollowing($following->followable))<i class="bi bi-person-check me-1"></i> {{ trans('following') }} @else <i class="bi bi-person-add me-1"></i> {{ trans('follow') }} @endif
                                            </a>
                                            @endif
                                        @else
                                            <a href="#follow-dialog" class="btn btn-mint rounded-pill has-popup"><i class="bi bi-person-add me-1"></i>{{ trans('follow') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        @empty

                            <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                                <div class="dashboard-body">
                                    <div class="upload-image my-3">
                                        <h4 class="mb-3">{{ trans('user_has_not_followed_anyone_yet') }}.</h4>
                                    </div>

                                </div>
                            </div><!--/dashboard-card-->

                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>

    $(document).on('click', '.followUser', function(e) {
        e.preventDefault();
        let a = $(this).attr('id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ route('follow') }}',
            method: 'post',
            dataType: "json",
            data: {item: a},
            success: function(e) {

                var t;

                if (e.bool === true){

                    $("#" + a).removeClass('btn-mint');
                    $("#" + a).addClass('btn-danger');
                    $("#" + a).text('{{ trans('following') }}');
                    t = $("#followers-" + a).text(), $("#followers-" + a).text(++t);

                }else if(e.bool === false){

                    $("#" + a).removeClass('btn-danger');
                    $("#" + a).addClass('btn-mint');
                    $("#" + a).text('{{ trans('follow') }}');
                    t = $("#followers-" + a).text(), $("#followers-" + a).text(--t);

                }

                if (e.status == 200) {

                    tata.success("Success", e.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                }
            },
            error: function(e) {

                tata.error("Error", '{{ trans('please_login_to_follow_user') }}', {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });
            }
        });
    });

    $(document).on('mouseout' , '.btn-danger' , function(e) {
        let a = $(this).attr('id');
        $("#" + a).text('{{ trans('following') }}');
    });
    $(document).on('mouseover' , '.btn-danger' , function(e) {
        let a = $(this).attr('id');
        $("#" + a).text('{{ trans('unfollow') }}');
    });
</script>
@endsection
