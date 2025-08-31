@extends('layouts.user')

@section('content')

    <div class="mb-4" data-aos="fade-down" data-aos-easing="linear">
        <h4><i class="bi bi-speedometer me-2"></i> {{ trans('overview') }}</h4>
    </div>


    <div class="row mt-2">

        <!-- stats start -->
        <div class="col-lg-4" data-aos="fade-down" data-aos-easing="linear">
            @if(get_setting('payments_on_site') == 'Yes')
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm icon-with-bg rounded-2 me-3">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mt-0 mb-0"><span class="counter">{{ Auth::user()->earnings }}</span> </h3>
                                <p class="text-muted mb-0">{{ trans('earnings') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm icon-with-bg rounded-2 me-3">
                            <i class="bi bi-journals"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mt-0 mb-0"><span class="counter">{{ $posts }}</span> </h3>
                            <p class="text-muted mb-0">{{ trans('posts') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm icon-with-bg rounded-2 me-3">
                            <i class="bi bi-eye"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mt-0 mb-0"><span class="counter">{{ $viewers }}</span></h3>
                            <p class="text-muted mb-0">{{ trans('views') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @if(get_setting('payments_on_site') == 'No')
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm icon-with-bg rounded-2 me-3 flex-shrink-0">
                            <i class="bi bi-bookmarks"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mt-0 mb-0"><span class="counter">{{ $bookmarks }}</span></h3>
                                <p class="text-muted mb-0">{{ trans('bookmarks') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- stats end -->

        <!-- stats start -->
        <div class="col-lg-4" data-aos="fade-down" data-aos-easing="linear" data-aos-delay="1000">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm icon-with-bg rounded-2 me-3 flex-shrink-0">
                         <i class="bi bi-chat-left-dots"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mt-0 mb-0"><span class="counter">{{ $comments }}</span></h3>
                            <p class="text-muted mb-0">{{ trans('comments') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm icon-with-bg rounded-2 me-3">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mt-0 mb-0"><span class="counter">{{ $replies }}</span></h3>
                            <p class="text-muted mb-0">{{ trans('replies') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @if(get_setting('payments_on_site') == 'Yes')
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm icon-with-bg rounded-2 me-3 flex-shrink-0">
                            <i class="bi bi-bookmarks"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mt-0 mb-0"><span class="counter">{{ $bookmarks }}</span></h3>
                                <p class="text-muted mb-0">{{ trans('bookmarks') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- stats end -->

        <!-- profile widget star -->
        <div class="col-lg-4" data-aos="fade-down" data-aos-easing="linear" data-aos-delay="1500">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm icon-with-bg rounded-2 me-3 flex-shrink-0">
                         <i class="bi bi-person-plus"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mt-0 mb-0"><span class="counter">{{ Auth::user()->followings->count() }}</span></h3>
                            <p class="text-muted mb-0">{{ trans('following') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm icon-with-bg rounded-2 me-3 flex-shrink-0">
                         <i class="bi bi-people"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mt-0 mb-0"><span class="counter">{{ Auth::user()->followers->count() }}</span></h3>
                            <p class="text-muted mb-0">{{ trans('followers') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--/row-->

    <div class="row">
        <div class="col-lg-8">
            <div class="card my-5" data-aos="fade-up" data-aos-easing="linear">
                <div class="card-body">
                    <h5 class="my-3 ms-3">{{ trans('profile') }} {{ trans('views') }}</h5>
                    <div id="chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-12">
            <div class="card my-5" data-aos="fade-up" data-aos-easing="linear" data-aos-delay="1000">
                <div class="card-body follow-users">
                    <h5 class="my-3 ms-3">{{ trans('new_followers') }}</h5>
                    <ul class="list-group list-group-flush">
                        @forelse(Auth::user()->followers()->with('followers')->limit('5')->get() as $follower)

                            <li class="list-group-item border-0">
                                <div class="media align-items-center">
                                    <div class="media-head me-3">
                                        <div class="avatar avatar-sm">
                                        <a href="{{ route('user', ['username' => $follower->username]) }}"><img src="{{ my_asset('uploads/users/'.$follower->image) }}" alt="user" class="avatar-img avatar-rounded"></a>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <a href="{{ route('user', ['username' => $follower->username]) }}" class="d-block text-capitalize">{{ $follower->name }}</a>
                                        <span class="d-block text-muted fs-7">{{'@'.$follower->username  }}</span>
                                    </div>
                                </div>
                            </li>

                        @empty

                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div><!--/row-->

@endsection

@section('scripts')
<script src="{{ my_asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
<script>
    $(document).ready(function () {

        "use strict";

        /* ==========================================================================
        ApexChart Line Graph
        ========================================================================== */

        var options = {
            series: [{
                name: '{{ trans('profile') }} {{ trans('views') }}',
                data: {{ $user_views }}
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            grid: {
                row: {
                    colors: ['transparent', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            colors: ['#1BC5BD']
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();


    });
</script>

@endsection
