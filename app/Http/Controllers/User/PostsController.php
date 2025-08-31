<?php

namespace App\Http\Controllers\User;

use App\Models\Posts;
use App\Models\Points;
use App\Models\PostsViews;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Admin\Categories;
use App\Http\Controllers\Controller;
use App\Models\Comments;
use App\Models\Notifications;
use App\Models\Reactions;
use App\Models\Replies;
use App\Models\Reports;
use App\Models\Shares;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Overtrue\LaravelLike\Traits\Likeable;
use Snipe\BanBuilder\CensorWords;

class PostsController extends Controller
{
    use Likeable;

    public function add()
    {

        if (Auth::user()->subscription()->categories === 1) {
            $categories = Categories::where('status', '1')->get();
        } else {
            $categories = Categories::where('pro', '0')->where('status', '1')->get();
        }

        return view('user.posts.add', ['categories' => $categories]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'keywords' => 'required|string',
            'body' => 'required|string|min:'. get_setting('minimum_characters') .'|max:'.get_setting('maximum_characters'),
            'public' => 'required',
            'comments' => 'required',
            'likes' => 'required',
        ],[
            'title.required' => 'Title is Required',
            'title.string' => 'Title should be a String',
            'title.max:255' => 'Title should be max 255 characters',
            'description.required' => 'Description is Required',
            'description.string' => 'Description should be a String',
            'keywords.required' => 'Keywords is Required',
            'keywords.string' => 'Keywords should be a String',
            'body.required' => 'Body is Required',
            'body.string' => 'Body should be a String',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        //word censored
        if(get_setting('word_censored') == 1) {

            $censor = new CensorWords;
            $title = $censor->censorString($request->title)['clean'];
            $description = $censor->censorString($request->description)['clean'];
            $body = $censor->censorString($request->body)['clean'];

        } else {
            $title = $request->title;
            $description = $request->description;
            $body = $request->body;
        }

        $post_id = Str::random(9);

        $item = new Posts();
        $item->post_id = $post_id;
        $item->user_id = Auth::user()->id;
        $item->category_id = $request->category_id;
        $item->title = $title;
        $item->slug = Str::slug($title, '-');
        $item->description = $description;
        $item->keywords = $request->keywords;
        $item->body = $body;
        $item->public = $request->public;
        $item->comments = $request->comments;
        $item->likes = $request->likes;
        if (get_setting('new_posts') === 'Moderation') {
            $item->status = '0';
        }

        if ($item->save()) {

            //** create tags
            $tags = explode(",", $request->tags);
            $item->tag($tags);

            if(get_setting('points_system') == '1'){
                Points::create([
                    'user_id' => Auth::user()->id,
                    'type' => '3',
                    'score' => get_setting('new_posts_no'),
                ]);
            }

            if (get_setting('new_posts') === 'Moderation') {

                return response()->json([
                    'status' => 200,
                    'messages' => 'Your Post has been Added but it is under Review by Admin before being published'
                ]);

            }else{

                return response()->json([
                    'status' => 200,
                    'messages' => 'Post added Successfully'
                ]);
            }

        }else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }

    }

    public function upload(Request $request)
    {
        if (Auth::check()) {

            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = md5(microtime()).'.'.$ext;
            $file->move('public/uploads/trumbowyg/',$filename);

            $url =  URL::asset('public/uploads/trumbowyg/'.$filename);

            return response()->json([
                'status' => 200,
                'url' => $url
            ]);

        }else {

            return response()->json([
                'status' => 400,
                'messages' => 'Please Login to upload'
            ]);
        }
    }

    public function list()
    {
        $categories = Categories::all();
        $posts = Posts::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(get_setting('results_per_page'));
        return view('user.posts.list', ['categories' => $categories, 'posts' => $posts]);
    }

    public function paginatePosts()
    {
        $posts = Posts::where('user_id', Auth::user()->id)
                        ->orderByDesc('pinned')
                        ->orderBy('created_at', 'desc')
                        ->paginate(get_setting('results_per_page'));
        return view('frontend.pagination.user.posts', ['posts' => $posts]);
    }

    public function edit(Request $request)
    {
		$id = $request->id;
        $post = Posts::where('id',$id)->where('user_id', Auth::user()->id)->first();
        if (!$post) {
            return redirect()->route('user.posts.list');
        }

        if (Auth::user()->subscription()->categories === 1) {
            $categories = Categories::where('status', '1')->get();
        } else {
            $categories = Categories::where('pro', '0')->where('status', '1')->get();
        }
        return view('user.posts.edit', ['categories' => $categories,'post' => $post]);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'keywords' => 'required|string',
            'body' => 'required|string|min:'. get_setting('minimum_characters') .'|max:'.get_setting('maximum_characters'),
            'public' => 'required',
            'comments' => 'required',
            'likes' => 'required',
        ],[
            'title.required' => 'Title is Required',
            'title.string' => 'Title should be a String',
            'title.max:255' => 'Title should be max 255 characters',
            'description.required' => 'Description is Required',
            'description.string' => 'Description should be a String',
            'keywords.required' => 'Keywords is Required',
            'keywords.string' => 'Keywords should be a String',
            'body.required' => 'Body is Required',
            'body.string' => 'Body should be a String',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        //word censored
        if(get_setting('word_censored') == 1) {

            $censor = new CensorWords;
            $title = $censor->censorString($request->title)['clean'];
            $description = $censor->censorString($request->description)['clean'];
            $body = $censor->censorString($request->body)['clean'];

        } else {
            $title = $request->title;
            $description = $request->description;
            $body = $request->body;
        }

        $item = Posts::find($request->id);
        $item->category_id = $request->category_id;
        $item->title = $title;
        $item->slug = Str::slug($title, '-');
        $item->description = $description;
        $item->keywords = $request->keywords;
        $item->body = $body;
        $item->public = $request->public;
        $item->comments = $request->comments;
        $item->likes = $request->likes;

        if ($item->update()) {

            //** old tags
            $old_tags = explode(",", $request->old_tags);
            $item->untag($old_tags);

            //** create tags
            $tags = explode(",", $request->tags);
            $item->tag($tags);

            return response()->json([
                'status' => 200,
                'messages' => 'Post updated Successfully'
            ]);

        }else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }


    }

	public function destroy(Request $request){

        $id = $request->id;
        $item = Posts::find($id);

        $views = PostsViews::where('post_id', $item->id)->get();
        if ($views != null) {
            foreach ($views as $view) {
                $view->delete();
            }
        }

        $comments = Comments::where('post_id', $item->id)->get();
        if ($comments != null) {
            foreach ($comments as $comment) {

                //Replies
                $replies = Replies::where('comment_id', $comment->id)->get();
                if ($replies != null) {
                    foreach ($replies as $reply) {
                        Reports::where('report_id', $reply->id)->where('report_type', 'Reply')->delete();
                        $reply->delete();
                    }
                }

                Reports::where('report_id', $comment->id)->where('report_type', 'Comment')->delete();
                $comment->delete();
            }
        }

        $item->untag();
        Reactions::where('reactable_id', $item->id)->delete();
        Reports::where('report_id', $item->id)->where('report_type', 'Post')->delete();
        Shares::where('post_id', $item->id)->delete();
        Notifications::where('post_id', $item->id)->delete();
        $item->delete();
        return response()->json(['status' => 'Post Deleted Successfully']);
	}

	public function pin(Request $request){

        $id = $request->id;
        Posts::where('id',$id)->update(['pinned' => 1]);

        return response()->json(['messages' => 'Post Pinned Successfully']);
	}

	public function unpin(Request $request){

        $id = $request->id;
        Posts::where('id',$id)->update(['pinned' => 0]);

        return response()->json(['messages' => 'Post Unpinned Successfully']);
	}

	public function close(Request $request){

        $id = $request->id;
        Posts::where('id',$id)->update(['closed' => 1]);

        return response()->json(['messages' => 'Post Closed Successfully']);
	}

	public function open(Request $request){

        $id = $request->id;
        Posts::where('id',$id)->update(['closed' => 0]);

        return response()->json(['messages' => 'Post Opened Successfully']);
	}

}
