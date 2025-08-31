

<div class="vine-sidebar position-sticky"

@if (get_setting('site_direction') == 'ltr')
    data-aos="fade-right" data-aos-easing="ease-in-sine"
@elseif (get_setting('site_direction') == 'rtl')
    data-aos="fade-left" data-aos-easing="ease-in-sine"
@endif>

    <div class="ps-3 d-flex align-items-center">

        <!-- Responsive navbar toggler -->
        <button class="navbar-toggler d-block d-xl-none btn btn-mint rounded-3 w-100" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav2"
            aria-controls="navbarNav2" aria-expanded="false" aria-label="Toggle navigation">
            <span class="h5 me-5 text-white"> <i class="bi bi-back me-2"></i> {{ trans('sidebar') }}</span>
            <span class="navbar-toggler-animation">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>
    </div>

  <div class="navbar-collapse d-xl-block collapse" id="navbarNav2">
    <ul class="navbar-nav flex-column">


     <li class="nav-item  {{ Route::is('home') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('home') }}">
        <span class="nav-icon-wrap"><i class="bi bi-house-door"></i></span>
        <span class="nav-link-text">{{ trans('home') }}</span>
        </a>
     </li>
     <li class="nav-item {{ Route::is('home.posts') ? 'active' : '' }}
     {{ Route::is('home.post') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('home.posts') }}">
         <span class="nav-icon-wrap"><i class="bi bi-list-task"></i></span>
         <span class="nav-link-text">{{ trans('discussions') }}</span>
       </a>
     </li>
    @if (Auth::check())
        <li class="nav-item {{ Route::is('feed') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('feed') }}">
            <span class="nav-icon-wrap"><i class="bi bi-rss"></i></span>
            <span class="nav-link-text">{{ trans('feed') }}</span>
        </a>
        </li>
    @endif
     <li class="nav-item {{ Route::is('users') ? 'active' : '' }}
     {{ Route::is('user') ? 'active' : '' }}
     {{ Route::is('user.posts') ? 'active' : '' }}
     {{ Route::is('user.comments') ? 'active' : '' }}
     {{ Route::is('user.replies') ? 'active' : '' }}
     {{ Route::is('user.followers') ? 'active' : '' }}
     {{ Route::is('user.following') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('users') }}">
         <span class="nav-icon-wrap"><i class="bi bi-people"></i></span>
         <span class="nav-link-text">{{ trans('users') }}</span>
       </a>
     </li>
     <li class="nav-info">{{ trans('discover') }} </li>
     <li class="nav-item {{ Route::is('categories') ? 'active' : '' }} {{ Route::is('category') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('categories') }}">
         <span class="nav-icon-wrap"><i class="bi bi-back"></i></span>
         <span class="nav-link-text">{{ trans('categories') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('tags') ? 'active' : '' }} {{ Route::is('tag') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('tags') }}">
         <span class="nav-icon-wrap"><i class="bi bi-tags"></i></span>
         <span class="nav-link-text">{{ trans('tags') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('leaderboard') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('leaderboard') }}">
         <span class="nav-icon-wrap"><i class="bi bi-list-stars"></i></span>
         <span class="nav-link-text">{{ trans('leaderboard') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('stats') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('stats') }}">
         <span class="nav-icon-wrap"><i class="bi bi-columns-gap"></i></span>
         <span class="nav-link-text">{{ trans('stats') }} & {{ trans('online_users') }}</span>
       </a>
     </li>
    @if(get_setting('payments_on_site') == 'Yes')
     <li class="nav-item {{ Route::is('plans') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('plans') }}">
         <span class="nav-icon-wrap"><i class="bi bi-credit-card-2-front"></i></span>
         <span class="nav-link-text">{{ trans('pricing_plans') }}</span>
       </a>
     </li>
    @endif
     <li class="nav-item {{ Route::is('points') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('points') }}">
         <span class="nav-icon-wrap"><i class="bi bi-file-ppt"></i></span>
         <span class="nav-link-text">{{ trans('earn') }}
            {{ get_setting('payments_on_site') == 'Yes' ? '/ '. trans('buy_points') : trans('points') }}
            </span>
       </a>
     </li>
     <li class="nav-info">{{ trans('categories') }} </li>
    @foreach (App\Models\Admin\Categories::where('status', 1)->limit(get_setting('categories_widget'))->get() as $category)
        <li class="nav-item">
            <a class="nav-link" href="{{ route('category', ['slug' => $category->slug]) }}">
                <span class="nav-icon-wrap">
                    <img src="{{ my_asset('uploads/categories/'.$category->image) }}" class="img-fluid rounded-2" alt="{{ $category->name }}">
                </span>
                <span class="nav-link-text">{{ $category->name }}</span>
                @if(get_setting('payments_on_site') == 'Yes')
                    @if ($category->pro == 1)
                        <span class="badge bg-red-mint ms-1">{{ trans('pro') }}</span>
                    @endif
                @endif
            </a>
        </li>
    @endforeach
    </ul>

    <ul class="navbar-nav flex-column nav-trend mb-3 d-none d-lg-block" data-aos="fade-right" data-aos-easing="linear">
        <li class="nav-info">{{ trans('trending_posts') }} </li>
        @foreach (App\Models\Posts::where('status', 1)->where('public', 1)->withCount('search_comments')->orderBy('search_comments_count', 'desc')->limit(get_setting('trending_posts_widget'))->get() as $post)
            <li class="nav-item">
                <h4 class="nav-content">
                    <a href="{{ route('home.post', ['post_id' => $post->post_id, 'slug' => $post->slug]) }}">{{ $post->title }}</a></h4>
                <div class="view">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                        <polyline points="17 6 23 6 23 12"></polyline>
                    </svg>
                    <div>{{ $post->comments()->count() }} {{ trans('comments') }} </div>
                </div>
            </li>
        @endforeach


    </ul>

    @if(Auth::check())
        @if (get_setting('ads') == 1)
            @if(get_setting('payments_on_site') == 'Yes')
                @if (Auth::user()->subscription()->ads == 0)
                    <div class="sidebar-ad">
                        {!! get_setting('sidebar_ad') !!}
                    </div>
                @endif
            @else
                <div class="sidebar-ad">
                    {!! get_setting('sidebar_ad') !!}
                </div>
            @endif
        @endif
    @else
        @if (get_setting('ads') == 1)
            <div class="sidebar-ad">
                {!! get_setting('sidebar_ad') !!}
            </div>
        @endif
    @endif


  </div>


</div><!--/social-sidebar-->
