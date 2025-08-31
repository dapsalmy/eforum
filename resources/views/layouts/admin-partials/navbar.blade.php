
<nav class="navbar navbar-expand navbar-light navbar-bg">
    <a class="sidebar-toggle js-sidebar-toggle">
      <i class="hamburger align-self-center"></i>
    </a>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align">
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">{{ trans('languages') }}</a>
                <div class="dropdown-menu">
                    @php
                        $languages = get_all_languages();
                        $current_locale = App::currentLocale();
                    @endphp
                    @foreach($languages as $lang)
                        @if($lang === $current_locale)
                            <span class="dropdown-item ml-2 mr-2 text-gray-700">{{ ucfirst($lang) }} <span class="badge bg-danger">Active</span></span>
                        @else
                            <a class="dropdown-item ml-1 underline ml-2 mr-2" href="{{ route('language.change', ['locale' => $lang]) }}">
                                <span>{{ ucfirst($lang) }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                    <i class="align-middle" data-feather="settings"></i>
                </a>

  <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
    <img src="{{ my_asset('uploads/users/'.Auth::user()->image) }}" class="avatar img-fluid rounded me-1" alt="{{ Auth::user()->name }}" /> <span class="text-dark">{{ Auth::user()->name }}</span>
  </a>
                <div class="dropdown-menu dropdown-menu-end">
                  @if(Auth::user()->role()->site_settings == 1)
                    <a class="dropdown-item" href="{{ route('admin.settings.site') }}"><i class="align-middle me-1" data-feather="settings"></i> {{ trans('settings') }}</a>
                  @endif
                    <a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="align-middle me-1" data-feather="user"></i> {{ trans('profile') }}</a>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ trans('logout') }}</a>

                                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                      @csrf
                                  </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
