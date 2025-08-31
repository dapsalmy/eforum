@extends('layouts.front')

@section('styles')
<link rel="stylesheet" href="{{ my_asset('assets/vendors/emoji-picker/lib/css/emoji.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/magnific-popup/magnific-popup.css') }}">
<script src="{{ my_asset('assets/vendors/emoji-picker/lib/js/config.js') }}"></script>
<script src="{{ my_asset('assets/vendors/emoji-picker/lib/js/util.js') }}"></script>
<script src="{{ my_asset('assets/vendors/emoji-picker/lib/js/jquery.emojiarea.js') }}"></script>
<script src="{{ my_asset('assets/vendors/emoji-picker/lib/js/emoji-picker.js') }}"></script>
<script src="{{ my_asset('assets/vendors/magnific-popup/magnific-popup.js') }}"></script>

@endsection

@section('content')


@if (Auth::check() && \App\Models\Block::where('user_id', Auth::user()->id)->where('block_id', $user->id)->exists())

    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-body">
            <div class="upload-image my-3">
                <h4 class="mb-3">{{ trans('you_have_blocked') }} {{ $user->name }}.</h4>
            </div>
        </div>
    </div><!--/dashboard-card-->

@elseif (Auth::check() && \App\Models\Block::where('user_id', $user->id)->where('block_id', Auth::user()->id)->exists())

    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-body">
            <div class="upload-image my-3">
                <h4 class="mb-3">{{ trans('user_not_available') }}.</h4>
            </div>
        </div>
    </div><!--/dashboard-card-->

@else

    <div class="user-header media mb-5" data-aos="fade-down" data-aos-easing="linear">
        <div class="media-head me-4">
            <img src="{{ my_asset('uploads/users/'.$user->image) }}" alt="Avatar">
            @if(Cache::has('user-is-online-' . $user->id))
             <span class="text-white wd-1 ht-1 rounded-pill profile-online"></span>
            @endif
        </div>
        <div class="media-body">
                <h3>{{ $user->name }}
                    @if($user->verified == 1)
                        <span class="verified-badge" data-bs-toggle="tooltip" aria-label="{{ trans('verified_user') }}" data-bs-original-title="{{ trans('verified_user') }}">
                        <i class="bi bi-patch-check"></i>
                        </span>
                    @endif

                    @if($user->role == 'Admin')
                        <span class="mod" data-bs-toggle="tooltip" aria-label="Admin" data-bs-original-title="Admin">
                            <i class="bi bi-octagon-half"></i>
                        </span>
                    @endif

                    @if($user->role == 'Moderator')
                        <span class="mod" data-bs-toggle="tooltip" aria-label="Moderator" data-bs-original-title="Moderator">
                            <i class="bi bi-hexagon-half"></i>
                        </span>
                    @endif

                    @if ($user->user_sub($user->id)->plan_id != 1)
                        <span class="mod" data-bs-toggle="tooltip" aria-label="Pro" data-bs-original-title="Pro">
                            <i class="bi bi-star-half"></i>
                        </span>
                    @endif
                </h3>
            <p class="text-muted ms-0 mb-2 my-2">
                <span class="me-3"><i class="bi bi-box-arrow-in-down me-2"></i>{{ trans('joined') }}: {{ \Carbon\Carbon::parse($user->created_at)->isoFormat('D MMMM YYYY') }}</span>
                <span><i class="bi bi-eye me-2"></i>{{ trans('last_seen') }}: {{ \Carbon\Carbon::parse($user->last_seen)->diffForHumans() }} </span>
            </p>
            <p class="text-muted ms-0 mb-2 my-2">
                <span class="me-3"><i class="bi bi-file-ppt me-2"></i>{{ trans('points') }}: {{ $user->total_points() }}</span>
                <span class="me-3"><i class="bi bi-eye-slash me-2"></i>{{ trans('profile_views') }}: {{ $user->views() }}</span>
                @if(get_setting('payments_on_site') == 'Yes')
                    <span class="me-3"><i class="bi bi-piggy-bank me-2"></i>{{ trans('earnings') }}: {{ get_setting('currency_symbol') }}{{ $user->earnings }}</span>
                @endif
            </p>
            <p class="text-muted ms-0 mb-2 my-2">
                @if ($user->profession != '')
                <span class="me-3"><i class="bi bi-card-text me-2"></i>{{ $user->profession }}</span>
                @endif
                @if ($user->location != '' || $user->country != NULL)
                <span class="me-3"><i class="bi bi-pin-map me-2"></i>{{ $user->location }},

                    @if($user->country != NULL)
                    {{ App\Models\Admin\Country::where('code',$user->country)->first()->name }}
                    @endif
                </span>
                @endif
            </p>

            <div class="user-header-socials d-flex my-2">
                @if ($user->twitter != '')
                 <a href="{{ $user->twitter }}" target="_blank"><i class="bi bi-twitter"></i></a>
                @endif
                @if ($user->facebook != '')
                 <a href="{{ $user->facebook }}" target="_blank"><i class="bi bi-facebook"></i></a>
                @endif
                @if ($user->instagram != '')
                 <a href="{{ $user->instagram }}" target="_blank"><i class="bi bi-instagram"></i></a>
                @endif
                @if ($user->linkedin != '')
                 <a href="{{ $user->linkedin }}" target="_blank"><i class="bi bi-linkedin"></i></a>
                @endif
                @if ($user->website != '')
                 <a href="{{ $user->website }}" target="_blank">{{ $user->website }}</a>
                @endif
            </div>

            <div class="user-header-follow d-flex align-items-center g-20">
                <span class="d-flex flex-column me-2">
                    <span class="h6" id="following-{{ $user->id }}">{{ $user->followings->count() }}</span>
                    <span class="meta">{{ trans('following') }}</span>
                </span>
                <span class="d-flex flex-column">
                    <span class="h6" id="followers-{{ $user->id }}">{{ $user->followers->count() }}</span>
                    <span class="meta">{{ trans('followers') }}</span>
                </span>
                @if(Auth::check())
                 @if ($user->id != Auth::user()->id)
                    <a href="javascript:void(0);" id="{{ $user->id }}" class="btn btn-xs @if(Auth::user()->isFollowing($user)) btn-danger @else btn-mint @endif followUser">
                      @if(Auth::user()->isFollowing($user)) <i class="bi bi-person-check me-1"></i> {{ trans('following') }} @else <i class="bi bi-person-add me-1"></i> {{ trans('follow') }} @endif
                    </a>

                    @if (\App\Models\Chats::where('sender_id', Auth::user()->id)->where('receiver_id', $user->id)->orWhere('sender_id', $user->id)->where('receiver_id',  Auth::user()->id)->exists())
                     <a href="{{ route('user.chats') }}" class="btn btn-xs btn-border d-none d-sm-block"><i class="bi bi-chat-text me-1"></i> {{ trans('message') }}</a>
                    @else
                     <a href="#small-dialog-{{ $user->id }}" class="btn btn-xs btn-border d-none d-sm-block has-popup"><i class="bi bi-chat-text me-1"></i>{{ trans('message') }}</a>
                    @endif

                    @if(get_setting('payments_on_site') == 'Yes')
                        @if (Auth::user()->subscription()->tips == 1)
                        <a href="#tip-dialog-{{ $user->id }}" class="btn btn-xs btn-red d-none d-sm-block has-popup"><i class="bi bi-currency-dollar me-1"></i>{{ trans('tip') }}</a>
                        @endif
                    @endif

                 @endif
                @else
                    <a href="#follow-dialog" class="btn btn-xs btn-mint d-none d-sm-block has-popup"><i class="bi bi-person-add me-1"></i> {{ trans('follow') }}</a>
                    <a href="#small-dialog-{{ $user->id }}" class="btn btn-xs btn-border d-none d-sm-block has-popup"><i class="bi bi-chat-text me-1"></i>{{ trans('message') }}</a>
                    @if(get_setting('payments_on_site') == 'Yes')
                        <a href="#tip-dialog-{{ $user->id }}" class="btn btn-xs btn-red d-none d-sm-block has-popup"><i class="bi bi-currency-dollar me-1"></i>{{ trans('tip') }}</a>
                    @endif
                @endif
            </div>

            <div class="d-block d-sm-none">
                @if(Auth::check())
                    @if ($user->id != Auth::user()->id)
                        <a href="javascript:void(0);" id="{{ $user->id }}" class="btn @if(Auth::user()->isFollowing($user)) btn-danger @else btn-mint @endif followUser">
                        @if(Auth::user()->isFollowing($user)) <i class="bi bi-person-check me-1"></i> {{ trans('following') }} @else <i class="bi bi-person-add me-1"></i> {{ trans('follow') }} @endif
                        </a>

                        @if (\App\Models\Chats::where('sender_id', Auth::user()->id)->where('receiver_id', $user->id)->orWhere('sender_id', $user->id)->where('receiver_id',  Auth::user()->id)->exists())
                        <a href="{{ route('user.chats') }}" class="btn btn-border my-2"><i class="bi bi-chat-text me-1"></i> {{ trans('message') }}</a>
                        @else
                        <a href="#small-dialog-{{ $user->id }}" class="btn btn-border my-2 has-popup"><i class="bi bi-chat-text me-1"></i>{{ trans('message') }}</a>
                        @endif
                        @if(get_setting('payments_on_site') == 'Yes')
                            @if (Auth::user()->subscription()->tips == 1)
                            <a href="#tip-dialog-{{ $user->id }}" class="btn btn-red d-none d-sm-block has-popup"><i class="bi bi-currency-dollar me-1"></i>{{ trans('send') }} {{ trans('tip') }}</a>
                            @endif
                        @endif

                    @endif
                @else
                    <a href="#follow-dialog" class="btn btn-mint my-2 has-popup"><i class="bi bi-person-add me-1"></i> {{ trans('follow') }}</a>
                    <a href="#small-dialog-{{ $user->id }}" class="btn btn-border my-2 has-popup"><i class="bi bi-chat-text me-1"></i>{{ trans('message') }}</a>
                    @if(get_setting('payments_on_site') == 'Yes')
                        <a href="#tip-dialog-{{ $user->id }}" class="btn btn-red d-none d-sm-block has-popup"><i class="bi bi-currency-dollar me-1"></i>{{ trans('send') }} {{ trans('tip') }}</a>
                    @endif
                @endif
            </div>

            @if(Auth::check())
                @if($user->isFollowing(Auth::user()))
                <p class="small text-muted"><span class="badge bg-red">{{ trans('follows_you') }}</span></p>
                @endif
            @endif

        </div>
        <div class="card-action-wrap">
            @if (Auth::check())
                @if (Auth::user()->id != $user->id)
                    <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></a>
                    <div class="dropdown-menu dropdown-menu-end">

                        <a href="#report-dialog" class="dropdown-item has-popup"><i class="bi bi-exclamation-octagon me-2"></i> {{ trans('report') }} {{ trans('user') }}</a>
                        @if(get_setting('payments_on_site') == 'Yes')
                            @if (Auth::user()->subscription()->users == 1)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="block('{{ route('block') }}','{{ $user->id }}','Block User?');"><i class="bi bi-trash3 me-2"></i> {{ trans('block') }} {{ trans('user') }}</a>
                            @endif
                        @else
                            <a class="dropdown-item" href="javascript:void(0)" onclick="block('{{ route('block') }}','{{ $user->id }}','Block User?');"><i class="bi bi-trash3 me-2"></i> {{ trans('block') }} {{ trans('user') }}</a>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div class="row g-4" data-aos="fade-up" data-aos-easing="linear">
        <div class="col-12">
                <ul class="vine-nav pb-0 px-2 px-lg-0 rounded-top">
                    <li class="nav-item">
                        <a class="nav-link mb-0 {{ Route::is('user') ? 'active' : '' }}" href="{{ route('user', ['username' => $user->username]) }}">
                            <i class="bi bi-person-lines-fill me-1"></i>{{ trans('bio') }} & {{ trans('badges') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 {{ Route::is('user.posts') ? 'active' : '' }}" href="{{ route('user.posts', ['username' => $user->username]) }}">
                            <i class="bi bi-journals me-1"></i>{{ trans('posts') }} ({{ $user->posts->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 {{ Route::is('user.comments') ? 'active' : '' }}" href="{{ route('user.comments', ['username' => $user->username]) }}">
                            <i class="bi bi-chat-left-dots me-1"></i> {{ trans('comments') }} ({{ $user->comments->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 {{ Route::is('user.replies') ? 'active' : '' }}" href="{{ route('user.replies', ['username' => $user->username]) }}">
                            <i class="bi bi-chat-dots me-1"></i> {{ trans('replies') }} ({{ $user->replies->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 {{ Route::is('user.followers') ? 'active' : '' }}" href="{{ route('user.followers', ['username' => $user->username]) }}">
                            <i class="bi bi-people me-1"></i>{{ trans('followers') }} ({{ $user->followers->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 {{ Route::is('user.following') ? 'active' : '' }}" href="{{ route('user.following', ['username' => $user->username]) }}">
                            <i class="bi bi-people me-1"></i>{{ trans('following') }} ({{ $user->followings->count() }})
                        </a>
                    </li>
                </ul>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="mt-5">
                @if(Route::is('user'))

                    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                        <div class="dashboard-body">
                            <h3>{{ $user->name }}</h3>
                            <h6 class="text-muted mt-2 mb-4">{{'@'.$user->username }}</h6>
                            <p class="text-muted ms-0 mb-2 my-2">
                                <span class="me-3"><i class="bi bi-file-ppt me-2"></i>{{ trans('points') }}: {{ $user->total_points() }}</span>
                                <span class="me-3"><i class="bi bi-eye-slash me-2"></i>{{ trans('profile_views') }}: {{ $user->views() }}</span>
                                @if(get_setting('payments_on_site') == 'Yes')
                                    <span class="me-3"><i class="bi bi-piggy-bank me-2"></i>{{ trans('earnings') }}: {{ get_setting('currency_symbol') }}{{ $user->earnings }}</span>
                                @endif
                            </p>
                            <p class="text-muted ms-0 mb-2 my-2">
                                <span class="me-3"><i class="bi bi-box-arrow-in-down me-2"></i>{{ trans('joined') }}: {{ \Carbon\Carbon::parse($user->created_at)->isoFormat('D MMMM YYYY') }}</span>
                                <span><i class="bi bi-eye me-2"></i>{{ trans('last_seen') }}: {{ \Carbon\Carbon::parse($user->last_seen)->diffForHumans() }} </span>
                            </p>
                            <p class="text-muted ms-0 mb-2 my-2">
                                @if ($user->profession != '')
                                <span class="me-3"><i class="bi bi-card-text me-2"></i>{{ $user->profession }}</span>
                                @endif
                                @if ($user->location != '' || $user->country != NULL)
                                <span class="me-3"><i class="bi bi-pin-map me-2"></i>{{ $user->location }},
                                    @if($user->country != NULL)
                                        {{ App\Models\Admin\Country::where('code',$user->country)->first()->name }}
                                    @endif</span>
                                @endif
                            </p>

                            <div class="user-header-socials d-flex my-2">
                                @if ($user->twitter != '')
                                <a href="{{ $user->twitter }}" target="_blank"><i class="bi bi-twitter"></i></a>
                                @endif
                                @if ($user->facebook != '')
                                <a href="{{ $user->facebook }}" target="_blank"><i class="bi bi-facebook"></i></a>
                                @endif
                                @if ($user->instagram != '')
                                <a href="{{ $user->instagram }}" target="_blank"><i class="bi bi-instagram"></i></a>
                                @endif
                            </div>

                            <h5 class="mt-4">{{ trans('bio') }}</h5>
                            <p>{{ $user->bio }}</p>
                            <h5 class="mt-4"><i class="bi bi-file-ppt me-2"></i>{{ trans('points') }} {{ trans('earned') }} </h5>
                            <p><span class="badge bg-red fs-7">{{ $user->total_points() }}</span></p>

                        </div>
                    </div><!--/dashboard-card-->

                    <div class="row">
                        @forelse ($badges as $badge)
                            <div class="col-lg-4">
                                <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                                    <div class="dashboard-body">
                                        <div class="badge-position">
                                            <img src="{{ my_asset('uploads/badges/'.$badge->image) }}" class="img-fluid mb-2 {{ $badge->score > $user->total_points() ? 'locked' : '' }}" alt="Badge">
                                            @if ($badge->score > $user->total_points())
                                              <span class="bi bi-lock-fill badge-locked"></span>
                                            @endif
                                        </div>
                                        <h5>{{ $badge->name }}</h5>
                                        <p> {{ trans('points') }}: <span class="badge bg-green">{{ $badge->score }}</span> </p>
                                    </div>
                                </div><!--/dashboard-card-->
                            </div>
                        @empty

                            <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                                <div class="dashboard-body">
                                    <div class="upload-image my-3">
                                        <h4 class="mb-3">{{ trans('no_badges_available') }}.</h4>
                                    </div>

                                </div>
                            </div><!--/dashboard-card-->

                        @endforelse
                    </div>
                @elseif(Route::is('user.posts'))

                    <div class="posts" id="posts">
                        @include('frontend.pagination.user.posts')
                    </div><!--/posts-->

                @elseif(Route::is('user.comments'))

                    <div class="comments" id="comments">
                        @include('frontend.pagination.user.comments')
                    </div><!--/comments-->

                @elseif(Route::is('user.replies'))

                    <div class="replies" id="replies">
                        @include('frontend.pagination.user.replies')
                    </div><!--/replies-->

                @elseif(Route::is('user.followers'))
                    <div class="row">
                        @forelse($user->followers()->with('followers')->get() as $follower)


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
                                            <span class="verified-badge" data-bs-toggle="tooltip" aria-label="Verified User" data-bs-original-title="Verified User">
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
                @elseif(Route::is('user.following'))
                    <div class="row">
                        @forelse($user->followings()->with('followable')->get() as $following)

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
                                            <span class="verified-badge" data-bs-toggle="tooltip" aria-label="Verified User" data-bs-original-title="Verified User">
                                                <i class="bi bi-patch-check"></i>
                                            </span>
                                        @endif<br>
                                        <span class="mb-0 small"><i class="bi bi-person-check me-1"></i>
                                            <span id="followers-{{ $following->followable->id }}">{{ $following->followable->followers->count() }}</span>
                                            {{ trans('following') }}
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

    @if(Auth::check())
        <div id="small-dialog-{{ $user->id }}" class="white-popup zoom-anim-dialog mfp-hide">
            <div class="mfp-modal-header">
                <span class="mb-2">
                    <img src="{{ my_asset('uploads/users/'.$user->image) }}" class="rounded-circle" alt="User">
                </span>
                <h4>{{ $user->name }}</h4>
            </div>
            <div class="mfp-modal-body py-4">

                <div class="w-100 pt-3 pb-3 px-4">

                    <form id="create_message" method="POST">
                        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
                        <div class="emoji-picker-container">
                            <textarea name="message" id="message" class="chat-message-input mb-3" rows="5" data-emojiable="true" data-emoji-input="unicode"
                            placeholder="Start a Conversation with {{ $user->name }}..."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <button type="submit" class="btn btn-mint w-100" id="create_message_btn"><i class="bi bi-send fs-xl me-2"></i>{{ trans('send') }}</button>
                    </form>

                </div>
            </div>
        </div>

        <div id="tip-dialog-{{ $user->id }}" class="white-popup zoom-anim-dialog mfp-hide">
            <div class="mfp-modal-header">
                <span class="mb-2">
                    <img src="{{ my_asset('uploads/users/'.$user->image) }}" class="rounded-circle" alt="User">
                </span>
                <h4 class="mb-3">{{ $user->name }}</h4>
                <h6>{{ trans('your') }} {{ trans('wallet') }} - {{ get_setting('currency_symbol') }}{{ Auth::user()->wallet }}</h6>
            </div>
            <div class="mfp-modal-body py-4">

                <div class="w-100 pt-3 pb-3 px-4">

                    <form id="tip_user" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
                        <label for="tip">{{ trans('send') }} {{ trans('tip') }}</label>
                        <input type="number" name="tip" id="tip" placeholder="{{ trans('minimum') }} {{ trans('tip') }} - {{ get_setting('currency_symbol') }}{{ get_setting('min_tip') }}">

                        <button type="submit" class="btn btn-mint w-100 mt-4" id="tip_user_btn"><i class="bi bi-send fs-xl me-2"></i>{{ trans('tip') }}</button>
                    </form>

                </div>
            </div>
        </div>

        <div id="report-dialog" class="white-popup zoom-anim-dialog mfp-hide">
            <div class="mfp-modal-header">
                <span class="mb-2">
                    <img src="{{ my_asset('uploads/users/'.$user->image) }}" class="rounded-circle" alt="User">
                </span>
                <h4 class="mb-1">{{ $user->name }}</h4>
            </div>
            <div class="mfp-modal-body py-4">

                <div class="w-100 pt-3 pb-3 px-4">

                    <form id="report_user" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
                        <h5 class="mb-3">{{ trans('report') }} {{ trans('user') }}</h5>

                        <div>
                            <label>{{ trans('category') }}</label>
                            <select name="category" id="category">
                                <option value="Hate Speech">Hate Speech</option>
                                <option value="Harassment">Harassment</option>
                                <option value="False Information">False Information</option>
                                <option value="Spam">Spam</option>
                                <option value="Nudity">Nudity</option>
                                <option value="Violence">Violence</option>
                                <option value="Suicide or Self-Injury">Suicide or Self-Injury</option>
                                <option value="Terrorism">Terrorism</option>
                                <option value="Something Else">Something Else</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div>
                            <label>{{ trans('reason') }}</label>
                            <textarea name="reason" id="reason" rows="4" placeholder="{{ trans('reason') }}"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <button type="submit" class="btn btn-mint w-100 mt-4" id="report_user_btn"><i class="bi bi-send fs-xl me-2"></i>{{ trans('report') }} {{ trans('user') }}</button>
                    </form>

                </div>
            </div>
        </div>
    @else
        <div id="follow-dialog" class="white-popup zoom-anim-dialog mfp-hide">
                <h4>{{ trans('please_login_to_follow_user') }}.</h4>
        </div>
        <div id="small-dialog-{{ $user->id }}" class="white-popup zoom-anim-dialog mfp-hide">
                <h4>{{ trans('please_login_to_send_message') }}.</h4>
        </div>
        <div id="tip-dialog-{{ $user->id }}" class="white-popup zoom-anim-dialog mfp-hide">
                <h4>{{ trans('please_login_to_tip_the_user') }}.</h4>
        </div>
    @endif

@endif

@endsection

@section('scripts')
<script>
    $('.has-popup').magnificPopup({
        type: 'inline',
        fixedContentPos: true,
        fixedBgPos: true,
        overflowY: 'auto',
        closeBtnInside: false,
        preloader: false,
        midClick: true,
        removalDelay: 300,
        mainClass: 'my-mfp-zoom-in'
    });

    $(function () {
        // Initializes and creates emoji set from sprite sheet
        window.emojiPicker = new EmojiPicker({
            emojiable_selector: '[data-emojiable=true]',
            assetsPath: '{{ my_asset('assets/vendors/emoji-picker/lib/img/') }}',
            popupButtonClasses: 'bi bi-emoji-smile'
        });

        window.emojiPicker.discover();
    });

    $(function() {

        // create message ajax request
        $(document).on('submit', '#create_message', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#create_message_btn").text('{{ trans('sending') }}...');
            $.ajax({
                url: '{{ route('user.chats.create') }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {

                end_load();

                if (response.status == 400) {

                    showError('message', response.messages.message);
                    $("#create_message_btn").text('{{ trans('send') }}');

                }else if (response.status == 200) {

                    tata.success("Success", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    removeValidationClasses("#create_message");
                    $("#create_message")[0].reset();
                    window.location.reload();

                }else if(response.status == 401){

                    tata.error("Error", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    $("#create_message")[0].reset();
                    window.location.reload();

                }

                }
            });
        });

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

        // tip user
        $(document).on('submit', '#tip_user', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#tip_user_btn").text('{{ trans('sending') }}...');
            $.ajax({
                url: '{{ route('user.tip') }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {

                end_load();

                if (response.status == 200) {

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

        // report user
        $(document).on('submit', '#report_user', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#report_user_btn").text('{{ trans('sending') }}...');
            $.ajax({
                url: '{{ route('reportuser') }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {

                    end_load();

                    if (response.status == 400) {

                        showError('reason', response.messages.reason);
                        $("#report_user_btn").text('{{ trans('report') }} {{ trans('user') }}');

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

    });

    function likePost(a) {
        $.ajax({
            url: '{{ route('like') }}',
            method: 'post',
            dataType: "json",
            data: {item: a},
            success: function(e) {
                var t;
                1 == e.bool ? ($("#like-icon-" + a).removeClass("text-muted").addClass("text-danger"), t = $("#like-" + a).text(), $("#like-" + a).text(++t)) : 0 == e.bool && ($("#like-icon-" + a).removeClass("text-danger").addClass("text-muted"), t = $("#like-" + a).text(), $("#like-" + a).text(--t))

                if (e.status == 200) {

                    tata.success("Success", e.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                }
            },
            error: function(e) {

                tata.error("Error", '{{ trans('please_login_to_like') }}', {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });
            }
        })
    }

    $(document).on('click', '#user_posts .pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];

        $.ajax({
            url: "{{ url('profile/'.$user->username.'/posts/pagination/?page=') }}" + page,
            data: {},
            success: function(response) {
                $('#posts').html(response);

                window.scroll({
                    top: 0, left: 0,
                    behavior: 'smooth'
                });
            }
        });

    });

    $(document).on('click', '#user_comments .pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];

        $.ajax({
            url: "{{ url('profile/'.$user->username.'/comments/pagination/?page=') }}" + page,
            data: {},
            success: function(response) {
                $('#comments').html(response);

                window.scroll({
                    top: 0, left: 0,
                    behavior: 'smooth'
                });
            }
        });

    });

    $(document).on('click', '#user_replies .pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];

        $.ajax({
            url: "{{ url('profile/'.$user->username.'/replies/pagination/?page=') }}" + page,
            data: {},
            success: function(response) {
                $('#replies').html(response);

                window.scroll({
                    top: 0, left: 0,
                    behavior: 'smooth'
                });
            }
        });

    });

</script>
@endsection
