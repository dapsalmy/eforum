<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Utility\SettingsUtility;
use App\Http\Controllers\Controller;
use App\Models\Admin\Badge;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                if($type == 'logo_light'){
                    if($request->hasFile('logo_light'))
                    {
                        $path = 'public/uploads/settings/'.get_setting('logo_light');
                        if(File::exists($path)){
                            File::delete($path);
                        }
                        $file = $request->file('logo_light');
                        $ext = $file->getClientOriginalExtension();
                        $filename = time().'.'.$ext;
                        $file->move('public/uploads/settings/',$filename);
                        $value = $filename;
                    }
                    SettingsUtility::save_settings($type,trim($value));
                }

                if($type == 'logo_dark'){
                    if($request->hasFile('logo_dark'))
                    {
                        $path = 'public/uploads/settings/'.get_setting('logo_dark');
                        if(File::exists($path)){
                            File::delete($path);
                        }
                        $file = $request->file('logo_dark');
                        $ext = $file->getClientOriginalExtension();
                        $filename = time().'.'.$ext;
                        $file->move('public/uploads/settings/',$filename);
                        $value = $filename;
                    }
                    SettingsUtility::save_settings($type,trim($value));
                }

                if($type == 'favicon'){
                    if($request->hasFile('favicon'))
                    {
                        $path = 'public/uploads/settings/'.get_setting('favicon');
                        if(File::exists($path)){
                            File::delete($path);
                        }
                        $file = $request->file('favicon');
                        $ext = $file->getClientOriginalExtension();
                        $filename = time().'.'.$ext;
                        $file->move('public/uploads/settings/',$filename);
                        $value = $filename;
                    }
                    SettingsUtility::save_settings($type,trim($value));
                }

                if($type == 'login_bg'){
                    if($request->hasFile('login_bg'))
                    {
                        $path = 'public/uploads/settings/'.get_setting('login_bg');
                        if(File::exists($path)){
                            File::delete($path);
                        }
                        $file = $request->file('login_bg');
                        $ext = $file->getClientOriginalExtension();
                        $filename = time().'.'.$ext;
                        $file->move('public/uploads/settings/',$filename);
                        $value = $filename;
                    }
                    SettingsUtility::save_settings($type,trim($value));
                }

                SettingsUtility::save_settings($type,trim($value));

                if($type == 'timezone'){
                    overWriteEnvFile('APP_TIMEZONE',trim($value));
                }

                if($type == 'dates_language'){
                    overWriteEnvFile('DATE_LANGUAGE',trim($value));
                }
            }
        }

        return redirect()->back()->with('success', 'Settings Updated Successfully');
    }


    public function home()
    {
        return view('admin.settings.index');
    }

    public function home_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                if($type == 'home_bg'){
                    if($request->hasFile('home_bg'))
                    {
                        $path = 'public/uploads/settings/'.get_setting('home_bg');
                        if(File::exists($path)){
                            File::delete($path);
                        }
                        $file = $request->file('home_bg');
                        $ext = $file->getClientOriginalExtension();
                        $filename = time().'.'.$ext;
                        $file->move('public/uploads/settings/',$filename);
                        $value = $filename;
                    }
                    SettingsUtility::save_settings($type,trim($value));
                }

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Home Settings Updated Successfully');
    }


    public function forum()
    {
        return view('admin.settings.index');
    }

    public function forum_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Forum Settings Updated Successfully');
    }


    public function points()
    {
        $badges = Badge::all();
        return view('admin.settings.index', ['badges' => $badges]);
    }

    public function points_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Points Settings Updated Successfully');
    }

    public function currency()
    {
        return view('admin.settings.index');
    }

    public function currency_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                if($type == 'currency_code'){
                    overWriteEnvFile('CURRENCY_CODE',trim($value));
                }

                if($type == 'currency_symbol'){
                    overWriteEnvFile('CURRENCY_SYMBOL',trim($value));
                }

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Currency Settings Updated Successfully');
    }

    public function payments()
    {
        return view('admin.settings.index');
    }

    public function payments_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Payments Settings Updated Successfully');
    }

    public function gateways()
    {
        return view('admin.settings.index');
    }

    public function gateways_post(Request $request)
    {
        $inputs = $request->except(['_token']);
        
        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {
                SettingsUtility::save_settings($type,trim($value));
            }
        }

        return redirect()->back()->with('success', 'Gateways Settings Updated Successfully');
    }
    
    public function storage()
    {
        return view('admin.settings.index');
    }

    public function storage_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Storage Settings Updated Successfully');
    }

    public function ads()
    {
        return view('admin.settings.index');
    }

    public function ads_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Ads Set Successfully');
    }

    public function analytics()
    {
        return view('admin.settings.index');
    }

    public function analytics_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Analytics Set Successfully');
    }

    public function adsense()
    {
        return view('admin.settings.index');
    }

    public function adsense_post(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Adsense Set Successfully');
    }

}
