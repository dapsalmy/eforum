

		<!-- ==============================================
		 Navbar
		=============================================== -->
		<header class="vine-navbar fixed-top">
			<nav class="navbar navbar-expand-lg">
				<div class="container align-items-center">
					<div class="logo">
						<a class="navbar-brand" href="{{ route('home') }}">
							<img class="logo-dark" src="{{ my_asset('uploads/settings/'.get_setting('logo_dark')) }}" alt="Logo">
						</a>
					</div>
					<div class="offcanvas nav-offcanvas offcanvas-start" tabindex="-1" id="offcanvas_Header_01" aria-labelledby="offcanvas_Header_01">
						<div class="offcanvas-header flex-wrap border-bottom border-gray-200">
							<div class="offcanvas-title">

                                @if (Auth::check())
								<div class="d-flex align-items-center">
									<div class="avatar"><img class="avatar-img rounded-circle" src="{{ my_asset('uploads/users/'.Auth::user()->image) }}" alt="User"></div>
									<div class="col ps-3">
										<h6 class="mb-0">{{ Auth::user()->name }}</h6><span class="fs-xs fw-400">@ {{ Auth::user()->username }}</span></div>
								</div>
                                @else
								<div class="d-flex align-items-center">
									<div class="avatar"><img class="avatar-img rounded-circle" src="{{ my_asset('uploads/settings/'.get_setting('favicon')) }}" alt="User"></div>
									<div class="col ps-3">
										<h6 class="mb-0">{{ get_setting('site_name') }}</h6><span class="fs-xs fw-400">Great Forum Platform</span></div>
								</div>
                                @endif
							</div>
							<button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#offcanvas_Header_01" aria-label="Close"></button>
						</div>
						<div class="offcanvas-body">

                            <!-- Nav Search START -->
                            <div class="col-xl-5">
                                <div class="nav mt-3 mt-lg-0 px-lg-4 flex-nowrap align-items-center">
                                    <div class="search-form nav-item w-100">
                                        <form class="rounded position-relative">
                                            <input class="bg-opacity-10 border-0" data-toggle="search" type="search" placeholder="{{ trans('search_posts') }}..." aria-label="Search">
                                        </form>
                                        <ul id="searchFormBox" class="dropdown-menu dropdown-animation dropdown-menu-end min-w-auto mt-2" aria-labelledby="searchDropdown">

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- Nav Search END -->
							<ul class="navbar-nav">

								<li class="nav-item dropdown mega-dropdown-md">
									<a id="MegaMenu" class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="bi bi-collection me-2"></i>{{ trans('pages') }}
									</a>
									<div class="dropdown-menu p-0">
										<div class="d-lg-flex">
											<div class="mega-dropdown-column pt-lg-3 pb-lg-4 first">
												<div class="mt-4 px-3 justify-content-center">
													<h4 class="py-2 leading-normal font-bold">
														Join <span>{{ \App\Models\User::count().'+' }}</span> other users as we learn, build, and grow together.
													</h4>
													<hr>
													<p>{{ get_setting('site_description') }}</p>
												</div>
											</div>
										  <div class="mega-dropdown-column pt-lg-3 pb-lg-4 px-3">
											<h6 class="mega-menu-title">{{ trans('forum') }}</h6>
											<ul class="list-unstyled mb-0 ps-2">
											  <li><a href="{{ route('home') }}" class="dropdown-item"><i class="bi bi-house-door me-2"></i> {{ trans('home') }}</a></li>
											  <li><a href="{{ route('home.posts') }}" class="dropdown-item"><i class="bi bi-list-task me-2"></i> {{ trans('discussions') }}</a></li>
											  <li><a href="{{ route('users') }}" class="dropdown-item"><i class="bi bi-people me-2"></i> {{ trans('users') }}</a></li>
											  <li><a href="{{ route('categories') }}" class="dropdown-item"><i class="bi bi-back me-2"></i> {{ trans('categories') }}</a></li>
											  <li><a href="{{ route('tags') }}" class="dropdown-item"><i class="bi bi-tags me-2"></i> {{ trans('tags') }}</a></li>
											  <li><a href="{{ route('stats') }}" class="dropdown-item text-nowrap"><i class="bi bi-columns-gap me-2"></i> {{ trans('stats') }} & {{ trans('online_users') }}</a></li>
											  <li><a href="{{ route('sitemap') }}" class="dropdown-item text-nowrap"><i class="bi bi-diagram-3 me-2"></i> {{ trans('sitemap') }} </a></li>
											</ul>
										  </div>
										  <div class="mega-dropdown-column pt-lg-3 pb-lg-4 px-3">
											<h6 class="mega-menu-title">{{ trans('pages') }}</h6>
											<ul class="list-unstyled mb-0 ps-2">
											  <li><a href="{{ route('about') }}" class="dropdown-item"><i class="bi bi-app me-2"></i>{{ trans('about') }}</a></li>

                                            @if(get_setting('payments_on_site') == 'Yes')
											  <li><a href="{{ route('plans') }}" class="dropdown-item"><i class="bi bi-wallet2 me-2"></i>{{ trans('pricing_plans') }}</a></li>
                                            @endif
											  <li><a href="{{ route('faqs') }}" class="dropdown-item"><i class="bi bi-list me-2"></i>{{ trans('faqs') }}</a></li>
											  <li><a href="{{ route('rules') }}" class="dropdown-item"><i class="bi bi-patch-check me-2"></i> {{ trans('community_rules') }}</a></li>
											  <li><a href="{{ route('privacy') }}" class="dropdown-item"><i class="bi bi-bar-chart-steps me-2"></i>{{ trans('privacy_policy') }}</a></li>
											  <li><a href="{{ route('terms') }}" class="dropdown-item"><i class="bi bi-list-ul me-2"></i>{{ trans('terms') }} & {{ trans('regulations') }}</a></li>
											  <li><a href="{{ route('cookie') }}" class="dropdown-item"><i class="bi bi-body-text me-2"></i>{{ trans('cookie_policy') }}</a></li>
											</ul>
										  </div>
										  <div class="mega-dropdown-column pt-lg-3 pb-lg-4 px-3">
											<h6 class="mega-menu-title">{{ trans('rewards') }}</h6>
											<ul class="list-unstyled mb-0 ps-2">
												<li><a href="{{ route('leaderboard') }}" class="dropdown-item"><i class="bi bi-list-stars me-2"></i>{{ trans('leaderboard') }}</a></li>
												<li><a href="{{ route('points') }}" class="dropdown-item"><i class="bi bi-file-ppt me-2"></i>{{ trans('earn') }}
                                                    {{ get_setting('payments_on_site') == 'Yes' ? '/ '. trans('buy_points') : trans('points') }}</a></li>
												<li><a href="{{ route('badges') }}" class="dropdown-item"><i class="bi bi-award me-2"></i> {{ trans('badges') }}</a></li>
											</ul>
										  </div>
										</div>
									</div>
								</li>

							</ul>
						</div>
					</div>
					<div class="header-end d-flex justify-content-end">

						<!--  Add Post Start -->
						<div class="h-col h-plus-toggle d-none d-sm-block">
							<a class="btn btn-mint rounded-circle" href="{{ route('user.posts.add') }}">
								<i class="bi bi-plus-lg"></i>
							</a>
						</div>
						<!-- Add Post END -->

						<!-- Translate dropdown START -->
						<div class="nav-item dropdown h-col">
                            <a class="h-translate-icon h-icon" href="#" id="languageDropdown" role="button" data-bs-auto-close="outside" data-bs-display="static" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-translate"> </i>
                            </a>
                            <ul class="dropdown-menu dropdown-animation dropdown-menu-end dropdown-menu-sm-start min-w-auto mt-2" aria-labelledby="languageDropdown">
                                @php
                                    $languages = get_all_languages();
                                    $current_locale = App::currentLocale();
                                @endphp
                                @foreach($languages as $lang)
                                    @if($lang == $current_locale)
                                        <li> <span class="dropdown-item me-4">{{ ucfirst($lang) }} <span class="badge bg-danger">{{ trans('active') }}</span></span> </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item me-4" href="{{ route('language.change', ['locale' => $lang]) }}">
                                                <span>{{ ucfirst($lang) }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
						</div>
						<!-- Translate dropdown END -->

                    @if (Auth::user())

						<!-- Messages dropdown START -->
						<div class="nav-item dropdown h-col d-none d-lg-block">

							<a class="h-message-icon h-icon" href="#header_message_bar"  data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
								<i class="bi bi-chat-left-text"></i> <sup>{{ Auth::user()->messages_count() }}</sup>
							</a>

							<!-- Notification dropdown menu START -->
							<div class="dropdown-menu dropdown-animation dropdown-menu-end dropdown-menu-size-md p-0 shadow-lg border-0">
								<div class="card bg-transparent border-0">
									<div class="card-header bg-transparent py-4 d-flex justify-content-between align-items-center">
										<h6 class="m-0">{{ trans('messages') }} <span class="badge bg-danger bg-opacity-10 text-danger ms-2">{{ Auth::user()->messages_count() }} {{ trans('new') }}</span></h6>

									</div>
									<div data-simplebar
                                    @if (get_setting('site_direction') == 'rtl')
                                        data-simplebar-direction='rtl'
                                    @endif  class="card-body p-0 dropdown-body">
										<ul class="list-group list-unstyled list-group-flush">

                                            @forelse (Auth::user()->chats_nav() as $chat)
                                                @if($chat->user_receiver->id != Auth::user()->id)
                                                    <li>
                                                        <a href="{{ route('user.chats.messages', ['chat_id' => $chat->id]) }}" class="list-group-item-action border-0 d-flex p-3">
                                                            <div class="me-3">
                                                                <div class="avatar avatar-md">
                                                                    <img class="avatar-img rounded-circle" src="{{ my_asset('uploads/users/'.$chat->user_receiver->image) }}" alt="avatar">
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1">{{ $chat->user_receiver->name }}</h6>
                                                            @if($chat->latest_message_nav())
                                                                @if ($chat->latest_message_nav()->file_ext === 'Text')
                                                                    <p class="small m-0">{{ $chat->latest_message_nav()->message }}</p>
                                                                @elseif($chat->latest_message_nav()->file_ext === 'Gif')
                                                                    <p class="small m-0">{{ trans('sent_you_a_gif_image') }}</p>
                                                                @elseif($chat->latest_message_nav()->file_ext === 'Image')
                                                                    <p class="small m-0">{{ trans('sent_you_an_image') }}</p>
                                                                @elseif($chat->latest_message_nav()->file_ext === 'Video')
                                                                    <p class="small m-0">{{ trans('sent_you_a_video') }}</p>
                                                                @elseif($chat->latest_message_nav()->file_ext === 'Audio')
                                                                    <p class="small m-0">{{ trans('sent_you_an_audio') }}</p>
                                                                @elseif($chat->latest_message_nav()->file_ext === 'Zip')
                                                                    <p class="small m-0">{{ trans('sent_a_zip_file') }}</p>
                                                                @endif
                                                            @else
                                                                <p class="small m-0">{{ trans('not_replied') }}</p>
                                                            @endif
                                                            @if($chat->latest_message_nav())
                                                                <small class="text-muted fs-xs">{{ $chat->latest_message_nav()->created_at->diffForHumans() }}</small>
                                                                @if ($chat->unread_messages() > 0)
                                                                    <span class="badge bg-danger bg-opacity-10 text-danger ms-2">{{ $chat->unread_messages() }} {{ trans('unread') }}</span>
                                                                @endif
                                                            @endif
                                                            </div>
                                                        </a>
                                                    </li><!-- Notif item -->
                                                @elseif($chat->user_sender->id != Auth::user()->id)
                                                    <li>
                                                        <a href="{{ route('user.chats.messages', ['chat_id' => $chat->id]) }}" class="list-group-item-action border-0 d-flex p-3">
                                                            <div class="me-3">
                                                                <div class="avatar avatar-md">
                                                                    <img class="avatar-img rounded-circle" src="{{ my_asset('uploads/users/'.$chat->user_sender->image) }}" alt="avatar">
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1">{{ $chat->user_sender->name }}</h6>
                                                            @if($chat->latest_message_nav())
                                                                @if ($chat->latest_message_nav()->file_ext === 'Text')
                                                                    <p class="small m-0">{{ $chat->latest_message_nav()->message }}</p>
                                                                @elseif($chat->latest_message_nav()->file_ext === 'Gif')
                                                                    <p class="small m-0">{{ trans('sent_you_a_gif_image') }}</p>
                                                                @elseif($chat->latest_message_nav()->file_ext === 'Image')
                                                                    <p class="small m-0">{{ trans('sent_you_an_image') }}</p>
                                                                @elseif($chat->latest_message_nav()->file_ext === 'Video')
                                                                    <p class="small m-0">{{ trans('sent_you_a_video') }}</p>
                                                                @elseif($chat->latest_message_nav()->file_ext === 'Audio')
                                                                    <p class="small m-0">{{ trans('sent_you_an_audio') }}</p>
                                                                @elseif($chat->latest_message_nav()->file_ext === 'Zip')
                                                                    <p class="small m-0">{{ trans('sent_a_zip_file') }}</p>
                                                                @endif
                                                            @else
                                                                <p class="small m-0">{{ trans('not_replied') }}</p>
                                                            @endif
                                                            @if($chat->latest_message_nav())
                                                                <small class="text-muted fs-xs">{{ $chat->latest_message_nav()->created_at->diffForHumans() }}</small>
                                                                @if ($chat->unread_messages() > 0)
                                                                    <span class="badge bg-danger bg-opacity-10 text-danger ms-2">{{ $chat->unread_messages() }} {{ trans('unread') }}</span>
                                                                @endif
                                                            @endif
                                                            </div>
                                                        </a>
                                                    </li><!-- Notif item -->
                                                @endif


                                            @empty

                                                <li>
                                                    <a href="#" class="list-group-item-action border-0 d-flex p-3">
                                                        <div class="me-3">
                                                            <div class="avatar avatar-md"></div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ trans('no_chats_available') }}</h6>
                                                        </div>
                                                    </a>
                                                </li><!-- Notif item -->
                                            @endforelse


										</ul>
									</div>
									<!-- Button -->
									<div class="card-footer bg-transparent border-0 py-3 text-center position-relative">
										<a href="{{ route('user.chats') }}" class="stretched-link">{{ trans('see_all_chats') }}</a>
									</div>
								</div>
							</div>
							<!-- Notification dropdown menu END -->
						</div>
						<!-- Messages dropdown END -->

						<!-- Notification dropdown START -->
						<div class="nav-item dropdown h-col d-none d-lg-block">

							<a class="h-notification-icon h-icon" href="javascript:void(0);" onclick="markAsRead()" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
								<i class="bi bi-bell"></i> <sup id="notify">{{ Auth::user()->notification_count() }}</sup>
							</a>

							<!-- Notification dropdown menu START -->
							<div class="dropdown-menu dropdown-animation dropdown-menu-end dropdown-menu-size-md p-0 shadow-lg border-0">
								<div class="card bg-transparent border-0">
									<div class="card-header bg-transparent py-4 d-flex justify-content-between align-items-center">
										<h6 class="m-0">{{ trans('notifications') }} <span class="badge bg-danger bg-opacity-10 text-danger ms-2">{{ Auth::user()->notification_count() }}</span></h6>

									</div>
									<div data-simplebar
                                    @if (get_setting('site_direction') == 'rtl')
                                        data-simplebar-direction='rtl'
                                    @endif class="card-body p-0 dropdown-body">
										<ul class="list-group list-unstyled list-group-flush">

                                            @forelse(Auth::user()->notifications() as $notification)

                                                <li>
                                                    @if ($notification->post_id != null)

                                                        <a href="{{ url('post/'.App\Models\Posts::find($notification->post_id)->post_id.'/'.App\Models\Posts::find($notification->post_id)->slug) }}" class="list-group-item-action border-0 d-flex p-3">

                                                    @else

                                                        <a href="{{ url('profile/'.App\Models\User::find($notification->sender_id)->username.'/') }}" class="list-group-item-action border-0 d-flex p-3">

                                                    @endif
                                                        <div class="me-3">
                                                            <div class="avatar avatar-md">
                                                                <img class="avatar-img rounded-circle" src="{{ my_asset('uploads/users/'.App\Models\User::find($notification->sender_id)->image) }}" alt="avatar">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            @if($notification->notification_type == "Comment")

                                                                <h6 class="mb-1">{{ App\Models\User::find($notification->sender_id)->name }} {{ trans('commented_your_post') }}</h6>

                                                            @elseif($notification->notification_type == "Reply")

                                                                <h6 class="mb-1">{{ App\Models\User::find($notification->sender_id)->name }} {{ trans('replied_your_comment') }}</h6>

                                                            @elseif($notification->notification_type == "Like Post")

                                                                <h6 class="mb-1">{{ App\Models\User::find($notification->sender_id)->name }} {{ trans('liked_your_post') }}</h6>

                                                            @elseif($notification->notification_type == "Like Comment")

                                                                <h6 class="mb-1">{{ App\Models\User::find($notification->sender_id)->name }} {{ trans('liked_your_comment') }}</h6>

                                                            @elseif($notification->notification_type == "Like Reply")

                                                                <h6 class="mb-1">{{ App\Models\User::find($notification->sender_id)->name }} {{ trans('liked_your_reply') }}</h6>

                                                            @elseif($notification->notification_type == "React Post")

                                                                <h6 class="mb-1">{{ App\Models\User::find($notification->sender_id)->name }} {{ trans('reacted_to_your_post') }}</h6>

                                                            @elseif($notification->notification_type == "Following User")

                                                                <h6 class="mb-1">{{ App\Models\User::find($notification->sender_id)->name }} {{ trans('has_followed_you') }}</h6>

                                                            @elseif($notification->notification_type == "Tip")

                                                                <h6 class="mb-1">{{ App\Models\User::find($notification->sender_id)->name }} {{ trans('sent_you_a_tip_of') }} {{ get_setting('currency_symbol') }}{{ App\Models\Payment::find($notification->tip_id)->amount }}.</h6>

                                                            @endif

                                                            @if ($notification->post_id != null)
														        <p class="small m-0">{{ App\Models\Posts::find($notification->post_id)->title }}</p>
                                                            @endif

                                                            <small class="text-muted fs-xs">{{ $notification->created_at->diffForHumans() }}</small>
                                                        </div>
                                                    </a>
                                                </li><!-- Notif item -->

                                            @empty

                                                <li>
                                                    <a href="#" class="list-group-item-action border-0 d-flex p-3">
                                                        <div class="me-3">
                                                            <div class="avatar avatar-md"></div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ trans('no_notifications_available') }}</h6>
                                                        </div>
                                                    </a>
                                                </li><!-- Notif item -->
                                            @endforelse

										</ul>
									</div>
									<!-- Button -->
									<div class="card-footer bg-transparent border-0 py-3 text-center position-relative">
										<a href="{{ route('user.notifications') }}" class="stretched-link">{{ trans('view_all_notifications') }}</a>
									</div>
								</div>
							</div>
							<!-- Notification dropdown menu END -->
						</div>
						<!-- Notification dropdown END -->

						<!-- Account -->
						<div class="nav-item ms-3 dropdown h-col">
							<a href="#" id="navbarShoppingCartDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-dropdown-animation>
							<div class="d-flex">
							<img class="avatar-sm avatar-img rounded-circle" src="{{ my_asset('uploads/users/'.Auth::user()->image) }}" alt="Image Description">
							<div class="profile-text d-none d-sm-block">
								<div class="profile-head text-muted">{{ trans('hello') }},</div>
								<div class="text-nowrap">{{ Auth::user()->name }} </div>
							</div>
							</div>
							</a>

							<div class="dropdown-menu dropdown-animation dropdown-menu-end dropdown-menu-size-md p-2 shadow-lg border-0" aria-labelledby="navbarShoppingCartDropdown" style="min-width: 16rem;">
                                <a class="d-flex align-items-center p-2" href="{{ route('user', ['username' => Auth::user()->username]) }}">
                                    <div class="flex-shrink-0">
                                    <img class="avatar avatar-lg" src="{{ my_asset('uploads/users/'.Auth::user()->image) }}" alt="Image Description">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                    <span class="d-block fw-semibold">{{ Auth::user()->name }}</span>
                                    @if(get_setting('payments_on_site') == 'Yes')
                                        <span class="d-block text-muted small">{{ trans('wallet') }} : {{ get_setting('currency_symbol') }}{{ Auth::user()->wallet }}</span>
                                        <span class="d-block text-muted small">{{ trans('earnings') }} : {{ get_setting('currency_symbol') }}{{ Auth::user()->earnings }}</span>
                                    @endif
                                    </div>
                                </a>

                                <div class="dropdown-divider my-3"></div>
                                <a class="dropdown-item" href="{{ route('user', ['username' => Auth::user()->username]) }}"><span class="dropdown-item-icon"><i class="bi-person"></i></span>{{ trans('profile') }} </a>

                                @if(Auth::user()->role === "Admin" || Auth::user()->role === "Moderator")
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}"><span class="dropdown-item-icon"><i class="bi-person"></i></span>{{ trans('admin') }} {{ trans('dashboard') }}</a>
                                @endif
                                <a class="dropdown-item" href="{{ route('user.overview') }}"><span class="dropdown-item-icon"><i class="bi-house-door"></i></span> {{ trans('user') }} {{ trans('dashboard') }}</a>

                                <a class="dropdown-item" href="{{ route('user.posts.list') }}"><span class="dropdown-item-icon"><i class="bi bi-journals"></i></span> {{ trans('posts') }} </a>
                                <a class="dropdown-item d-block d-sm-none" href="{{ route('user.notifications') }}">
                                    <span class="dropdown-item-icon"><i class="bi bi-bell"></i></span> {{ trans('notifications') }}
                                    <span class="badge bg-red ms-auto">{{ Auth::user()->notification_count() }}</span>
                                </a>
                                <a class="dropdown-item d-block d-sm-none" href="{{ route('user.chats') }}">
                                    <span class="dropdown-item-icon"><i class="bi bi-chat-left-text"></i></span> {{ trans('messages') }}
                                    <span class="badge bg-red ms-auto">{{ Auth::user()->messages_count() }}</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('user.profile') }}"><span class="dropdown-item-icon"><i class="bi bi-gear"></i></span> {{ trans('settings') }} </a>
                                @if(get_setting('payments_on_site') == 'Yes')
                                    <a class="dropdown-item" href="{{ route('user.wallet') }}"><span class="dropdown-item-icon"><i class="bi-wallet2"></i></span> {{ trans('wallet') }}</a>
                                @endif

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <span class="dropdown-item-icon"><i class="bi-box-arrow-right"></i></span> {{ trans('logout') }}</a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                </form>
							</div>
						</div><!-- End Account -->

                    @else

                      <div class="nav-item">
                        <a href="{{ route('auth.login') }}" class="btn btn-mint rounded-5 px-4 py-2 ms-3">{{ trans('login') }}</a>
                        <a href="{{ route('auth.register') }}" class="btn btn-red rounded-5 px-4 py-2">{{ trans('register') }}</a>
                      </div>

                    @endif

						<!-- Mobile Toggle -->
						<div class="h-col h-toggler d-xl-none">
							<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_Header_01" aria-controls="offcanvas_Header_01">
							 <span class="px-navbar-toggler-icon"></span>
							</button>
						</div>
					</div>
				</div>
			</nav>
		</header>


            <!-- start search block -->
            <div class="search-overlay" data-toggle="search-close"></div>
            <div data-simplebar class="search-block overflow-auto bg-body">
                <div data-toggle="search-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>

                <div class="is-hidden">
                    <input type="text" id="search" placeholder="Search users, tags & posts ..." aria-label="search-query">
                </div>
                <div id="searchResults">

                    <div class="mt-4 pt-2 is-hidden popular-people">
                        <div class="search-meta">
                            <div class="section-title mb-3">
                                <p class="title h4 mb-0">Popular Users</p>
                            </div>

                            <div class="people-grid">

                                @foreach (\App\Models\User::withCount('search_views')->orderBy('search_views_count', 'desc')->limit(5)->get() as $user)
                                    <a href="{{ route('user', ['username' => $user->username]) }}" class="people-grid-item">
                                        <div class="people-grid-image">
                                        <img src="{{ my_asset('uploads/users/'.$user->image) }}" alt="Browse Vectors">
                                        </div>
                                        <p class="people-grid-title">{{ $user->name }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-2 is-hidden popular-tags">
                        <div class="search-meta">
                            <div class="section-title mb-3">
                                <p class="title h4 mb-0">Popular Tags</p>
                            </div>
                            <div class="tags">
                                <a href="{{ route('tags') }}" class="tag-link green">All Tags <i class="bi bi-arrow-right"></i></a>
                                @foreach (\Conner\Tagging\Model\Tag::orderByDesc('count')->limit(10)->get() as $tag)
                                    <a href="{{ route('tag', ['slug' => $tag->slug]) }}" class="tag-link">{{ $tag->name }} ({{ $tag->count }})</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-2 is-hidden recent-posts">
                        <div class="search-meta">
                            <div class="section-title mb-3 pb-1">
                                <p class="title h4 mb-0">Popular Posts</p>
                            </div>
                            <div class="row gy-4">

                                @foreach (\App\Models\Posts::where('status', 1)->where('public', 1)->withCount('search_views')->orderBy('search_views_count', 'desc')->limit(6)->get() as $post)
                                    <div class="col-md-6">
                                        <article class="row gx-3 align-items-start position-relative">
                                            <div class="col-auto">
                                            <img loading="lazy" class="img-fluid" src="{{ my_asset('uploads/users/'.$post->user->image) }}" alt="image" width="75" height="75">
                                            </div>
                                            <div class="col">
                                            <span class="d-block lh-1 mb-2 zIndexed line-clamp clamp-2">
                                                <a class="small lh-1 text-muted" href="{{ route('category', ['slug' => $post->category->slug]) }}">{{ $post->category->name }}</a>
                                            </span>
                                            <h6><a class="fs-lg lh-base text-link stretched-link" href="{{ route('home.post', ['post_id' => $post->post_id, 'slug' => $post->slug]) }}">
                                                {{ $post->title }}</a>
                                            </h6>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- end search block -->
