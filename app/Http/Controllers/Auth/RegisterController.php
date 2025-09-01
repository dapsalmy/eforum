<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Plans;
use App\Models\Points;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        if (get_setting('recaptcha_active') == 'Yes') {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users|alpha_num:ascii',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:4|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:4',
                'terms' => 'accepted',
                'g-recaptcha-response' => 'required|recaptcha'
            ],[
                'name.required' => 'Name is Required',
                'name.string' => 'Name should be a String',
                'name.max' => 'Name max characters should be 255',
                'username.required' => 'Username is Required',
                'username.string' => 'Username should be a String',
                'username.max' => 'Username max characters should be 255',
                'username.unique:users' => 'Username should be unique from other Users',
                'username.alpha_num' => 'Username should be Alphanumeral characters',
                'email.required' => 'Email is Required',
                'email.string' => 'Email should be a String',
                'email.email' => 'Email should be an Email',
                'email.max:255' => 'Email max characters should be 255',
                'email.unique:users' => 'Email should be unique from other Users',
                'password.required' => 'Password is Required',
                'password.string' => 'Password should be a String',
                'password.min:4' => 'Password should be min 4 characters',
                'password.same:password_confirmation' => 'Confirmation Password should be the same ',
                'password_confirmation.required' => 'Confirmation Password is Required',
                'password_confirmation.min:4' => 'Password Confirmation should be min 4 characters',
                'terms.accepted' => 'Please check our Terms and Conditions to Register',
                'g-recaptcha-response.recaptcha' => 'Captcha verification failed',
                'g-recaptcha-response.required' => 'Please complete the captcha'
            ]);

        }else {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users|alpha_num:ascii',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:4|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:4',
                'terms' => 'accepted'
            ],[
                'name.required' => 'Name is Required',
                'name.string' => 'Name should be a String',
                'name.max' => 'Name max characters should be 255',
                'username.required' => 'Username is Required',
                'username.string' => 'Username should be a String',
                'username.max' => 'Username max characters should be 255',
                'username.unique:users' => 'Username should be unique from other Users',
                'username.alpha_num' => 'Username should be Alphanumeral characters',
                'email.required' => 'Email is Required',
                'email.string' => 'Email should be a String',
                'email.email' => 'Email should be an Email',
                'email.max:255' => 'Email max characters should be 255',
                'email.unique:users' => 'Email should be unique from other Users',
                'password.required' => 'Password is Required',
                'password.string' => 'Password should be a String',
                'password.min:4' => 'Password should be min 4 characters',
                'password.same:password_confirmation' => 'Confirmation Password should be the same ',
                'password_confirmation.required' => 'Confirmation Password is Required',
                'password_confirmation.min:4' => 'Password Confirmation should be min 4 characters',
                'terms.accepted' => 'Please check our Terms and Conditions to Register',
            ]);
        }

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

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
        if ($request->gender == 'Male') {
            $male = ["male.png", "male-2.jpg"];
            $throw = array_rand($male);
            $item->image = $male[$throw];
        }elseif($request->gender == 'Female') {
            $female = ["female.png", "female-2.jpg"];
            $throw_f = array_rand($female);
            $item->image = $female[$throw_f];
        }
        $item->password = Hash::make($request->password);
        $item->wallet = '0.00';
        $item->earnings = '0.00';
        $item->plan_id = $plan->id;
        if ($plan->verified == 1) {
            $item->verified = '1';
        }else {
            $item->verified = '0';
        }
        if ($item->save()) {

            Auth::login($item);

            $sub = Subscription::create([
                'user_id' => Auth::user()->id,
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
                'user_id' => Auth::user()->id,
                'type_id' => $sub->id,
                'type' => '1', //Subscription
                'amount' => $plan->price,
                'status' => '1'
            ]);

            if(get_setting('points_system') == 1){
                Points::create([
                    'user_id' => Auth::user()->id,
                    'type' => '2',
                    'score' => get_setting('registration')
                ]);

                Points::create([
                    'user_id' => Auth::user()->id,
                    'type' => '9',
                    'score' => $plan->points
                ]);
            }

            return response()->json([
                'status' => 200,
                'messages' => trans('registered_successfully')
            ]);

        }
        else{
            \App\Services\ErrorTrackingService::reportMessage('User registration failed', 'error', [
                'email' => $request->email,
                'username' => $request->username,
                'plan_id' => $plan->id ?? null,
            ]);

            return response()->json([
                'status' => 401,
                'messages' => trans('error_something_went_wrong')
            ]);

        }

    }

}
