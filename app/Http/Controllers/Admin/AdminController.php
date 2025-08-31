<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Posts;
use App\Models\Replies;
use App\Models\Comments;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::count();
        $posts = Posts::count();
        $comments = Comments::count();
        $replies = Replies::count();

        $earnings = [];
        //Looping through the month array to get count for each month in the provided year

        for($i = 1; $i <= 12; $i++){
            $earnings[] = Transaction::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::today()->startOfMonth()->month($i))
            ->groupBy(DB::raw("Month(created_at)"))
            ->sum('amount');
        }

        $earnings = json_encode(array_values($earnings), JSON_NUMERIC_CHECK);

        $transactions = Transaction::sum('amount');
        $commissions = Payment::sum('admin_amount');

        return view('admin.dashboard', ['posts' => $posts,
                                        'comments' => $comments,
                                        'replies' => $replies,
                                        'users' => $users,
                                        'earnings' => $earnings,
                                        'transactions' => $transactions,
                                        'commissions' => $commissions]);
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|max:255|alpha_num:ascii',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'same:confirm_password',
        ],[
            'name.required' => 'Category Name is Required',
            'name.string' => 'Category Name should be a String',
            'name.max' => 'Category Name max characters should be 255',
            'email.required' => 'Email is Required',
            'email.string' => 'Email should be a String',
            'email.email' => 'Email should be an Email',
            'email.max:255' => 'Email max characters should be 255',
            'username.required' => 'Username is Required',
            'username.string' => 'Username should be a String',
            'username.max' => 'Username max characters should be 255',
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
        if ($request->password) {
            $item->password = Hash::make($request->password);
        }
        $item->image = $filename;
        if ($item->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Admin updated Successfully'
            ]);
        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Something went wrong'
            ]);

        }

    }
}
