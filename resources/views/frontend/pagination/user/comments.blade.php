

@forelse ($comments as $comment)

    <div class="comments-box" data-aos="fade-up" data-aos-easing="linear">
        <div class="card comments-box-header">
            <div class="card-header card-header-action">
                <div class="media align-items-center justify-content-between">
                    <div class="media-head me-2">
                        <div class="avatar">
                            <a href="{{ route('user', ['username' => $comment->post->user->username]) }}"><img src="{{ my_asset('uploads/users/'.$comment->post->user->image ) }}" alt="user" class="avatar-img rounded-circle"></a>
                        </div>
                    </div>
                    <div class="media-body">
                        <div><a href="{{ route('home.post', ['post_id' => $comment->post->post_id, 'slug' => $comment->post->slug]) }}">{{ $comment->post->title }}</a></div>
                        <div class="fs-7">
                            <a href="{{ route('user', ['username' => $comment->post->user->username]) }}">{{ $comment->post->user->name }}</a>
                            @if($comment->post->user->verified == 1)
                                <span class="verified-badge" data-bs-toggle="tooltip" aria-label="Verified User" data-bs-original-title="Verified User">
                                    <i class="bi bi-patch-check"></i>
                                </span>
                            @endif

                            @if($comment->post->user->role == 'Admin')
                                <span class="mod" data-bs-toggle="tooltip" aria-label="Admin" data-bs-original-title="Admin">
                                    <i class="bi bi-octagon-half"></i>
                                </span>
                            @endif

                            @if($comment->post->user->role == 'Moderator')
                                <span class="mod" data-bs-toggle="tooltip" aria-label="Moderator" data-bs-original-title="Moderator">
                                    <i class="bi bi-hexagon-half"></i>
                                </span>
                            @endif

                            @if ($comment->post->user->user_sub($comment->post->user->id)->plan_id != 1)
                                <span class="mod" data-bs-toggle="tooltip" aria-label="Pro" data-bs-original-title="Pro">
                                    <i class="bi bi-star-half"></i>
                                </span>
                            @endif
                            <span class="ms-1"> {{ $comment->post->created_at->diffForHumans() }} {{ trans('in') }} </span> <a href="{{ route('category', ['slug' => $comment->post->category->slug]) }}" class="cat">{{ $comment->post->category->name }}</a></div>
                    </div>
                </div>
                <div class="card-action-wrap">
                    @if (Auth::check())
                        @if (Auth::user()->id === $comment->user->id)
                            <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('user.comments.edit', ['id' => $comment->id]) }}"><i class="bi bi-pencil me-2"></i> {{ trans('edit') }} {{ trans('comment') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="delete_item('{{ route('user.comments.destroy') }}','{{ $comment->id }}','{{ trans('delete_this_comment') }}');"><i class="bi bi-trash3 me-2"></i> {{ trans('delete') }} {{ trans('comment') }}</a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="comments-box-body">
            <div class="card">
            </div>
            <div class="content">
                {!! $comment->body !!}
            </div>
        </div>
    </div>

@empty

    <div class="row g-0">
        <div class="col-12">

            <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                <div class="dashboard-body">
                    <div class="upload-image my-3">
                        <h4 class="mb-3">{{ trans('no_comments_available') }}.</h4>
                    </div>

                </div>
            </div><!--/dashboard-card-->

        </div>
    </div>

@endforelse

@if ($comments->hasPages())
<div id="user_comments" data-aos="fade-up" data-aos-easing="linear">
    {!! $comments->appends(request()->all())->links('layouts.pagination.new') !!}
</div>
@endif
