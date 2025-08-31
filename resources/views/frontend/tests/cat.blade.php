@foreach(App\Models\Admin\Categories::limit(1)->get() as $category)
 <img src="{{ my_asset('uploads/categories/'.$category->image) }}" class="img-fluid cat" alt="Image" width="200px" height="200px">
 <h4>{{ $category->name }}</h4>
@endforeach
