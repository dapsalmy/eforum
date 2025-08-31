<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Chats;
use App\Models\Posts;
use App\Models\Shares;
use App\Models\Payment;
use App\Models\Replies;
use App\Models\Reports;
use App\Models\Comments;
use App\Models\Deposits;
use App\Models\Messages;
use App\Models\Withdraw;
use App\Models\Reactions;
use App\Models\PostsViews;
use App\Models\ReportUser;
use App\Models\Admin\Badge;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Conner\Tagging\Model\Tag;
use Illuminate\Support\Carbon;
use App\Models\Admin\Categories;
use Conner\Tagging\Model\Tagged;
use App\Models\Admin\BanDuration;
use Snipe\BanBuilder\CensorWords;
use Conner\Tagging\TaggingUtility;
use App\Models\Admin\EmailTemplate;
use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    public function badges()
    {
        $badges = Badge::all();
        return view('admin.forum.badges', ['badges' => $badges]);
    }

    public function store_badges(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required',
            'score' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ],[
            'name.required' => 'Badge Name is Required',
            'name.string' => 'Badge Name should be a String',
            'name.max' => 'Badge Name max characters should be 255',
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
        $file->move('public/uploads/badges/',$filename);

        $item = new Badge();
        $item->name = $request->name;
        $item->description = $request->description;
        $item->score = $request->score;
        $item->image = $filename;
        if ($item->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Badge added Successfully'
            ]);

        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }
    }

    public function edit_badges(Request $request)
    {
		$id = $request->id;
		$emp = Badge::find($id);
		return response()->json($emp);
    }

    public function update_badges(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'edit_name' => 'required|string|max:255',
            'edit_description' => 'required',
            'edit_score' => 'required|numeric',
        ],[
            'edit_name.required' => 'Badge Name is Required',
            'edit_name.string' => 'Badge Name should be a String',
            'edit_name.max' => 'Badge Name max characters should be 255',
            'edit_description.required' => 'Description is Required'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        if($request->hasFile('edit_image'))
        {
            $path = 'public/uploads/badges/'.$request->old_image;
            if(File::exists($path)){
                File::delete($path);
            }
            $file = $request->file('edit_image');
            $ext = $file->getClientOriginalExtension();
            //$filename = time().'.'.$ext;
            $filename = md5(microtime()).'.'.$ext;
            $file->move('public/uploads/badges/',$filename);
        }else{
            $filename = $request->old_image;
        }

        $badge = Badge::find($request->badge_id);
        $badge->name = $request->edit_name;
        $badge->description = $request->edit_description;
        $badge->score = $request->edit_score;
        $badge->image = $filename;
        if ($badge->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Badge updated Successfully'
            ]);
        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Something went wrong'
            ]);

        }
    }

	public function destroy_badges(Request $request){

        $id = $request->id;
        $badge = Badge::find($id);
        $path = 'public/uploads/badges/'.$badge->image;
        if(File::exists($path)){
            File::delete($path);
        }
        $badge->delete();
        return response()->json(['status' => 'Badge Deleted Successfully']);
	}

    public function withdrawals()
    {
        $withdrawals = Withdraw::orderBy('created_at', 'desc')->get();
        return view('admin.forum.withdrawals', ['withdrawals' => $withdrawals]);
    }

	public function paid(Request $request){

        $id = $request->id;

        Withdraw::where('id',$id)->update([
            'status' => 1
        ]);
        $w = Withdraw::where('id',$id)->first();

        if (get_setting('enable_emails') == 'Yes') {
            $re = User::where('id',$w->user_id)->first();

                //Replace paremeter
                $replace = array(
                    '{url}'              => env('APP_URL'),
                    '{logo}'              => get_setting('logo_dark'),
                    '{sitename}'              => get_setting('site_name'),
                    '{year}'              => date('Y'),
                    '{name}'              => $re->name,
                    '{currency}'              => get_setting('currency_symbol'),
                    '{amount}'              => $w->amount,
                );

                //Send email
                $template = EmailTemplate::where('name','payouts')->first();
                $template->body = process_string($replace, $template->body);

                Mail::to($re->email)->send(new GeneralMail($template));
        }

        return response()->json(['status' => 'Paid Successfully']);
	}

	public function unpaid(Request $request){

        $id = $request->id;

        Withdraw::where('id',$id)->update([
            'status' => 0
        ]);

        return response()->json(['status' => 'Payment Cancelled Successfully']);
	}

    public function deposits()
    {
        $deposits = Deposits::orderBy('created_at', 'desc')->get();
        return view('admin.forum.deposits', ['deposits' => $deposits]);
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::orderBy('created_at', 'desc')->get();
        return view('admin.forum.subscriptions', ['subscriptions' => $subscriptions]);
    }

    public function tips()
    {
        $tips = Payment::orderBy('created_at', 'desc')->get();
        return view('admin.forum.tips', ['tips' => $tips]);
    }

    public function transactions()
    {
        $transactions = Transaction::orderBy('created_at', 'desc')->get();
        return view('admin.forum.transactions', ['transactions' => $transactions]);
    }

    public function bandurations()
    {
        $bandurations = BanDuration::orderBy('id','asc')->get();
        return view('admin.forum.bandurations', ['bandurations' => $bandurations]);
    }

    public function store_bandurations(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'time' => 'required',
        ],[
            'title.required' => 'Title is Required',
            'title.string' => 'Title should be a String',
            'title.max' => 'Title max characters should be 255',
            'time.required' => 'Time is Required',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $item = new BanDuration();
        $item->title = $request->title;
        $item->time = $request->time;
        if ($item->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Ban Duration added Successfully'
            ]);

        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }
    }

    public function edit_bandurations(Request $request)
    {
		$id = $request->id;
		$emp = BanDuration::find($id);
		return response()->json($emp);
    }

    public function update_bandurations(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'edit_title' => 'required|string|max:255',
            'edit_time' => 'required',
        ],[
            'edit_title.required' => 'Title is Required',
            'edit_title.string' => 'Title should be a String',
            'edit_title.max' => 'Title max characters should be 255',
            'edit_time.required' => 'Time is Required'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $ban = BanDuration::find($request->ban_id);
        $ban->title = $request->edit_title;
        $ban->time = $request->edit_time;
        if ($ban->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Ban Duration updated Successfully'
            ]);
        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Something went wrong'
            ]);

        }
    }

	public function destroy_bandurations(Request $request){

        $id = $request->id;
        $badge = BanDuration::find($id);
        $badge->delete();
        return response()->json(['status' => 'Ban Duration Deleted Successfully']);
	}

    public function users_reports()
    {
        $reports = ReportUser::orderBy('created_at','desc')->get();
        $durations = BanDuration::orderBy('id','asc')->get();
        return view('admin.forum.user_reports', ['reports' => $reports,'durations' => $durations]);
    }

    public function get_user(Request $request)
    {
		$id = $request->id;
		$emp = User::find($id);
		return response()->json($emp);
    }

    public function ban_user(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'reason' => 'required',
        ],[
            'reason.required' => 'Reason is Required',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }


		$user = User::find($request->user_id);
        $new_date = Carbon::now()->addMinutes($request->time);
        $user->ban([
            'comment' => $request->reason,
            'expired_at' => $new_date,
        ]);

        if (get_setting('enable_emails') == 'Yes') {
                //Replace paremeter
                $replace = array(
                    '{url}'              => env('APP_URL'),
                    '{logo}'              => get_setting('logo_dark'),
                    '{sitename}'              => get_setting('site_name'),
                    '{year}'              => date('Y'),
                    '{name}'              => $user->name,
                );

                //Send email
                $template = EmailTemplate::where('name','banned')->first();
                $template->body = process_string($replace, $template->body);

                Mail::to($user->email)->send(new GeneralMail($template));
        }

        return response()->json([
            'status' => 200,
            'messages' => 'User Banned Successfully'
        ]);

    }

    public function remove_ban(Request $request)
    {
		$user = User::find($request->id);
        $user->unban();
        return response()->json(['status' => 'Ban Removed Successfully']);
    }

    public function banned_users()
    {
        $users = User::onlyBanned()->get();
        return view('admin.forum.banned_users', ['users' => $users]);
    }

    public function list_posts()
    {
        $categories = Categories::all();
        $posts = Posts::orderBy('created_at', 'desc')->get();
        return view('admin.forum.posts', ['categories' => $categories, 'posts' => $posts]);
    }

    public function edit_posts(Request $request)
    {
		$id = $request->id;
        $post = Posts::find($id);
        $categories = Categories::all();
        return view('admin.forum.posts', ['categories' => $categories,'post' => $post]);
    }

    public function update_posts(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'keywords' => 'required|string',
            'body' => 'required|string|min:'. get_setting('minimum_characters') .'|max:'.get_setting('maximum_characters')
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
        $item->status = $request->status;

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

	public function destroy_posts(Request $request){

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


    public function tags()
    {
        $tags = Tag::orderBy('id', 'desc')->get();
        return view('admin.forum.tags', ['tags' => $tags]);
    }

    public function edit_tags(Request $request)
    {
		$id = $request->id;
        $tag = Tag::find($id);
        return view('admin.forum.tags', ['tag' => $tag]);
    }

    public function update_tags(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ],[
            'name.required' => 'Name is Required',
            'name.string' => 'Name should be a String',
            'name.max:255' => 'Name should be max 255 characters',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }


        $item = Tag::find($request->id);
        $new_name = $request->name;
        $slug = TaggingUtility::slug($new_name);

        $tagged = Tagged::where('tag_slug', $item->slug)->get();
        if ($tagged != null) {
            foreach ($tagged as $tagg) {
                $tagg->update(['tag_name' => $new_name, 'tag_slug' => $slug]);
            }
        }

        if ($item->update(['name' => $new_name, 'slug' => $slug])) {

            return response()->json([
                'status' => 200,
                'messages' => 'Tag updated Successfully'
            ]);

        }else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }


    }

	public function destroy_tags(Request $request){

        $id = $request->id;
        $item = Tag::find($id);

        $tagged = Tagged::where('tag_slug', $item->slug)->get();
        if ($tagged != null) {
            foreach ($tagged as $tagg) {
                $tagg->delete();
            }
        }

        $item->delete();
        return response()->json(['status' => 'Tag Deleted Successfully']);
	}

    public function comments()
    {
        $comments = Comments::orderBy('created_at', 'desc')->get();
        return view('admin.forum.comments', ['comments' => $comments]);
    }

    public function editComment(Request $request)
    {
		$id = $request->id;
        $comment = Comments::find($id);
        return view('admin.forum.comments', ['comment' => $comment]);
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
            $comment->status = $request->status;
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
        Replies::where('comment_id', $comment->id)->delete();
        $comment->delete();
        return response()->json(['status' => 'Comment Deleted Successfully']);
    }


    public function replies()
    {
        $replies = Replies::all();
        return view('admin.forum.replies', ['replies' => $replies]);
    }

    public function editReply(Request $request)
    {
		$id = $request->id;
        $reply = Replies::find($id);
        return view('admin.forum.replies', ['reply' => $reply]);
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
            $reply->status = $request->status;
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

    public function chats()
    {
        $chats = Chats::orderByDesc('created_at')->get();
        return view('admin.forum.chats', ['chats' => $chats]);
    }

    public function messages(Request $request)
    {
        $chat_id = $request->chat_id;

        $chats = Chats::orderByDesc('created_at')->get();
        $messages = Messages::where('chat_id', $chat_id)->orderBy("created_at", "asc")->get();
        $current_chat = Chats::where('id', $chat_id)->first();
        return view('admin.forum.messages', ['chats' => $chats, 'messages' => $messages, 'chat_id' => $chat_id, 'current_chat' => $current_chat]);
    }

	public function delete_chats(Request $request){

        $id = $request->id;
        $item = Chats::find($id);

        $messages = Messages::where('chat_id', $item->id)->get();
        if ($messages != null) {
            foreach ($messages as $message) {

                if ($message->file_ext === 'Image' || $message->file_ext === 'Video' || $message->file_ext === 'Audio' || $message->file_ext === 'Zip') {

                    $path = 'public/uploads/attachments/'.$message->attachment_name;
                    if(File::exists($path)){
                        File::delete($path);
                    }

                }
                $message->delete();
            }
        }

        $item->delete();

        return response()->json(['status' => 'Chat Deleted Successfully']);
	}

    public function posts_reports()
    {
        $reports = Reports::where('report_type', 'Post')->orderBy('created_at','desc')->get();
        return view('admin.forum.reports', ['reports' => $reports]);
    }

    public function comments_reports()
    {
        $reports = Reports::where('report_type', 'Comment')->orderBy('created_at','desc')->get();
        return view('admin.forum.reports', ['reports' => $reports]);
    }

    public function replies_reports()
    {
        $reports = Reports::where('report_type', 'Reply')->orderBy('created_at','desc')->get();
        return view('admin.forum.reports', ['reports' => $reports]);
    }

}
