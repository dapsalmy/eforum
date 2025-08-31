<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Plans;
use App\Models\Points;
use App\Models\Payment;
use App\Models\Withdraw;
use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Carbon;
use App\Models\Admin\EmailTemplate;
use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PricingController extends Controller
{
    public function index()
    {
        $plans = Plans::where('status', 1)->orderBy('order', 'asc')->get();
        return view('user.plans.index', ['plans' => $plans]);
    }

    public function pay(Request $request)
    {

        $id = $request->plan_id;
        $plan = Plans::find($id);

        if ($plan->price > Auth::user()->wallet)
        {
            return response()->json([
                'status' => 401,
                'messages' => 'You have less money in your Wallet to purchase this Plan'
            ]);
        }

        $amount = Auth::user()->wallet - $plan->price;

        //User wallet
        User::where('id',Auth::user()->id)->update([
            'wallet' => $amount,
            'plan_id' => $plan->id
        ]);

        if ($plan->verified == 1) {
            User::where('id',Auth::user()->id)->update([
                'verified' => 1,
            ]);
        }else {
             User::where('id',Auth::user()->id)->update([
                 'verified' => 0,
             ]);
        }

        //Old Subscriptions
        Subscription::where('user_id', Auth::user()->id)->update([
            'status' => 0
        ]);

        if ($plan->duration == 'Monthly') {
            $new_date = Carbon::now()->addMonth();
        }elseif($plan->duration == 'Quaterly'){
            $new_date = Carbon::now()->addMonths('4');
        }elseif($plan->duration == 'Yearly'){
            $new_date = Carbon::now()->addYear();
        }elseif($plan->duration == 'Lifetime'){
            $new_date = Carbon::now()->addYears(50);
        }

        $item = new Subscription();
        $item->user_id = Auth::user()->id;
        $item->plan_id = $plan->id;
        $item->price = $plan->price;
        $item->points = $plan->points;
        $item->verified = $plan->verified;
        $item->categories = $plan->categories;
        $item->tips = $plan->tips;
        $item->comments = $plan->comments;
        $item->reactions = $plan->reactions;
        $item->followers = $plan->followers;
        $item->messages = $plan->messages;
        $item->users = $plan->users;
        $item->viewers = $plan->viewers;
        $item->ads = $plan->ads;
        $item->status = 1;
        $item->will_expire = $new_date;
        if ($item->save()) {

            Transaction::create([
                'user_id' => Auth::user()->id,
                'type_id' => $item->id,
                'type' => '1', //Subscription
                'amount' => $plan->price,
                'status' => '1'
            ]);

            if($plan->points != 0){
                if(get_setting('points_system') == '1'){
                    Points::create([
                        'user_id' => Auth::user()->id,
                        'type' => '9',
                        'score' => $plan->points
                    ]);
                }
            }

            return response()->json([
                'status' => 200,
                'messages' => 'Subscription Paid Successfully'
            ]);

        }else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }

    }

    public function subscriptions()
    {
        $subscriptions = Subscription::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        return view('user.plans.subscriptions', ['subscriptions' => $subscriptions]);
    }

    public function invoice($id)
    {
      $subscription = Subscription::whereId($id)->first();

      if ($subscription->user_id != Auth::user()->id) {
        return redirect()->route('user.subscriptions');
      }

     return view('user.plans.invoice', [
       'subscription' => $subscription,
     ]);
    }

    public function cancel(Request $request)
    {

       $id = $request->id;
       $plan = Plans::where('price', '0.00')->first();

       if ($plan->price > Auth::user()->wallet)
       {
           return response()->json([
               'status' => 401,
               'messages' => 'You have less money in your Wallet to purchase this Plan',
           ]);
       }

       $amount = Auth::user()->wallet - $plan->price;

       //User wallet
       User::where('id',Auth::user()->id)->update([
           'wallet' => $amount,
           'plan_id' => $plan->id
       ]);

       if ($plan->verified == 1) {
           User::where('id',Auth::user()->id)->update([
               'verified' => 1,
           ]);
       }else {
            User::where('id',Auth::user()->id)->update([
                'verified' => 0,
            ]);
       }

       //Old Subscriptions
       Subscription::where('user_id', Auth::user()->id)->update([
           'status' => 0
       ]);

       if ($plan->duration == 'Monthly') {
           $new_date = Carbon::now()->addMonth();
       }elseif($plan->duration == 'Quaterly'){
           $new_date = Carbon::now()->addMonths('4');
       }elseif($plan->duration == 'Yearly'){
           $new_date = Carbon::now()->addYear();
       }elseif($plan->duration == 'Lifetime'){
           $new_date = Carbon::now()->addYears(50);
       }

       $item = new Subscription();
       $item->user_id = Auth::user()->id;
       $item->plan_id = $plan->id;
       $item->price = $plan->price;
       $item->points = $plan->points;
       $item->verified = $plan->verified;
       $item->categories = $plan->categories;
       $item->tips = $plan->tips;
       $item->comments = $plan->comments;
       $item->reactions = $plan->reactions;
       $item->followers = $plan->followers;
       $item->messages = $plan->messages;
       $item->users = $plan->users;
       $item->viewers = $plan->viewers;
       $item->ads = $plan->ads;
       $item->status = 1;
       $item->will_expire = $new_date;
       if ($item->save()) {

        Transaction::create([
            'user_id' => Auth::user()->id,
            'type_id' => $item->id,
            'type' => '1', //Subscription
            'amount' => $plan->price,
            'status' => '1'
        ]);

           if($plan->points != '0'){
                if(get_setting('points_system') == '1'){
                    Points::create([
                        'user_id' => Auth::user()->id,
                        'type' => '9',
                        'score' => $plan->points
                    ]);
                }
           }

           return response()->json([
               'status' => 200,
               'messages' => 'Subscription Cancelled Successfully'
           ]);

       }else{

           return response()->json([
               'status' => 200,
               'messages' => 'Error, Something went wrong',
           ]);

       }

    }

    public function tip(Request $request)
    {

        $user_id = $request->user_id;
        $tip = $request->tip;

        if ($tip < get_setting('min_tip'))
        {
            return response()->json([
                'status' => 401,
                'messages' => 'Tip should me more than then Minimum',
            ]);
        }

        if ($tip > Auth::user()->wallet)
        {
            return response()->json([
                'status' => 401,
                'messages' => 'You have less money in your Wallet to send Tip',
            ]);
        }

        $amount = Auth::user()->wallet - $tip;

        //User wallet
        User::where('id',Auth::user()->id)->update([
            'wallet' => $amount
        ]);

        $admin_amount = (get_setting('tips_commission')/100) * $tip;
        $user_amount = $tip - $admin_amount;
        $new_user = User::where('id',$user_id)->first();
        $total_user_amount = $user_amount + $new_user->earnings;

        //User earnings
        User::where('id',$user_id)->update([
            'earnings' => $total_user_amount
        ]);

        $item = new Payment();
        $item->sender_id = Auth::user()->id;
        $item->receiver_id = $user_id;
        $item->commission_fee = get_setting('tips_commission');
        $item->admin_amount = $admin_amount;
        $item->amount = $user_amount;
        if ($item->save()) {

            //Notification
            Notifications::create([
                'sender_id' => Auth::user()->id,
                'recipient_id' => $user_id,
                'notification_type' => "Tip",
                'tip_id' => $item->id,
                'seen' => 2,
            ]);

            if (get_setting('enable_emails') == 'Yes') {
                $re = User::where('id',$user_id)->first();
                if ($re->email_tip == '1') {

                    //Replace paremeter
                    $replace = array(
                        '{url}'              => env('APP_URL'),
                        '{logo}'              => get_setting('logo_dark'),
                        '{sitename}'              => get_setting('site_name'),
                        '{year}'              => date('Y'),
                        '{name}'              => Auth::user()->name,
                        '{image}'              => Auth::user()->image,
                        '{currency}'              => get_setting('currency_symbol'),
                        '{amount}'              => $tip,
                    );

                    //Send email
                    $template = EmailTemplate::where('name','tip')->first();
                    $template->body = process_string($replace, $template->body);

                    Mail::to($re->email)->send(new GeneralMail($template));
                }
            }

            return response()->json([
                'status' => 200,
                'messages' => 'Sent '. get_setting('currency_symbol').$tip .' Successfully',
            ]);

        }else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong',
            ]);

        }
    }

    public function earnings()
    {
        $earnings = Payment::where('receiver_id',Auth::user()->id)->orderBy('id', 'desc')->get();
        $tips = Payment::where('sender_id',Auth::user()->id)->orderBy('id', 'desc')->get();
        return view('user.earnings.index', ['earnings' => $earnings,'tips' => $tips]);
    }

    public function withdrawals()
    {
        $withdrawals = Withdraw::where('user_id',Auth::user()->id)->orderBy('id', 'desc')->get();
        return view('user.earnings.withdrawals', ['withdrawals' => $withdrawals]);
    }

    public function set(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'paypal_email' => 'required|string|email|max:255',
        ],[
            'paypal_email.required' => 'PayPal Email is Required',
            'paypal_email.string' => 'PayPal Email should be a String',
            'paypal_email.email' => 'PayPal Email should be an Email',
            'paypal_email.max:255' => 'PayPal Email max characters should be 255',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $paypal_email = $request->paypal_email;

        //User
        User::where('id',Auth::user()->id)->update([
            'paypal_email' => $paypal_email
        ]);

        return response()->json([
                'status' => 200,
                'messages' => 'Successfully set your PayPal Email'
        ]);

    }

    public function withdraw(Request $request)
    {

        $amount = $request->amount;

        if (Auth::user()->paypal_email === Null)
        {
            return response()->json([
                'status' => 401,
                'messages' => 'Please set your PayPal Email in order to Withdraw',
            ]);
        }

        if ($amount < get_setting('min_withdraw'))
        {
            return response()->json([
                'status' => 401,
                'messages' => 'Withdrawal should me more than then Minimum',
            ]);
        }

        if ($amount > Auth::user()->earnings)
        {
            return response()->json([
                'status' => 401,
                'messages' => 'You have less money in your Earnings to Withdraw',
            ]);
        }

        $balance = Auth::user()->earnings - $amount;

        //User wallet
        User::where('id',Auth::user()->id)->update([
            'earnings' => $balance
        ]);


        $process_date = Carbon::now()->addDays(get_setting('days_withdraw'));

        $item = new Withdraw();
        $item->user_id = Auth::user()->id;
        $item->paypal_email = Auth::user()->paypal_email;
        $item->amount = $amount;
        $item->status = 0;
        $item->process_date = $process_date;
        if ($item->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Withdrew '. get_setting('currency_symbol').$amount .' Successfully',
            ]);

        }else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong',
            ]);

        }

    }
}
