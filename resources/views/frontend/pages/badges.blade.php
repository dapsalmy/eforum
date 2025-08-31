@extends('layouts.front')

@section('content')


    <div class="vine-header mb-4" data-aos="fade-down" data-aos-easing="linear">
        <div class="container">
            <div class="row px-3">
                <div class="col-lg-6">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('home') }}"><span class="bi bi-house me-1"></span>{{ trans('home') }}</a></li>
                        <li>{{ trans('badges') }}</li>
                    </ul>
                    <h2 class="mb-2">{{ trans('badges') }}</h2>
                </div>
            </div>
        </div>
    </div><!--/vine-header-->

    <div class="badges">
        <div class="row">
            @foreach ($badges as $badge)

                <div class="badges-box" data-aos="fade-up" data-aos-easing="linear">
                    <div class="badges-badge"> Points: {{ $badge->score }} </div>
                    <div class="badges-box-img">
                        <img src="{{ my_asset('uploads/badges/'.$badge->image) }}" class="img-fluid" alt="User">
                    </div>
                    <div class="badges-title-box">
                        <h3 class="mb-2">{{ $badge->name }}</h3>
                        <p>{{ $badge->description }}</p>
                    </div>
                </div><!--/leadeboard-box-->

            @endforeach


        </div>
    </div>

@endsection
