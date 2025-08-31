@extends('layouts.message')

@section('styles')
<link rel="stylesheet" href="{{ my_asset('assets/vendors/magnific-popup/magnific-popup.css') }}">
<script src="{{ my_asset('assets/vendors/magnific-popup/magnific-popup.js') }}"></script>

@endsection

@section('content')

    <!-- Sidebar START -->
    <div class="col-lg-4">

        <!-- Divider -->
        <div class="d-flex align-items-center my-5 d-lg-none">
            <button class="border-0 bg-transparent" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <i class="btn btn-mint bi bi-sliders2"></i>
                <span class="h6 mb-0 fw-bold d-lg-none ms-2">{{ trans('chats') }}</span>
            </button>
        </div>
                <!-- Advanced filter responsive toggler END -->
        <div class="card card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h5 mb-0">{{ trans('active_chats') }} <span class="badge bg-green bg-opacity-10 text-white">{{ $chats->count() }}</span></h1>
                <!-- Chat new create message item START -->
                <div class="dropend position-relative">
                    <div class="nav">
                        <a class="icon-md rounded-circle btn btn-sm btn-mint has-popup" href="#small-dialog"><i class="bi bi-pencil-square"></i></a>
                    </div>
                </div>
                <!-- Chat new create message item END -->


                <div id="small-dialog" class="white-popup zoom-anim-dialog mfp-hide">
                    <div class="mfp-modal-body py-4">

                        <div class="w-100 pt-3 pb-3 px-4">
                            <h4 class="mb-4">{{ trans('start_new_chat') }}</h4>
                            <input type="text" name="search" id="searchUser" placeholder="{{ trans('search_users') }}">

                          <div class="mt-5 px-3" id="searchUsersBox">
                          </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <nav class="navbar navbar-light navbar-expand-lg mx-0">
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar">
            <!-- Offcanvas header -->
            <div class="offcanvas-header">
                <button type="button" class="btn-close text-reset ms-auto" data-bs-dismiss="offcanvas"></button>
            </div>

            <!-- Offcanvas body -->
            <div class="offcanvas-body p-0">
                <div class="card card-chat-list card-body">

                    <!-- Search chat START -->
                    <div class="position-relative">
                        <input class="py-2" type="search" id="searchChats" placeholder="{{ trans('search_for_chats') }}" aria-label="Search">
                    </div>
                    <!-- Search chat END -->
                    <!-- Chat list tab START -->
                    <div class="mt-4 h-100">
                    <div data-simplebar
                    @if (get_setting('site_direction') == 'rtl')
                        data-simplebar-direction='rtl'
                    @endif class="chat-tab-list">
                        <ul class="nav flex-column nav-pills nav-pills-soft" id="sidebarChats">

                            @include('user.messages.chats')

                        </ul>
                    </div>
                    </div>
                    <!-- Chat list tab END -->
                </div>
            </div>
        </div>
        </nav>
    </div>
    <!-- Sidebar START -->

    <!-- Chat conversation START -->
    <div class="col-lg-8 px-lg-4">
        <div class="card card-chat rounded-start-lg-0 border-start-lg-0">
            <div class="card-body h-100">
                <div class="h-100">
                    <!-- Chat conversation START -->
                    <div class="chat-conversation-content chat-conversation-content-lg" id="messagesContent">
                    <div class="chat-display flex-column d-flex justify-content-center text-center h-100">
                        <h2><i class="bi bi-chat-square-text me-1"></i> {{ trans('messages') }}</h2>
                        <p class="mb-2">{{ trans('inbox_of_your_messages') }}</p>
                        <a href="#small-dialog" class="btn btn-mint rounded-pill has-popup"> <i class="bi bi-send me-1"></i> {{ trans('new_message') }}</a>
                    </div>

                    </div><!-- Chat conversation END -->
                </div>
            </div>

        </div>
    </div>
        <!-- Chat conversation END -->

@endsection

@section('scripts')
    <script src="{{ my_asset('assets/frontend/js/messages.js') }}"></script>

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

    </script>

@endsection
