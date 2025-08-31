<!DOCTYPE html>
<html lang="en" data-theme="light"
@if (get_setting('site_direction') == 'ltr')
    dir="ltr"
@elseif (get_setting('site_direction') == 'rtl')
    dir="rtl"
@endif>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('user') }} {{ trans('dashboard') }} - {{ get_setting('site_name') }}</title>
    <meta name="description" content="Forum & Community Discussions HTML Template">
    <meta name="keywords" content="bootstrap 5, forum, community, support, social, q&a, mobile, html">
    <meta name="robots" content="all,follow">

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

    <!-- ==============================================
     Fonts
    =============================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla&display=swap" rel="stylesheet">

	<script src="{{ my_asset('assets/vendors/jquery/jquery.min.js') }}"></script>
	<script src="{{ my_asset('assets/vendors/bootstrap/bootstrap.bundle.js') }}"></script>
	<script src="{{ my_asset('assets/vendors/popper/popper.min.js') }}"></script>

    <script>
    "use strict";
    var APP_URL = {!! json_encode(url('/')) !!}
    </script>

    @yield('styles')

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
		<section class="dashboard">
			<div class="container">
				<div class="row">

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
                        <div class="col-sm-12 col-lg-3 mb-5">
                            @include('layouts.frontend-partials.sidebar-dashboard')
                        </div>
                        <div class="col-12 col-lg-9">
                            @yield('content')
                        </div>
                    @endif

                </div><!--/row-->
            </div><!--/container-->
        </section>

        @include('layouts.frontend-partials.footer')

    </div>


	<!-- ==============================================
	Scripts
	=============================================== -->
	<script src="{{ my_asset('assets/vendors/simplebar/simplebar.min.js') }}"></script>
	<script src="{{ my_asset('assets/vendors/aos/aos.js') }}"></script>
    <script src="{{ my_asset('assets/vendors/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ my_asset('assets/vendors/tata/tata.js') }}"></script>
	<script src="{{ my_asset('assets/frontend/js/main.js') }}"></script>
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

    @if (!Route::is('user.posts.add') && !Route::is('user.posts.edit'))
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
