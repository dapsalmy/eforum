

@forelse($users as $user)
    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12" data-aos="fade-up" data-aos-easing="linear">
        <div class="user-wrap">
            <div class="user-badge">
                <div class="d-flex ms-auto">
                    @if(Auth::check())
                    @if (Auth::user()->id != $user->id)
                        <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add Friend" href="javascript:void(0);" id="{{ $user->id }}" class="btn @if(Auth::user()->isFollowing($user)) btn-danger @else btn-mint @endif btn-rounded ms-2 followUser">
                            <i class="bi @if(Auth::user()->isFollowing($user)) bi-person-check @else bi-person-plus @endif" id="follow-icon-{{ $user->id }}"></i>
                        </a>

                        @if (\App\Models\Chats::where('sender_id', Auth::user()->id)->where('receiver_id', $user->id)->orWhere('sender_id', $user->id)->where('receiver_id',  Auth::user()->id)->exists())
                            <a href="{{ route('user.chats') }}" class="btn btn-mint btn-rounded ms-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Message">
                            <i class="bi bi-chat-left-text"></i></a>
                        @else
                        <a href="#small-dialog-{{ $user->id }}" class="btn btn-mint btn-rounded ms-2 has-popup" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Message">
                        <i class="bi bi-chat-left-text"></i></a>

                        @endif
                    @endif
                    @endif

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
                @else

                <div id="small-dialog-{{ $user->id }}" class="white-popup zoom-anim-dialog mfp-hide">
                        <h4>{{ trans('please_login_to_send_message') }}.</h4>
                </div>
                @endif

                </div>
            </div><!--/user-badge-->
            <div class="user-thumb">
                <a href="{{ route('user', ['username' => $user->username]) }}">
                <img src="{{ my_asset('uploads/users/'.$user->image) }}" class="img-fluid rounded-circle" alt="User">
                </a>
            </div>
            <div class="user-caption">
                <h4 class="mb-0">
                    <a href="{{ route('user', ['username' => $user->username]) }}" class="position-relative">{{ $user->name }}
                        @if(Cache::has('user-is-online-' . $user->id))
                            <span class="user_online_lg"></span>
                        @endif
                        @if($user->verified == 1)
                            <span class="verified-badge" data-bs-toggle="tooltip" aria-label="Verified User" data-bs-original-title="Verified User">
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
                    </a>
                </h4>
                @if ($user->location != '' || $user->country != NULL)
                    <span class="text-muted small mb-0"> <i class="bi bi-geo-alt"></i> {{ $user->location }},
                        @if($user->country != NULL)
                            {{ App\Models\Admin\Country::where('code',$user->country)->first()->name }}
                        @endif
                    </span>
                    <br>
                @endif
                <span class="small mb-0">{{ trans('member_since') }} {{ $user->created_at->format('Y') }}</span>
            </div>
            <ul class="user-stats my-4 pt-3">
                <li><span class="item-number">{{ $user->posts_count() }}</span> <span class="item-text">{{ trans('posts') }}</span> </li>
                <li><span class="item-number" id="followers-{{ $user->id }}">{{ $user->followers->count() }}</span> <span class="item-text">{{ trans('followers') }}</span></li>
                <li><span class="item-number" id="following-{{ $user->id }}">{{ $user->followings->count() }}</span> <span class="item-text">{{ trans('following') }}</span></li>
            </ul><!--/user-stats-->
        </div>
    </div><!--col-lg-4-->
@empty

<div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
    <div class="dashboard-body">
        <div class="upload-image my-3">
            <h4 class="mb-3">{{ trans('no_users_available') }}.</h4>
        </div>

    </div>
</div><!--/dashboard-card-->

@endforelse


@if ($users->hasPages())
<div>
    {!! $users->appends(request()->all())->links('layouts.pagination.new') !!}
</div>
@endif

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

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
    });

</script>
