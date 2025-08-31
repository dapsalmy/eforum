@extends('layouts.admin')

@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">
      <div class="row mt-50">

        <div class="col-12">
            @if(Route::is('admin.plans.list'))
                <div class="card">
                    <div class="card-header">

                        <div class="d-md-flex justify-content-between mb-10">
                            <h5 class="h4 mb-3">{{ trans('plans') }}</h5>
                            <div>
                                <a href="{{ route('admin.plans.add') }}" class="btn btn-success mb-0">+ {{ trans('add') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @forelse($plans as $plan)
                        <div class="col-lg-4 mb-5">
                            <div class="price">
                                <div class="price-title">
                                    <h4>{{ $plan->name }}</h4>
                                    <h2><span><b>{{ get_setting('currency_symbol') }}{{ $plan->price }}</b>/{{ $plan->duration }}</span></h3>
                                </div>
                                <div class="price-list">
                                    <ul>
                                        <li><i class="bi bi-check2"></i> {{ $plan->points }} {{ trans('points_topup') }}</li>
                                        <li class="{{ $plan->verified == '0' ? 'price-disable' : ''}}">
                                            <i class="bi {{ $plan->verified == '1' ? 'bi-check2' : 'bi-x'}} "></i> {{ trans('verified_checkmark') }}
                                        </li>
                                        <li class="{{ $plan->categories == '0' ? 'price-disable' : ''}}">
                                            <i class="bi {{ $plan->categories == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('access_to_pro_categories') }}
                                        </li>
                                        <li class="{{ $plan->tips == '0' ? 'price-disable' : ''}}">
                                            <i class="bi {{ $plan->tips == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('access_to_tips') }}
                                        </li>
                                        <li class="{{ $plan->comments == '0' ? 'price-disable' : ''}}">
                                            <i class="bi {{ $plan->comments == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('allow_or_close_comments_on_posts') }}
                                        </li>
                                        <li class="{{ $plan->reactions == '0' ? 'price-disable' : ''}}">
                                            <i class="bi {{ $plan->reactions == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('show_or_hide_reactions_or_likes_on_posts') }}
                                        </li>
                                        <li class="{{ $plan->followers == '0' ? 'price-disable' : ''}}">
                                            <i class="bi {{ $plan->followers == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('allow_user_to_post_discussions_to_only_followers') }}
                                        </li>
                                        <li class="{{ $plan->messages == '0' ? 'price-disable' : ''}}">
                                            <i class="bi {{ $plan->messages == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('mute_chat_messages') }}
                                        </li>
                                        <li class="{{ $plan->users == '0' ? 'price-disable' : ''}}">
                                            <i class="bi {{ $plan->users == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('block_users') }}
                                        </li>
                                        <li class="{{ $plan->viewers == '0' ? 'price-disable' : ''}}">
                                            <i class="bi {{ $plan->viewers == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('view_profile_visitors') }}
                                        </li>
                                        <li class="{{ $plan->ads == '0' ? 'price-disable' : ''}}">
                                            <i class="bi {{ $plan->ads == '1' ? 'bi-check2' : 'bi-x'}}"></i> {{ trans('no_ads') }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('admin.plans.edit', ['id' => $plan->id]) }}" class="btn btn-success">{{ trans('edit') }}</a>
                                    <a href="javascript:void(0)" onclick="delete_item('{{ route('admin.plans.destroy') }}','{{ $plan->id }}','{{ trans('delete_plan') }}?');" class="btn btn-danger">{{ trans('delete') }}</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-lg-6">
                            <div class="price">
                                <h4>{{ trans('no_pricing_plans_available') }}</h4>
                            </div>
                        </div>

                    @endforelse
                </div>
            @elseif(Route::is('admin.plans.add'))

                <div class="card">
                    <div class="card-header">

                        <div class="d-md-flex justify-content-between mb-10">
                            <h5 class="h4 mb-3">{{ trans('plans') }}</h5>
                            <div>
                                <a href="{{ route('admin.plans.list') }}" class="btn btn-success mb-0">{{ trans('list') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-style settings-card-2 mb-5">
                    <form id="add_plan_form" method="POST">
                        @csrf

                        <div class="row px-3 py-3">
                            <div class="col-lg-6">
                                <div class="input-style-1">
                                    <label for="name">{{ trans('plan_name') }}</label>
                                    <input name="name" id="name" type="text" placeholder="{{ trans('plan_name') }}" />
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-style-1">
                                    <label for="price">{{ trans('plan_price') }}</label>
                                    <input name="price" id="price" type="number" placeholder="{{ trans('plan_price') }}" />
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="select-style-1">
                                    <label for="duration">{{ trans('duration') }}</label>
                                    <div class="select-position">
                                        <select name="duration" id="duration" class="form-select form-control">
                                            <option value="Monthly">{{ trans('monthly') }}</option>
                                            <option value="Quarterly">{{ trans('quarterly') }}</option>
                                            <option value="Yearly">{{ trans('yearly') }}</option>
                                            <option value="Lifetime">{{ trans('lifetime') }}</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-style-1">
                                    <label for="points">{{ trans('points_topup') }}</label>
                                    <input name="points" id="points" type="number" placeholder="{{ trans('points_topup') }}" />
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="verified" class="custom-control-input" value="1" id="verified">
                                        <label class="custom-control-label" for="verified">{{ trans('verified_checkmark') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="categories" class="custom-control-input" value="1" id="categories">
                                        <label class="custom-control-label" for="categories">{{ trans('access_to_pro_categories') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="tips" class="custom-control-input" value="1" id="tips">
                                        <label class="custom-control-label" for="tips">{{ trans('access_to_tips') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="comments" class="custom-control-input" value="1" id="comments">
                                        <label class="custom-control-label" for="comments">{{ trans('allow_or_close_comments_on_posts') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="reactions" class="custom-control-input" value="1" id="reactions">
                                        <label class="custom-control-label" for="reactions">{{ trans('show_or_hide_reactions_or_likes_on_posts') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="followers" class="custom-control-input" value="1" id="followers">
                                        <label class="custom-control-label" for="followers">{{ trans('allow_user_to_post_discussions_to_only_followers') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="messages" class="custom-control-input" value="1" id="messages">
                                        <label class="custom-control-label" for="messages">{{ trans('mute_chat_messages') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="users" class="custom-control-input" value="1" id="users">
                                        <label class="custom-control-label" for="users">{{ trans('block_users') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="viewers" class="custom-control-input" value="1" id="viewers">
                                        <label class="custom-control-label" for="viewers">{{ trans('view_profile_visitors') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="ads" class="custom-control-input" value="1" id="ads">
                                        <label class="custom-control-label" for="ads"> {{ trans('no_ads') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-style-1">
                                    <label for="order">{{ trans('order') }}</label>
                                    <input name="order" id="order" type="number" placeholder="{{ trans('order') }}" />
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="select-style-1">
                                    <label for="status">{{ trans('status') }}</label>
                                    <div class="select-position">
                                        <select name="status" id="status" class="form-select form-control">
                                            <option value="1">{{ trans('active') }}</option>
                                            <option value="0">{{ trans('not_active') }}</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                            <button type="submit" class="main-btn primary-btn btn-hover" id="add_plan_btn">{{ trans('submit') }}</button>
                            </div>

                        </div>

                    </form>
                </div>

            @elseif(Route::is('admin.plans.edit'))

                <div class="card">
                    <div class="card-header">

                        <div class="d-md-flex justify-content-between mb-10">
                            <h5 class="h4 mb-3">{{ trans('edit') }}</h5>
                            <div>
                                <a href="{{ route('admin.plans.list') }}" class="btn btn-success mb-0">{{ trans('list') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-style settings-card-2 mb-5">
                    <form id="edit_plan_form" method="POST">
                        @csrf

                        <input type="hidden" name="id" id="id" value="{{ $plan->id }}">

                        <div class="row px-3 py-3">
                            <div class="col-lg-6">
                                <div class="input-style-1">
                                    <label for="name">{{ trans('plan_name') }}</label>
                                    <input name="name" id="name" type="text" value="{{ $plan->name }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-style-1">
                                    <label for="price">{{ trans('plan_price') }}</label>
                                    <input name="price" id="price" type="number" value="{{ $plan->price }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="select-style-1">
                                    <label for="duration">{{ trans('duration') }}</label>
                                    <div class="select-position">
                                        <select name="duration" id="duration" class="form-select form-control">
                                            <option value="Monthly" {{ $plan->duration == 'Monthly' ? 'selected="selected"' : '' }}>{{ trans('monthly') }}</option>
                                            <option value="Quarterly" {{ $plan->duration == 'Quarterly' ? 'selected="selected"' : '' }}>{{ trans('quarterly') }}</option>
                                            <option value="Yearly" {{ $plan->duration == 'Yearly' ? 'selected="selected"' : '' }}>{{ trans('yearly') }}</option>
                                            <option value="Lifetime" {{ $plan->duration == 'Lifetime' ? 'selected="selected"' : '' }}>{{ trans('lifetime') }}</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-style-1">
                                    <label for="points">{{ trans('points_topup') }}</label>
                                    <input name="points" id="points" type="number" value="{{ $plan->points }}" >
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="verified" class="custom-control-input" value="1" id="verified" {{ $plan->verified == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="verified">{{ trans('verified_checkmark') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="categories" class="custom-control-input" value="1" id="categories" id="verified" {{ $plan->categories == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="categories">{{ trans('access_to_pro_categories') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="tips" class="custom-control-input" value="1" id="tips" {{ $plan->tips == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="tips">{{ trans('access_to_tips') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="comments" class="custom-control-input" value="1" id="comments" {{ $plan->comments == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="comments">{{ trans('allow_or_close_comments_on_posts') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="reactions" class="custom-control-input" value="1" id="reactions" {{ $plan->reactions == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="reactions">{{ trans('show_or_hide_reactions_or_likes_on_posts') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="followers" class="custom-control-input" value="1" id="followers" {{ $plan->followers == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="followers">{{ trans('allow_user_to_post_discussions_to_only_followers') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="messages" class="custom-control-input" value="1" id="messages" {{ $plan->messages == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="messages">{{ trans('mute_chat_messages') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="users" class="custom-control-input" value="1" id="users" {{ $plan->users == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="users">{{ trans('block_users') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="viewers" class="custom-control-input" value="1" id="viewers" {{ $plan->viewers == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="viewers">{{ trans('view_profile_visitors') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                        <input type="checkbox" name="ads" class="custom-control-input" value="1" id="ads" {{ $plan->ads == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="ads"> {{ trans('no_ads') }}</label>
                                        <p class="text-muted mb-2"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-style-1">
                                    <label for="order">{{ trans('oder') }}</label>
                                    <input name="order" id="order" type="number" value="{{ $plan->order }}" />
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="select-style-1">
                                    <label for="status">{{ trans('status') }}</label>
                                    <div class="select-position">
                                        <select name="status" id="status" class="form-select form-control">
                                            <option value="1" {{ $plan->status == '1' ? 'selected="selected"' : '' }}>{{ trans('active') }}</option>
                                            <option value="0" {{ $plan->status == '0' ? 'selected="selected"' : '' }}>{{ trans('not_active') }}</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                            <button type="submit" class="main-btn primary-btn btn-hover" id="edit_plan_btn">{{ trans('submit') }}</button>
                            </div>

                        </div>

                    </form>
                </div>
            @endif
        </div>


      </div><!-- row -->
     </div><!-- container -->
    </section>

</main>
@endsection

@section('scripts')
<script>
    //Add Plan
    $(document).on('submit', '#add_plan_form', function(e) {
      e.preventDefault();
      start_load();
      const fd = new FormData(this);
      $("#add_plan_btn").text('{{ trans('adding') }}...');
      $.ajax({
        url: '{{ route('admin.plans.add') }}',
        method: 'post',
        data: fd,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {

          end_load();

          if (response.status == 400) {

              showError('name', response.messages.name);
              showError('price', response.messages.price);
              showError('points', response.messages.points);
              showError('order', response.messages.order);
              $("#add_plan_btn").text('{{ trans('submit') }}');

          }else if (response.status == 200) {

              tata.success("Success", response.messages, {
              position: 'tr',
              duration: 3000,
              animate: 'slide'
              });

              window.location.reload();

          }else if(response.status == 401){

              tata.error("Error", response.messages, {
              position: 'tr',
              duration: 3000,
              animate: 'slide'
              });

              window.location.reload();

          }

        }
      });
    });


    //Edit Plan
    $(document).on('submit', '#edit_plan_form', function(e) {
      e.preventDefault();
      start_load();
      const fd = new FormData(this);
      $("#edit_plan_btn").text('{{ trans('updating') }}...');
      $.ajax({
        url: '{{ route('admin.plans.update') }}',
        method: 'post',
        data: fd,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {

          end_load();

          if (response.status == 400) {

              showError('name', response.messages.name);
              showError('price', response.messages.price);
              showError('points', response.messages.points);
              showError('order', response.messages.order);
              $("#edit_plan_btn").text('{{ trans('submit') }}');

          }else if (response.status == 200) {

              tata.success("Success", response.messages, {
              position: 'tr',
              duration: 3000,
              animate: 'slide'
              });

              window.location.reload();

          }else if(response.status == 401){

              tata.error("Error", response.messages, {
              position: 'tr',
              duration: 3000,
              animate: 'slide'
              });

              window.location.reload();

          }

        }
      });
    });
</script>
@endsection
