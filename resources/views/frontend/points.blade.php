@extends('layouts.front')

@section('styles')
<link rel="stylesheet" href="{{ my_asset('assets/vendors/magnific-popup/magnific-popup.css') }}">
@endsection

@section('content')

    <div class="vine-header mb-4" data-aos="fade-down" data-aos-easing="linear">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('home') }}"><span class="bi bi-house me-1"></span>{{ trans('home') }}</a></li>
                        <li>{{ trans('points') }}</li>
                    </ul>
                    <h2 class="mb-2">{{ trans('points') }}</h2>
                    <p>{{ trans('earn_points_by_doing_the_following') }}</p>
                </div>
            </div>
        </div>
    </div><!--/vine-header-->

    <div class="points">
        <div class="container">
            <div class="row">
                <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                    <div class="counter-box">
                        <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                        <h2 class="mb-2">{{ get_setting('login') }} {{ trans('points') }}</h2>
                        <p>{{ trans('login') }} {{ trans('points') }}</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                    <div class="counter-box">
                        <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                        <h2 class="mb-2">{{ get_setting('registration') }} {{ trans('points') }}</h2>
                        <p>{{ trans('registration') }} {{ trans('points') }}</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                    <div class="counter-box">
                        <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                        <h2 class="mb-2">{{ get_setting('new_posts_no') }} {{ trans('points') }}</h2>
                        <p>{{ trans('post') }} {{ trans('points') }}</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                    <div class="counter-box">
                        <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                        <h2 class="mb-2">{{ get_setting('comment') }} Points</h2>
                        <p>{{ trans('comments') }} {{ trans('points') }}</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                    <div class="counter-box">
                        <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                        <h2 class="mb-2">{{ get_setting('reply') }} {{ trans('points') }}</h2>
                        <p>{{ trans('reply') }} {{ trans('points') }}</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                    <div class="counter-box">
                        <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                        <h2 class="mb-2">{{ get_setting('like') }} {{ trans('points') }}</h2>
                        <p>{{ trans('like') }} {{ trans('points') }}</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                    <div class="counter-box">
                        <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                        <h2 class="mb-2">{{ get_setting('reaction') }} {{ trans('points') }}</h2>
                        <p>{{ trans('reaction') }} {{ trans('points') }}</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                    <div class="counter-box">
                        <div class="box-particles"><img src="{{ my_asset('uploads/settings/box-particle-2.svg') }}" alt="Image"></div>
                        <h2 class="mb-2">{{ get_setting('share') }} {{ trans('points') }}</h2>
                        <p>{{ trans('share') }} {{ trans('points') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@if(get_setting('payments_on_site') == 'Yes')

    <div class="points">
        <div class="container">
            <div class="row">
                <h2 class="my-4" data-aos="fade-up" data-aos-easing="linear">{{ trans('buy_points') }}</h2>
                @foreach ($points as $point)
                    <div class="col-lg-4" data-aos="fade-up" data-aos-easing="linear">
                        <div class="counter-box">
                            <h2 class="mb-2">{{ $point->value }} {{ trans('points') }}</h2>
                            @if (Auth::check())
                                <a href="#point-dialog" class="btn btn-red btn-sm px-2 has-popup">{{ trans('purchase') }} @ {{ get_setting('currency_symbol') }}{{ $point->price }}</a>
                            @else
                                <a href="#point-dialog" class="btn btn-red btn-sm px-2 has-popup">{{ trans('purchase') }} @ {{ get_setting('currency_symbol') }}{{ $point->price }}</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if (Auth::check())
        <div id="point-dialog" class="white-popup zoom-anim-dialog mfp-hide">
            <div class="mfp-modal-header">
                <span class="mb-2">
                    <img src="{{ my_asset('uploads/users/'.Auth::user()->image) }}" class="rounded-circle" alt="User">
                </span>
                <h4>{{ trans('your') }} {{ trans('wallet') }} - {{ get_setting('currency_symbol') }}{{ Auth::user()->wallet }}</h4>
            </div>
            <div class="mfp-modal-body py-4">

                <div class="w-100 pt-3 pb-3 px-4">

                    <form id="buy_points" method="POST">
                        @csrf
                        <label for="point_id">{{ trans('choose_points') }}</label>
                        <select name="point_id" id="point_id">
                            @foreach ($points as $point)
                                <option value="{{ $point->id }}">{{ $point->value }} {{ trans('points') }} - {{ get_setting('currency_symbol') }}{{ $point->price }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-mint w-100 mt-4" id="buy_points_btn"><i class="bi bi-send fs-xl me-2"></i>{{ trans('pay') }}</button>
                    </form>

                </div>
            </div>
        </div>
    @else
        <div id="point-dialog" class="white-popup zoom-anim-dialog mfp-hide">
                <h4>{{ trans('please_login_to_purchase_points') }}.</h4>
        </div>
    @endif

@endif

@endsection

@section('scripts')
<script src="{{ my_asset('assets/vendors/magnific-popup/magnific-popup.js') }}"></script>
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

    // Pay for Plan
    $(document).on('submit', '#buy_points', function(e) {
        e.preventDefault();
        start_load();
        const fd = new FormData(this);
        $("#buy_points_btn").text('{{ trans('sending') }}...');
        $.ajax({
            url: '{{ route('points.buy') }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {

            end_load();

            if (response.status == 200) {

                tata.success("Success", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });

                window.location.reload();

            }else if(response.status == 401){

                tata.error("Error", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });

                window.location.reload();

            }

            }
        });
    });
</script>
@endsection
