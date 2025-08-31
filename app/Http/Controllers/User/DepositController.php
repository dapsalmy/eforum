<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Deposits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\EmailTemplate;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Unicodeveloper\Paystack\Paystack;
use Illuminate\Support\Facades\Redirect;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class DepositController extends Controller
{

    public function index()
    {
        $deposits = Deposits::whereUserId(Auth::user()->id)->orderBy('id', 'desc')->get();
        return view('user.deposits.index', ['deposits' => $deposits]);
    }

    public function invoice($id)
    {
      $deposit = Deposits::whereId($id)->whereStatus('1')->first();

      if ($deposit->user_id != Auth::user()->id) {
        return redirect()->route('user.wallet');
      }


      $totalAmount = ($deposit->amount + $deposit->transaction_fee);

     return view('user.deposits.invoice', [
       'deposit' => $deposit,
       'totalAmount' => $totalAmount,
     ]);
    }

    public function add_funds(Request $request)
    {
        switch ($request->payment_gateway) {
            case 'PayPal':
                return $this->payPal($request);
                break;

            case 'Stripe':
                return $this->stripe($request);
                break;

            case 'Paystack':
                return $this->paystack($request);
                break;

            case 'Bank':
                return $this->sendBankTransfer();
                break;
        }
    }

    protected function paypal($request){

        $urlSuccess = route('paypal.success');
        $urlCancel   = route('user.wallet');

        $feePayPal   = get_setting('paypal_fee');
        $centsPayPal =  get_setting('paypal_fee_cents');

        $amountFixed = number_format($request->amount + ($request->amount * $feePayPal / 100) + $centsPayPal, 2, '.', '');

        try {
            // Init PayPal
            $provider = new PayPalClient();
            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);


            $order = $provider->createOrder([
              "intent"=> "CAPTURE",
              'application_context' =>
                  [
                      'return_url' => $urlSuccess,
                      'cancel_url' => $urlCancel,
                      'shipping_preference' => 'NO_SHIPPING'
                  ],
              "purchase_units"=> [
                   [
                      "amount"=> [
                          "currency_code"=> get_setting('currency_code'),
                          "value"=> $amountFixed,
                          'breakdown' => [
                            'item_total' => [
                              "currency_code"=> get_setting('currency_code'),
                              "value"=> $amountFixed
                            ],
                          ],
                      ],
                       'description' => 'Add Funds '. Auth::user()->name,

                       'items' => [
                         [
                           'name' => 'Add Funds '. Auth::user()->name,
                            'category' => 'DIGITAL_GOODS',
                              'quantity' => '1',
                              'unit_amount' => [
                                "currency_code"=> get_setting('currency_code'),
                                "value" => $amountFixed
                              ],
                         ],
                      ],

                      'custom_id' => http_build_query([
                          'id' => Auth::user()->id,
                          'amount' => $request->amount,
                          'taxes' => '',
                          'type' => 'deposit'
                      ]),
                  ],
              ],
          ]);

          $url = $order['links'][1]['href'];

          return Redirect::to($url);


        } catch (\Exception $e) {

          Log::debug($e);

          return response()->json([
            'errors' => ['error' => $e->getMessage()]
          ]);
        }
    }

    public function paypal_success(Request $request)
    {

      // Init PayPal
      $provider = new PayPalClient();
      $token = $provider->getAccessToken();
      $provider->setAccessToken($token);

      try {
        // Get PaymentOrder using our transaction ID
        $order = $provider->capturePaymentOrder($request->token);
        $deposit_id = $order['purchase_units'][0]['payments']['captures'][0]['id'];

        // Parse the custom data parameters
        parse_str($order['purchase_units'][0]['payments']['captures'][0]['custom_id'] ?? null, $data);

        if ($order['status'] && $order['status'] === "COMPLETED") {
          if ($data) {

                // Check outh POST variable and insert in DB
                $verified_deposit_id = Deposits::where('deposit_id', $deposit_id)->first();

                  if (!isset($verified_deposit_id)) {
                    // Insert Deposit

                    DB::table('deposits')->insert([
                        'user_id' => $data['id'],
                        'deposit_id' => $deposit_id,
                        'amount' => $data['amount'],
                        'gateway' => 'PayPal',
                        'percentage_applied' => get_setting('paypal_fee').'% + '.get_setting('paypal_fee_cents'),
                        'transaction_fee' => number_format(($data['amount'] * get_setting('paypal_fee') / 100) + get_setting('paypal_fee_cents'), 2, '.', ''),
                        'status' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    // Add Funds to User
                    User::find($data['id'])->increment('wallet', $data['amount']);

                    if (get_setting('enable_emails') == 'Yes') {
                        $re = User::where('id',$data['id'])->first();
                        if ($re->email_funds == '1') {

                            //Replace paremeter
                            $replace = array(
                                '{url}'              => env('APP_URL'),
                                '{logo}'              => get_setting('logo_dark'),
                                '{sitename}'          => get_setting('site_name'),
                                '{year}'              => date('Y'),
                                '{name}'              => $re->name,
                                '{currency}'              => get_setting('currency_symbol'),
                                '{amount}'              => $data['amount'],
                            );

                            //Send email
                            $template = EmailTemplate::where('name','funds')->first();
                            $template->body = process_string($replace, $template->body);

                            Mail::to($re->email)->send(new GeneralMail($template));
                        }
                    }


                  }// <--- Verified Txn ID

                    return redirect()->route('user.wallet')->with('success', 'Deposited '. get_setting('currency_symbol').$data['amount'] .' Successfully');

          }// data

          return redirect('/');
        }

      }  catch (\Exception $e) {
        return redirect('/');
      }

    }


    protected function stripe($request)
    {

          $feeStripe   = get_setting('stripe_fee');
          $centsStripe = get_setting('stripe_fee_cents');

          if (get_setting('currency_code') == 'JPY') {
            $amountFixed = round($request->amount + ($request->amount * $feeStripe / 100) + $centsStripe);
          } else {
            $amountFixed = number_format($request->amount + ($request->amount * $feeStripe / 100) + $centsStripe, 2, '.', '');
          }

            $amount   = get_setting('currency_code') == 'JPY' ? $amountFixed : ($amountFixed*100);

            $currency_code = get_setting('currency_code');
            $description = 'Add Funds '. Auth::user()->name;

          $stripe = new \Stripe\StripeClient(get_setting('stripe_secret'));

          $checkout = $stripe->checkout->sessions->create([
            'line_items' => [[
              'price_data' => [
                'currency' => $currency_code,
                'product_data' => [
                  'name' => $description,
                ],
                'unit_amount' => $amount,
              ],
              'quantity' => 1,
            ]],
            'mode' => 'payment',

                'metadata' => [
                  'user' => Auth::user()->id,
                  'amount' => $request->amount,
                  'taxes' => '',
                  'type' => 'deposit'
                ],

            'payment_method_types' => ['card'],
            'customer_email' => Auth::user()->email,

            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
          ]);

          // Insert Deposit
          DB::table('deposits')->insert([
            'user_id' => Auth::user()->id,
            'deposit_id' => $checkout->id,
            'amount' => $request->amount,
            'gateway' => 'Stripe',
            'percentage_applied' => get_setting('stripe_fee').'% + '.get_setting('stripe_fee_cents'),
            'transaction_fee' => number_format(($request->amount * get_setting('stripe_fee') / 100) + get_setting('stripe_fee_cents'), 2, '.', ''),
            'status' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
          ]);

          return Redirect::to($checkout->url);

    }

    public function stripe_success(Request $request)
    {

        $stripe = new \Stripe\StripeClient(get_setting('stripe_secret'));
        $session_id = $request->get('session_id');
        $session = $stripe->checkout->sessions->retrieve($session_id);
        if (!$session) {
            return redirect()->route('user.wallet')->with('error', 'Invalid Session ID');
        }else{
                $deposit = Deposits::where('deposit_id', $session_id)->first();
                if (!$deposit) {
                  return redirect()->route('user.wallet')->with('error', 'Deposit Transaction not found');
                }

                $deposit->update(['status'=> 1]);

                // Add Funds to User
                User::find(Auth::user()->id)->increment('wallet', $deposit->amount);

                if (get_setting('enable_emails') == 'Yes') {

                    if (Auth::user()->email_funds == '1') {

                        //Replace paremeter
                        $replace = array(
                            '{url}'              => env('APP_URL'),
                            '{logo}'              => get_setting('logo_dark'),
                            '{sitename}'          => get_setting('site_name'),
                            '{year}'              => date('Y'),
                            '{name}'              => Auth::user()->name,
                            '{currency}'          => get_setting('currency_symbol'),
                            '{amount}'            => $deposit->amount,
                        );

                        //Send email
                        $template = EmailTemplate::where('name','funds')->first();
                        $template->body = process_string($replace, $template->body);

                        Mail::to(Auth::user()->email)->send(new GeneralMail($template));
                    }
                }

                return redirect()->route('user.wallet')->with('success', 'Deposited '. get_setting('currency_symbol').$deposit->amount .' Successfully');
        }

    }

    public function stripe_cancel(Request $request)
    {

        $stripe = new \Stripe\StripeClient(get_setting('stripe_secret'));
        $session_id = $request->get('session_id');
        $session = $stripe->checkout->sessions->retrieve($session_id);
        if (!$session) {
            return redirect()->route('user.wallet')->with('error', 'Invalid Session ID');
        }else{
                Deposits::where('deposit_id', $session_id)->delete();
                return redirect()->route('user.wallet')->with('success', 'Stripe Payment Cancelled.');
        }

    }

}
