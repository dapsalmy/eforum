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
                <a class="nav-link {{ Route::is('admin.roles.list') ? 'active' : '' }}
                {{ Route::is('admin.roles.edit') ? 'active' : '' }}" href="{{ route('admin.roles.list') }}">
                    <i class="align-middle me-1" data-feather="layers"></i> {{ trans('roles') }} </a>
                </li>
                <li class="nav-item">
                <a class="nav-link
                {{ Route::is('admin.roles.add') ? 'active' : '' }}" href="{{ route('admin.roles.add') }}">
                    <i class="align-middle me-1" data-feather="plus-square"></i> {{ trans('add_role') }}</a>
                </li>
            </ul>
            </div>
         </div>

        </div>

      <div class="col-lg-9">
        @if(Route::is('admin.roles.list') )

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="h4 mb-0">{{ trans('roles') }}</h5>
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable_cms" class="table table-bordered table-reload">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('name') }}</th>
                                    <th>{{ trans('permissions') }}</th>
                                    <th class="text-right">{{ trans('options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $key => $role)
                                    <tr>
                                        <td>{{ ($key+1) }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            @if ($role->categories == 1)
                                                <span class="badge bg-success">{{ trans('categories') }}</span>
                                            @endif
                                            @if ($role->badges == 1)
                                                <span class="badge bg-success">{{ trans('badges') }}</span>
                                            @endif
                                            @if ($role->posts == 1)
                                                <span class="badge bg-success">{{ trans('posts') }}</span>
                                            @endif
                                            @if ($role->comments == 1)
                                                <span class="badge bg-success">{{ trans('comments') }}</span>
                                            @endif
                                            @if ($role->replies == 1)
                                                <span class="badge bg-success">{{ trans('replies') }}</span>
                                            @endif
                                            @if ($role->chats == 1)
                                                <span class="badge bg-success">{{ trans('chats') }}</span>
                                            @endif
                                            @if ($role->reports == 1)
                                                <span class="badge bg-success">{{ trans('reports') }}</span>
                                            @endif
                                            @if ($role->ban_durations == 1)
                                                <span class="badge bg-success">{{ trans('ban_durations') }}</span>
                                            @endif
                                            @if ($role->banned_users == 1)
                                                <span class="badge bg-success">{{ trans('banned_users') }}</span>
                                            @endif
                                            @if ($role->plans == 1)
                                                <span class="badge bg-success">{{ trans('plans') }}</span>
                                            @endif
                                            @if ($role->buy_points == 1)
                                                <span class="badge bg-success">{{ trans('buy_points') }}</span>
                                            @endif
                                            @if ($role->deposits == 1)
                                                <span class="badge bg-success">{{ trans('deposits') }}</span>
                                            @endif
                                            @if ($role->subscriptions == 1)
                                                <span class="badge bg-success">{{ trans('subscriptions') }}</span>
                                            @endif
                                            @if ($role->tips == 1)
                                                <span class="badge bg-success">{{ trans('tips') }}</span>
                                            @endif
                                            @if ($role->withdrawals == 1)
                                                <span class="badge bg-success">{{ trans('withdrawals') }}</span>
                                            @endif
                                            @if ($role->transactions == 1)
                                                <span class="badge bg-success">{{ trans('transactions') }}</span>
                                            @endif
                                            @if ($role->users == 1)
                                                <span class="badge bg-success">{{ trans('users') }}</span>
                                            @endif
                                            @if ($role->roles == 1)
                                                <span class="badge bg-success">{{ trans('roles') }}</span>
                                            @endif
                                            @if ($role->pages == 1)
                                                <span class="badge bg-success">{{ trans('pages') }}</span>
                                            @endif
                                            @if ($role->faqs == 1)
                                                <span class="badge bg-success">{{ trans('faqs') }}</span>
                                            @endif
                                            @if ($role->site_settings == 1)
                                                <span class="badge bg-success">{{ trans('site_settings') }}</span>
                                            @endif
                                            @if ($role->gateways == 1)
                                                <span class="badge bg-success">{{ trans('payment_gateways') }}</span>
                                            @endif
                                            @if ($role->language == 1)
                                                <span class="badge bg-success">{{ trans('languages') }}</span>
                                            @endif
                                            @if ($role->mail == 1)
                                                <span class="badge bg-success">{{ trans('mail') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right">

                                            <a  href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-soft-success btn-icon btn-circle btn-sm btn icon editIcon" title="Edit">
                                                <i class="align-middle" data-feather="edit-2"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="Delete"
                                            onclick="delete_item('{{ route('admin.roles.destroy') }}','{{ $role->id }}','{{ trans('delete_this_role') }}?');">
                                                <i class="align-middle" data-feather="trash"></i>
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    </div>
                </div>
            </div>

        @elseif(Route::is('admin.roles.add') )

            <div class="card-style settings-card-2 mb-5">
                <form id="add_role_form" method="POST">
                    @csrf

                    <div class="row px-3 py-3">
                        <div class="col-lg-6">
                            <div class="input-style-1">
                                <label for="name">{{ trans('role_name') }}</label>
                                <input name="name" id="name" type="text" placeholder="{{ trans('role_name') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <h4 class="mb-3">{{ trans('permissions') }}</h4>
                        <hr>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="categories" class="custom-control-input" value="1" id="categories">
                                    <label class="custom-control-label" for="categories">{{ trans('categories') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="badges" class="custom-control-input" value="1" id="badges">
                                    <label class="custom-control-label" for="badges">{{ trans('badges') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="posts" class="custom-control-input" value="1" id="posts">
                                    <label class="custom-control-label" for="posts">{{ trans('posts') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="comments" class="custom-control-input" value="1" id="comments">
                                    <label class="custom-control-label" for="comments">{{ trans('comments') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="replies" class="custom-control-input" value="1" id="replies">
                                    <label class="custom-control-label" for="replies">{{ trans('replies') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="chats" class="custom-control-input" value="1" id="chats">
                                    <label class="custom-control-label" for="chats">{{ trans('chats') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="reports" class="custom-control-input" value="1" id="reports">
                                    <label class="custom-control-label" for="reports">{{ trans('reports') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="ban_durations" class="custom-control-input" value="1" id="ban_durations">
                                    <label class="custom-control-label" for="ban_durations">{{ trans('ban_durations') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="banned_users" class="custom-control-input" value="1" id="banned_users">
                                    <label class="custom-control-label" for="banned_users">{{ trans('banned_users') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="plans" class="custom-control-input" value="1" id="plans">
                                    <label class="custom-control-label" for="plans">{{ trans('plans') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="buy_points" class="custom-control-input" value="1" id="buy_points">
                                    <label class="custom-control-label" for="buy_points"> {{ trans('buy_points') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="deposits" class="custom-control-input" value="1" id="deposits">
                                    <label class="custom-control-label" for="deposits"> {{ trans('deposits') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="subscriptions" class="custom-control-input" value="1" id="subscriptions">
                                    <label class="custom-control-label" for="subscriptions"> {{ trans('subscriptions') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="tips" class="custom-control-input" value="1" id="tips">
                                    <label class="custom-control-label" for="tips"> {{ trans('tips') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="withdrawals" class="custom-control-input" value="1" id="withdrawals">
                                    <label class="custom-control-label" for="withdrawals"> {{ trans('withdrawals') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="transactions" class="custom-control-input" value="1" id="transactions">
                                    <label class="custom-control-label" for="transactions"> {{ trans('transactions') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="users" class="custom-control-input" value="1" id="users">
                                    <label class="custom-control-label" for="users"> {{ trans('users') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="roles" class="custom-control-input" value="1" id="roles">
                                    <label class="custom-control-label" for="roles"> {{ trans('roles') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="pages" class="custom-control-input" value="1" id="pages">
                                    <label class="custom-control-label" for="pages"> {{ trans('pages') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="faqs" class="custom-control-input" value="1" id="faqs">
                                    <label class="custom-control-label" for="faqs"> {{ trans('faqs') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="site_settings" class="custom-control-input" value="1" id="site_settings">
                                    <label class="custom-control-label" for="site_settings"> {{ trans('site_settings') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="gateways" class="custom-control-input" value="1" id="gateways">
                                    <label class="custom-control-label" for="gateways"> {{ trans('payment_gateways') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="language" class="custom-control-input" value="1" id="language">
                                    <label class="custom-control-label" for="language"> {{ trans('languages') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="mail" class="custom-control-input" value="1" id="mail">
                                    <label class="custom-control-label" for="mail"> {{ trans('mail') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover" id="add_role_btn">{{ trans('submit') }}</button>
                        </div>

                    </div>

                </form>
            </div>

        @elseif(Route::is('admin.roles.edit') )

            <div class="card-style settings-card-2 mb-5">
                <form id="edit_role_form" method="POST">
                    @csrf

                    <input type="hidden" name="id" id="id" value="{{ $role->id }}">

                    <div class="row px-3 py-3">
                        <div class="col-lg-6">
                            <div class="input-style-1">
                                <label for="name">{{ trans('role_name') }}</label>
                                <input name="name" id="name" type="text" value="{{ $role->name }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <h4 class="mb-3">{{ trans('permissions') }}</h4>
                        <hr>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="categories" class="custom-control-input" value="1" id="categories"
                                    {{ $role->categories == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="categories">{{ trans('categories') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="badges" class="custom-control-input" value="1" id="badges"
                                    {{ $role->badges == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="badges">{{ trans('badges') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="posts" class="custom-control-input" value="1" id="posts"
                                    {{ $role->posts == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="posts">{{ trans('posts') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="comments" class="custom-control-input" value="1" id="comments"
                                    {{ $role->comments == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="comments">{{ trans('comments') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="replies" class="custom-control-input" value="1" id="replies"
                                    {{ $role->replies == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="replies">{{ trans('replies') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="chats" class="custom-control-input" value="1" id="chats"
                                    {{ $role->chats == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="chats">{{ trans('chats') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="reports" class="custom-control-input" value="1" id="reports"
                                    {{ $role->reports == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="reports">{{ trans('reports') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="ban_durations" class="custom-control-input" value="1" id="ban_durations"
                                    {{ $role->ban_durations == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="ban_durations">{{ trans('ban_durations') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="banned_users" class="custom-control-input" value="1" id="banned_users"
                                    {{ $role->banned_users == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="banned_users">{{ trans('banned_users') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="plans" class="custom-control-input" value="1" id="plans"
                                    {{ $role->plans == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="plans">{{ trans('plans') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="buy_points" class="custom-control-input" value="1" id="buy_points"
                                    {{ $role->buy_points == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="buy_points"> {{ trans('buy_points') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="deposits" class="custom-control-input" value="1" id="deposits"
                                    {{ $role->deposits == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="deposits"> {{ trans('deposits') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="subscriptions" class="custom-control-input" value="1" id="subscriptions"
                                    {{ $role->subscriptions == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="subscriptions"> {{ trans('subscriptions') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="tips" class="custom-control-input" value="1" id="tips"
                                    {{ $role->tips == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="tips"> {{ trans('tips') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="withdrawals" class="custom-control-input" value="1" id="withdrawals"
                                    {{ $role->withdrawals == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="withdrawals"> {{ trans('withdrawals') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="transactions" class="custom-control-input" value="1" id="transactions"
                                    {{ $role->transactions == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="transactions"> {{ trans('transactions') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="users" class="custom-control-input" value="1" id="users"
                                    {{ $role->users == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="users"> {{ trans('users') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="roles" class="custom-control-input" value="1" id="roles"
                                    {{ $role->roles == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="roles"> {{ trans('roles') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="pages" class="custom-control-input" value="1" id="pages"
                                    {{ $role->pages == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="pages"> {{ trans('pages') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="faqs" class="custom-control-input" value="1" id="faqs"
                                    {{ $role->faqs == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="faqs"> {{ trans('faqs') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="site_settings" class="custom-control-input" value="1" id="site_settings"
                                    {{ $role->site_settings == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="site_settings"> {{ trans('site_settings') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="gateways" class="custom-control-input" value="1" id="gateways"
                                    {{ $role->gateways == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="gateways"> {{ trans('payment_gateways') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="language" class="custom-control-input" value="1" id="language"
                                    {{ $role->language == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="language"> {{ trans('languages') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="mail" class="custom-control-input" value="1" id="mail"
                                    {{ $role->mail == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="mail"> {{ trans('mail') }}</label>
                                    <p class="text-muted mb-2"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                        <button type="submit" class="main-btn primary-btn btn-hover" id="edit_role_btn">{{ trans('submit') }}</button>
                        </div>

                    </div>

                </form>
            </div>

        @endif

    </div><!-- col-lg-9 -->




    </div><!-- row -->
   </div><!-- container -->
  </section>

</main>
@endsection



@section('scripts')

<script>
    //Add Role
    $(document).on('submit', '#add_role_form', function(e) {
      e.preventDefault();
      start_load();
      const fd = new FormData(this);
      $("#add_role_btn").text('{{ trans('submitting') }}...');
      $.ajax({
        url: '{{ route('admin.roles.add') }}',
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
              $("#add_role_btn").text('{{ trans('submit') }}');

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

    //Edit Role
    $(document).on('submit', '#edit_role_form', function(e) {
      e.preventDefault();
      start_load();
      const fd = new FormData(this);
      $("#edit_role_btn").text('{{ trans('updating') }}...');
      $.ajax({
        url: '{{ route('admin.roles.update') }}',
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
              $("#edit_role_btn").text('{{ trans('submit') }}');

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
