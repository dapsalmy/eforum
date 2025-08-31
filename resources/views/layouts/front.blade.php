<!DOCTYPE html>
<html lang="en" data-theme="light"
 @if (get_setting('site_direction') == 'ltr')
     dir="ltr"
 @elseif (get_setting('site_direction') == 'rtl')
     dir="rtl"
 @endif
>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if (Route::is('home.posts'))
        <title>{{ trans('discussions') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('home.post'))
        <title>{{ $post->title }} by {{ $post->user->name }}</title>
        <meta name="description" content="{{ $post->description }}">
        <meta name="keywords" content="{{ $post->keywords }}">
    @elseif (Route::is('feed'))
        <title>{{ trans('feed') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('users'))
        <title>{{ trans('users') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('categories'))
        <title>{{ trans('categories') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('category'))
        <title>{{ $category->name }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('tags'))
        <title>{{ trans('tags') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('tag'))
        <title>{{ $tag->name }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('stats'))
        <title>{{ trans('stats') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('plans'))
        <title>{{ trans('pricing_plans') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('points'))
        <title>{{ trans('points') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('badges'))
        <title>{{ trans('badges') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('about') || Route::is('rules') || Route::is('privacy') || Route::is('terms') || Route::is('cookie'))
        <title>{{ $page->meta_title }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ $page->meta_description }}">
        <meta name="keywords" content="{{ $page->meta_keywords }}">
    @elseif (Route::is('faqs'))
        <title>{{ trans('faqs') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('leaderboard'))
        <title>{{ trans('leaderboard') }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @elseif (Route::is('user') || Route::is('user.posts') || Route::is('user.comments') || Route::is('user.replies') || Route::is('user.followers') || Route::is('user.following'))
        <title>{{ $user->name }} - {{ get_setting('site_name') }}</title>
        <meta name="description" content="{{ $user->bio }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @else
        <title>{{ get_setting('site_name') }} - {{ get_setting('site_title') }}</title>
        <meta name="description" content="{{ get_setting('site_description') }}">
        <meta name="keywords" content="{{ get_setting('site_keywords') }}">
    @endif
    <meta name="robots" content="all,follow">


    @if (Route::is('home.post'))
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $post->title }}" />
    <meta property="og:description" content="{{ $post->description }}" />
    <meta property="og:image" content="" />
    <meta property="og:site_name" content="{{ get_setting('site_name') }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@famousdeaths" />
    <meta name="twitter:title" content="{{ $post->title }}" />
    <meta name="twitter:description" content="{{ $post->description }}" />
    <meta property="twitter:image" content="" />
    <meta name="twitter:creator" content="@famousdeaths" />
    <meta name="st:robots" content="all, follow">
    <link rel="canonical" href="{{ route('home.post', ['post_id' => $post->post_id, 'slug' => $post->slug]) }}" />
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "NewsArticle",
            "headline": "{{ $post->title }}",
            "url": "{{ route('home.post', ['post_id' => $post->post_id, 'slug' => $post->slug]) }}",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "{{ route('home.post', ['post_id' => $post->post_id, 'slug' => $post->slug]) }}"
            },
            "articleSection": "{{ $post->category->name }}",
            "author": {
                "@type": "Person",
                "name": "{{ $post->user->name }}",
                "url": "{{ route('user', ['username' => $post->user->username]) }}"
            },
            "creator": "{{ $post->user->name }}",
            "publisher": {
                "@type": "Organization",
                "name": "Famous Deaths",
                "logo": "{{ my_asset('uploads/settings/'.get_setting('favicon')) }}"
            },
            "keywords": "{{ $post->keywords }}",
            "dateCreated": "{{ $post->created_at }}-05:00",
            "datePublished": "{{ $post->created_at }}-05:00",
            "dateModified": "{{ $post->updated_at }}-05:00"
        }
    </script>
@endif

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="app-url" content="{{ env('APP_URL')}}">

    <script>
        //Check local storage
        let localS = localStorage.getItem('theme')
            themeToSet = localS

        // If local storage is not set, we check the OS preference
        if(!localS){
            themeToSet = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
        }

        //Set the correct theme
        document.documentElement.setAttribute('data-theme', themeToSet)
    </script>

    @if (get_setting('analytics') != '')
        {!! get_setting('analytics') !!}
    @endif

    @if (get_setting('adsense') != '')
        {!! get_setting('adsense') !!}
    @endif

    <!-- ==============================================
    Favicons
    =============================================== -->
    <link href="{{ my_asset('uploads/settings/'.get_setting('favicon')) }}" rel="icon">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ my_asset('manifest.json') }}">
    <meta name="theme-color" content="#008751">
    <link rel="apple-touch-icon" href="{{ my_asset('assets/icons/icon-192x192.png') }}">

    <!-- ==============================================
     CSS Styles
    =============================================== -->

@if (get_setting('site_direction') == 'ltr')
    <link rel="stylesheet" href="{{ my_asset('assets/vendors/bootstrap/bootstrap.min.css') }}">
@elseif (get_setting('site_direction') == 'rtl')
    <link rel="stylesheet" href="{{ my_asset('assets/vendors/bootstrap/bootstrap.rtl.min.css') }}">
@endif
    <link rel="stylesheet" href="{{ my_asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ my_asset('assets/vendors/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ my_asset('assets/vendors/aos/aos.css') }}">
    <link rel="stylesheet" href="{{ my_asset('assets/vendors/sweetalert/sweetalert2.min.css') }}">
    @if (get_setting('site_direction') == 'ltr')
    <link rel="stylesheet" href="{{ my_asset('assets/frontend/css/style.css') }}">
@elseif (get_setting('site_direction') == 'rtl')
    <link rel="stylesheet" href="{{ my_asset('assets/frontend/css/style-rtl.css') }}">
@endif
    <link rel="stylesheet" href="{{ my_asset('assets/frontend/css/nigerian-theme.css') }}">
    <link rel="stylesheet" href="{{ my_asset('assets/frontend/css/mobile.css') }}">


	<script src="{{ my_asset('assets/vendors/jquery/jquery.min.js') }}"></script>
	<script src="{{ my_asset('assets/vendors/bootstrap/bootstrap.bundle.js') }}"></script>
	<script src="{{ my_asset('assets/vendors/popper/popper.min.js') }}"></script>
	<script src="{{ my_asset('assets/frontend/js/cookie.js') }}"></script>


    <script>
    "use strict";
    var APP_URL = {!! json_encode(url('/')) !!}
    </script>

    @yield('styles')

    <!-- ==============================================
     Fonts
    =============================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla&display=swap" rel="stylesheet">

</head>
<body>

    <!-- Switcher Icon -->
    <div class="switcher switcher-show" id="theme-switcher">
        <i id="switcher-icon" class="bi bi-moon"></i>
    </div>

    <!-- Back to Top -->
	<a href="#" id="back-to-top"></a>


	<div class="vine-wrapper">

        @include('layouts.frontend-partials.navbar')

		<!-- ==============================================
		 Main
		=============================================== -->
		<section class="vine-main">
			<div class="container">
				<div class="row">
                    @if(Auth::check())
                      @if (Auth::user()->isBanned())

                        <div class="col-12">
                            <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                                <div class="dashboard-body">
                                    <div class="upload-image my-3">
                                        <h4 class="mb-3">You have been Banned till</h4>
                                        <h2 class="mb-3">{{ date('d M, Y  H:i:s', strtotime(Auth::user()->ban_details()->expired_at)) }}</h2>
                                        <h4>Reason</h4>
                                        <p>{{ Auth::user()->ban_details()->comment }}</p>
                                    </div>

                                </div>
                            </div><!--/dashboard-card-->
                        </div>

                      @else
                        <div class="col-sm-12 col-lg-3 mb-5 border-end">
                            @include('layouts.frontend-partials.sidebar')
                        </div>
                        <div class="col-lg-9 ps-lg-5">
                            @yield('content')

                            @if(Auth::check())
                                @if (get_setting('ads') == 1)
                                   @if(get_setting('payments_on_site') == 'Yes')
                                        @if (Auth::user()->subscription()->ads == 0)
                                            <div class="ad-spot text-center mt-4" data-aos="fade-up" data-aos-easing="linear">
                                                <div class="ad-box">
                                                    {!! get_setting('footer_ad') !!}
                                                </div>
                                            </div>
                                        @endif
                                   @else
                                        <div class="ad-spot text-center mt-4" data-aos="fade-up" data-aos-easing="linear">
                                            <div class="ad-box">
                                                {!! get_setting('footer_ad') !!}
                                            </div>
                                        </div>
                                   @endif
                                @endif
                            @else
                                @if (get_setting('ads') == 1)
                                    <div class="ad-spot text-center mt-4" data-aos="fade-up" data-aos-easing="linear">
                                        <div class="ad-box">
                                            {!! get_setting('footer_ad') !!}
                                        </div>
                                    </div>
                                @endif
                            @endif


                        </div>
                      @endif

                    @else
                        <div class="col-sm-12 col-lg-3 mb-5 border-end">
                            @include('layouts.frontend-partials.sidebar')
                        </div>
                        <div class="col-lg-9 ps-lg-5">

                            @yield('content')

                            @if(Auth::check())
                                @if (get_setting('ads') == 1)
                                   @if(get_setting('payments_on_site') == 'Yes')
                                        @if (Auth::user()->subscription()->ads == 0)
                                            <div class="ad-spot text-center mt-4" data-aos="fade-up" data-aos-easing="linear">
                                                <div class="ad-box">
                                                    {!! get_setting('footer_ad') !!}
                                                </div>
                                            </div>
                                        @endif
                                   @else
                                        <div class="ad-spot text-center mt-4" data-aos="fade-up" data-aos-easing="linear">
                                            <div class="ad-box">
                                                {!! get_setting('footer_ad') !!}
                                            </div>
                                        </div>
                                   @endif
                                @endif
                            @else
                                @if (get_setting('ads') == 1)
                                    <div class="ad-spot text-center mt-4" data-aos="fade-up" data-aos-easing="linear">
                                        <div class="ad-box">
                                            {!! get_setting('footer_ad') !!}
                                        </div>
                                    </div>
                                @endif
                            @endif

                        </div>
                    @endif

                </div><!--/row-->
            </div><!--/container-->
        </section>

        @include('layouts.frontend-partials.footer')

    </div>

    <div class="cookie-form hiden" id="cookiePopup">
        <div class="cookie-body">
            <h5><img src="{{ my_asset('uploads/settings/cookie.svg') }}" class="cookie-img me-2" alt="Cookie">Cookie Notice</h5>
            <p class="my-3">{{ get_setting('cookie_consent') }} <a href="">Cookie Policy</a></p>
            <button id="acceptCookie" class="btn btn-sm btn-mint">Accept Cookies</button>
        </div>
    </div>


	<!-- ==============================================
	Scripts
	=============================================== -->
	<script src="{{ my_asset('assets/vendors/simplebar/simplebar.min.js') }}"></script>
	<script src="{{ my_asset('assets/vendors/aos/aos.js') }}"></script>
    <script src="{{ my_asset('assets/vendors/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ my_asset('assets/vendors/tata/tata.js') }}"></script>
	    <script src="{{ my_asset('assets/frontend/js/main.js') }}"></script>
    <script src="{{ my_asset('assets/frontend/js/mobile-enhancements.js') }}"></script>
    <script src="{{ my_asset('assets/frontend/js/form-validation.js') }}"></script>
    <script src="{{ my_asset('assets/backend/js/functions.js') }}"></script>

    @if (Session::has('success'))
      <script>
        tata.success("Success", "{{ Session::get('success') }}", {
          position: 'tr',
          duration: 3000,
          animate: 'slide'
        });
      </script>
    @endif

    @if (Session::has('error'))
      <script>
        tata.error("Error", "{{ Session::get('error') }}", {
          position: 'tr',
          duration: 6000,
          animate: 'slide'
        });
      </script>
    @endif


    @if (!Route::is('home.post') && !Route::is('user.comments.edit') && !Route::is('user.replies.edit'))
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        </script>
    @endif

    @yield('scripts')

</body>
</html>
