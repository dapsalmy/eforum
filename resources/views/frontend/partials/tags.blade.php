
@forelse($tags as $tag)

    <div class="col-lg-6" data-aos="fade-up" data-aos-easing="linear">




    <div class="tag-box-new">
        <a href="{{ route('tag', ['slug' => $tag->slug]) }}">
            <div class="tag-box-header">
                <h2 class="tag-box-title">
                    <span class="label">{{'#'.$tag->name }}</span>
                    <svg class="icon icon-arrow icon-arrow-up-right" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.00032 3.28592L12.7144 8L8.00037 12.714" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M3.2863 7.99996L12.2427 7.99975" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </h2>
                <span class="tag-box-count">{{ $tag->count }} {{ trans('posts') }}</span>
            </div>
        </a>

        @if (App\Models\Posts::withAnyTag($tag->name)->latest()->first())

            <div class="post-box d-flex mb-2">
                <div class="card">
                    <div class="card-header card-header-action py-3">
                        <div class="media align-items-center">
                            <div class="media-head me-2">
                                <div class="avatar">
                                    <a href="{{ route('user', ['username' => App\Models\Posts::withAnyTag($tag->name)->latest()->first()->user->username]) }}"><img src="{{ my_asset('uploads/users/'.App\Models\Posts::withAnyTag($tag->name)->latest()->first()->user->image) }}" alt="user" class="avatar-img rounded-circle"></a>
                                </div>
                            </div>
                            <div class="media-body">
                                <h6><a href="{{ route('home.post', ['post_id' => App\Models\Posts::withAnyTag($tag->name)->latest()->first()->post_id, 'slug' => App\Models\Posts::withAnyTag($tag->name)->latest()->first()->slug]) }}">
                                    {{ App\Models\Posts::withAnyTag($tag->name)->latest()->first()->title }}</a>
                                </h6>
                                <span><a href="{{ route('user', ['username' => App\Models\Posts::withAnyTag($tag->name)->latest()->first()->user->username]) }}">
                                    <span class="small">{{ App\Models\Posts::withAnyTag($tag->name)->latest()->first()->user->name }},</span>
                                    </a><span class="ms-1 small">{{ App\Models\Posts::withAnyTag($tag->name)->latest()->first()->created_at->diffForHumans() }}</span> </span>
                            </div>
                        </div>
                    </div>
                </div><!--/card-->
            </div>

        @else

            <div class="post-box d-flex mb-2">
                <p class="text-center p-4">{{ trans('no_posts_available') }}</p>
            </div>
        @endif
    </div>
</div><!--/col-lg-6-->

@empty

    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-body">
            <div class="upload-image my-3">
                <h4 class="mb-3">{{ trans('no_tags_available') }}.</h4>
            </div>
        </div>
    </div><!--/dashboard-card-->

@endforelse



@if ($tags->hasPages())
<div>
   {!! $tags->appends(request()->all())->links('layouts.pagination.new') !!}
</div>
@endif
