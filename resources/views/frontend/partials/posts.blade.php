

@forelse($posts as $post)
    <div class="post-box card d-flex mx-0 {{ $post->solved == '1' ? 'solved' : '' }}" data-aos="fade-up" data-aos-easing="linear">

        @if ($post->solved == '1' && $post->closed == '1')
        <div class="post-badge"> <i class="bi bi-check2-circle"></i> {{ trans('solved') }} & <i class="bi bi-lock"></i> {{ trans('closed') }}</div>
        @elseif ($post->solved == '1' && $post->closed == '0')
        <div class="post-badge"> <i class="bi bi-check2-circle"></i> {{ trans('solved') }} </div>
        @elseif ($post->solved == '0' && $post->closed == '1')
        <div class="post-badge"> <i class="bi bi-lock"></i> {{ trans('closed') }} </div>
        @endif

            <div class="card-header card-header-action">
                <div class="media align-items-center">
                    <div class="media-head me-2">
                        <div class="avatar custom-tooltip">
                            <a href="{{ route('user', ['username' => $post->user->username]) }}">
                                <img src="{{ my_asset('uploads/users/'.$post->user->image) }}" alt="user" class="avatar-img rounded-circle">
                                @if(Cache::has('user-is-online-' . $post->user->id))
                                    <span class="user_online"></span>
                                @endif
                            </a>

                            <div class="custom-tooltip-dropdown">
                                <div class="author-action d-flex flex-wrap align-items-center mb-3">
                                    <div class="flex-shrink-0 avatar"><img src="{{ my_asset('uploads/users/'.$post->user->image) }}" class="rounded-circle" alt="avatar"></div>
                                    <div class="ms-2">
                                        <span class="author-username">{{ $post->user->total_points() }} {{ trans('points') }}</span>
                                        <span class="author-follow-text">{{ $post->user->followers->count() }} {{ trans('followers') }}</span>
                                    </div>
                                </div>
                                <h6 class="author-name mb-1">{{ $post->user->name }}</h6>
                                <p class="author-desc smaller mb-3">{{ Str::limit($post->user->bio, 80) }}</p>
                                <div class="follow-wrap mb-3">
                                    <h6 class="mb-1 smaller text-uppercase">{{ trans('followed_by') }}</h6>
                                    <div class="avatar-group">
                                        @foreach($post->user->followers()->with('followers')->limit(5)->get() as $follower)
                                        <a href="{{ route('user', ['username' => $follower->username]) }}" class="avatar-group-avatar">
                                            <img src="{{ my_asset('uploads/users/'.$follower->image) }}" alt="avatar">
                                        </a>
                                        @endforeach
                                    </div>
                                </div>

                                <a href="{{ route('user', ['username' => $post->user->username]) }}" class="btn btn-sm btn-mint rounded-pill"><span>{{ trans('view') }} {{ trans('profile') }}</span></a>
                            </div>

                        </div>
                    </div>
                    <div class="media-body">
                        <a href="{{ route('user', ['username' => $post->user->username]) }}">{{ $post->user->name }}
                            @if($post->user->verified == 1)
                                <span class="verified-badge" data-bs-toggle="tooltip" aria-label="{{ trans('verified_user') }}" data-bs-original-title="{{ trans('verified_user') }}">
                                    <i class="bi bi-patch-check"></i>
                                </span>
                            @endif

                            @if($post->user->role == 'Admin')
                                <span class="mod" data-bs-toggle="tooltip" aria-label="Admin" data-bs-original-title="Admin">
                                    <i class="bi bi-octagon-half"></i>
                                </span>
                            @endif

                            @if($post->user->role == 'Moderator')
                                <span class="mod" data-bs-toggle="tooltip" aria-label="Moderator" data-bs-original-title="Moderator">
                                    <i class="bi bi-hexagon-half"></i>
                                </span>
                            @endif

                            @if ($post->user->user_sub($post->user->id)->plan_id != 1)
                                <span class="mod" data-bs-toggle="tooltip" aria-label="Pro" data-bs-original-title="Pro">
                                    <i class="bi bi-star-half"></i>
                                </span>
                            @endif
                        </a>
                        <div class="fs-7"><span> {{ $post->created_at->diffForHumans() }} {{ trans('in') }} </span> <a href="{{ route('category', ['slug' => $post->category->slug]) }}" class="cat">{{ $post->category->name }}</a></div>
                    </div>
                </div>
                    <div class="card-action-wrap">
                        @if ($post->pinned == '1')
                            <p class="me-2 text-danger d-flex"><i class="bi bi-pin"></i> <span class="d-none d-sm-block">{{ trans('pinned') }}</span></p>
                        @endif
                        @if (Auth::check())
                            @if (Auth::user()->id === $post->user->id)
                                <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></a>
                                <div class="dropdown-menu dropdown-menu-end">

                                    @if ($post->pinned == '0')
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="pin('{{ route('user.posts.pin') }}','{{ $post->id }}');"><i class="bi bi-pin me-2"></i> {{ trans('pin') }}</a>
                                    @elseif ($post->pinned == '1')
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="pin('{{ route('user.posts.unpin') }}','{{ $post->id }}');"><i class="bi bi-pin me-2"></i> {{ trans('unpin') }}</a>
                                    @endif
                                    @if ($post->closed == '1')
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="open_post({{ $post->id }})"><i class="bi bi-unlock me-2"></i> {{ trans('open') }} {{ trans('post') }}</a>
                                    @elseif ($post->closed == '0')
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="close_post({{ $post->id }})"><i class="bi bi-lock me-2"></i> {{ trans('close') }} {{ trans('post') }}</a>
                                    @endif

                                    <a class="dropdown-item" href="{{ route('user.posts.edit', ['id' => $post->id]) }}"><i class="bi bi-pencil me-2"></i> {{ trans('edit') }} {{ trans('post') }}</a>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="delete_item('{{ route('user.posts.destroy') }}','{{ $post->id }}','{{ trans('delete_this_post') }}');"><i class="bi bi-trash3 me-2"></i> {{ trans('delete') }} {{ trans('post') }}</a>
                                </div>
                            @endif
                        @endif
                    </div>
            </div>
            <div class="card-body">
                <h3><a href="{{ route('home.post', ['post_id' => $post->post_id, 'slug' => $post->slug]) }}">{{ $post->title }}</a></h3>
                {!! strip_tags(substr($post->body, 0, get_setting('maximum_preview_chars'))) !!}

                <div class="tags my-4">
                    @foreach ($post->tags as $tag)
                        <a href="{{ route('tag', ['slug' => $tag->slug]) }}" class="tag-link">{{ $tag->name }}</a>
                    @endforeach
                </div>

            @if ($post->likes == '1')
                @if (get_setting('react_like') == 'React')
                        @php $reactionSummary = $post->reaction_summary->toArray(); @endphp
                        @php if (array_key_exists('like', $reactionSummary) || array_key_exists('love', $reactionSummary) || array_key_exists('haha', $reactionSummary)
                        || array_key_exists('wow', $reactionSummary) || array_key_exists('yay', $reactionSummary) || array_key_exists('sad', $reactionSummary)
                    || array_key_exists('mad', $reactionSummary)) {  @endphp
                            <ul class="reaction-list">
                                @php if (array_key_exists('like', $reactionSummary)) {  @endphp
                                <li class="reaction-box">
                                    <img src="{{ my_asset('uploads/reactions/like.png') }}">
                                    <p>@php echo $likesCount = $reactionSummary['like']; @endphp</p>
                                </li>
                                @php } @endphp
                                @php if (array_key_exists('love', $reactionSummary)) {  @endphp
                                <li class="reaction-box">
                                    <img src="{{ my_asset('uploads/reactions/love.png') }}">
                                    <p>@php echo $lovesCount = $reactionSummary['love']; @endphp</p>
                                </li>
                                @php } @endphp
                                @php if (array_key_exists('haha', $reactionSummary)) {  @endphp
                                <li class="reaction-box">
                                    <img src="{{ my_asset('uploads/reactions/haha.png') }}">
                                    <p>@php echo $hahasCount = $reactionSummary['haha']; @endphp</p>
                                </li>
                                @php } @endphp
                                @php if (array_key_exists('wow', $reactionSummary)) {  @endphp
                                <li class="reaction-box">
                                    <img src="{{ my_asset('uploads/reactions/wow.png') }}">
                                    <p>@php echo $wowsCount = $reactionSummary['wow']; @endphp</p>
                                </li>
                                @php } @endphp
                                @php if (array_key_exists('yay', $reactionSummary)) {  @endphp
                                <li class="reaction-box">
                                    <img src="{{ my_asset('uploads/reactions/yay.png') }}">
                                    <p>@php echo $yaysCount = $reactionSummary['yay']; @endphp</p>
                                </li>
                                @php } @endphp
                                @php if (array_key_exists('sad', $reactionSummary)) {  @endphp
                                <li class="reaction-box">
                                    <img src="{{ my_asset('uploads/reactions/sad.png') }}">
                                    <p>@php echo $sadsCount = $reactionSummary['sad']; @endphp</p>
                                </li>
                                @php } @endphp
                                @php if (array_key_exists('mad', $reactionSummary)) {  @endphp
                                <li class="reaction-box">
                                    <img src="{{ my_asset('uploads/reactions/mad.png') }}">
                                    <p>@php echo $madsCount = $reactionSummary['mad']; @endphp</p>
                                </li>
                                @php } @endphp
                            </ul>
                        @php } @endphp
                @endif
            @endif

            </div>

            <div class="card-footer">

                <div class="qa-stats">
                @if ($post->likes == '1')
                    @if (get_setting('react_like') == 'React')
                        <div class="qa-item post-share react-sec">
                            <a href="javascript:void(0);" class="qa-link">
                                @if (Auth::check() && $post->is_reacted)
                                @php $user_react = json_decode($post->reacted(),true); @endphp

                                @php if ($user_react['type'] == 'like') { @endphp
                                    <div class="qa-icon shown" id="react-icon-like-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/like.png') }}" class="avatar avatar-small"></div>
                                @php }else{ @endphp
                                    <div class="qa-icon hiden" id="react-icon-like-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/like.png') }}" class="avatar avatar-small"></div>
                                @php  } @endphp
                                @php if ($user_react['type'] == 'love') { @endphp
                                    <div class="qa-icon shown" id="react-icon-love-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/love.png') }}" class="avatar avatar-small"></div>
                                @php }else{ @endphp
                                    <div class="qa-icon hiden" id="react-icon-love-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/love.png') }}" class="avatar avatar-small"></div>
                                @php  } @endphp
                                @php if ($user_react['type'] == 'haha') { @endphp
                                    <div class="qa-icon shown" id="react-icon-haha-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/haha.png') }}" class="avatar avatar-small"></div>
                                @php }else{ @endphp
                                    <div class="qa-icon hiden" id="react-icon-haha-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/haha.png') }}" class="avatar avatar-small"></div>
                                @php  } @endphp
                                @php if ($user_react['type'] == 'wow') { @endphp
                                    <div class="qa-icon shown" id="react-icon-wow-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/wow.png') }}" class="avatar avatar-small"></div>
                                @php }else{ @endphp
                                    <div class="qa-icon hiden" id="react-icon-wow-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/wow.png') }}" class="avatar avatar-small"></div>
                                @php  } @endphp
                                @php if ($user_react['type'] == 'yay') { @endphp
                                    <div class="qa-icon shown" id="react-icon-yay-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/yay.png') }}" class="avatar avatar-small"></div>
                                @php }else{ @endphp
                                    <div class="qa-icon hiden" id="react-icon-yay-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/yay.png') }}" class="avatar avatar-small"></div>
                                @php  } @endphp
                                @php if ($user_react['type'] == 'sad') { @endphp
                                    <div class="qa-icon shown" id="react-icon-sad-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/sad.png') }}" class="avatar avatar-small"></div>
                                @php }else{ @endphp
                                    <div class="qa-icon hiden" id="react-icon-sad-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/sad.png') }}" class="avatar avatar-small"></div>
                                @php  } @endphp
                                @php if ($user_react['type'] == 'mad') { @endphp
                                    <div class="qa-icon shown" id="react-icon-mad-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/mad.png') }}" class="avatar avatar-small"></div>
                                @php }else{ @endphp
                                    <div class="qa-icon hiden" id="react-icon-mad-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/mad.png') }}" class="avatar avatar-small"></div>
                                @php  } @endphp
                            @else
                                <div class="qa-icon hiden" id="react-icon-like-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/like.png') }}" class="avatar avatar-small"></div>
                                <div class="qa-icon hiden" id="react-icon-love-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/love.png') }}" class="avatar avatar-small"></div>
                                <div class="qa-icon hiden" id="react-icon-haha-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/haha.png') }}" class="avatar avatar-small"></div>
                                <div class="qa-icon hiden" id="react-icon-wow-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/wow.png') }}" class="avatar avatar-small"></div>
                                <div class="qa-icon hiden" id="react-icon-yay-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/yay.png') }}" class="avatar avatar-small"></div>
                                <div class="qa-icon hiden" id="react-icon-sad-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/sad.png') }}" class="avatar avatar-small"></div>
                                <div class="qa-icon hiden" id="react-icon-mad-{{ $post->id }}"><img src="{{ my_asset('uploads/reactions/mad.png') }}" class="avatar avatar-small"></div>
                            @endif
                                <div class="qa-text">{{ trans('react') }}</div>
                                <div class="qa-text me-2">({{ $post->total_reactions()->count() }})</div>
                            </a>
                            <ul class="share-list">
                                <li><a class="reaction-1" href="javascript:void(0);" onclick="reactPost({{ $post->id }}, 'like')" data-bs-toggle="tooltip" aria-label="like" data-bs-original-title="like">
                                    <img src="{{ my_asset('uploads/reactions/like.png') }}"></a>
                                </li>
                                <li><a class="reaction-2" href="javascript:void(0);" onclick="reactPost({{ $post->id }}, 'love')" data-bs-toggle="tooltip" aria-label="love" data-bs-original-title="love">
                                    <img src="{{ my_asset('uploads/reactions/love.png') }}"></a>
                                </li>
                                <li><a class="reaction-3" href="javascript:void(0);" onclick="reactPost({{ $post->id }}, 'haha')" data-bs-toggle="tooltip" aria-label="haha" data-bs-original-title="haha">
                                    <img src="{{ my_asset('uploads/reactions/haha.png') }}"></a>
                                </li>
                                <li><a class="reaction-4" href="javascript:void(0);" onclick="reactPost({{ $post->id }}, 'wow')" data-bs-toggle="tooltip" aria-label="wow" data-bs-original-title="wow">
                                    <img src="{{ my_asset('uploads/reactions/wow.png') }}"></a>
                                </li>
                                <li><a class="reaction-5" href="javascript:void(0);" onclick="reactPost({{ $post->id }}, 'yay')" data-bs-toggle="tooltip" aria-label="yay" data-bs-original-title="yay">
                                    <img src="{{ my_asset('uploads/reactions/yay.png') }}"></a>
                                </li>
                                <li><a class="reaction-6" href="javascript:void(0);" onclick="reactPost({{ $post->id }}, 'sad')" data-bs-toggle="tooltip" aria-label="sad" data-bs-original-title="sad">
                                    <img src="{{ my_asset('uploads/reactions/sad.png') }}"></a>
                                </li>
                                <li><a class="reaction-7" href="javascript:void(0);" onclick="reactPost({{ $post->id }}, 'mad')" data-bs-toggle="tooltip" aria-label="mad" data-bs-original-title="mad">
                                    <img src="{{ my_asset('uploads/reactions/mad.png') }}"></a>
                                </li>
                            </ul>

                            @if ($post->user_reactions()->count() > 0)
                                <ul>
                                    @foreach ($post->user_reactions() as $view)
                                        <li><a href="{{ route('user', ['username' => $view->user->username]) }}"><img src="{{ my_asset('uploads/users/'.$view->user->image) }}" alt="User"></a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @elseif (get_setting('react_like') == 'Like')
                        <div class="qa-item">
                            <a href="javascript:void(0);" onclick="likePost({{ $post->id }})" class="qa-link">
                                <div class="qa-icon"><i id="like-icon-{{ $post->id }}" class="bi bi-heart-fill @if(Auth::check()) @if(Auth::user()->hasLiked($post)) text-danger @else text-muted @endif @else text-muted @endif"></i></div>
                                <div class="qa-text" id="like-{{ $post->id }}">@json($post->likers()->count())</div>
                            </a>
                            <div class="qa-text me-2">{{ trans('likes') }}</div>
                            @if ($post->user_likes()->count() > 0)
                                <ul>
                                    @foreach ($post->user_likes() as $view)
                                        <li><a href="{{ route('user', ['username' => $view->user->username]) }}"><img src="{{ my_asset('uploads/users/'.$view->user->image) }}" alt="User"></a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                @endif
                    <div class="qa-item">
                        <div class="qa-icon"><i class="bi bi-eye"></i></div>
                        <div class="qa-text">{{ $post->views()->count() }}</div>
                        <div class="qa-text">{{ trans('views') }}</div>
                    </div>
                @if ($post->comments == '1')
                    <div class="qa-item">
                        <a href="{{ route('home.post', ['post_id' => $post->post_id, 'slug' => $post->slug]) }}#comments" class="qa-link">
                            <div class="qa-icon"><i class="bi bi-chat-dots"></i></div>
                            <div class="qa-text">{{ $post->comments()->count() }}</div>
                            <div class="qa-text">{{ trans('comments') }}</div>
                        </a>
                    </div>
                @endif
                    <div class="qa-item">
                    <a href="javascript:void(0);" onclick="savePost({{ $post->id }})" class="qa-link">
                        <div class="qa-icon"><i id="save-icon-{{ $post->id }}" class="bi bi-bookmark-fill @if(Auth::check()) @if(Auth::user()->hasFavorited($post)) text-danger @else text-muted @endif @else text-muted @endif"></i></div>
                        <div class="qa-text" id="save-{{ $post->id }}">@json($post->favoriters()->count())</div>
                    </a>
                    </div>
                    <div class="qa-item">
                    <a href="javascript:void(0)" onclick="report('{{ route('report.post') }}','{{ $post->id }}','Report this Post');" class="qa-link">
                        <div class="qa-icon"><i class="bi bi-flag"></i></div>
                        <div class="qa-text">{{ trans('report') }}</div>
                    </a>
                    </div>
                    <div class="qa-item post-share">
                        <a href="javascript:void(0);" class="qa-link">
                        <div class="qa-icon"><i class="bi bi-share"></i></div>
                        <div class="qa-text">{{ $post->shares()->count() }}</div>
                        <div class="qa-text">{{ trans('shares') }}</div>
                        </a>
                        <ul class="share-list">
                            <li><a onclick="share('{{ route('share') }}','{{ $post->id }}','Facebook');" href="https://www.facebook.com/sharer/sharer.php?u={{ url('post/'.$post->post_id.'/'.$post->slug) }}&t={{ $post->title }}" target="_blank" class="color-fb"><i class="bi bi-facebook"></i></a></li>
                            <li><a onclick="share('{{ route('share') }}','{{ $post->id }}','Twitter');" href="https://twitter.com/share?url={{ url('post/'.$post->post_id.'/'.$post->slug) }}&text={{ $post->title }}" target="_blank" class="color-twitter"><i class="bi bi-twitter"></i></a></li>
                            <li><a onclick="share('{{ route('share') }}','{{ $post->id }}','Whatsapp');" href="https://wa.me/?text={{ $post->title }}%20{{ url('post/'.$post->post_id.'/'.$post->slug) }}" data-action="share/whatsapp/share" target="_blank" class="color-whatsapp"><i class="bi bi-whatsapp"></i></a></li>
                            <li><a onclick="share('{{ route('share') }}','{{ $post->id }}','Reddit');" href="https://www.reddit.com/submit?url={{ url('post/'.$post->post_id.'/'.$post->slug) }}&title={{ $post->title }}" target="_blank" class="color-reddit"><i class="bi bi-reddit"></i></a></li>
                            <li><a onclick="share('{{ route('share') }}','{{ $post->id }}','LinkedIn');" href="https://www.linkedin.com/shareArticle?mini=true&url={{ url('post/'.$post->post_id.'/'.$post->slug) }}&title={{ $post->title }}" target="_blank" class="color-linkedin"><i class="bi bi-linkedin"></i></a></li>
                        </ul>
                    </div>
                </div>

            </div>

    </div><!--/post-box-->
@empty

    <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
        <div class="dashboard-body">
            <div class="upload-image my-3">
                <h4 class="mb-3">{{ trans('no_posts_available') }}.</h4>
            </div>

        </div>
    </div><!--/dashboard-card-->

@endforelse


@if ($posts->hasPages())
    <div>
        {!! $posts->appends(request()->all())->links('layouts.pagination.new') !!}
    </div>
@endif

