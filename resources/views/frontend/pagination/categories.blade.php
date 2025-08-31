

@forelse($categories as $category)

<div class="col-lg-6" data-aos="fade-up" data-aos-easing="linear">

    <div class="ct-box">
        <div class="ct-thumb">
            <a href="{{ route('category', ['slug' => $category->slug]) }}">
                <img src="{{ my_asset('uploads/categories/'.$category->image) }}" class="img-fluid" alt="Image">
            </a>
        </div>
        <div class="companiesGroup hiredioShdaow">
            <div class="industryFlex">
                <div class="industryName">
                    <h5 class="title"><a href="{{ route('category', ['slug' => $category->slug]) }}" tabindex="-1">{{ $category->name }}
                        @if(get_setting('payments_on_site') == 'Yes')
                            @if ($category->pro == 1)
                            <span class="mod" data-bs-toggle="tooltip" aria-label="Pro" data-bs-original-title="Pro">
                                <i class="bi bi-star-fill"></i>
                            </span> @endif
                        @endif
                    </a></h5>
                    <span>{{ $category->posts()->count() }} posts</span>
                </div>
                <div class="industrylink"><a href="{{ route('category', ['slug' => $category->slug]) }}" tabindex="-1"><i class="industry-arrow"></i></a></div>
            </div>
            <div class="post-excerpt">
                {{ $category->description }}
            </div>
            <div class="companies-users">
                <ul>
                    @forelse ($category->top_users() as $post)
                        <li>
                            <a href="{{ route('user', ['username' => $post->user->username]) }}">
                                <img src="{{ my_asset('uploads/users/'.$post->user->image) }}" class="img-fluid" alt="image">
                            </a>
                        </li>
                    @empty
                    @endforelse
                </ul>
            </div>
            <a href="{{ route('category', ['slug' => $category->slug]) }}" class="hiredioLink" tabindex="-1"></a>
        </div>
    </div>

</div>


@empty

<div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
   <div class="dashboard-body">
       <div class="upload-image my-3">
           <h4 class="mb-3">{{ trans('no_categories_available') }}.</h4>
       </div>

   </div>
</div><!--/dashboard-card-->

@endforelse


@if ($categories->hasPages())
<div>
   {!! $categories->appends(request()->all())->links('layouts.pagination.new') !!}
</div>
@endif
