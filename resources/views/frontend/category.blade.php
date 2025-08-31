@extends('layouts.front')

@section('content')

    <div class="vine-header mb-4" data-aos="fade-down" data-aos-easing="linear">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('home') }}"><span class="bi bi-house me-1"></span>Home</a></li>
                        <li><a href="{{ route('categories') }}">Categories</a></li>
                        <li>{{ $category->name }}</li>
                    </ul>
                    <h2 class="mb-2">{{ $category->name }}
                        @if(get_setting('payments_on_site') == 'Yes')
                            @if ($category->pro == 1) <span class="badge bg-cat ms-1">Pro</span> @endif
                        @endif
                    </h2>
                    <p>{{ $category->description }}</p>
                </div>
            </div>
        </div>
    </div><!--/vine-header-->

@if(get_setting('payments_on_site') == 'Yes')

    @if ($category->pro === 1)
        @if (Auth::check())
            @if (Auth::user()->subscription()->categories === 1)
                <div class="filter mx-0">

                    <form class="form" id="search_form">
                        @csrf
                        <div class="filter-toolbar">
                            <input type="hidden" name="category_id" id="category_id" value="{{ $category->id }}">
                            <div class="filter-item" id="dateSort">
                                <label>{{ trans('date') }}</label>
                                <a class="filter-item-content dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <input type="hidden" name="date" id="date" value="">
                                    <span class="filter-value"></span>
                                    <span class="dropdown-btn"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li value="all" class="selected">{{ trans('all') }} </li>
                                    <li value="today">{{ trans('today') }}</li>
                                    <li value="week">{{ trans('week') }} </li>
                                    <li value="month">{{ trans('month') }} </li>
                                    <li value="year">{{ trans('year') }} </li>
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
                                    <li value="all" class="selected">{{ trans('all') }} </li>
                                    <li value="solved">{{ trans('solved') }}</li>
                                    <li value="pinned">{{ trans('pinned') }}</li>
                                    <li value="closed">{{ trans('closed') }}</li>
                                @if (get_setting('react_like') == 'React')
                                    <li value="most_reactions">{{ trans('most') }} {{ trans('reactions') }} </li>
                                @elseif (get_setting('react_like') == 'Like')
                                    <li value="most_likes">{{ trans('most') }} {{ trans('likes') }} </li>
                                @endif
                                    <li value="most_comments">{{ trans('most') }} {{ trans('comments') }} </li>
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

                <div class="discussions" id="discussions">

                    @include('frontend.partials.posts')

                </div><!--/discussions-->

            @else

                <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                    <div class="dashboard-body">
                        <div class="upload-image my-3">
                            <h4 class="mb-3">{{ trans('to_access_this_category') }}, {{ trans('you_need_to_purchase_a_new_pricing_plan') }}.</h4>
                            <a href="{{ route('plans') }}" class="btn btn-mint btn-md rounded-pill"><i class="bi bi-send me-2"></i>{{ trans('pricing_plans') }}</a>
                        </div>

                    </div>
                </div><!--/dashboard-card-->
            @endif
        @else

            <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
                <div class="dashboard-body">
                    <div class="upload-image my-3">
                        <h4 class="mb-3">{{ trans('login_to_access_this_category') }}.</h4>
                        <a href="{{ route('auth.login') }}" class="btn btn-mint btn-md rounded-pill">{{ trans('login') }}</a>
                    </div>

                </div>
            </div><!--/dashboard-card-->

        @endif
    @else
        <div class="filter mx-0">

            <form class="form" id="search_form">
                @csrf
                <div class="filter-toolbar">
                    <input type="hidden" name="category_id" id="category_id" value="{{ $category->id }}">
                    <div class="filter-item" id="dateSort">
                        <label>{{ trans('date') }}</label>
                        <a class="filter-item-content dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <input type="hidden" name="date" id="date" value="">
                            <span class="filter-value"></span>
                            <span class="dropdown-btn"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li value="all" class="selected">{{ trans('all') }} </li>
                            <li value="today">{{ trans('today') }}</li>
                            <li value="week">{{ trans('week') }} </li>
                            <li value="month">{{ trans('month') }} </li>
                            <li value="year">{{ trans('year') }} </li>
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
                            <li value="all" class="selected">{{ trans('all') }} </li>
                            <li value="solved">{{ trans('solved') }}</li>
                            <li value="pinned">{{ trans('pinned') }}</li>
                            <li value="closed">{{ trans('closed') }}</li>
                        @if (get_setting('react_like') == 'React')
                            <li value="most_reactions">{{ trans('most') }} {{ trans('reactions') }} </li>
                        @elseif (get_setting('react_like') == 'Like')
                            <li value="most_likes">{{ trans('most') }} {{ trans('likes') }} </li>
                        @endif
                            <li value="most_comments">{{ trans('most') }} {{ trans('comments') }} </li>
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

        <div class="discussions" id="discussions">

            @include('frontend.partials.posts')

        </div><!--/discussions-->
    @endif

@else

    <div class="filter mx-0">

        <form class="form" id="search_form">
            @csrf
            <div class="filter-toolbar">
                <input type="hidden" name="category_id" id="category_id" value="{{ $category->id }}">
                <div class="filter-item" id="dateSort">
                    <label>{{ trans('date') }}</label>
                    <a class="filter-item-content dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <input type="hidden" name="date" id="date" value="">
                        <span class="filter-value"></span>
                        <span class="dropdown-btn"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li value="all" class="selected">{{ trans('all') }} </li>
                        <li value="today">{{ trans('today') }}</li>
                        <li value="week">{{ trans('week') }} </li>
                        <li value="month">{{ trans('month') }} </li>
                        <li value="year">{{ trans('year') }} </li>
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
                        <li value="all" class="selected">{{ trans('all') }} </li>
                        <li value="solved">{{ trans('solved') }}</li>
                        <li value="pinned">{{ trans('pinned') }}</li>
                        <li value="closed">{{ trans('closed') }}</li>
                    @if (get_setting('react_like') == 'React')
                        <li value="most_reactions">{{ trans('most') }} {{ trans('reactions') }} </li>
                    @elseif (get_setting('react_like') == 'Like')
                        <li value="most_likes">{{ trans('most') }} {{ trans('likes') }} </li>
                    @endif
                        <li value="most_comments">{{ trans('most') }} {{ trans('comments') }} </li>
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

    <div class="discussions" id="discussions">

        @include('frontend.partials.posts')

    </div><!--/discussions-->

@endif

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

    //Date Default
    var date_text = $('#dateSort .dropdown-menu li.selected').text();
    var date_value = $('#dateSort .dropdown-menu li.selected').attr('value');
    $('#dateSort input').val(date_value);
    $('#dateSort .filter-value').html(date_text);

    //Sorting Default
    var sort_text = $('#sorting .dropdown-menu li.selected').text();
    var sort_value = $('#sorting .dropdown-menu li.selected').attr('value');
    $('#sorting input').val(sort_value);
    $('#sorting .filter-value').html(sort_text);

    $('#dateSort .dropdown-menu li').on('click', function() {
        var value = $(this).attr('value');
        var text = $(this).text();
        var item = $(this);
        item.closest('#dateSort').find('li.selected').removeClass('selected');
        $(this).addClass('selected');
        $('#dateSort').find('input').val(value);
        $('#dateSort').find('.filter-value').html(text);
        filterPosts();
    });

    $('#sorting .dropdown-menu li').on('click', function() {
        var value = $(this).attr('value');
        var text = $(this).text();
        var item = $(this);
        item.closest('#sorting').find('li.selected').removeClass('selected');
        $(this).addClass('selected');
        $('#sorting').find('input').val(value);
        $('#sorting').find('.filter-value').html(text);
        filterPosts();
    });

    //$('.filter-item .dropdown-menu li.selected').trigger('click');

    $(document).on('submit', '#search_form', function(e) {
        e.preventDefault();
        $("#search_posts_btn").text('{{ trans('searching') }}...');
        filterPosts();
    });

    function filterPosts() {

        let category_id = $('#category_id').val();
        let date = $('#date').val();
        let search_term = $('#search_term').val();
        let sort = $('#sort').val();

        let url = "{{ route('category.posts.sort') }}";
        $.ajax({
            type: "get",
            url: url,
            data: {
                'category_id': category_id,
                'date': date,
                'sort': sort,
                'search_term': search_term
            },
            success: function(response) {

                $("#search_posts_btn").text('{{ trans('search') }}');

                $('#discussions').html(response);
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
        let category_id = $('#category_id').val();
        let date = $('#date').val();
        let search_term = $('#search_term').val();
        let sort = $('#sort').val();

        fetchData(page, category_id, date, sort, search_term);
    });

    function fetchData(page, category_id, date, sort, search_term) {

        var category_id = category_id;
        var date = date;
        var sort = sort;
        var search_term = search_term;

        $.ajax({
            url: "{{ url('category/posts/pagination/?page=') }}" + page,
            data: {
                'category_id': category_id,
                'date': date,
                'sort': sort,
                'search_term': search_term
            },
            success: function(response) {
                $('#discussions').html(response);

                window.scroll({
                    top: 0, left: 0,
                    behavior: 'smooth'
                });
            }
        });
    }


</script>
@endsection
