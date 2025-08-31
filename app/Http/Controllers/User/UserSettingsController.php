<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Posts;
use App\Models\Payment;
use App\Models\Replies;
use App\Models\Comments;
use App\Models\Favorites;
use App\Models\UserViews;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Admin\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserSettingsController extends Controller
{

    public function profile()
    {
        $countries = Country::where('status','1')->get();
        return view('user.settings.index', ['countries' => $countries]);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|max:255|alpha_num:ascii',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            'username.alpha_num' => 'Username should be Alphanumeral characters',
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
            if ($request->old_image != 'avatar.jpg' || $request->old_image != 'male.png' || $request->old_image != 'male-2.png' || $request->old_image != 'female.png' || $request->old_image != 'female-2.jpg') {
                $path = 'public/uploads/users/'.$request->old_image;
                if(File::exists($path)){
                    File::delete($path);
                }
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            //$filename = time().'.'.$ext;
            $filename = md5(microtime()).'.'.$ext;
            $file->move('public/uploads/users/',$filename);
        }else{
            $filename = $request->old_image;
        }

        $item = User::find($request->user_id);
        $item->name = $request->name;
        $item->slug = Str::slug($request->name);
        $item->username = $request->username;
        $item->email = $request->email;
        $item->image = $filename;
        if ($request->phone) {
            $item->phone = $request->phone;
        }
        if ($request->profession) {
            $item->profession = $request->profession;
        }
        if ($request->gender) {
            $item->gender = $request->gender;
        }
        if ($request->bio) {
            $item->bio = $request->bio;
        }
        if ($request->location) {
            $item->location = $request->location;
        }
        if ($request->country) {
            $item->country = $request->country;
        }
        if ($request->website) {
            $item->website = $request->website;
        }
        if ($request->twitter) {
            $item->twitter = $request->twitter;
        }
        if ($request->facebook) {
            $item->facebook = $request->facebook;
        }
        if ($request->instagram) {
            $item->instagram = $request->instagram;
        }
        if ($request->linkedin) {
            $item->linkedin = $request->linkedin;
        }
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

    public function password()
    {
        return view('user.settings.index');
    }

    public function password_update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:4',
            'new_password' => 'required|string|min:4|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'min:4',
        ],[
            'current_password.required' => 'Current Password is Required',
            'new_password.required' => 'Password is Required',
            'new_password.string' => 'Password should be a String',
            'new_password.min:4' => 'Password should be min 4 characters',
            'password.same:confirm_password' => 'Confirmation Password should be the same ',
            'confirm_password.required' => 'Confirmation Password is Required',
            'confirm_password.min:4' => 'Password Confirmation should be min 4 characters',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }



        // The passwords matches
        if (!Hash::check($request->get('current_password'), Auth::user()->password))
        {
            return response()->json([
                'status' => 401,
                'messages' => 'Current Password is Invalid'
            ]);
        }

        // Current password and new password same
        if (strcmp($request->get('current_password'), $request->new_password) == 0)
        {
            return response()->json([
                'status' => 401,
                'messages' => 'New Password cannot be same as your current password.'
            ]);
        }

        $item = User::find(Auth::user()->id);
        $item->password =  Hash::make($request->new_password);
        if ($item->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'User Password updated Successfully'
            ]);
        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Something went wrong'
            ]);

        }

    }

    public function email_notifications()
    {
        return view('user.settings.index');
    }
    public function email_notifications_update(Request $request)
    {

        $item = User::find($request->user_id);
        $item->email_comment = ($request->email_comment == '1' ? '1' : '0');
        $item->email_reply = ($request->email_reply == '1' ? '1' : '0');
        $item->email_follower = ($request->email_follower == '1' ? '1' : '0');
        $item->email_message = ($request->email_message == '1' ? '1' : '0');
        $item->email_tip = ($request->email_tip == '1' ? '1' : '0');
        $item->email_funds = ($request->email_funds == '1' ? '1' : '0');
        if ($item->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'User Email Notifications updated Successfully'
            ]);
        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Something went wrong'
            ]);

        }

    }

    public function notifications()
    {
        return view('user.notifications.index');
    }

    public function mark_as_read()
    {
        $update = Auth::user()->mark_as_read();

        if($update == true)
        {
            return response()->json([
                'bool'=>true
            ]);

        } else
        {
            return response()->json([
                'bool'=>false
            ]);
        }
    }

	public function notifications_destroy(Request $request){

        $id = $request->id;
        $item = Notifications::find($id);
        $item->delete();
        return response()->json(['status' => 'Notification Deleted Successfully']);
	}

    public function bookmarks()
    {
        $posts = Posts::whereHas('favorited', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->orderByDesc('created_at')->paginate(get_setting('results_per_page'));

        return view('user.bookmarks.index', ['posts' => $posts]);
    }

    public function paginateBookmarks()
    {
        $posts = Posts::whereHas('favorited', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->orderByDesc('created_at')->paginate(get_setting('results_per_page'));
        return view('frontend.pagination.user.bookmarks', ['posts' => $posts]);
    }

	public function bookmarks_destroy(Request $request){

        $id = $request->id;
        Favorites::where('user_id', Auth::user()->id)->where('favoriteable_id', $id)->delete();
        return response()->json(['status' => 'Bookmark Removed Successfully']);
	}

    public function overview()
    {
        $posts = Posts::where('user_id', Auth::user()->id)->count();
        $comments = Comments::where('user_id', Auth::user()->id)->count();
        $replies = Replies::where('user_id', Auth::user()->id)->count();
        $viewers = UserViews::where('user_id', Auth::user()->id)->count();
        $bookmarks = Posts::whereHas('favorited', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->count();

        $earnings = Payment::where('receiver_id', Auth::user()->id)->sum('amount');

        $user_views = [];
        //Looping through the month array to get count for each month in the provided year

        for($i = 1; $i <= 12; $i++){
            $user_views[] = UserViews::where('user_id', Auth::user()->id)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::today()->startOfMonth()->month($i))
            ->groupBy(DB::raw("Month(created_at)"))
            ->count();
        }

        $user_views = json_encode(array_values($user_views));

        return view('user.overview.index', ['posts' => $posts,
                                            'comments' => $comments,
                                            'replies' => $replies,
                                            'viewers' => $viewers,
                                            'bookmarks' => $bookmarks,
                                            'user_views' => $user_views,
                                            'earnings' => $earnings]);
    }

    public function followers()
    {
        return view('user.overview.followers');
    }

    public function following()
    {
        return view('user.overview.followers');
    }
}
