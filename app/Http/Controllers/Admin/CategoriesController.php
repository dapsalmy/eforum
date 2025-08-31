<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Admin\Categories;
use App\Http\Controllers\Controller;
use App\Models\Comments;
use App\Models\Notifications;
use App\Models\Posts;
use App\Models\PostsViews;
use App\Models\Reactions;
use App\Models\Replies;
use App\Models\Reports;
use App\Models\Shares;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Categories::all();
        return view('admin.categories.index', compact('categories'));

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ],[
            'name.required' => 'Category Name is Required',
            'name.string' => 'Category Name should be a String',
            'name.max' => 'Category Name max characters should be 255',
            'description.required' => 'Description is Required',
            'image.required' => 'Image is Required',
            'image.image' => 'Image Field should be an Image',
            'image.mimes' => 'Jpeg, Png, Jpg, Gif, Svg are Allowed',
            'image.max' => 'Image max file is 2048',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $file = $request->file('image');
        $ext = $file->getClientOriginalExtension();
        //$filename = time().'.'.$ext;
        $filename = md5(microtime()).'.'.$ext;
        $file->move('public/uploads/categories/',$filename);

        $category = new Categories();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name, '-');
        $category->description = $request->description;
        $category->image = $filename;
        if (get_setting('payments_on_site') == 'Yes') {
            $category->pro = $request->pro;
        } else {
            $category->pro = '0';
        }
        $category->status = $request->status;
        if ($category->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Category added Successfully'
            ]);

        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }
    }

    public function edit(Request $request)
    {
		$id = $request->id;
		$emp = Categories::find($id);
		return response()->json($emp);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ],[
            'name.required' => 'Category Name is Required',
            'name.string' => 'Category Name should be a String',
            'name.max' => 'Category Name max characters should be 255',
            'description.required' => 'Description is Required',
            'image.image' => 'Image Field should be an Image',
            'image.mimes' => 'Jpeg, Png, Jpg, Gif, Svg are Allowed',
            'image.max' => 'Image max file is 2048',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        if($request->hasFile('image'))
        {
            $path = 'public/uploads/categories/'.$request->old_image;
            if(File::exists($path)){
                File::delete($path);
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            //$filename = time().'.'.$ext;
            $filename = md5(microtime()).'.'.$ext;
            $file->move('public/uploads/categories/',$filename);
        }else{
            $filename = $request->old_image;
        }

        $category = Categories::find($request->category_id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name, '-');
        $category->description = $request->description;
        $category->image = $filename;
        if (get_setting('payments_on_site') == 'Yes') {
            $category->pro = $request->pro;
        } else {
            $category->pro = '0';
        }
        $category->status = $request->status;
        if ($category->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Category updated Successfully'
            ]);
        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Something went wrong'
            ]);

        }
    }

	public function destroy(Request $request){

        $id = $request->id;
        $category = Categories::find($id);
        $users = User::all();

        $posts = Posts::where('category_id', $category->id)->get();
        if ($posts != null) {
            foreach ($posts as $post) {

                //Comments
                $comments = Comments::where('post_id', $post->id)->get();
                if ($comments != null) {
                    foreach ($comments as $comment) {

                        //Replies
                        $replies = Replies::where('comment_id', $comment->id)->get();
                        if ($replies != null) {
                            foreach ($replies as $reply) {
                                Reports::where('report_id', $reply->id)->where('report_type', 'Reply')->delete();
                                foreach ($users as $user) {
                                    $user->unlike($reply);
                                }
                                $reply->delete();
                            }
                        }
                        Reports::where('report_id', $comment->id)->where('report_type', 'Comment')->delete();
                        foreach ($users as $user) {
                            $user->unlike($comment);
                        }
                        $comment->delete();
                    }
                }

                //Posts Views
                $views = PostsViews::where('post_id', $post->id)->get();
                if ($views != null) {
                    foreach ($views as $view) {
                        $view->delete();
                    }
                }
                $post->untag();
                foreach ($users as $user) {
                    $user->unfavorite($post);
                    $user->unlike($post);
                }
                Reactions::where('user_id', $user->id)->where('reactable_id', $post->id)->delete();
                Reports::where('report_id', $post->id)->where('report_type', 'Post')->delete();
                Shares::where('post_id', $post->id)->delete();
                Notifications::where('post_id', $post->id)->delete();
                $post->delete();
            }
        }

        $path = 'public/uploads/categories/'.$category->image;
        if(File::exists($path)){
            File::delete($path);
        }
        $category->delete();
        return response()->json(['status' => 'Category Deleted Successfully']);
	}
}
