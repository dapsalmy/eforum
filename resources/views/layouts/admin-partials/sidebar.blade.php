

<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="{{ route('home') }}">
  <span class="align-middle">{{ get_setting('site_name') }}</span>
</a>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                {{ trans('overview') }}
            </li>

            <li class="sidebar-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.dashboard') }}">
                <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">{{ trans('dashboard') }}</span>
                </a>
            </li>

            <li class="sidebar-header">
                {{ trans('forum') }}
            </li>

        @if(Auth::user()->role()->categories == 1)
            <li class="sidebar-item  {{ Route::is('admin.categories.list') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.categories.list') }}">
                <i class="align-middle" data-feather="aperture"></i> <span class="align-middle">{{ trans('categories') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->badges == 1)
            <li class="sidebar-item  {{ Route::is('admin.badges.list') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.badges.list') }}">
                <i class="align-middle" data-feather="award"></i> <span class="align-middle">{{ trans('badges') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->posts == 1)
            <li class="sidebar-item {{ Route::is('admin.posts.list') ? 'active' : '' }}
            {{ Route::is('admin.posts.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.posts.list') }}">
                <i class="align-middle" data-feather="columns"></i> <span class="align-middle">{{ trans('posts') }}</span>
                </a>
            </li>
            <li class="sidebar-item {{ Route::is('admin.tags.list') ? 'active' : '' }}
            {{ Route::is('admin.tags.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.tags.list') }}">
                <i class="align-middle" data-feather="tag"></i> <span class="align-middle">{{ trans('tags') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->comments == 1)
            <li class="sidebar-item {{ Route::is('admin.comments.list') ? 'active' : '' }}
            {{ Route::is('admin.comments.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.comments.list') }}">
                <i class="align-middle" data-feather="menu"></i> <span class="align-middle">{{ trans('comments') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->replies == 1)
            <li class="sidebar-item {{ Route::is('admin.replies.list') ? 'active' : '' }}
            {{ Route::is('admin.replies.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.replies.list') }}">
                <i class="align-middle" data-feather="copy"></i> <span class="align-middle">{{ trans('replies') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->chats == 1)
            <li class="sidebar-item {{ Route::is('admin.chats') ? 'active' : '' }}
            {{ Route::is('admin.chats.messages') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.chats') }}">
                <i class="align-middle" data-feather="message-square"></i> <span class="align-middle">{{ trans('chats') }}</span>
                </a>
            </li>
        @endif

            <li class="sidebar-header">{{ trans('moderation') }}</li>
         @if(Auth::user()->role()->reports == 1)
            <li class="sidebar-item nav-item nav-item-has-children {{ Route::is('admin.reports.users') ? 'active' : '' }}
            {{ Route::is('admin.reports.posts') ? 'active' : '' }} {{ Route::is('admin.reports.comments') ? 'active' : '' }}
            {{ Route::is('admin.reports.replies') ? 'active' : '' }}">
                <a href="#0" class="collapsed" data-bs-toggle="collapse" data-bs-target="#ddmenu_1" aria-controls="ddmenu_1" aria-expanded="false" aria-label="Toggle navigation">
                <span class="icon"><i class="align-middle" data-feather="flag"></i></span>
                <span class="align-middle ms-1">{{ trans('reports') }}</span>
                </a>
                <ul id="ddmenu_1" class="collapse dropdown-nav">
                    <li><a href="{{ route('admin.reports.users') }}"><i class="lni lni-arrow-right"></i> {{ trans('users') }} {{ trans('reports') }}</a></li>
                </ul>
                <ul id="ddmenu_1" class="collapse dropdown-nav">
                    <li><a href="{{ route('admin.reports.posts') }}"><i class="lni lni-arrow-right"></i> {{ trans('posts') }} {{ trans('reports') }}</a></li>
                </ul>
                <ul id="ddmenu_1" class="collapse dropdown-nav">
                    <li><a href="{{ route('admin.reports.comments') }}"><i class="lni lni-arrow-right"></i> {{ trans('comments') }} {{ trans('reports') }}</a></li>
                </ul>
                <ul id="ddmenu_1" class="collapse dropdown-nav">
                    <li><a href="{{ route('admin.reports.replies') }}"><i class="lni lni-arrow-right"></i> {{ trans('replies') }} {{ trans('reports') }}</a></li>
                </ul>
            </li>
        @endif
        @if(Auth::user()->role()->ban_durations == 1)
            <li class="sidebar-item  {{ Route::is('admin.bandurations.list') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.bandurations.list') }}">
                <i class="align-middle" data-feather="slash"></i> <span class="align-middle">{{ trans('ban_durations') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->banned_users == 1)
            <li class="sidebar-item  {{ Route::is('admin.banned.users') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.banned.users') }}">
                <i class="align-middle" data-feather="x"></i> <span class="align-middle">{{ trans('banned_users') }}</span>
                </a>
            </li>
        @endif

    @if(get_setting('payments_on_site') == 'Yes')
        @if(Auth::user()->role()->plans == 1)
            <li class="sidebar-header">{{ trans('payments') }}</li>
            <li class="sidebar-item {{ Route::is('admin.plans.list') ? 'active' : '' }}
            {{ Route::is('admin.plans.add') ? 'active' : '' }}
            {{ Route::is('admin.plans.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.plans.list') }}">
                <i class="align-middle" data-feather="wind"></i> <span class="align-middle">{{ trans('plans') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->buy_points == 1)
            <li class="sidebar-item  {{ Route::is('admin.buypoints.list') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.buypoints.list') }}">
                <i class="align-middle" data-feather="copy"></i> <span class="align-middle">{{ trans('buy_points') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->deposits == 1)
            <li class="sidebar-item  {{ Route::is('admin.deposits') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.deposits') }}">
                <i class="align-middle" data-feather="list"></i> <span class="align-middle">{{ trans('deposits') }}</span>
                </a>
            </li>
         @endif
        @if(Auth::user()->role()->subscriptions == 1)
            <li class="sidebar-item  {{ Route::is('admin.subscriptions') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.subscriptions') }}">
                <i class="align-middle" data-feather="shuffle"></i> <span class="align-middle">{{ trans('subscriptions') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->tips == 1)
            <li class="sidebar-item  {{ Route::is('admin.tips') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.tips') }}">
                <i class="align-middle" data-feather="send"></i> <span class="align-middle">{{ trans('tips') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->withdrawals == 1)
            <li class="sidebar-item  {{ Route::is('admin.withdrawals') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.withdrawals') }}">
                <i class="align-middle" data-feather="align-justify"></i> <span class="align-middle">{{ trans('withdrawals') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->transactions == 1)
            <li class="sidebar-item  {{ Route::is('admin.transactions') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.transactions') }}">
                <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">{{ trans('transactions') }}</span>
                </a>
            </li>
        @endif
    @endif

            <li class="sidebar-header">
                {{ trans('account') }}
            </li>

            <li class="sidebar-item {{ Route::is('admin.profile') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.profile') }}">
                 <i class="align-middle" data-feather="user"></i> <span class="align-middle">{{ trans('profile') }}</span>
                </a>
            </li>
        @if(Auth::user()->role()->users == 1)
            <li class="sidebar-item {{ Route::is('admin.users.list') ? 'active' : '' }}
            {{ Route::is('admin.user') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.users.list') }}">
                 <i class="align-middle" data-feather="users"></i> <span class="align-middle">{{ trans('users') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->roles == 1)
            <li class="sidebar-item
            {{ Route::is('admin.roles.list') ? 'active' : '' }}
            {{ Route::is('admin.roles.add') ? 'active' : '' }}
            {{ Route::is('admin.roles.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.roles.list') }}">
                 <i class="align-middle" data-feather="trello"></i> <span class="align-middle">{{ trans('roles') }} & {{ trans('permissions') }}</span>
                </a>
            </li>
        @endif


        @if(Auth::user()->role()->pages == 1)
            <li class="sidebar-header">CMS</li>
            <li class="sidebar-item
            {{ Route::is('admin.pages.list') ? 'active' : '' }}
            {{ Route::is('admin.pages.add') ? 'active' : '' }}
            {{ Route::is('admin.pages.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.pages.list') }}">
                <i class="align-middle" data-feather="align-left"></i> <span class="align-middle">{{ trans('pages') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->faqs == 1)
            <li class="sidebar-item {{ Route::is('admin.faqs.list') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.faqs.list') }}">
                <i class="align-middle" data-feather="list"></i> <span class="align-middle">{{ trans('faqs') }}</span>
                </a>
            </li>
        @endif

        <li class="sidebar-item {{ Route::is('admin.country.list') ? 'active' : '' }}">
            <a class="sidebar-link" href="{{ route('admin.country.list') }}">
            <i class="align-middle" data-feather="flag"></i> <span class="align-middle">{{ add('countries') }}</span>
            </a>
        </li>

        @if(Auth::user()->role()->site_settings == 1)
            <li class="sidebar-header">{{ trans('settings') }}</li>
            <li class="sidebar-item
            {{ Route::is('admin.settings.site') ? 'active' : '' }}
            {{ Route::is('admin.settings.home') ? 'active' : '' }}
            {{ Route::is('admin.settings.forum') ? 'active' : '' }}
            {{ Route::is('admin.settings.points') ? 'active' : '' }}
            {{ Route::is('admin.settings.currency') ? 'active' : '' }}
            {{ Route::is('admin.settings.payments') ? 'active' : '' }}
            {{ Route::is('admin.settings.ads') ? 'active' : '' }}
            {{ Route::is('admin.settings.analytics') ? 'active' : '' }}
            {{ Route::is('admin.settings.adsense') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.settings.site') }}">
                <i class="align-middle" data-feather="check-square"></i> <span class="align-middle">{{ trans('site') }} {{ trans('settings') }}</span>
                </a>
            </li>
            <li class="sidebar-item
            {{ Route::is('admin.auth.google') ? 'active' : '' }}
            {{ Route::is('admin.auth.facebook') ? 'active' : '' }}
            {{ Route::is('admin.auth.email') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.auth.google') }}">
                  <i class="align-middle" data-feather="key"></i> <span class="align-middle">{{ trans('auth') }} {{ trans('settings') }}</span>
                </a>
            </li>
        @endif
        @if(get_setting('payments_on_site') == 'Yes')
            @if(Auth::user()->role()->gateways == 1)
                <li class="sidebar-item
                {{ Route::is('admin.gateways.paypal') ? 'active' : '' }}
                {{ Route::is('admin.gateways.stripe') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.gateways.paypal') }}">
                    <i class="align-middle" data-feather="credit-card"></i> <span class="align-middle">{{ trans('payment_gateways') }}</span>
                    </a>
                </li>
            @endif
        @endif
         @if(Auth::user()->role()->language == 1)
            <li class="sidebar-item
            {{ Route::is('admin.languages.index') ? 'active' : '' }}
            {{ Route::is('admin.languages.edit') ? 'active' : '' }}
            {{ Route::is('admin.languages.default') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.languages.index') }}">
                <i class="align-middle" data-feather="globe"></i> <span class="align-middle">{{ trans('languages') }} {{ trans('settings') }}</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->role()->mail == 1)
            <li class="sidebar-item {{ Route::is('admin.settings.mail') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.settings.mail') }}">
                <i class="align-middle" data-feather="mail"></i> <span class="align-middle">{{ trans('mail') }} {{ trans('settings') }}</span>
                </a>
            </li>
        @endif
        <li class="sidebar-item
        {{ Route::is('admin.email.list') ? 'active' : '' }}
        {{ Route::is('admin.email.edit') ? 'active' : '' }}
        {{ Route::is('admin.email.add') ? 'active' : '' }}">
            <a class="sidebar-link" href="{{ route('admin.email.list') }}">
            <i class="align-middle" data-feather="send"></i> <span class="align-middle">{{ trans('email_templates') }}</span>
            </a>
        </li>
        </ul>

    </div>
</nav>
