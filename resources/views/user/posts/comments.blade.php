@extends('layouts.user')

@section('content')


    <div class="d-flex justify-content-between mb-5" data-aos="fade-down" data-aos-easing="linear">
        <h4><i class="bi bi-chat-left-dots me-2"></i> {{ trans('comments') }}</h4>
        <a href="{{ route('home.posts') }}" class="btn btn-sm btn-mint rounded-pill"><i class="bi bi-plus-circle-dotted me-2"></i>{{ trans('add_comment') }}</a>
    </div>

    <div class="comments" id="comments">
        @include('frontend.pagination.user.comments')
    </div><!--/comments-->


@endsection

@section('scripts')
<script>


    $(document).on('click', '#user_comments .pagination-list a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];

        $.ajax({
            url: "{{ url('user/comments/pagination/?page=') }}" + page,
            data: {},
            success: function(response) {
                $('#comments').html(response);

                window.scroll({
                    top: 0, left: 0,
                    behavior: 'smooth'
                });
            }
        });

    });

</script>

@endsection

