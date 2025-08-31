

@forelse ($replies as $reply)

    <div class="comments-box" data-aos="fade-up" data-aos-easing="linear">

        <div class="card comments-box-header">
            <div class="card-header card-header-action">
                <div class="media align-items-center justify-content-between">
                    <div class="media-head me-2">
                        <div class="avatar">
                            <a href="{{ route('user', ['username' => $reply->comment->user->username]) }}"><img src="{{ my_asset('uploads/users/'.$reply->comment->user->image ) }}" alt="user" class="avatar-img rounded-circle"></a>
                        </div>
                    </div>
                    <div class="media-body">
                        <div><a href="{{ route('home.post', ['post_id' => $reply->comment->post->post_id, 'slug' => $reply->comment->post->slug]) }}">{{ $reply->comment->post->title }}</a></div>
                        <div class="fs-7">
                            <a href="{{ route('user', ['username' => $reply->comment->user->username]) }}">{{ $reply->comment->user->name }}</a>
                            @if($reply->comment->user->verified == 1)
                                <span class="verified-badge" data-bs-toggle="tooltip" aria-label="Verified User" data-bs-original-title="Verified User">
                                    <i class="bi bi-patch-check"></i>
                                </span>
                            @endif

                            @if($reply->comment->user->role == 'Admin')
                                <span class="mod" data-bs-toggle="tooltip" aria-label="Admin" data-bs-original-title="Admin">
                                    <i class="bi bi-octagon-half"></i>
                                </span>
                            @endif

                            @if($reply->comment->user->role == 'Moderator')
                                <span class="mod" data-bs-toggle="tooltip" aria-label="Moderator" data-bs-original-title="Moderator">
                                    <i class="bi bi-hexagon-half"></i>
                                </span>
                            @endif

                            @if ($reply->comment->user->user_sub($reply->comment->user->id)->plan_id != 1)
                                <span class="mod" data-bs-toggle="tooltip" aria-label="Pro" data-bs-original-title="Pro">
                                    <i class="bi bi-star-half"></i>
                                </span>
                            @endif
                            <span class="ms-1"> {{ $reply->comment->post->created_at->diffForHumans() }} {{ trans('in') }} </span> <a href="{{ route('category', ['slug' => $reply->comment->post->category->slug]) }}" class="cat">{{ $reply->comment->post->category->name }}</a></div>
                    </div>
                </div>
                <div class="card-action-wrap">
                    @if (Auth::check())
                        @if (Auth::user()->id === $reply->user->id)
                            <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('user.replies.edit', ['id' => $reply->id]) }}"><i class="bi bi-pencil me-2"></i> {{ trans('edit') }} {{ trans('reply') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="delete_item('{{ route('user.replies.destroy') }}','{{ $reply->id }}','{{ trans('delete_this_reply') }}');"><i class="bi bi-trash3 me-2"></i> {{ trans('delete') }} {{ trans('reply') }}</a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="comments-box-body">
            <div class="content">
                {!! $reply->body !!}
            </div>
        </div>
    </div>

@empty

    <div class="row g-0">
        <div class="col-12">

            <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                <div class="dashboard-body">
                    <div class="upload-image my-3">
                        <h4 class="mb-3">{{ trans('no_replies_available') }}.</h4>
                    </div>

                </div>
            </div><!--/dashboard-card-->

        </div>
    </div>

@endforelse

@if ($replies->hasPages())
<div id="user_replies" data-aos="fade-up" data-aos-easing="linear">
    {!! $replies->appends(request()->all())->links('layouts.pagination.new') !!}
</div>
@endif
