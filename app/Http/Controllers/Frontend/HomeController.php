<?php

namespace App\Http\Controllers\Frontend;

use DOMDocument;
use App\Models\User;
use App\Models\Posts;
use App\Models\Points;
use App\Models\Shares;
use App\Models\Replies;
use App\Models\Reports;
use App\Models\Comments;
use App\Models\Reactions;
use App\Models\PostsViews;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Carbon;
use App\Models\Admin\Categories;
use Snipe\BanBuilder\CensorWords;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\EmailTemplate;
use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Overtrue\LaravelLike\Traits\Likeable;

class HomeController extends Controller
{
    use Likeable;

    public function posts(Request $request)
    {
        $categories = \App\Services\CacheService::categories();
        $posts = Posts::where('status', 1)->where('public', 1)->orderByDesc('pinned')->orderByDesc('created_at')->paginate(get_setting('results_per_page'));

        return view('frontend.posts', ['posts' => $posts, 'categories' => $categories]);
    }

    public function sortPosts(Request $request)
    {
        $categories = Categories::where('pro', 0)->get();

        $categoryId = $request->get('category_id');
        $date = $request->get('date');
        $sort = $request->get('sort');
        $search_term = $request->get('search_term');

        $posts = Posts::query();

        if ($request->ajax()) {
            $posts = $this->filterPosts($posts, $categoryId, $date, $sort, $search_term);
        }
        $posts = $posts->where('status', 1)->where('public', 1)->orderByDesc('pinned')->orderByDesc('created_at')->paginate(get_setting('results_per_page'));
        return view('frontend.partials.posts', ['posts' => $posts, 'categories' => $categories]);
    }

    protected function filterPosts($posts, $categoryId, $date, $sort, $search_term)
    {

        if ($categoryId == 'all') {
            $posts = $posts;
        }else{
            $posts = $posts->where('category_id', $categoryId);
        }

        if ($date == 'all') {
            $posts = $posts;
        }elseif ($date == 'today') {
                $posts = $posts->whereDate('created_at', Carbon::today()->format('Y-m-d H:i:s'));
        }elseif ($date == 'week') {
                $posts = $posts->whereBetween('created_at', [Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'), Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')]);
        }elseif ($date == 'month') {
                $posts = $posts->whereDate('created_at', '>=', Carbon::now()->subDay(30)->format('Y-m-d H:i:s'));
        }elseif ($date == 'year') {
                $posts = $posts->whereYear('created_at', date('Y'));
        }

        if ($sort == 'all') {
            $posts = $posts;
        }elseif ($sort == 'solved') {
            $posts = $posts->where('solved', 1);
        }elseif ($sort == 'pinned') {
            $posts = $posts->where('pinned', 1);
        }elseif ($sort == 'closed') {
            $posts = $posts->where('closed', 1);
        }elseif ($sort == 'most_reactions') {
            $posts = $posts->withCount('reactions')->orderBy('reactions_count', 'desc');
        }elseif ($sort == 'most_likes') {
            $posts = $posts->withCount('search_likes')->orderBy('search_likes_count', 'desc');
        }elseif ($sort == 'most_comments') {
            $posts = $posts->withCount('search_comments')->orderBy('search_comments_count', 'desc');
        }

        if ($search_term != '') {
            $posts = $posts->where('title', 'like', '%'.$search_term.'%');
        }
        return $posts;
    }

    public function post(Request $request)
    {

        $post = Posts::where('post_id', $request->post_id)->first();

        PostsViews::createViewLog($post);

        return view('frontend.post', ['post' => $post]);
    }

    public function comments()
    {
        $comments = Comments::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(get_setting('results_per_page'));
        return view('user.posts.comments', ['comments' => $comments]);
    }

    public function paginateComments()
    {
        $comments = Comments::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(get_setting('results_per_page'));
        return view('frontend.pagination.user.comments', ['comments' => $comments]);
    }

    public function addComment(Request $request)
    {

        if (Auth::check())
        {

            $validator = Validator::make($request->all(), [
                'bodyComment' => 'required',
            ],[
                'bodyComment.required' => 'Body is Required',
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
                $body = $censor->censorString($request->bodyComment)['clean'];
            } else {
                $body = $request->bodyComment;
            }

            $comment = new Comments();
            $comment->post_id = $request->post_id;
            $comment->user_id = Auth::user()->id;
            $comment->body = $body;
            if (get_setting('new_comments') === 'Moderation') {
                $comment->status = '0';
            }

            if ($comment->save()) {

                if (Auth::user()->id != $request->recipient_id) {

                    // Notification
                    Notifications::create([
                        'sender_id' => Auth::user()->id,
                        'recipient_id' => $request->recipient_id,
                        'notification_type' => "Comment",
                        'post_id' => $request->post_id,
                        'comment_id' => $comment->id,
                        'seen' => 2,
                    ]);
                }

                //Add Points to User
                if(get_setting('points_system') == '1'){
                    Points::create([
                        'user_id' => Auth::user()->id,
                        'type' => '1',
                        'score' => get_setting('comment')
                    ]);
                }

                if (get_setting('enable_emails') == 'Yes') {
                    $re = User::where('id',$request->recipient_id)->first();
                    $po = Posts::where('id',$request->post_id)->first();
                    if ($re->email_comment == '1') {

                        //Replace paremeter
                        $replace = array(
                            '{url}'              => env('APP_URL'),
                            '{logo}'              => get_setting('logo_dark'),
                            '{sitename}'              => get_setting('site_name'),
                            '{year}'              => date('Y'),
                            '{name}'              => Auth::user()->name,
                            '{image}'              => Auth::user()->image,
                            '{post_title}'              => $po->title,
                            '{post_id}'              => $po->post_id,
                            '{post_slug}'              => $po->slug,
                        );

                        //Send email
                        $template = EmailTemplate::where('name','comment')->first();
                        $template->body = process_string($replace, $template->body);

                        Mail::to($re->email)->send(new GeneralMail($template));
                    }
                }

                if (get_setting('new_comments') === 'Moderation') {

                    return response()->json([
                        'status' => 200,
                        'messages' => 'Your Comment has been Added but it is under Review by Admin before being published'
                    ]);

                }else{

                    return response()->json([
                        'status' => 200,
                        'messages' => 'Comment added Successfully'
                    ]);
                }

            }else{

                return response()->json([
                    'status' => 401,
                    'messages' => 'Error, Something went wrong'
                ]);

            }

       }else{

            return response()->json([
                'status' => 402,
                'messages' => 'Please login to post a comment'
            ]);
       }
    }

    public function editComment(Request $request)
    {
		$id = $request->id;
        $comment = Comments::where('id',$id)->where('user_id', Auth::user()->id)->first();
        if (!$comment) {
            return redirect()->route('home.posts');
        }
        return view('frontend.comments.edit', ['comment' => $comment]);
    }

    public function updateComment(Request $request)
    {

        if (Auth::check())
        {

            $validator = Validator::make($request->all(), [
                'bodyComment' => 'required',
            ],[
                'bodyComment.required' => 'Body is Required',
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
                $body = $censor->censorString($request->bodyComment)['clean'];
            } else {
                $body = $request->bodyComment;
            }

            $comment = Comments::find($request->id);
            $comment->body = $body;
            if ($comment->update()) {

                return response()->json([
                    'status' => 200,
                    'messages' => 'Comment updated Successfully'
                ]);

            }else{

                return response()->json([
                    'status' => 401,
                    'messages' => 'Error, Something went wrong'
                ]);

            }

       }else{

            return response()->json([
                'status' => 402,
                'messages' => 'Please login to post a comment'
            ]);
       }
    }

    public function destroyComment(Request $request)
    {
        $id = $request->id;
        $comment = Comments::find($id);
        //Points::where('user_id', Auth::user()->id)->where('comment_id', $comment->id)->delete();
        Replies::where('comment_id', $comment->id)->delete();
        $comment->delete();
        return response()->json(['status' => 'Comment Deleted Successfully']);
    }

	public function markComment(Request $request){

        $id = $request->id;
        $post_id = $request->post_id;
        Posts::where('id',$post_id)->update(['solved' => 1]);
        Comments::where('id',$id)->update(['solution' => 1]);

        return response()->json(['messages' => 'Comment marked as solution Successfully']);
	}

	public function unmarkComment(Request $request){

        $id = $request->id;
        $post_id = $request->post_id;
        Posts::where('id',$post_id)->update(['solved' => 0]);
        Comments::where('id',$id)->update(['solution' => 0]);

        return response()->json(['messages' => 'Comment unmarked as solution Successfully']);
	}

    public function replies()
    {
        $replies = Replies::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(get_setting('results_per_page'));
        return view('user.posts.replies', ['replies' => $replies]);
    }

    public function paginateReplies(Request $request)
    {
        $replies = Replies::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(get_setting('results_per_page'));
        return view('frontend.pagination.user.replies', ['replies' => $replies]);
    }

    public function addReply(Request $request)
    {

        if (Auth::check())
        {

            $validator = Validator::make($request->all(), [
                'bodyReply' => 'required',
            ],[
                'bodyReply.required' => 'Body is Required',
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
                $body = $censor->censorString($request->bodyReply)['clean'];
            } else {
                $body = $request->bodyReply;
            }

            $item = new Replies();
            $item->comment_id = $request->comment_id;
            $item->user_id = Auth::user()->id;
            $item->body = $body;
            if (get_setting('new_replies') === 'Moderation') {
                $item->status = '0';
            }

            if ($item->save()) {


                if (Auth::user()->id != $request->recipient_id) {
                    // Notification
                    Notifications::create([
                        'sender_id' => Auth::user()->id,
                        'recipient_id' => $request->recipient_id,
                        'notification_type' => "Reply",
                        'post_id' => $request->post_id,
                        'reply_id' => $item->id,
                        'seen' => 2,
                    ]);
                }

                //Add Points to User
                if(get_setting('points_system') == '1'){
                    Points::create([
                        'user_id' => Auth::user()->id,
                        'type' => '1',
                        'score' => get_setting('reply')
                    ]);
                }

                if (get_setting('enable_emails') == 'Yes') {
                    $re = User::where('id',$request->recipient_id)->first();
                    $po = Posts::where('id',$request->post_id)->first();
                    if ($re->email_reply == '1') {

                        //Replace paremeter
                        $replace = array(
                            '{url}'              => env('APP_URL'),
                            '{logo}'              => get_setting('logo_dark'),
                            '{sitename}'              => get_setting('site_name'),
                            '{year}'              => date('Y'),
                            '{name}'              => Auth::user()->name,
                            '{image}'              => Auth::user()->image,
                            '{post_title}'              => $po->title,
                            '{post_id}'              => $po->post_id,
                            '{post_slug}'              => $po->slug,
                        );

                        //Send email
                        $template = EmailTemplate::where('name','reply')->first();
                        $template->body = process_string($replace, $template->body);

                        Mail::to($re->email)->send(new GeneralMail($template));
                    }
                }

                if (get_setting('new_replies') === 'Moderation') {

                    return response()->json([
                        'status' => 200,
                        'messages' => 'Your Reply has been Added but it is under Review by Admin before being published'
                    ]);

                }else{

                    return response()->json([
                        'status' => 200,
                        'messages' => 'Reply added Successfully'
                    ]);
                }

            }else{

                return response()->json([
                    'status' => 401,
                    'messages' => 'Error, Something went wrong'
                ]);

            }

        }else{

                return response()->json([
                    'status' => 402,
                    'messages' => 'Please Login to add Reply'
                ]);

        }
    }

    public function editReply(Request $request)
    {
		$id = $request->id;
        $reply = Replies::where('id',$id)->where('user_id', Auth::user()->id)->first();
        if (!$reply) {
            return redirect()->route('home.posts');
        }
        return view('frontend.replies.edit', ['reply' => $reply]);
    }

    public function updateReply(Request $request)
    {

        if (Auth::check())
        {

            $validator = Validator::make($request->all(), [
                'bodyReply' => 'required',
            ],[
                'bodyReply.required' => 'Body is Required',
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
                $body = $censor->censorString($request->bodyReply)['clean'];
            } else {
                $body = $request->bodyReply;
            }

            $reply = Replies::find($request->id);
            $reply->body = $body;
            if ($reply->update()) {

                return response()->json([
                    'status' => 200,
                    'messages' => 'Reply updated Successfully'
                ]);

            }else{

                return response()->json([
                    'status' => 401,
                    'messages' => 'Error, Something went wrong'
                ]);

            }

       }else{

            return response()->json([
                'status' => 402,
                'messages' => 'Please login to edit post'
            ]);
       }
    }

    public function destroyReply(Request $request)
    {
        $id = $request->id;
        $reply = Replies::find($id);
        //Points::where('user_id', Auth::user()->id)->where('reply_id', $reply->id)->delete();
        $reply->delete();
        return response()->json(['status' => 'Reply Deleted Successfully']);
    }

	public function markReply(Request $request){

        $id = $request->id;
        $post_id = $request->post_id;
        Posts::where('id',$post_id)->update(['solved' => 1]);
        Replies::where('id',$id)->update(['solution' => 1]);

        return response()->json(['messages' => 'Reply marked as solution Successfully']);
	}

	public function unmarkReply(Request $request){

        $id = $request->id;
        $post_id = $request->post_id;
        Posts::where('id',$post_id)->update(['solved' => 0]);
        Replies::where('id',$id)->update(['solution' => 0]);

        return response()->json(['messages' => 'Reply unmarked as solution Successfully']);
	}

    function react(Request $request){


        if (Auth::check())
        {
            $item = Posts::findOrFail($request->item);

            if ($request->type == 'like') {
                $item->toggleReaction('like');
            } elseif($request->type == 'love') {
                $item->toggleReaction('love');
            } elseif($request->type == 'haha') {
                $item->toggleReaction('haha');
            } elseif($request->type == 'wow') {
                $item->toggleReaction('wow');
            } elseif($request->type == 'yay') {
                $item->toggleReaction('yay');
            } elseif($request->type == 'sad') {
                $item->toggleReaction('sad');
            } elseif($request->type == 'mad') {
                $item->toggleReaction('mad');
            }

            if ($item->is_reacted) {

                if (Auth::user()->id != $item->user_id) {

                    Notifications::where('sender_id', Auth::user()->id)->where('post_id', $item->id)->where('notification_type', 'React Post')->delete();

                    $react_id = Reactions::where('user_id', Auth::user()->id)->where('reactable_id', $item->id)->first()->id;
                    //Notification
                    Notifications::create([
                        'sender_id' => Auth::user()->id,
                        'recipient_id' => $item->user_id,
                        'notification_type' => "React Post",
                        'post_id' => $item->id,
                        'react_id' => $react_id,
                        'seen' => 2,
                    ]);

                    //Add Points to User
                    if(get_setting('points_system') == '1'){
                        Points::create([
                            'user_id' => Auth::user()->id,
                            'type' => '7',
                            'score' => get_setting('reaction')
                        ]);
                    }

                }

                return response()->json([
                    'status' => 200,
                    'messages' => 'Reacted successfully'
                ]);

            }else{

                Notifications::where('sender_id', Auth::user()->id)->where('post_id', $item->id)->where('notification_type', 'React Post')->delete();

                return response()->json([
                    'status' => 202,
                    'messages' => 'Removed Reaction successfully'
                ]);

            }

        }else{

            return response()->json([
                'status' => 402,
                'messages' => 'Please Login to React'
            ]);

        }

    }

    function like(Request $request){

        $item = Posts::findOrFail($request->item);

        if(Auth::user()->hasLiked($item)) {

            Notifications::where('sender_id', Auth::user()->id)->where('post_id', $item->id)->where('notification_type', 'Like Post')->delete();

            Auth::user()->unlike($item);

            return response()->json([
                'bool'=>false,
                'status' => 200,
                'messages' => 'Removed Like successfully'
            ]);

        } else {

            $like = Auth::user()->like($item);

            if (Auth::user()->id != $item->user_id) {

                //Notification
                Notifications::create([
                    'sender_id' => Auth::user()->id,
                    'recipient_id' => $item->user_id,
                    'notification_type' => "Like Post",
                    'post_id' => $item->id,
                    'like_id' => Auth::user()->likes()->with('likeable')->where('likeable_type', 'App\Models\Posts')->first()->id,
                    'seen' => 2,
                ]);

                //Add Points to User
                if(get_setting('points_system') == '1'){
                    Points::create([
                        'user_id' => Auth::user()->id,
                        'type' => '6',
                        'score' => get_setting('like')
                    ]);
                }

            }

            return response()->json([
                'bool' => true,
                'status' => 200,
                'messages' => 'Liked Post successfully'
            ]);

        }
    }

    function likeComment(Request $request){

        $item = Comments::findOrFail($request->item);

        if(Auth::user()->hasLiked($item)) {

            Notifications::where('sender_id', Auth::user()->id)->where('post_id', $item->post->id)->where('notification_type', 'Like Comment')->delete();

            Auth::user()->unlike($item);

            return response()->json([
                'bool'=>false,
                'status' => 200,
                'messages' => 'Removed Like on Comment Successfully'
            ]);

        } else {

            $like = Auth::user()->like($item);

            if (Auth::user()->id != $item->user_id) {
                //Notification
                Notifications::create([
                    'sender_id' => Auth::user()->id,
                    'recipient_id' => $item->user_id,
                    'notification_type' => "Like Comment",
                    'post_id' => $item->post->id,
                    'like_id' => Auth::user()->likes()->with('likeable')->where('likeable_type', 'App\Models\Comments')->first()->id,
                    'seen' => 2,
                ]);

                //Add Points to User
                if(get_setting('points_system') == '1'){
                    Points::create([
                        'user_id' => Auth::user()->id,
                        'type' => '6',
                        'score' => get_setting('like')
                    ]);
                }
            }

            return response()->json([
                'bool' => true,
                'status' => 200,
                'messages' => 'Liked on Comment Successfully'
            ]);

        }
    }

    function likeReply(Request $request){

        $item = Replies::findOrFail($request->item);

        if(Auth::user()->hasLiked($item)) {

            Notifications::where('sender_id', Auth::user()->id)->where('post_id', $item->comment->post->id)->where('notification_type', 'Like Reply')->delete();

            Auth::user()->unlike($item);

            return response()->json([
                'bool'=>false,
                'status' => 200,
                'messages' => 'Removed Like on Reply Successfully'
            ]);

        } else {

            $like = Auth::user()->like($item);

            if (Auth::user()->id != $item->user_id) {
                //Notification
                Notifications::create([
                    'sender_id' => Auth::user()->id,
                    'recipient_id' => $item->user_id,
                    'notification_type' => "Like Reply",
                    'post_id' => $item->comment->post->id,
                    'like_id' => Auth::user()->likes()->with('likeable')->where('likeable_type', 'App\Models\Replies')->first()->id,
                    'seen' => 2,
                ]);

                //Add Points to User
                if(get_setting('points_system') == '1'){
                    Points::create([
                        'user_id' => Auth::user()->id,
                        'type' => '6',
                        'score' => get_setting('like')
                    ]);
                }
            }

            return response()->json([
                'bool' => true,
                'status' => 200,
                'messages' => 'Liked on Reply Successfully'
            ]);

        }
    }

    function save_favorite(Request $request){

        $item = Posts::findOrFail($request->item);

        if($request->user()->hasFavorited($item)) {

            $request->user()->unfavorite($item);

            return response()->json([
                'bool'=>false
            ]);

        } else {

            $request->user()->favorite($item);

            return response()->json([
                'bool'=>true
            ]);
        }

    }

    public function report_post(Request $request)
    {

        if (Auth::check())
        {

            $has = Reports::where('user_id', Auth::user()->id)->where('report_type', 'Post')->where('report_id', $request->id)->exists();
            if($has === true){
                return response()->json(['status' => 'You already Reported', 'action' => 'error']);
            }else{
                $report = Reports::create([
                    'user_id' => Auth::user()->id,
                    'report_type' => "Post",
                    'report_id' => $request->id,
                ]);

                if($report == true){

                    return response()->json(['status' => 'Post Reported Successfully', 'action' => 'success']);

                } else {

                    return response()->json(['status' => 'Something went wrong', 'action' => 'error']);
                }
            }
        }else {

            return response()->json(['status' => 'Please Login to Report', 'action' => 'error']);
        }
    }

    public function report_comment(Request $request)
    {

        if (Auth::check())
        {

            $has = Reports::where('user_id', Auth::user()->id)->where('report_type', 'Comment')->where('report_id', $request->id)->exists();
            if($has === true){
                return response()->json(['status' => 'You already Reported', 'action' => 'error']);
            }else{

                $report = Reports::create([
                    'user_id' => Auth::user()->id,
                    'report_type' => "Comment",
                    'report_id' => $request->id,
                ]);

                if($report == true){

                    return response()->json(['status' => 'Comment Reported Successfully', 'action' => 'success']);

                } else {

                    return response()->json(['status' => 'Something went wrong', 'action' => 'error']);
                }
            }

        }else {

            return response()->json(['status' => 'Please Login to Report', 'action' => 'error']);
        }
    }

    public function report_reply(Request $request)
    {

        if (Auth::check())
        {

            $has = Reports::where('user_id', Auth::user()->id)->where('report_type', 'Reply')->where('report_id', $request->id)->exists();
            if($has === true){
                return response()->json(['status' => 'You already Reported', 'action' => 'error']);
            }else{
                $report = Reports::create([
                    'user_id' => Auth::user()->id,
                    'report_type' => "Reply",
                    'report_id' => $request->id,
                ]);

                if($report == true){

                    return response()->json(['status' => 'Reply Reported Successfully', 'action' => 'success']);

                } else {

                    return response()->json(['status' => 'Something went wrong', 'action' => 'error']);
                }
            }

        }else {

            return response()->json(['status' => 'Please Login to Report', 'action' => 'error']);
        }
    }

    public function share(Request $request)
    {

        $item = Shares::create([
            'post_id' => $request->id,
            'user_id' => (Auth::check()) ? Auth::user()->id : null,
            'type' => $request->type
        ]);

        if($item == true){

            if (Auth::check()) {
                if(get_setting('points_system') == '1'){
                    Points::create([
                        'user_id' => Auth::user()->id,
                        'type' => '8',
                        'score' => get_setting('share')
                    ]);
                }
            }

            return response()->json(['status' => 'Shared Successfully']);

        } else {

            return response()->json(['status' => 'Something went wrong']);
        }

    }

}
