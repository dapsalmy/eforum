@foreach ($posts as $post)
   <li><a href="{{ route('home.post', ['post_id' => $post->post_id, 'slug' => $post->slug])}}" class="dropdown-item"> {{ $post->title }}</a></li>
@endforeach
