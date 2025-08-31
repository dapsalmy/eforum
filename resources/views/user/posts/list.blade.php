@extends('layouts.user')

@section('content')


    <div class="d-flex justify-content-between mb-5" data-aos="fade-down" data-aos-easing="linear">
        <h4><i class="bi bi-journals me-2"></i> {{ trans('posts') }}</h4>
        <a href="{{ route('user.posts.add') }}" class="btn btn-sm btn-mint rounded-pill"><i class="bi bi-plus-circle-dotted me-2"></i>{{ trans('add_post') }}</a>
    </div>


    <div class="posts" id="posts">
        @include('frontend.pagination.user.posts')
    </div><!--/posts-->


@endsection

@section('scripts')
<script>

    function likePost(a) {
        $.ajax({
            url: '{{ route('like') }}',
            method: 'post',
            dataType: "json",
            data: {item: a},
            success: function(e) {
                var t;
                1 == e.bool ? ($("#like-icon-" + a).removeClass("text-muted").addClass("text-danger"), t = $("#like-" + a).text(), $("#like-" + a).text(++t)) : 0 == e.bool && ($("#like-icon-" + a).removeClass("text-danger").addClass("text-muted"), t = $("#like-" + a).text(), $("#like-" + a).text(--t))

                if (e.status == 200) {

                    tata.success("Success", e.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                }
            },
            error: function(e) {

                tata.error("Error", '{{ trans('please_login_to_like') }}', {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });
            }
        })
    }

    $(document).on('click', '#user_posts .pagination-list a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];

        $.ajax({
            url: "{{ url('/user/posts/pagination/?page=') }}" + page,
            data: {},
            success: function(response) {
                $('#posts').html(response);

                window.scroll({
                    top: 0, left: 0,
                    behavior: 'smooth'
                });
            }
        });

    });
</script>

@endsection
