@extends('layouts.admin')


@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">

      <div class="row mt-50">

       <div class="col-lg-3">

        <div class="card">
        <div class="card-body">
          <ul class="nav nav-pills flex-column" id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link {{ Route::is('admin.settings.site') ? 'active' : '' }}" href="{{ route('admin.settings.site') }}">
                <i class="align-middle me-1" data-feather="layers"></i> {{ trans('site_settings') }} </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('admin.settings.home') ? 'active' : '' }}" href="{{ route('admin.settings.home') }}">
                <i class="align-middle me-1" data-feather="home"></i> {{ trans('home_settings') }} </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('admin.settings.forum') ? 'active' : '' }}" href="{{ route('admin.settings.forum') }}">
                <i class="align-middle me-1" data-feather="message-square"></i> {{ trans('forum_settings') }} </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('admin.settings.points') ? 'active' : '' }}" href="{{ route('admin.settings.points') }}">
                <i class="align-middle me-1" data-feather="plus-square"></i> {{ trans('points_settings') }} </a>
            </li>
        @if(get_setting('payments_on_site') == 'Yes')
            <li class="nav-item">
              <a class="nav-link {{ Route::is('admin.settings.currency') ? 'active' : '' }}" href="{{ route('admin.settings.currency') }}">
                <i class="align-middle me-1" data-feather="dollar-sign"></i> {{ trans('currency_settings') }} </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('admin.settings.payments') ? 'active' : '' }}" href="{{ route('admin.settings.payments') }}">
                <i class="align-middle me-1" data-feather="credit-card"></i> {{ trans('payments_settings') }} </a>
            </li>
        @endif
            <li class="nav-item">
              <a class="nav-link {{ Route::is('admin.settings.ads') ? 'active' : '' }}" href="{{ route('admin.settings.ads') }}">
                <i class="align-middle me-1" data-feather="layers"></i> {{ trans('ads_settings') }} </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('admin.settings.analytics') ? 'active' : '' }}" href="{{ route('admin.settings.analytics') }}">
                <i class="align-middle me-1" data-feather="bar-chart-2"></i> {{ trans('analytics_settings') }} </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('admin.settings.adsense') ? 'active' : '' }}" href="{{ route('admin.settings.adsense') }}">
                <i class="align-middle me-1" data-feather="command"></i> {{ trans('adsense_settings') }} </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('admin.settings.storage') ? 'active' : '' }}" href="{{ route('admin.settings.storage') }}">
                <i class="align-middle me-1" data-feather="hard-drive"></i> {{ trans('storage_settings') }} </a>
            </li>
          </ul>
        </div>
        </div>


       </div>

       <div class="col-lg-9">

        @if(Route::is('admin.settings.site') )

            <div class="card-style settings-card-2 mb-30">
                <form action="{{ route('admin.settings.site') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('allow_payments_on_site') }}</label>
                                <div class="select-position">
                                    <select name="payments_on_site" class="light-bg">
                                        <option value="Yes" {{ get_setting('payments_on_site') == "Yes" ? ' selected' : '' }}>{{ trans('yes') }}</option>
                                        <option value="No" {{ get_setting('payments_on_site') == "No" ? ' selected' : '' }}>{{ trans('no') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>LTR or RTL</label>
                                <div class="select-position">
                                    <select name="site_direction" class="light-bg">
                                        <option value="ltr" {{ get_setting('site_direction') == "ltr" ? ' selected' : '' }}>LTR</option>
                                        <option value="rtl" {{ get_setting('site_direction') == "rtl" ? ' selected' : '' }}>RTL</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('dates_language') }}</label>
                                <input type="text" name="dates_language" value="{{ get_setting('dates_language') }}"/>
                                <p class="small">{{ trans('get_language_code_here') }} :- <span class="text-danger ms-2">https://laravel-lang.com/usage-list-of-locales.html#list_of_codes_with_names</span> </p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('enable_sending_of_emails') }}</label>
                                <div class="select-position">
                                    <select name="enable_emails" class="light-bg">
                                        <option value="Yes" {{ get_setting('enable_emails') == "Yes" ? ' selected' : '' }}>{{ trans('yes') }}</option>
                                        <option value="No" {{ get_setting('enable_emails') == "No" ? ' selected' : '' }}>{{ trans('no') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('site_name') }}</label>
                                <input type="text" name="site_name" value="{{ get_setting('site_name') }}" placeholder="Site Name" />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('site_title') }}</label>
                                <input type="text" name="site_title" value="{{ get_setting('site_title') }}" placeholder="Site Title" />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('site_description') }}</label>
                                <textarea type="text" name="site_description" class="form-control" rows="5">{{ get_setting('site_description') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('site_keywords') }}</label>
                                <textarea type="text" name="site_keywords" class="form-control" rows="5">{{ get_setting('site_keywords') }}</textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('contact_email') }}</label>
                                <input type="text" name="contact_email" value="{{ get_setting('contact_email') }}" placeholder="Contact Email Address" />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('contact_address') }}</label>
                                <input type="text" name="contact_address" value="{{ get_setting('contact_address') }}" placeholder="Contact Address" />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('contact_phone') }}</label>
                                <input type="text" name="contact_phone" value="{{ get_setting('contact_phone') }}" placeholder="Contact Phone" />
                            </div>
                        </div>
                        <hr>
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('timezone') }}</label>
                                <div class="select-position">
                                <select name="timezone" class="light-bg">
                                    <option value="Default" {{ get_setting('timezone') == "" ? ' selected' : '' }}>{{ trans('default') }}</option>
                                    @foreach(timezone_identifiers_list() as $value)
                                        <option value="{{ $value }}" {{ get_setting('timezone') == $value ? ' selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('cookie_consent') }}</label>
                                <textarea type="text" name="cookie_consent" class="form-control" rows="5">{{ get_setting('cookie_consent') }}</textarea>
                            </div>
                        </div>

                        <div class="alert alert-warning d-none" id="validationErrorsBox"></div>

                        <div class="col-12 mb-5 d-flex justify-content-left" io-image-input="true">


                            <div class="photo me-5">
                                <label for="Logo" class="form-label">{{ trans('logo') }}</label>
                                <div class="d-block">
                                    <div class="image-picker">
                                        <img id='logo_dark_preview' class="image previewImage" src="{{ get_setting('logo_dark') != "" ? my_asset('uploads/settings/'.get_setting('logo_dark')) : my_asset('backend/img/default.jpg') }}">
                                        <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip" data-placement="top" data-bs-original-title="Change app logo">
                                            <label>
                                                <svg class="svg-inline--fa fa-pen" id="profileImageIcon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pen" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M362.7 19.32C387.7-5.678 428.3-5.678 453.3 19.32L492.7 58.75C517.7 83.74 517.7 124.3 492.7 149.3L444.3 197.7L314.3 67.72L362.7 19.32zM421.7 220.3L188.5 453.4C178.1 463.8 165.2 471.5 151.1 475.6L30.77 511C22.35 513.5 13.24 511.2 7.03 504.1C.8198 498.8-1.502 489.7 .976 481.2L36.37 360.9C40.53 346.8 48.16 333.9 58.57 323.5L291.7 90.34L421.7 220.3z"></path></svg><!-- <i class="fa-solid fa-pen" id="profileImageIcon"></i> Font Awesome fontawesome.com -->
                                                <input id="logo_dark" class="image-upload d-none" accept=".png, .jpg, .jpeg" name="logo_dark" type="file">
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="photo me-5">
                                <label for="Favicon" class="form-label">{{ trans('favicon') }}</label>
                                <div class="d-block">
                                    <div class="image-picker">
                                        <img id='favicon_preview' class="image previewImage" src="{{ get_setting('favicon') != "" ? my_asset('uploads/settings/'.get_setting('favicon')) : my_asset('backend/img/default.jpg') }}">
                                        <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip" data-placement="top" data-bs-original-title="Change app logo">
                                            <label>
                                                <svg class="svg-inline--fa fa-pen" id="profileImageIcon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pen" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M362.7 19.32C387.7-5.678 428.3-5.678 453.3 19.32L492.7 58.75C517.7 83.74 517.7 124.3 492.7 149.3L444.3 197.7L314.3 67.72L362.7 19.32zM421.7 220.3L188.5 453.4C178.1 463.8 165.2 471.5 151.1 475.6L30.77 511C22.35 513.5 13.24 511.2 7.03 504.1C.8198 498.8-1.502 489.7 .976 481.2L36.37 360.9C40.53 346.8 48.16 333.9 58.57 323.5L291.7 90.34L421.7 220.3z"></path></svg><!-- <i class="fa-solid fa-pen" id="profileImageIcon"></i> Font Awesome fontawesome.com -->
                                                <input id="favicon" class="image-upload d-none" accept=".png, .jpg, .jpeg" name="favicon" type="file">
                                            </label>
                                        </span>
                                    </div>
                                </div>
                           </div>

                        </div>

                        <div class="col-12 mb-5">

                            <div class="photo me-5">
                                <label for="Login Bg" class="form-label">{{ trans('login_image') }}</label>
                                <div class="d-block">
                                    <div class="image-picker login-bg">
                                        <img id='login_bg_preview' class="image previewImage" src="{{ my_asset('uploads/settings/'.get_setting('login_bg')) }}">
                                        <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip" data-placement="top" data-bs-original-title="Change app logo">
                                            <label>
                                                <svg class="svg-inline--fa fa-pen" id="profileImageIcon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pen" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M362.7 19.32C387.7-5.678 428.3-5.678 453.3 19.32L492.7 58.75C517.7 83.74 517.7 124.3 492.7 149.3L444.3 197.7L314.3 67.72L362.7 19.32zM421.7 220.3L188.5 453.4C178.1 463.8 165.2 471.5 151.1 475.6L30.77 511C22.35 513.5 13.24 511.2 7.03 504.1C.8198 498.8-1.502 489.7 .976 481.2L36.37 360.9C40.53 346.8 48.16 333.9 58.57 323.5L291.7 90.34L421.7 220.3z"></path></svg><!-- <i class="fa-solid fa-pen" id="profileImageIcon"></i> Font Awesome fontawesome.com -->
                                                <input id="login_bg" class="image-upload d-none" accept=".png, .jpg, .jpeg" name="login_bg" type="file">
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                        </div>
                    </div>

                </form>
            </div>

        @elseif(Route::is('admin.settings.home') )

            <div class="card-style settings-card-2 mb-30">
                <form action="{{ route('admin.settings.home') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('title') }}</label>
                                <input type="text" name="home_title" value="{{ get_setting('home_title') }}" placeholder="Title" />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('sub_title') }}</label>
                                <input type="text" name="home_sub_title" value="{{ get_setting('home_sub_title') }}" placeholder="Sub Title" />
                            </div>
                        </div>

                        <div class="alert alert-warning d-none" id="validationErrorsBox"></div>

                        <div class="col-12 mb-5">

                            <div class="photo me-5">
                                <label for="Home Bg" class="form-label">{{ trans('home_image') }}</label>
                                <div class="d-block">
                                    <div class="image-picker login-bg">
                                        <img id='home_bg_preview' class="image previewImage" src="{{ my_asset('uploads/settings/'.get_setting('home_bg')) }}">
                                        <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip" data-placement="top" data-bs-original-title="Change app logo">
                                            <label>
                                                <svg class="svg-inline--fa fa-pen" id="profileImageIcon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pen" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M362.7 19.32C387.7-5.678 428.3-5.678 453.3 19.32L492.7 58.75C517.7 83.74 517.7 124.3 492.7 149.3L444.3 197.7L314.3 67.72L362.7 19.32zM421.7 220.3L188.5 453.4C178.1 463.8 165.2 471.5 151.1 475.6L30.77 511C22.35 513.5 13.24 511.2 7.03 504.1C.8198 498.8-1.502 489.7 .976 481.2L36.37 360.9C40.53 346.8 48.16 333.9 58.57 323.5L291.7 90.34L421.7 220.3z"></path></svg><!-- <i class="fa-solid fa-pen" id="profileImageIcon"></i> Font Awesome fontawesome.com -->
                                                <input id="home_bg" class="image-upload d-none" accept=".png, .jpg, .jpeg" name="home_bg" type="file">
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                        </div>
                    </div>

                </form>
            </div>

        @elseif(Route::is('admin.settings.forum') )

            <div class="card-style settings-card-2 mb-30">
                <form action="{{ route('admin.settings.forum') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('pause_new_posts') }}</label>
                                <div class="select-position">
                                    <select name="pause_new_posts" class="light-bg">
                                        <option value="Active" {{ get_setting('pause_new_posts') == "Active" ? ' selected' : '' }}>{{ trans('active') }}</option>
                                        <option value="Pause" {{ get_setting('pause_new_posts') == "Pause" ? ' selected' : '' }}>{{ trans('pause') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('react_or_like_on_posts') }}</label>
                                <div class="select-position">
                                    <select name="react_like" class="light-bg">
                                        <option value="React" {{ get_setting('react_like') == "React" ? ' selected' : '' }}>{{ trans('react') }}</option>
                                        <option value="Like" {{ get_setting('react_like') == "Like" ? ' selected' : '' }}>{{ trans('like') }}</option>
                                    </select>
                                </div>
                                <p class="small">{{ trans('choose_whether_users_to_react_with_emojis_or_like_on_posts') }}.</p>
                            </div>
                        </div>
                        <hr>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('results_per_page') }}</label>
                                <input type="number" name="results_per_page" value="{{ get_setting('results_per_page') }}" />
                                <p class="small">{{ trans('how_many_results_per_page_you_want_to_show') }}.</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('minimum_characters') }}</label>
                                <input type="number" name="minimum_characters" value="{{ get_setting('minimum_characters') }}" />
                                <p class="small">{{ trans('how_many_minimum_characters_are_needed_for_each_post') }}.</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('maximum_characters') }}</label>
                                <input type="number" name="maximum_characters" value="{{ get_setting('maximum_characters') }}" />
                                <p class="small">{{ trans('how_many_maximum_characters_are_needed_for_each_post') }}.</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('maximum_preview_characters') }}</label>
                                <input type="number" name="maximum_preview_chars" value="{{ get_setting('maximum_preview_chars') }}" />
                                <p class="small">{{ trans('how_many_characters_to_show_for_preview_of_each_post') }}.</p>
                            </div>
                        </div>
                        <hr>
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('new_posts') }}</label>
                                <div class="select-position">
                                    <select name="new_posts" class="light-bg">
                                        <option value="Visible" {{ get_setting('new_posts') == "Visible" ? ' selected' : '' }}>{{ trans('immediately_visible') }}</option>
                                        <option value="Moderation" {{ get_setting('new_posts') == "Moderation" ? ' selected' : '' }}>{{ trans('in_moderation') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('new_comments') }}</label>
                                <div class="select-position">
                                    <select name="new_comments" class="light-bg">
                                        <option value="Visible" {{ get_setting('new_comments') == "Visible" ? ' selected' : '' }}>{{ trans('immediately_visible') }}</option>
                                        <option value="Moderation" {{ get_setting('new_comments') == "Moderation" ? ' selected' : '' }}>{{ trans('in_moderation') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('new_replies') }}</label>
                                <div class="select-position">
                                    <select name="new_replies" class="light-bg">
                                        <option value="Visible" {{ get_setting('new_replies') == "Visible" ? ' selected' : '' }}>{{ trans('immediately_visible') }}</option>
                                        <option value="Moderation" {{ get_setting('new_replies') == "Moderation" ? ' selected' : '' }}>{{ trans('in_moderation') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('censor_words') }}</label>
                                <div class="select-position">
                                    <select name="word_censored" class="light-bg">
                                        <option value="1" {{ get_setting('word_censored') == "1" ? ' selected' : '' }}>Enable</option>
                                        <option value="0" {{ get_setting('word_censored') == "0" ? ' selected' : '' }}>Disable</option>
                                    </select>
                                </div>
                                <p class="small">{{ trans('add_censored_words_in_the_file') }} <span class="text-danger">path: vendor\snipe\banbuilder\src\dict\dictionary.php</span> </p>
                            </div>
                        </div>
                        <hr>
                        <h4 class="mb-3">{{ trans('widgets') }}</h4>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('categories_widget') }}</label>
                                <input type="number" name="categories_widget" value="{{ get_setting('categories_widget') }}" />
                                <p class="small">{{ trans('how_many_items_to_be_shown') }}.</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('trending_posts_widget') }}</label>
                                <input type="number" name="trending_posts_widget" value="{{ get_setting('trending_posts_widget') }}" />
                                <p class="small">{{ trans('how_many_items_to_be_shown') }}.</p>
                            </div>
                        </div>



                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                        </div>
                    </div><!--/row-->

                </form>
            </div>

        @elseif(Route::is('admin.settings.points') )

            <div class="card-style settings-card-2 mb-30">
                <form action="{{ route('admin.settings.points') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('points_system') }}</label>
                                <div class="select-position">
                                    <select name="points_system" class="light-bg">
                                        <option value="1" {{ get_setting('points_system') == "1" ? ' selected' : '' }}>{{ trans('active') }}</option>
                                        <option value="0" {{ get_setting('points_system') == "0" ? ' selected' : '' }}>{{ trans('pause') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h4 class="mb-3">{{ trans('scores') }}</h4>
                        <div class="col-lg-6">
                            <div class="input-style-1">
                                <label>{{ trans('new_posts') }}</label>
                                <input type="number" name="new_posts_no" value="{{ get_setting('new_posts_no') }}" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-style-1">
                                <label>{{ trans('like') }}</label>
                                <input type="number" name="like" value="{{ get_setting('like') }}" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-style-1">
                                <label>{{ trans('comment') }}</label>
                                <input type="number" name="comment" value="{{ get_setting('comment') }}" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-style-1">
                                <label>{{ trans('reply') }}</label>
                                <input type="number" name="reply" value="{{ get_setting('reply') }}" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-style-1">
                                <label>{{ trans('reaction') }}</label>
                                <input type="number" name="reaction" value="{{ get_setting('reaction') }}" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-style-1">
                                <label>{{ trans('share') }}</label>
                                <input type="number" name="share" value="{{ get_setting('share') }}" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-style-1">
                                <label>{{ trans('registration') }}</label>
                                <input type="number" name="registration" value="{{ get_setting('registration') }}" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-style-1">
                                <label>{{ trans('login') }}</label>
                                <input type="number" name="login" value="{{ get_setting('login') }}" />
                            </div>
                        </div>



                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                        </div>
                    </div><!--/row-->

                </form>
            </div>

        @elseif(Route::is('admin.settings.currency') )

            <div class="card-style settings-card-2 mb-30">
                <form action="{{ route('admin.settings.currency') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('currency_code') }}</label>
                                <input type="text" name="currency_code" value="{{ get_setting('currency_code') }}" placeholder="{{ trans('currency_code') }} e.g USD" />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('currency_symbol') }}</label>
                                <input type="text" name="currency_symbol" value="{{ get_setting('currency_symbol') }}" placeholder="{{ trans('currency_symbol') }} e.g $" />
                            </div>
                        </div>
                        <div class="col-12">
                         <div class="select-style-1">
                            <label>{{ trans('currency_position') }}</label>
                            <div class="select-position">
                                <select name="currency_position" class="light-bg">
                                    <option @if (get_setting('currency_position') == 'left') selected="selected" @endif value="left">{{get_setting('currency_symbol')}}99 - {{ trans('left') }}</option>
                                    <option @if (get_setting('currency_position') == 'left_space') selected="selected" @endif value="left_space">{{get_setting('currency_symbol')}} 99 - {{ trans('left_with_space') }}</option>
                                    <option @if (get_setting('currency_position') == 'right') selected="selected" @endif value="right">99{{get_setting('currency_symbol')}} - {{ trans('right') }}</option>
                                    <option @if (get_setting('currency_position') == 'right_space') selected="selected" @endif value="right_space">99 {{get_setting('currency_symbol')}} - {{ trans('right_with_space') }}</option>
                                </select>
                            </div>
                          </div>
                        </div>

                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                        </div>
                    </div>

                </form>
            </div>

        @elseif(Route::is('admin.settings.payments') )

            <div class="card-style settings-card-2 mb-30">
                <form action="{{ route('admin.settings.payments') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('minimum_deposit') }}</label>
                                <input value="{{ get_setting('min_deposit') }}" name="min_deposit" type="number" min="1" autocomplete="off" class="form-control onlyNumber">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('minimum_tip') }}</label>
                                <input value="{{ get_setting('min_tip') }}" name="min_tip" type="number" min="1" autocomplete="off" class="form-control onlyNumber">
                            </div>
                        </div>
                        <div class="col-12">
                          <div class="select-style-1">
                            <label>{{ trans('commissions_on_tips') }}</label>
                            <div class="select-position">
                                <select name="tips_commission" class="light-bg">
                                    @for ($i=1; $i <= 95; ++$i)
                                      <option @if (get_setting('tips_commission') == $i) selected="selected" @endif value="{{$i}}">{{$i}}%</option>
                                    @endfor
                                </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('minimum_withdrawal') }}</label>
                                <input value="{{ get_setting('min_withdraw') }}" name="min_withdraw" type="number" min="1" autocomplete="off" class="form-control onlyNumber">
                            </div>
                        </div>
                        <div class="col-12">
                          <div class="select-style-1">
                            <label>{{ trans('days_to_process_withdrawals') }}</label>
                            <div class="select-position">
                                <select name="days_withdraw" class="light-bg">
                                    @for ($i=1; $i <= 25; ++$i)
                                      <option @if (get_setting('days_withdraw') == $i) selected="selected" @endif value="{{$i}}">{{$i}} {{ trans('days') }}</option>
                                    @endfor
                                </select>
                            </div>
                          </div>
                        </div>

                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                        </div>
                    </div>

                </form>
            </div>

        @elseif(Route::is('admin.settings.ads') )

            <div class="card-style settings-card-2 mb-30">
                <form action="{{ route('admin.settings.ads') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="select-style-1">
                                <label>{{ trans('ads') }}</label>
                                <div class="select-position">
                                    <select name="ads" class="light-bg">
                                        <option value="1" {{ get_setting('ads') == "1" ? ' selected' : '' }}>{{ trans('active') }}</option>
                                        <option value="0" {{ get_setting('ads') == "0" ? ' selected' : '' }}>{{ trans('pause') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('top_ad') }}</label>
                                <textarea name="top_ad" rows="4">{{ get_setting('top_ad') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('footer_ad') }}</label>
                                <textarea name="footer_ad" rows="4">{{ get_setting('footer_ad') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('sidebar_ad') }}</label>
                                <textarea name="sidebar_ad" rows="5">{{ get_setting('sidebar_ad') }}</textarea>
                            </div>
                        </div>

                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                        </div>
                    </div>

                </form>
            </div>

        @elseif(Route::is('admin.settings.analytics') )

            <div class="card-style settings-card-2 mb-30">
                <form action="{{ route('admin.settings.analytics') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('analytics') }}</label>
                                <textarea name="analytics" rows="4">{{ get_setting('analytics') }}</textarea>
                            </div>
                        </div>

                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                        </div>
                    </div>

                </form>
            </div>

        @elseif(Route::is('admin.settings.adsense') )

            <div class="card-style settings-card-2 mb-30">
                <form action="{{ route('admin.settings.adsense') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="input-style-1">
                                <label>{{ trans('adsense') }}</label>
                                <textarea name="adsense" rows="4">{{ get_setting('adsense') }}</textarea>
                            </div>
                        </div>

                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                        </div>
                    </div>

                </form>
            </div>

        @elseif(Route::is('admin.settings.storage') )

            @include('admin.settings.storage')

        @endif

    </div><!-- col-lg-9 -->

</div><!-- row -->
</div><!-- container -->
</section>

</main>

@endsection


@section('scripts')

<script>
$(document).on('change', '#logo_light', function () {
  if (isValidFile($(this), '#validationErrorsBox')) {
    displayPhoto(this, '#logo_light_preview');
  }
});

$(document).on('change', '#logo_dark', function () {
  if (isValidFile($(this), '#validationErrorsBox')) {
    displayPhoto(this, '#logo_dark_preview');
  }
});

$(document).on('change', '#favicon', function () {
  if (isValidFile($(this), '#validationErrorsBox')) {
    displayPhoto(this, '#favicon_preview');
  }
});

$(document).on('change', '#login_bg', function () {
  if (isValidFile($(this), '#validationErrorsBox')) {
    displayPhoto(this, '#login_bg_preview');
  }
});

$(document).on('change', '#home_bg', function () {
  if (isValidFile($(this), '#validationErrorsBox')) {
    displayPhoto(this, '#home_bg_preview');
  }
});

</script>

@endsection
