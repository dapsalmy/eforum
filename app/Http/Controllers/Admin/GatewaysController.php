<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Utility\SettingsUtility;
use App\Http\Controllers\Controller;

class GatewaysController extends Controller
{
    public function paypal()
    {
        return view('admin.gateways.index');
    }

    public function paypal_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                if($type == 'paypal_mode'){
                    overWriteEnvFile('PAYPAL_MODE',trim($value));
                }
                if($type == 'paypal_client_id'){
                    overWriteEnvFile('PAYPAL_CLIENT_ID',trim($value));
                }
                if($type == 'paypal_secret'){
                    overWriteEnvFile('PAYPAL_CLIENT_SECRET',trim($value));
                }

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'PayPal Settings Updated Successfully');
    }

    public function stripe()
    {
        return view('admin.gateways.index');
    }

    public function stripe_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Stripe Settings Updated Successfully');
    }
}
