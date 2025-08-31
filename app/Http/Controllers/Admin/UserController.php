<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Block;
use App\Models\Chats;
use App\Models\Plans;
use App\Models\Posts;
use App\Models\Points;
use App\Models\Shares;
use App\Models\Payment;
use App\Models\Replies;
use App\Models\Reports;
use App\Models\Comments;
use App\Models\Deposits;
use App\Models\Messages;
use App\Models\Withdraw;
use App\Models\Reactions;
use App\Models\UserViews;
use App\Models\PostsViews;
use App\Models\ReportUser;
use App\Models\Admin\Badge;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Overtrue\LaravelFollow\Followable;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        return view('admin.users.index', ['users' => $users, 'roles' => $roles]);

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users|alpha_num:ascii',
            'password' => 'required|string|min:4',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ],[
            'name.required' => 'Category Name is Required',
            'name.string' => 'Category Name should be a String',
            'name.max' => 'Category Name max characters should be 255',
            'email.required' => 'Email is Required',
            'email.string' => 'Email should be a String',
            'email.email' => 'Email should be an Email',
            'email.max:255' => 'Email max characters should be 255',
            'email.unique:users' => 'Email should be unique from other Users',
            'username.required' => 'Username is Required',
            'username.string' => 'Username should be a String',
            'username.max' => 'Username max characters should be 255',
            'username.unique:users' => 'Username should be unique from other Users',
            'username.alpha_num' => 'Username should be Alphanumeral characters',
            'password.required' => 'Password is Required',
            'password.string' => 'Password should be a String',
            'password.min:4' => 'Password should be min 4 characters',
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
        $file->move('public/uploads/users/',$filename);

        //Plan
        $plan = Plans::where('price', '0.00')->first();

        if ($plan->duration == 'Monthly') {
            $new_date = Carbon::now()->addMonth();
        }elseif($plan->duration == 'Quaterly'){
            $new_date = Carbon::now()->addMonths('4');
        }elseif($plan->duration == 'Yearly'){
            $new_date = Carbon::now()->addYear();
        }elseif($plan->duration == 'Lifetime'){
            $new_date = Carbon::now()->addYears(50);
        }

        $item = new User();
        $item->name = $request->name;
        $item->slug = Str::slug($request->name);
        $item->username = $request->username;
        $item->email = $request->email;
        $item->password = Hash::make($request->password);
        $item->image = $filename;
        $item->role = $request->role;
        $item->wallet = '0.00';
        $item->earnings = '0.00';
        $item->plan_id = $plan->id;
        if ($plan->verified == 1) {
            $item->verified = '1';
        }else {
            $item->verified = '0';
        }
        if ($item->save()) {

            $n = User::where('email',$request->email)->first();

            $sub = Subscription::create([
                'user_id' => $n->id,
                'plan_id' => $plan->id,
                'price' => $plan->price,
                'points' => $plan->points,
                'verified' => $plan->verified,
                'categories' => $plan->categories,
                'tips' => $plan->tips,
                'comments' => $plan->comments,
                'reactions' => $plan->reactions,
                'followers' => $plan->followers,
                'messages' => $plan->messages,
                'users' => $plan->users,
                'viewers' => $plan->viewers,
                'ads' => $plan->ads,
                'status' => 1,
                'will_expire' => $new_date
            ]);

            Transaction::create([
                'user_id' => $n->id,
                'type_id' => $sub->id,
                'type' => '1', //Subscription
                'amount' => $plan->price,
                'status' => '1'
            ]);

            return response()->json([
                'status' => 200,
                'messages' => 'User added Successfully'
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
		$emp = User::find($id);
		return response()->json($emp);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'edit_name' => 'required|string|max:255',
            'edit_email' => 'required|string|email|max:255',
            'edit_username' => 'required|string|max:255|alpha_num:ascii',
            'edit_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ],[
            'edit_name.required' => 'Category Name is Required',
            'edit_name.string' => 'Category Name should be a String',
            'edit_name.max' => 'Category Name max characters should be 255',
            'edit_email.required' => 'Email is Required',
            'edit_email.string' => 'Email should be a String',
            'edit_email.email' => 'Email should be an Email',
            'edit_email.max:255' => 'Email max characters should be 255',
            'edit_email.unique:users' => 'Email should be unique from other Users',
            'edit_username.required' => 'Username is Required',
            'edit_username.string' => 'Username should be a String',
            'edit_username.max' => 'Username max characters should be 255',
            'edit_image.image' => 'Image Field should be an Image',
            'edit_image.mimes' => 'Jpeg, Png, Jpg, Gif, Svg are Allowed',
            'edit_image.max' => 'Image max file is 2048',
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
            if ($request->old_image != 'avatar.jpg' || $request->old_image != 'male.png' || $request->old_image != 'male-2.png' || $request->old_image != 'female.png' || $request->old_image != 'female-2.jpg') {
                $path = 'public/uploads/users/'.$request->old_image;
                if(File::exists($path)){
                    File::delete($path);
                }
            }
            $file = $request->file('edit_image');
            $ext = $file->getClientOriginalExtension();
            //$filename = time().'.'.$ext;
            $filename = md5(microtime()).'.'.$ext;
            $file->move('public/uploads/users/',$filename);
        }else{
            $filename = $request->old_image;
        }

        $item = User::find($request->user_id);
        $item->name = $request->edit_name;
        $item->slug = Str::slug($request->edit_name);
        $item->username = $request->edit_username;
        $item->email = $request->edit_email;
        if ($request->edit_password) {
            $item->password = Hash::make($request->edit_password);
        }
        $item->image = $filename;
        $item->role = $request->edit_role;
        if ($item->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'User updated Successfully'
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
        $user = User::find($id);

        $posts = Posts::where('user_id', $user->id)->get();
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
                                $user->unlike($reply);
                                $reply->delete();
                            }
                        }
                        Reports::where('report_id', $comment->id)->where('report_type', 'Comment')->delete();
                        $user->unlike($comment);
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
                $user->unfavorite($post);
                $user->unlike($post);
                Reactions::where('user_id', $user->id)->where('reactable_id', $post->id)->delete();
                Reports::where('report_id', $post->id)->where('report_type', 'Post')->delete();
                Shares::where('post_id', $post->id)->delete();
                $post->delete();
            }
        }

        //Chats & Messages
        $chats = Chats::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->get();
        if ($chats != null) {
            foreach ($chats as $chat) {

                $messages = Messages::where('chat_id', $chat->id)->get();
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

                $chat->delete();
            }
        }

        //User Views
        $user_views = UserViews::where('user_id', $user->id)->get();
        if ($user_views != null) {
            foreach ($user_views as $user_view) {
                $user_view->delete();
            }
        }

        //Notifications
        Notifications::where('sender_id', $user->id)->orWhere('recipient_id', $user->id)->delete();

        //Following
        Followable::where('user_id', $user->id)->orWhere('followable_id', $user->id)->delete();

        //Blocks
        Block::where('user_id', $user->id)->orWhere('block_id', $user->id)->delete();

        //Bans & Report User
        $user->unban();
        ReportUser::where('sender_id', $user->id)->orWhere('user_id', $user->id)->delete();

        //Points
        Points::where('user_id', $user->id)->delete();

        //Payments
        Deposits::where('user_id', $user->id)->delete();
        Payment::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->delete();
        Subscription::where('user_id', $user->id)->delete();
        Transaction::where('user_id', $user->id)->delete();
        Withdraw::where('user_id', $user->id)->delete();



        if ($user->image != 'avatar.jpg' || $user->image != 'male.png' || $user->image != 'male-2.png' || $user->image != 'female.png' || $user->image != 'female-2.jpg') {
            $path = 'public/uploads/users/'.$user->image;
            if(File::exists($path)){
                File::delete($path);
            }
        }
        $user->delete();
        return response()->json(['status' => 'User Deleted Successfully']);
	}

    public function funds($id)
    {
        $user = User::find($id);
        return view('admin.users.funds', ['user' => $user]);

    }

    public function update_funds(Request $request)
    {
        $item = User::find($request->id);
        $item->wallet = $request->wallet;
        if ($item->update()) {

            return redirect()->back()->with('success', 'User Wallet successfully updated');

        }
        else{

            return redirect()->back()->with('error', 'Something went wrong!');

        }
    }

    public function user(Request $request)
    {

        $username = $request->username;
        $user = User::where('username', $username)->first();
        $posts = Posts::where('user_id', $user->id)->count();
        $comments = Comments::where('user_id', $user->id)->count();
        $replies = Replies::where('user_id', $user->id)->count();
        $viewers = UserViews::where('user_id', $user->id)->count();

        $earnings = Payment::where('receiver_id', $user->id)->sum('amount');

        return view('admin.users.user', ['user' => $user,
                                            'posts' => $posts,
                                            'comments' => $comments,
                                            'replies' => $replies,
                                            'viewers' => $viewers,
                                            'earnings' => $earnings]);
    }

}
