@foreach ($users as $user)

   @if ($user->id != Auth::user()->id)
    <div class="media align-items-center my-4">
        <div class="media-head me-2">
            <div class="avatar custom-tooltip">
                <img src="{{ my_asset('uploads/users/'.$user->image) }}" alt="user" class="avatar-img rounded-circle">
            </div>
        </div>
        <div class="media-body d-flex justify-content-between">
            <p>{{ $user->name }}</p>
            <a href="javascript:void(0)" onclick="create_chat({{ $user->id }});" class="btn btn-mint btn-xs rounded-pill">{{ trans('create_chat') }}</a>
        </div>
    </div>
   @endif

@endforeach
