

<div class="dash-sidebar position-sticky"

@if (get_setting('site_direction') == 'ltr')
    data-aos="fade-right" data-aos-easing="ease-in-sine"
@elseif (get_setting('site_direction') == 'rtl')
    data-aos="fade-left" data-aos-easing="ease-in-sine"
@endif>

    <div class="ps-3 d-flex align-items-center">
      <div class="media align-items-center">
          <div class="media-head me-2">
              <div class="avatar avatar-lg">
                  <img src="{{ my_asset('uploads/users/'.Auth::user()->image) }}" alt="user" class="avatar-img rounded-circle">
              </div>
          </div>
          <div class="media-body">
              <h5>{{ Auth::user()->name }}
                @if(Auth::user()->verified == 1)
                    <span class="verified-badge" data-bs-toggle="tooltip" aria-label="Verified User" data-bs-original-title="Verified User">
                    <i class="bi bi-patch-check"></i>
                    </span>
                @endif
              </h5>
              <p class="small mb-0">{{ trans('member_since') }} {{ Auth::user()->created_at->format('Y') }}</p>
          </div>
      </div>

      <!-- Responsive navbar toggler -->
      <button class="navbar-toggler ms-auto d-block d-xl-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav2"
          aria-controls="navbarNav2" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-animation user-backend">
              <span></span>
              <span></span>
              <span></span>
          </span>
      </button>
    </div>

  <div class="navbar-collapse d-xl-block collapse" id="navbarNav2">
    <ul class="navbar-nav flex-column">
     <li class="nav-info">{{ trans('dashboard') }} </li>
     <li class="nav-item {{ Route::is('user.overview') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.overview') }}">
         <span class="nav-icon-wrap"><i class="bi bi-speedometer"></i></span>
         <span class="nav-link-text">{{ trans('overview') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.posts.add') ? 'active' : '' }}
     {{ Route::is('user.posts.list') ? 'active' : '' }}
     {{ Route::is('user.posts.edit') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.posts.list') }}">
         <span class="nav-icon-wrap"><i class="bi bi-journals"></i></span>
         <span class="nav-link-text">{{ trans('posts') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.comments.list') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.comments.list') }}">
         <span class="nav-icon-wrap"><i class="bi bi-chat-left-dots"></i></span>
         <span class="nav-link-text">{{ trans('comments') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.replies.list') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.replies.list') }}">
         <span class="nav-icon-wrap"><i class="bi bi-chat-dots"></i></span>
         <span class="nav-link-text">{{ trans('replies') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.bookmarks') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.bookmarks') }}">
         <span class="nav-icon-wrap"><i class="bi bi-bookmarks"></i></span>
         <span class="nav-link-text">{{ trans('bookmarks') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.notifications') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.notifications') }}">
         <span class="nav-icon-wrap"><i class="b bi-bell"></i></span>
         <span class="nav-link-text">{{ trans('notifications') }}</span>
         <span class="badge bg-red ms-auto">{{ Auth::user()->notification_count() }}</span>
       </a>
     </li>
     <li class="nav-item">
       <a class="nav-link" href="{{ route('user.chats') }}">
         <span class="nav-icon-wrap"><i class="b bi-chat-left-text"></i></span>
         <span class="nav-link-text">{{ trans('messages') }}</span>
         <span class="badge bg-red ms-auto">{{ Auth::user()->messages_count() }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('followers') ? 'active' : '' }}
     {{ Route::is('following') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('followers') }}">
         <span class="nav-icon-wrap"><i class="b bi-people"></i></span>
         <span class="nav-link-text">{{ trans('followers') }} & {{ trans('following') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.blocks') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.blocks') }}">
         <span class="nav-icon-wrap"><i class="b bi-x-octagon"></i></span>
         <span class="nav-link-text">{{ trans('blocks') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('profile.viewers') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('profile.viewers') }}">
         <span class="nav-icon-wrap"><i class="b bi-ui-checks"></i></span>
         <span class="nav-link-text">{{ trans('profile_viewers') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.points') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.points') }}">
         <span class="nav-icon-wrap"><i class="b bi-file-ppt"></i></span>
         <span class="nav-link-text">{{ trans('points') }}</span>
       </a>
     </li>
     <li class="nav-info">{{ trans('account') }} </li>
     <li class="nav-item {{ Route::is('user.profile') ? 'active' : '' }}
     {{ Route::is('user.password') ? 'active' : '' }}  {{ Route::is('user.email.notifications') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.profile') }}">
         <span class="nav-icon-wrap"><i class="bi bi-gear"></i></span>
         <span class="nav-link-text">{{ trans('settings') }}</span>
       </a>
     </li>

@if(get_setting('payments_on_site') == 'Yes')
     <li class="nav-item {{ Route::is('user.wallet') ? 'active' : '' }}
     {{ Route::is('user.wallet.invoice') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.wallet') }}">
         <span class="nav-icon-wrap"><i class="bi bi-wallet2"></i></span>
         <span class="nav-link-text">{{ trans('wallet') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.pricing') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.pricing') }}">
         <span class="nav-icon-wrap"><i class="bi bi-credit-card-2-front"></i></span>
         <span class="nav-link-text">{{ trans('pricing_plans') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.subscriptions') ? 'active' : '' }}
     {{ Route::is('user.subscriptions.invoice') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.subscriptions') }}">
         <span class="nav-icon-wrap"><i class="bi bi-arrow-repeat"></i></span>
         <span class="nav-link-text">{{ trans('subscriptions') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.earnings') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.earnings') }}">
         <span class="nav-icon-wrap"><i class="bi bi-currency-dollar"></i></span>
         <span class="nav-link-text">{{ trans('earnings') }} & {{ trans('tips') }}</span>
       </a>
     </li>
     <li class="nav-item {{ Route::is('user.withdrawals') ? 'active' : '' }}">
       <a class="nav-link" href="{{ route('user.withdrawals') }}">
         <span class="nav-icon-wrap"><i class="bi bi-piggy-bank"></i></span>
         <span class="nav-link-text">{{ trans('withdrawals') }}</span>
       </a>
     </li>
@endif

    </ul>
  </div>


</div>
