@extends('layouts.user')

@section('content')



<h4 class="mb-4" data-aos="fade-down" data-aos-easing="linear"><i class="bi bi-gear me-2"></i>{{ trans('settings') }}</h4>
<div class="row g-4" data-aos="fade-up" data-aos-easing="linear">
    <div class="col-12">
        <div class="vine-tabs pb-0 px-2 px-lg-0 rounded-top">
            <ul class="nav nav-tabs nav-bottom-line nav-responsive border-0 nav-justified" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link mb-0 {{ Route::is('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}">
                        <i class="bi bi-gear fa-fw me-2"></i>{{ trans('edit_profile') }}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link mb-0 {{ Route::is('user.password') ? 'active' : '' }}" href="{{ route('user.password') }}">
                        <i class="bi bi-lock me-2"></i> {{ trans('security_settings') }}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link mb-0 {{ Route::is('user.email.notifications') ? 'active' : '' }}" href="{{ route('user.email.notifications') }}">
                        <i class="bi bi-bell me-2"></i> {{ trans('email_notifications') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="mt-5">

          @if(Route::is('user.profile') )
            <div data-aos="fade-up" data-aos-easing="linear">
                <div class="dashboard-card">
                    <div class="dashboard-header">
                        <h4>{{ trans('profile_details') }}</h4>
                    </div>
                    <div class="dashboard-body">


                        <form id="user_profile_form" method="POST">
                            @csrf

                            <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
                            <input type="hidden" name="old_image" id="old_image" value="{{ Auth::user()->image }}">

                            <div class="col-lg-12 mb-5 d-flex justify-content-left" io-image-input="true">

                            <div class="photo me-5">
                                <div class="d-block">
                                    <div class="image-picker">
                                        <img id='image_preview' class="image previewImage" src="{{ my_asset('uploads/users/'. Auth::user()->image) }}">
                                        <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip" data-placement="top" data-bs-original-title="Change Image">
                                            <label>
                                                <i class="bi bi-pencil"></i>

                                                <input id="image" class="image-upload d-none" accept=".png, .jpg, .jpeg" name="image" type="file">
                                            </label>
                                            <div class="invalid-feedback"></div>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            </div>

                            <div class="row g-3">
                              <div class="col-sm-12">
                                  <label class="form-label">{{ trans('name') }}*</label>
                                  <input type="text" name="name" id="name" value="{{ Auth::user()->name }}" placeholder="{{ trans('name') }}">
                                  <div class="invalid-feedback"></div>
                              </div>
                              <div class="col-sm-12">
                                  <label class="form-label">{{ trans('email') }}*</label>
                                  <input type="text" name="email" id="email" value="{{ Auth::user()->email }}" placeholder="{{ trans('email') }}">
                                  <div class="invalid-feedback"></div>
                              </div>
                              <div class="col-sm-12">
                                  <label>{{ trans('username') }}*</label>
                                  <input type="text" name="username" id="username" value="{{ Auth::user()->username }}" placeholder="{{ trans('username') }}">
                                  <div class="invalid-feedback"></div>
                              </div>
                              <div class="col-sm-6">
                                  <label>{{ trans('profession') }}</label>
                                  <input name="profession" id="profession" type="text" value="{{ Auth::user()->profession }}" placeholder="Eg. Web Developer">
                                  <div class="invalid-feedback"></div>
                              </div>
                              <div class="col-sm-6">
                                  <label>{{ trans('gender') }}</label>
                                  <select name="gender">
                                     <option value="Male" {{ Auth::user()->gender == 'Male' ? 'selected="selected"' : '' }}>{{ trans('male') }}</option>
                                     <option value="Female" {{ Auth::user()->gender == 'Female' ? 'selected="selected"' : '' }}>{{ trans('female') }}</option>
                                  </select>
                                  <div class="invalid-feedback"></div>
                              </div>
                              <div class="col-sm-12">
                                  <label class="form-label">{{ trans('bio') }}</label>
                                  <textarea name="bio" id="bio" rows="5" placeholder="Bio">{{ Auth::user()->bio }}</textarea>
                                  <div class="invalid-feedback"></div>
                              </div>
                              <div class="col-sm-6">
                                  <label>{{ trans('location') }}</label>
                                  <input name="location" id="location" type="text" value="{{ Auth::user()->location }}" placeholder="Ex.San Francisco, CA">
                                  <div class="invalid-feedback"></div>
                              </div>
                              <div class="col-sm-6">
                                  <label>{{ trans('country') }}</label>
                                    <select name="country" id="country">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->code }}" {{ Auth::user()->country == $country->code ? 'selected="selected"' : '' }}>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                <div class="invalid-feedback"></div>
                              </div>

                              <div class="col-12">
                                  <label class="form-label">{{ trans('your_website') }}</label>
                                  <input type="text" name="website" id="website" value="{{ Auth::user()->website }}" placeholder="{{ trans('your_website') }}">
                                  <div class="invalid-feedback"></div>
                              </div>
                              <div class="col-12">
                                  <label class="form-label">Twitter</label>
                                  <div class="d-flex justify-content-center">
                                      <span class="input-group-text border-0 bg-input" id="basic-addon1_"><i class="bi-twitter"></i></span>
                                      <input type="text" name="twitter" id="twitter" value="{{ Auth::user()->twitter }}" placeholder="Twitter Username">
                                      <div class="invalid-feedback"></div>
                                  </div>
                              </div>
                              <div class="col-12">
                                  <label class="form-label">Facebook</label>
                                  <div class="d-flex justify-content-center">
                                      <span class="input-group-text border-0 bg-input" id="basic-addon1_01"><i class="bi-facebook"></i></span>
                                      <input type="text" name="facebook" id="facebook" value="{{ Auth::user()->facebook }}" placeholder="Facebook Username">
                                      <div class="invalid-feedback"></div>
                                  </div>
                              </div>
                              <div class="col-12"><label class="form-label">Instagram</label>
                                  <div class="d-flex justify-content-center">
                                      <span class="input-group-text border-0 bg-input" id="basic-addon1_02"><i class="bi-instagram"></i></span>
                                      <input type="text" name="instagram" id="instagram" value="{{ Auth::user()->instagram }}" placeholder="Instagram Username">
                                      <div class="invalid-feedback"></div>
                                  </div>
                              </div>
                              <div class="col-12">
                                  <label class="form-label">Linkedin</label>
                                  <div class="d-flex justify-content-center">
                                      <span class="input-group-text border-0 bg-input" id="basic-addon1_02"><i class="bi-linkedin"></i></span>
                                      <input type="text" name="linkedin" id="linkedin" value="{{ Auth::user()->linkedin }}" placeholder="Linkedin Username">
                                      <div class="invalid-feedback"></div>
                                  </div>
                              </div>
                            </div>
                            <div class="d-flex pt-5">
                              <button type="submit" id="user_profile_btn" class="btn btn-mint me-3">{{ trans('save_changes') }}</button>
                            </div>
                        </form>

                    </div>
                </div><!--/dashboard-card-->
            </div><!-- Tab content 1 END -->
          @elseif(Route::is('user.password') )
            <div data-aos="fade-up" data-aos-easing="linear">

                <div class="dashboard-card">
                    <div class="dashboard-header">
                        <h4>{{ trans('change_password') }}</h4>
                    </div>
                    <div class="dashboard-body">

                        <!-- Password -->
                        <form id="user_password_form" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12 mb-4">
                                    <label class="form-label fs-base">{{ trans('current_password') }}</label>
                                    <div class="password-toggle">
                                        <input type="password" name="current_password" id="current_password" placeholder="{{ trans('current_password') }}">
                                        <label class="password-toggle-btn" aria-label="Show/hide password">
                                            <input class="password-toggle-check" id="togglePassword-1" type="checkbox">
                                            <span class="password-toggle-indicator"></span>
                                        </label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-4">
                                    <label class="form-label fs-base">{{ trans('new_password') }}</label>
                                    <div class="password-toggle">
                                        <input type="password" name="new_password" id="new_password" placeholder="{{ trans('new_password') }}">
                                        <label class="password-toggle-btn" aria-label="Show/hide password">
                                            <input class="password-toggle-check" id="togglePassword-2" type="checkbox">
                                            <span class="password-toggle-indicator"></span>
                                        </label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-4">
                                    <label class="form-label fs-base">{{ trans('confirm_new_password') }}</label>
                                    <div class="password-toggle">
                                        <input type="password" name="confirm_password" id="confirm_password" placeholder="{{ trans('confirm_new_password') }}">
                                        <label class="password-toggle-btn" aria-label="Show/hide password">
                                            <input class="password-toggle-check" id="togglePassword-3" type="checkbox">
                                            <span class="password-toggle-indicator"></span>
                                        </label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex mb-3 pt-2">
                            <button type="submit" id="user_password_btn" class="btn btn-mint me-3">{{ trans('save_changes') }}</button>
                            </div>
                        </form>

                    </div>
                </div><!--/dashboard-card-->
            </div><!-- Tab content 3 END -->
            @elseif(Route::is('user.email.notifications') )
              <div data-aos="fade-up" data-aos-easing="linear">

                  <div class="dashboard-card">
                      <div class="dashboard-header">
                          <h4>{{ trans('email_notifications') }}</h4>
                      </div>
                      <div class="dashboard-body">

                          <form id="user_email_form" method="POST">
                              @csrf

                              <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
                              <div class="row align-items-end pb-3 mb-2 mb-sm-4">
                                <div class="col-lg-6 col-sm-7">
                                  <label class="form-label fs-base">{{ add('your_email') }}</label>
                                  <input type="text" name="email" value="{{ Auth::user()->email }}" readonly>
                                </div>
                              </div>

                              <div class="form-group">
                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="email_comment" class="custom-control-input" value="1" id="email_comment" {{ Auth::user()->email_comment == '1' ? 'checked' : '' }}>
                                      <label class="custom-control-label" for="email_comment">{{ trans('new_comment_on_your_post') }}</label>
                                      <p class="text-muted mb-2"></p>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="email_reply" class="custom-control-input" value="1" id="email_reply" {{ Auth::user()->email_reply == '1' ? 'checked' : '' }}>
                                      <label class="custom-control-label" for="email_reply">{{ trans('new_reply_on_your_comment') }}</label>
                                      <p class="text-muted mb-2"></p>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="email_follower" class="custom-control-input" value="1" id="email_follower" {{ Auth::user()->email_follower == '1' ? 'checked' : '' }}>
                                      <label class="custom-control-label" for="email_follower">{{ trans('new_follower') }}</label>
                                      <p class="text-muted mb-2"></p>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="email_message" class="custom-control-input" value="1" id="email_message" {{ Auth::user()->email_message == '1' ? 'checked' : '' }}>
                                      <label class="custom-control-label" for="email_message">{{ trans('new_message') }}</label>
                                      <p class="text-muted mb-2"></p>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="email_tip" class="custom-control-input" value="1" id="email_tip" {{ Auth::user()->email_tip == '1' ? 'checked' : '' }}>
                                      <label class="custom-control-label" for="email_tip">{{ trans('received_a_tip') }}</label>
                                      <p class="text-muted mb-2"></p>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="email_funds" class="custom-control-input" value="1" id="email_funds" {{ Auth::user()->email_funds == '1' ? 'checked' : '' }}>
                                      <label class="custom-control-label" for="email_funds">{{ trans('added_funds') }}</label>
                                      <p class="text-muted mb-2"></p>
                                  </div>
                              </div>



                              <div class="d-flex mb-3 pt-2">
                              <button type="submit" id="user_email_btn" class="btn btn-mint me-3">{{ trans('save_changes') }}</button>
                              </div>
                          </form>

                      </div>
                  </div><!--/dashboard-card-->
              </div><!-- Tab content 3 END -->
          @endif

        </div>
    </div>
</div>

@endsection


@section('scripts')

<script>

// Toggle Passwords

    $('#togglePassword-1').on('click', function(){
      var passInput=$("#current_password");
      if(passInput.attr('type')==='password')
        {
          passInput.attr('type','text');
      }else{
         passInput.attr('type','password');
      }
    });

    $('#togglePassword-2').on('click', function(){
      var passInput=$("#new_password");
      if(passInput.attr('type')==='password')
        {
          passInput.attr('type','text');
      }else{
         passInput.attr('type','password');
      }
    });

    $('#togglePassword-3').on('click', function(){
    var passInput=$("#confirm_password");
    if(passInput.attr('type')==='password')
        {
        passInput.attr('type','text');
    }else{
        passInput.attr('type','password');
    }
    });


// Image Change
   $(document).on('change', '#image', function () {
     if (isValidFile($(this), '#validationErrorsBox')) {
       displayPhoto(this, '#image_preview');
     }
   });

    $(function() {

       // update user ajax request
       $(document).on('submit', '#user_profile_form', function(e) {
           e.preventDefault();
           start_load();
           const fd = new FormData(this);
           $("#user_profile_btn").text('{{ trans('updating') }}...');
           $.ajax({
               method: 'POST',
               url: '{{ route('user.profile') }}',
               data: fd,
               cache: false,
               contentType: false,
               processData: false,
               dataType: 'json',
               success: function(response) {
                   end_load();

                   if (response.status == 400) {

                       showError('name', response.messages.name);
                       showError('email', response.messages.email);
                       showError('username', response.messages.username);
                       showError('image', response.messages.image);
                       $("#user_profile_btn").text('{{ trans('save_changes') }}');

                   }else if (response.status == 200) {

                       tata.success("Success", response.messages, {
                       position: 'tr',
                       duration: 3000,
                       animate: 'slide'
                       });

                       removeValidationClasses("#user_profile_form");
                       $("#user_profile_form")[0].reset();
                       window.location.reload();

                   }else if(response.status == 401){

                       tata.error("Error", response.messages, {
                       position: 'tr',
                       duration: 3000,
                       animate: 'slide'
                       });

                       $("#user_profile_form")[0].reset();
                       window.location.reload();

                   }

               }
           });
       });

       // password ajax request
       $(document).on('submit', '#user_password_form', function(e) {
           e.preventDefault();
           start_load();
           const fd = new FormData(this);
           $("#user_password_btn").text('{{ trans('updating') }}...');
           $.ajax({
               method: 'POST',
               url: '{{ route('user.password') }}',
               data: fd,
               cache: false,
               contentType: false,
               processData: false,
               dataType: 'json',
               success: function(response) {
                   end_load();

                   if (response.status == 400) {

                       showError('current_password', response.messages.current_password);
                       showError('new_password', response.messages.new_password);
                       showError('confirm_password', response.messages.confirm_password);
                       $("#user_password_btn").text('{{ trans('save_changes') }}');

                   }else if (response.status == 200) {

                       tata.success("Success", response.messages, {
                       position: 'tr',
                       duration: 3000,
                       animate: 'slide'
                       });

                       removeValidationClasses("#user_password_form");
                       $("#user_password_form")[0].reset();
                       window.location.reload();

                   }else if(response.status == 401){

                       tata.error("Error", response.messages, {
                       position: 'tr',
                       duration: 3000,
                       animate: 'slide'
                       });

                       $("#user_password_form")[0].reset();
                       window.location.reload();

                   }

               }
           });
       });

       // email ajax request
       $(document).on('submit', '#user_email_form', function(e) {
           e.preventDefault();
           start_load();
           const fd = new FormData(this);
           $("#user_email_btn").text('{{ trans('updating') }}...');
           $.ajax({
               method: 'POST',
               url: '{{ route('user.email.notifications') }}',
               data: fd,
               cache: false,
               contentType: false,
               processData: false,
               dataType: 'json',
               success: function(response) {
                   end_load();

                   if (response.status == 400) {

                       $("#user_password_btn").text('{{ trans('save_changes') }}');

                   }else if (response.status == 200) {

                       tata.success("Success", response.messages, {
                       position: 'tr',
                       duration: 3000,
                       animate: 'slide'
                       });

                       removeValidationClasses("#user_email_form");
                       $("#user_email_form")[0].reset();
                       window.location.reload();

                   }else if(response.status == 401){

                       tata.error("Error", response.messages, {
                       position: 'tr',
                       duration: 3000,
                       animate: 'slide'
                       });

                       $("#user_email_form")[0].reset();
                       window.location.reload();

                   }

               }
           });
       });

   });
</script>

@endsection
