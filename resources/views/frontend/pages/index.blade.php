@extends('layouts.front')

@section('content')


    <div class="vine-header mb-4" data-aos="fade-down" data-aos-easing="linear">
        <div class="container">
            <div class="row px-3">
                <div class="col-lg-6">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('home') }}"><span class="bi bi-house me-1"></span>{{ trans('home') }}</a></li>
                        <li>{{ $page->title }}</li>
                    </ul>
                    <h2 class="mb-2">{{ $page->title }}</h2>
                </div>
            </div>
        </div>
    </div><!--/vine-header-->

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="content" data-aos="fade-up" data-aos-easing="linear">
                {!!  $page->description  !!}
            </div>
        </div>
    </div>

@endsection
