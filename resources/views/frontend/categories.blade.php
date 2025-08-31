@extends('layouts.front')

@section('content')

    <div class="vine-header mb-4" data-aos="fade-down" data-aos-easing="linear">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('home') }}"><span class="bi bi-house me-1"></span>{{ trans('home') }}</a></li>
                        <li>{{ trans('categories') }}</li>
                    </ul>
                    <h2 class="mb-2">{{ trans('categories') }}</h2>
                </div>
            </div>
        </div>
    </div><!--/vine-header-->

    <div class="categories">
        <div class="row" id="categories">
            @include('frontend.pagination.categories')
        </div>
    </div><!--/categories-->

@endsection



@section('scripts')
<script>

    $(document).on('click', '.pagination-list a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];

        $.ajax({
            url: "{{ url('categories/pagination/?page=') }}" + page,
            data: {},
            success: function(response) {
                $('#categories').html(response);

                window.scroll({
                    top: 0, left: 0,
                    behavior: 'smooth'
                });
            }
        });

    });
</script>
@endsection
