

@foreach ($chats as $chat)

    @if($chat->user_receiver->id != Auth::user()->id)

        <li class="position-relative" data-bs-dismiss="offcanvas">
            <a href="{{ route('user.chats.messages', ['chat_id' => $chat->id]) }}" class="nav-link">
                <div class="d-flex">
                    <div class="flex-shrink-0 avatar avatar-md me-2">
                        <img class="avatar-img rounded-circle" src="{{ my_asset('uploads/users/'.$chat->user_receiver->image) }}" alt="User">
                        @if(Cache::has('user-is-online-' . $chat->user_receiver->id))
                            <span class="user_online"></span>
                        @endif
                    </div>
                    <div class="flex-grow-1 message-by">
                        <div class="d-flex justify-content-between">
                            <h5>
                                {{ $chat->user_receiver->name }}
                                @if ($chat->unread_messages() > 0)
                                <span class="badge bg-danger bg-opacity-10 text-danger ms-2">{{ $chat->unread_messages() }}</span>
                                @endif
                            </h5>
                            @if($chat->latest_message())
                                <span>{{ $chat->latest_message()->created_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    @if($chat->latest_message())
                        @if ($chat->latest_message()->file_ext === 'Text')
                            <p>{{ $chat->latest_message()->message }}</p>
                        @elseif($chat->latest_message()->file_ext === 'Gif')
                            <p>{{ trans('gif_image') }}</p>
                        @elseif($chat->latest_message()->file_ext === 'Image')
                            <p>{{ trans('image_sent') }}</p>
                        @elseif($chat->latest_message()->file_ext === 'Video')
                            <p>{{ trans('video_sent') }}</p>
                        @elseif($chat->latest_message()->file_ext === 'Audio')
                            <p>{{ trans('audio_sent') }}</p>
                        @elseif($chat->latest_message()->file_ext === 'Zip')
                            <p>{{ trans('zip_sent') }}</p>
                        @endif

                    @endif
                    </div>
                </div>
            </a>
        </li><!-- Chat user item -->

    @elseif($chat->user_sender->id != Auth::user()->id)

        <li class="position-relative" data-bs-dismiss="offcanvas">
            <a href="{{ route('user.chats.messages', ['chat_id' => $chat->id]) }}" class="nav-link">
                <div class="d-flex">
                    <div class="flex-shrink-0 avatar avatar-md me-2">
                        <img class="avatar-img rounded-circle" src="{{ my_asset('uploads/users/'.$chat->user_sender->image) }}" alt="User">
                        @if(Cache::has('user-is-online-' . $chat->user_sender->id))
                            <span class="user_online"></span>
                        @endif
                    </div>
                    <div class="flex-grow-1 message-by">
                        <div class="d-flex justify-content-between">
                            <h5>
                                {{ $chat->user_sender->name }}
                                @if ($chat->unread_messages() > 0)
                                <span class="badge bg-danger bg-opacity-10 text-danger ms-2">{{ $chat->unread_messages() }}</span>
                                @endif
                            </h5>
                            @if($chat->latest_message())
                                <span>{{ $chat->latest_message()->created_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    @if($chat->latest_message())
                        @if ($chat->latest_message()->file_ext === 'Text')
                            <p>{{ $chat->latest_message()->message }}</p>
                        @elseif($chat->latest_message()->file_ext === 'Gif')
                            <p>{{ trans('gif_image') }}</p>
                        @elseif($chat->latest_message()->file_ext === 'Image')
                            <p>{{ trans('image_sent') }}</p>
                        @elseif($chat->latest_message()->file_ext === 'Video')
                            <p>{{ trans('video_sent') }}</p>
                        @elseif($chat->latest_message()->file_ext === 'Audio')
                            <p>{{ trans('audio_sent') }}</p>
                        @elseif($chat->latest_message()->file_ext === 'Zip')
                            <p>{{ trans('zip_sent') }}</p>
                        @endif
                    @endif
                    </div>
                </div>
            </a>
        </li><!-- Chat user item -->

    @endif

@endforeach
