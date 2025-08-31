@extends('layouts.user')

@section('content')


    <div class="d-flex justify-content-between mb-5" data-aos="fade-down" data-aos-easing="linear">
        <h4><i class="bi bi-chat-dots me-2"></i> {{ trans('replies') }}</h4>
        <a href="{{ route('home.posts') }}" class="btn btn-sm btn-mint rounded-pill"><i class="bi bi-plus-circle-dotted me-2"></i>{{ trans('add_reply') }}</a>
    </div>

    <div class="replies" id="replies">
        @include('frontend.pagination.user.replies')
    </div><!--/replies-->


@endsection

@section('scripts')
<script>
    $(document).on('click', '#user_replies .pagination-list a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];

        $.ajax({
            url: "{{ url('user/replies/pagination/?page=') }}" + page,
            data: {},
            success: function(response) {
                $('#replies').html(response);

                window.scroll({
                    top: 0, left: 0,
                    behavior: 'smooth'
                });
            }
        });

    });
</script>
@endsection
