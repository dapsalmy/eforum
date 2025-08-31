
<div class="mt-4 pt-2 is-hidden popular-people">
    <div class="search-meta">
        <div class="section-title mb-3">
            <p class="title h4 mb-0">Users</p>
        </div>

        <div class="people-grid">

            @foreach ($users as $user)
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
            <p class="title h4 mb-0">Tags</p>
        </div>
        <div class="tags">
            <a href="{{ route('tags') }}" class="tag-link green">All Tags <i class="bi bi-arrow-right"></i></a>
            @foreach ($tags as $tag)
                <a href="{{ route('tag', ['slug' => $tag->slug]) }}" class="tag-link">{{ $tag->name }} ({{ $tag->count }})</a>
            @endforeach
        </div>
    </div>
</div>
<div class="mt-4 pt-2 is-hidden recent-posts">
    <div class="search-meta">
        <div class="section-title mb-3 pb-1">
            <p class="title h4 mb-0">Posts</p>
        </div>
        <div class="row gy-4">

            @foreach ($posts as $post)
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
