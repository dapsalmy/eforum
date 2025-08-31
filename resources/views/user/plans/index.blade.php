@extends('layouts.user')

@section('styles')
<link rel="stylesheet" href="{{ my_asset('assets/vendors/magnific-popup/magnific-popup.css') }}">
<script src="{{ my_asset('assets/vendors/magnific-popup/magnific-popup.js') }}"></script>
@endsection

@section('content')

    <div class="mb-4" data-aos="fade-down" data-aos-easing="linear">
        <h4><i class="bi bi-credit-card-2-front me-2"></i> {{ trans('pricing_plans') }}</h4>
    </div>

    <div class="plans">
        <div class="row">

            @forelse($plans as $plan)
                <div class="col-lg-6 mb-5" data-aos="fade-up" data-aos-easing="linear">
                    <div class="price">
                            <h4>{{ $plan->name }}</h4>
                        <div class="price-price">
                            <span><b>{{ get_setting('currency_symbol') }}{{ $plan->price }}</b> /{{ $plan->duration }}</span>
                        </div>
                        <div class="price-list">
                            <ul>
                                <li><i class="bi bi-check2"></i> {{ $plan->points }} {{ trans('points_topup') }}</li>
                                <li class="{{ $plan->verified == '0' ? 'price-disable' : ''}}">
                                    <i class="bi {{ $plan->verified == '1' ? 'bi-check2' : 'bi-x'}} "></i> {{ trans('verified_checkmark') }}
                                </li>
                                <li class="{{ $plan->categories == '0' ? 'price-disable' : ''}}">
                                    <i class="bi {{ $plan->categories == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('access_to_pro_categories') }}
                                </li>
                                <li class="{{ $plan->tips == '0' ? 'price-disable' : ''}}">
                                    <i class="bi {{ $plan->tips == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('access_to_tips') }}
                                </li>
                                <li class="{{ $plan->comments == '0' ? 'price-disable' : ''}}">
                                    <i class="bi {{ $plan->comments == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('allow_or_close_comments_on_posts') }}
                                </li>
                                <li class="{{ $plan->reactions == '0' ? 'price-disable' : ''}}">
                                    <i class="bi {{ $plan->reactions == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('show_or_hide_reactions_or_likes_on_posts') }}
                                </li>
                                <li class="{{ $plan->followers == '0' ? 'price-disable' : ''}}">
                                    <i class="bi {{ $plan->followers == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('allow_user_to_post_discussions_to_only_followers') }}
                                </li>
                                <li class="{{ $plan->messages == '0' ? 'price-disable' : ''}}">
                                    <i class="bi {{ $plan->messages == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('mute_chat_messages') }}
                                </li>
                                <li class="{{ $plan->users == '0' ? 'price-disable' : ''}}">
                                    <i class="bi {{ $plan->users == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('block_users') }}
                                </li>
                                <li class="{{ $plan->viewers == '0' ? 'price-disable' : ''}}">
                                    <i class="bi {{ $plan->viewers == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('view_profile_visitors') }}
                                </li>
                                <li class="{{ $plan->ads == '0' ? 'price-disable' : ''}}">
                                    <i class="bi {{ $plan->ads == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('no_ads') }}
                                </li>
                            </ul>
                        </div>
                        @if (Auth::check())
                        <div class="mt-3">
                            @if (Auth::user()->plan_id == $plan->id)
                                <a href="javascript:void(0)" class="btn btn-danger btn-sm rounded-pill"><i class="bi bi-arrow-repeat me-1"></i>{{ trans('subscribed') }}</a>
                            @else
                                <a href="#sub-dialog-{{ Auth::user()->id }}" class="btn btn-mint btn-sm rounded-pill has-popup"><i class="bi bi-arrow-repeat me-1"></i>{{ trans('subscribe_now') }} / {{ get_setting('currency_symbol') }}{{ $plan->price }}</a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                        <div class="dashboard-body">
                            <div class="upload-image my-3">
                                <h4 class="mb-3">{{ trans('no_pricing_plans_available') }}.</h4>
                            </div>
                        </div>
                    </div><!--/dashboard-card-->
                </div>

            @endforelse

        </div>
    </div>

    @if(Auth::check())
        <div id="sub-dialog-{{ Auth::user()->id }}" class="white-popup zoom-anim-dialog mfp-hide">
            <div class="mfp-modal-header">
                <span class="mb-2">
                    <img src="{{ my_asset('uploads/users/'.Auth::user()->image) }}" class="rounded-circle" alt="User">
                </span>
                <h4>{{ trans('your') }} {{ trans('wallet') }} - {{ get_setting('currency_symbol') }}{{ Auth::user()->wallet }}</h4>
            </div>
            <div class="mfp-modal-body py-4">

                <div class="w-100 pt-3 pb-3 px-4">

                    <form id="pay_plan" method="POST">
                        @csrf
                        <label for="plan_id">{{ trans('choose_plan') }}</label>
                        <select name="plan_id" id="plan_id">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} - {{ get_setting('currency_symbol') }}{{ $plan->price }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-mint w-100 mt-4" id="pay_plan_btn"><i class="bi bi-send fs-xl me-2"></i>{{ trans('pay') }}</button>
                    </form>

                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
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
    $(document).on('submit', '#pay_plan', function(e) {
        e.preventDefault();
        start_load();
        const fd = new FormData(this);
        $("#pay_plan_btn").text('{{ trans('sending') }}...');
        $.ajax({
            url: '{{ route('user.pricing.pay') }}',
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
