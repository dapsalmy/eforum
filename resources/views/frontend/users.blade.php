@extends('layouts.front')

@section('styles')
<link rel="stylesheet" href="{{ my_asset('assets/vendors/emoji-picker/lib/css/emoji.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/magnific-popup/magnific-popup.css') }}">
<script src="{{ my_asset('assets/vendors/emoji-picker/lib/js/config.js') }}"></script>
<script src="{{ my_asset('assets/vendors/emoji-picker/lib/js/util.js') }}"></script>
<script src="{{ my_asset('assets/vendors/emoji-picker/lib/js/jquery.emojiarea.js') }}"></script>
<script src="{{ my_asset('assets/vendors/emoji-picker/lib/js/emoji-picker.js') }}"></script>
<script src="{{ my_asset('assets/vendors/magnific-popup/magnific-popup.js') }}"></script>

@endsection

@section('content')

    <div class="vine-header mb-4" data-aos="fade-down" data-aos-easing="linear">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('home') }}"><span class="bi bi-house me-1"></span>{{ trans('home') }}</a></li>
                        <li>{{ trans('users') }}</li>
                    </ul>
                    <h2 class="mb-2">{{ trans('users') }}</h2>
                </div>
            </div>
        </div>
    </div><!--/vine-header-->

    <div class="filter mx-0">
        <form class="form" id="search_form">
            @csrf
            <div class="filter-toolbar">
                <div class="filter-item" id="locationSort">
                    <label>{{ trans('location') }}</label>
                    <a class="filter-item-content dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <input type="hidden" name="location" id="location" value="">
                        <span class="filter-value"></span>
                        <span class="dropdown-btn"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-2x dropdown-location">
                           <li value="all" class="selected">{{ trans('all') }} </li>
                           @foreach ($countries as $country)
                            <li value="{{ $country->code }}">{{ $country->name }}</li>
                           @endforeach

                    </ul>
                </div>
                <div class="filter-item" id="sorting">
                    <label>{{ trans('sorting') }}</label>
                    <a class="filter-item-content dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <input type="hidden" name="sort" id="sort" value="">
                        <span class="filter-value"></span>
                        <span class="dropdown-btn"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li value="all" class="selected">{{ trans('all') }}</li>
                        <li value="recent">{{ trans('recent') }} </li>
                        <li value="most_posts">{{ trans('most') }} {{ trans('posts') }}</li>
                        <li value="most_comments">{{ trans('top') }} {{ trans('comments') }}</li>
                    </ul>
                </div>
                <div class="filter-item" id="numberSort">
                    <label>{{ trans('number') }}</label>
                    <a class="filter-item-content dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <input type="hidden" name="number" id="number" value="">
                        <span class="filter-value"></span>
                        <span class="dropdown-btn"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li value="12" class="selected">{{ trans('show') }} 12 </li>
                        <li value="24">{{ trans('show') }} 24 </li>
                        <li value="36">{{ trans('show') }} 36 </li>
                        <li value="48">{{ trans('show') }} 48 </li>
                        <li value="60">{{ trans('show') }} 60 </li>
                        <li value="100">{{ trans('show') }} 100 </li>
                    </ul>
                </div>

                <!-- Nav Search START -->
                <div class="w-100 mt-3 mb-3">
                    <div class="nav px-4 flex-nowrap align-items-center">
                        <div class="search-form nav-item w-100">
                            <input class="border-0" name="search_term" id="search_term" type="search" placeholder="{{ trans('search') }}" aria-label="Search">
                        </div>
                    </div>
                </div>
                <!-- Nav Search END -->
                <button type="submit" id="search_posts_btn" class="btn btn-md btn-mint">{{ trans('search') }}</button>
            </div>
        </form>
    </div><!--/filter-->

    <div class="users mt-5">
        <div class="row" id="users_data">
        </div>
    </div>


@endsection

@section('scripts')

<script>

    $(function() {

        // create message ajax request
        $(document).on('submit', '#create_message', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#create_message_btn").text('{{ trans('sending') }}...');
            $.ajax({
                url: '{{ route('user.chats.create') }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {

                end_load();

                if (response.status == 400) {

                    showError('message', response.messages.message);
                    $("#create_message_btn").text('{{ trans('send') }}');

                }else if (response.status == 200) {

                    tata.success("Success", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    removeValidationClasses("#create_message");
                    $("#create_message")[0].reset();
                    window.location.reload();

                }else if(response.status == 401){

                    tata.error("Error", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    $("#create_message")[0].reset();
                    window.location.reload();

                }

                }
            });
        });
    });

    //Location Default
    var loc_text = $('#locationSort .dropdown-menu li.selected').text();
    var loc_value = $('#locationSort .dropdown-menu li.selected').attr('value');
    $('#locationSort input').val(loc_value);
    $('#locationSort .filter-value').html(loc_text);

    //Number Default
    var num_text = $('#numberSort .dropdown-menu li.selected').text();
    var num_value = $('#numberSort .dropdown-menu li.selected').attr('value');
    $('#numberSort input').val(num_value);
    $('#numberSort .filter-value').html(num_text);

    //Sorting Default
    var sort_text = $('#sorting .dropdown-menu li.selected').text();
    var sort_value = $('#sorting .dropdown-menu li.selected').attr('value');
    $('#sorting input').val(sort_value);
    $('#sorting .filter-value').html(sort_text);

    filterUsers();

    $('#locationSort .dropdown-menu li').on('click', function() {
        var value = $(this).attr('value');
        var text = $(this).text();
        var item = $(this);
        item.closest('#locationSort').find('li.selected').removeClass('selected');
        $(this).addClass('selected');
        $('#locationSort').find('input').val(value);
        $('#locationSort').find('.filter-value').html(text);
        filterUsers();
    });

    $('#sorting .dropdown-menu li').on('click', function() {
        var value = $(this).attr('value');
        var text = $(this).text();
        var item = $(this);
        item.closest('#sorting').find('li.selected').removeClass('selected');
        $(this).addClass('selected');
        $('#sorting').find('input').val(value);
        $('#sorting').find('.filter-value').html(text);
        filterUsers();
    });

    $('#numberSort .dropdown-menu li').on('click', function() {
        var value = $(this).attr('value');
        var text = $(this).text();
        var item = $(this);
        item.closest('#numberSort').find('li.selected').removeClass('selected');
        $(this).addClass('selected');
        $('#numberSort').find('input').val(value);
        $('#numberSort').find('.filter-value').html(text);
        filterUsers();
    });

    $(document).on('submit', '#search_form', function(e) {
        e.preventDefault();
        $("#search_posts_btn").text('{{ trans('searching') }}...');
        filterUsers();
    });

    function filterUsers() {

        let location = $('#location').val();
        let sort = $('#sort').val();
        let number = $('#number').val();
        let search_term = $('#search_term').val();

        let url = "{{ route('users.sort') }}";
        $.ajax({
            type: "get",
            url: url,
            data: {
                'location': location,
                'number': number,
                'sort': sort,
                'search_term': search_term
            },
            success: function(response) {

                $('#users_data').html(response);

                $("#search_posts_btn").text('Search');
            }
        }).done(function() {
            setTimeout(() => {
                $("#overlay, #overlay2").fadeOut(300);
            }, 500);
        });
    }

    $(document).on('click', '.pagination-list a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        let location = $('#location').val();
        let sort = $('#sort').val();
        let number = $('#number').val();
        let search_term = $('#search_term').val();

        fetchData(page, location, sort, number, search_term);
    });

    function fetchData(page, location, sort, number, search_term) {

        var location = location;
        var sort = sort;
        var number = number;
        var search_term = search_term;

        $.ajax({
            url: "{{ url('users/pagination/?page=') }}" + page,
            data: {
                'location': location,
                'sort': sort,
                'number': number,
                'search_term': search_term
            },
            success: function(response) {

                $('#users_data').html(response);

                window.scroll({
                    top: 0, left: 0,
                    behavior: 'smooth'
                });
            }
        });
    }

    $(document).on('click', '.followUser', function(e) {
        e.preventDefault();
        let a = $(this).attr('id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ route('follow') }}',
            method: 'post',
            dataType: "json",
            data: {item: a},
            success: function(e) {

                var t;

                if (e.bool === true){

                    $("#" + a).removeClass('btn-mint');
                    $("#" + a).addClass('btn-danger');
                    $("#follow-icon-" + a).removeClass('bi-person-plus');
                    $("#follow-icon-" + a).addClass('bi-person-check');
                    t = $("#followers-" + a).text(), $("#followers-" + a).text(++t);

                }else if(e.bool === false){

                    $("#" + a).removeClass('btn-danger');
                    $("#" + a).addClass('btn-mint');
                    $("#follow-icon-" + a).removeClass('bi-person-check');
                    $("#follow-icon-" + a).addClass('bi-person-plus');
                    t = $("#followers-" + a).text(), $("#followers-" + a).text(--t);

                }

                if (e.status == 200) {

                    tata.success("Success", e.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                }
            },
            error: function(e) {

                tata.error("Error", 'Please Login to Follow', {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });
            }
        });

        $(document).on('mouseout' , '.btn-danger' , function(e) {
            let a = $(this).attr('id');
            $("#follow-icon-" + a).removeClass('bi-person-plus');
            $("#follow-icon-" + a).addClass('bi-person-check');
        });
        $(document).on('mouseover' , '.btn-danger' , function(e) {
            let a = $(this).attr('id');
            $("#follow-icon-" + a).removeClass('bi-person-check');
            $("#follow-icon-" + a).addClass('bi-person-plus');
        });
    });
</script>

@endsection
