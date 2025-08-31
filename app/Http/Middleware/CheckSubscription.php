<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Plans;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Carbon::now() >= Auth::user()->subscription()->will_expire) {

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

            if ($plan->verified == 1) {
                $verified = '1';
            }else {
                $verified = '0';
            }

            User::find(Auth::user()->id)->update([
                'plan_id' => $plan->id,
                'verified' => $verified
            ]);

            Subscription::find(Auth::user()->subscription()->id)->update([
                'status' => 0,
            ]);

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
        }

        return $next($request);
    }
}
