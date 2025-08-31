@extends('layouts.user')

@section('content')


    <div class="d-flex justify-content-between mb-4" data-aos="fade-down" data-aos-easing="linear">
        <h4><i class="bi bi-bell me-2"></i> {{ trans('notifications') }}</h4>
        <a href="javascript:void(0);" onclick="markAsReadTwo()" class="btn btn-sm btn-danger rounded-pill"><i class="bi bi-check-circle me-2"></i>{{ trans('mark_as_read') }}</a>
    </div>

    <div class="mb-5">

        @forelse(Auth::user()->all_notifications() as $notification)

        <div class="d-flex align-items-center justify-content-between py-3 border-bottom px-lg-6 px-4 notification-card read" data-aos="fade-up" data-aos-easing="linear">
            <div class="d-flex">
                <div class="avatar avatar-xl me-3">
                    <img class="rounded-circle" src="{{ my_asset('uploads/users/'.App\Models\User::find($notification->sender_id)->image) }}" alt="User">
                </div>
                <div class="me-3 flex-1">
                <h5>{{ App\Models\User::find($notification->sender_id)->name }}</h5>
                <p class="text-muted small mb-2">{{ $notification->created_at->diffForHumans() }}</p>
                @if($notification->notification_type == "Comment")

                <p><span class="me-1">💬</span>{{ trans('commented_your_post') }}
                    <a href="{{ url('post/'.App\Models\Posts::find($notification->post_id)->post_id.'/'.App\Models\Posts::find($notification->post_id)->slug) }}" class="fw-bold">
                        "{{ App\Models\Posts::find($notification->post_id)->title }}" </a></p>

                @elseif($notification->notification_type == "Reply")

                <p><span class="me-1">💬</span>{{ trans('replied_your_comment') }}
                    <a href="{{ url('post/'.App\Models\Posts::find($notification->post_id)->post_id.'/'.App\Models\Posts::find($notification->post_id)->slug) }}" class="fw-bold">
                        "{{ App\Models\Posts::find($notification->post_id)->title }}" </a></p>

                @elseif($notification->notification_type == "Like Post")

                    <p><span class="me-1">👍</span>{{ trans('liked_your_post') }}
                        <a href="{{ url('post/'.App\Models\Posts::find($notification->post_id)->post_id.'/'.App\Models\Posts::find($notification->post_id)->slug) }}" class="fw-bold">
                            "{{ App\Models\Posts::find($notification->post_id)->title }}" </a></p>

                @elseif($notification->notification_type == "Like Comment")

                    <p><span class="me-1">👍</span>{{ trans('liked_your_comment') }}
                        <a href="{{ url('post/'.App\Models\Posts::find($notification->post_id)->post_id.'/'.App\Models\Posts::find($notification->post_id)->slug) }}" class="fw-bold">
                            "{{ App\Models\Posts::find($notification->post_id)->title }}" </a></p>

                @elseif($notification->notification_type == "Like Reply")

                    <p><span class="me-1">👍</span>{{ trans('liked_your_reply') }}
                        <a href="{{ url('post/'.App\Models\Posts::find($notification->post_id)->post_id.'/'.App\Models\Posts::find($notification->post_id)->slug) }}" class="fw-bold">
                            "{{ App\Models\Posts::find($notification->post_id)->title }}" </a></p>

                @elseif($notification->notification_type == "React Post")

                    <p>
                        @if (App\Models\Reactions::find($notification->react_id)->type == 'like')
                            <span class="me-1"><img src="{{ my_asset('uploads/reactions/like.png') }}" class="avatar avatar-small"></span>
                        @elseif(App\Models\Reactions::find($notification->react_id)->type == 'love')
                            <span class="me-1"><img src="{{ my_asset('uploads/reactions/love.png') }}" class="avatar avatar-small"></span>
                        @elseif(App\Models\Reactions::find($notification->react_id)->type == 'haha')
                            <span class="me-1"><img src="{{ my_asset('uploads/reactions/haha.png') }}" class="avatar avatar-small"></span>
                        @elseif(App\Models\Reactions::find($notification->react_id)->type == 'wow')
                            <span class="me-1"><img src="{{ my_asset('uploads/reactions/wow.png') }}" class="avatar avatar-small"></span>
                        @elseif(App\Models\Reactions::find($notification->react_id)->type == 'yay')
                            <span class="me-1"><img src="{{ my_asset('uploads/reactions/yay.png') }}" class="avatar avatar-small"></span>
                        @elseif(App\Models\Reactions::find($notification->react_id)->type == 'sad')
                            <span class="me-1"><img src="{{ my_asset('uploads/reactions/sad.png') }}" class="avatar avatar-small"></span>
                        @elseif(App\Models\Reactions::find($notification->react_id)->type == 'mad')
                            <span class="me-1"><img src="{{ my_asset('uploads/reactions/mad.png') }}" class="avatar avatar-small"></span>
                        @endif

                        {{ trans('reacted_to_your_post') }}
                        <a href="{{ url('post/'.App\Models\Posts::find($notification->post_id)->post_id.'/'.App\Models\Posts::find($notification->post_id)->slug) }}" class="fw-bold">
                            "{{ App\Models\Posts::find($notification->post_id)->title }}" </a></p>

                @elseif($notification->notification_type == "Following User")

                    <p>{{ trans('has_followed_you') }}.</p>

                @elseif($notification->notification_type == "Tip")

                    <p>{{ trans('sent_you_a_tip_of') }} {{ get_setting('currency_symbol') }}{{ App\Models\Payment::find($notification->tip_id)->amount }}.</p>

                @endif
                </div>
            </div>
            <a href="javascript:void(0)" onclick="delete_item('{{ route('user.notifications.destroy') }}','{{ $notification->id }}','{{ trans('delete_this_notification') }}');" class="remove-icon-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ trans('remove') }}"><i class="bi bi-x"></i></a>
        </div><!--/notification-card-->

        @empty

            <div class="row g-0">
                <div class="col-12">

                    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                        <div class="dashboard-body">
                            <div class="upload-image my-3">
                                <h4 class="mb-3">{{ trans('no_notifications_available') }}.</h4>
                             </div>

                        </div>
                    </div><!--/dashboard-card-->

                </div>
            </div>

        @endforelse


    </div>

    <div>
        {!! Auth::user()->all_notifications()->links('layouts.pagination.new') !!}
    </div>

@endsection
