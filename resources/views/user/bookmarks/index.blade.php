@extends('layouts.user')

@section('content')

    <div class="d-flex justify-content-between mb-4" data-aos="fade-down" data-aos-easing="linear">
        <h4><i class="bi bi-bookmark-check me-2"></i> {{ trans('bookmarks') }}</h4>
    </div>


    <div class="bookmarks" id="bookmarks">
        @include('frontend.pagination.user.bookmarks')
    </div><!--/replies-->

@endsection


@section('scripts')
<script>
    $(document).on('click', '#user_bookmarks .pagination-list a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];

        $.ajax({
            url: "{{ url('user/bookmarks/pagination/?page=') }}" + page,
            data: {},
            success: function(response) {
                $('#bookmarks').html(response);

                window.scroll({
                    top: 0, left: 0,
                    behavior: 'smooth'
                });
            }
        });

    });
</script>
@endsection
