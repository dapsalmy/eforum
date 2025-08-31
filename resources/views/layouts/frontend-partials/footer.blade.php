
		<!-- ==============================================
		 Footer
		=============================================== -->
        <footer class="footer">
			<div class="footer-top">
				<div class="container">
					<div class="inner">
						<div class="row">
							<div class="col-lg-5 col-md-6"

                                @if (get_setting('site_direction') == 'ltr')
                                    data-aos="fade-right" data-aos-easing="ease-in-sine"
                                @elseif (get_setting('site_direction') == 'rtl')
                                    data-aos="fade-left" data-aos-easing="ease-in-sine"
                                @endif
                            >
								<div class="footer-item footer-about">
									<div class="logo">
										<a href="index.html">
											<img src="{{ my_asset('uploads/settings/'.get_setting('logo_dark')) }}" alt="Logo">
										</a>
									</div>
									<p>{{ get_setting('site_description') }}</p>
									<div class="social">
										<ul>
											<li class="facebook">
												<a href="#"><i class="bi bi-facebook"></i></a>
											</li>
											<li class="twitter">
												<a href="#"><i class="bi bi-twitter"></i></a>
											</li>
											<li class="youtube">
												<a href="#"><i class="bi bi-youtube"></i></a>
											</li>
											<li class="instagram">
												<a href="#"><i class="bi bi-instagram"></i></a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="col-lg-2"

                            @if (get_setting('site_direction') == 'ltr')
                                data-aos="fade-right" data-aos-easing="ease-in-sine" data-aos-delay="100"
                            @elseif (get_setting('site_direction') == 'rtl')
                                data-aos="fade-left" data-aos-easing="ease-in-sine" data-aos-delay="100"
                            @endif
                             >
								<div class="footer-item footer-menu">
									<h6>Forum</h6>
									<ul>
										<li><a href="{{ route('home.posts') }}">{{ trans('discussions') }}</a></li>
										<li><a href="{{ route('users') }}">{{ trans('users') }}</a></li>
										<li><a href="{{ route('categories') }}">{{ trans('categories') }}</a></li>
										<li><a href="{{ route('tags') }}">{{ trans('tags') }}</a></li>
										<li><a href="{{ route('stats') }}">{{ trans('stats') }} & {{ trans('online_users') }}</a></li>
									</ul>
								</div>
							</div>
							<div class="col-lg-2"

                            @if (get_setting('site_direction') == 'ltr')
                                data-aos="fade-right" data-aos-easing="ease-in-sine" data-aos-delay="200"
                            @elseif (get_setting('site_direction') == 'rtl')
                                data-aos="fade-left" data-aos-easing="ease-in-sine" data-aos-delay="200"
                            @endif>
								<div class="footer-item footer-menu">
									<h6>Pages</h6>
									<ul>
										<li><a href="{{ route('about') }}">{{ trans('about') }}</a></li>
										<li><a href="{{ route('faqs') }}">{{ trans('faqs') }}</a></li>
										<li><a href="{{ route('leaderboard') }}">{{ trans('leaderboard') }}</a></li>
										<li><a href="{{ route('badges') }}">{{ trans('badges') }}</a></li>
										<li><a href="{{ route('rules') }}">{{ trans('community_rules') }}</a></li>
									</ul>
								</div>
							</div>
							<div class="col-lg-3"

                            @if (get_setting('site_direction') == 'ltr')
                                data-aos="fade-right" data-aos-easing="ease-in-sine" data-aos-delay="300"
                            @elseif (get_setting('site_direction') == 'rtl')
                                data-aos="fade-left" data-aos-easing="ease-in-sine" data-aos-delay="300"
                            @endif>
								<div class="footer-item footer-menu">
									<h6>{{ trans('contact_us') }}</h6>
									<div class="address">
										<ul>
											<li>
												<p>{{ trans('address') }}</p>
												<strong>{{ get_setting('contact_address') }}</strong>
											</li>
											<li>
												<p>{{ trans('email') }}</p>
												<strong>{{ get_setting('contact_email') }}</strong>
											</li>
											<li>
												<p>{{ trans('contact') }}</p>
												<strong>{{ get_setting('contact_phone') }}</strong>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="footer-bottom"

            @if (get_setting('site_direction') == 'ltr')
                data-aos="fade-right" data-aos-easing="linear"
            @elseif (get_setting('site_direction') == 'rtl')
                data-aos="fade-left" data-aos-easing="linear"
            @endif
            >
				<div class="container">
					<div class="inner">
						<div class="copyright">© {{ date('Y') }} {{ get_setting('site_name') }}. {{ trans('all_rights_reserved') }}</div>
						<div class="menu">
							<ul>
								<li><a href="{{ route('terms') }}">{{ trans('terms') }}</a></li>
								<li><a href="{{ route('privacy') }}">{{ trans('privacy_policy') }}</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</footer>
