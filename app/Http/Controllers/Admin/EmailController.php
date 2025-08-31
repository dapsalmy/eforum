<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index()
    {
        return view('admin.email.index');
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                if($type == 'mail_mailer'){
                    overWriteEnvFile('MAIL_MAILER',trim($value));
                }

                if($type == 'mail_host'){
                    overWriteEnvFile('MAIL_HOST',trim($value));
                }

                if($type == 'mail_port'){
                    overWriteEnvFile('MAIL_PORT',trim($value));
                }

                if($type == 'mail_username'){
                    overWriteEnvFile('MAIL_USERNAME',trim($value));
                }

                if($type == 'mail_password'){
                    overWriteEnvFile('MAIL_PASSWORD',trim($value));
                }

                if($type == 'mail_encryption'){
                    overWriteEnvFile('MAIL_ENCRYPTION',trim($value));
                }

                if($type == 'mail_from_address'){
                    overWriteEnvFile('MAIL_FROM_ADDRESS',trim($value));
                }

                if($type == 'mail_from_name'){
                    overWriteEnvFile('MAIL_FROM_NAME',trim($value));
                }

                if($type == 'mail_from_domain'){
                    overWriteEnvFile('MAIL_FROM_DOMAIN',trim($value));
                }

                if($type == 'mail_return_path'){
                    overWriteEnvFile('MAIL_RETURN_PATH',trim($value));
                }

                // Amazon SES Settings
                if($type == 'aws_access_key_id'){
                    overWriteEnvFile('AWS_ACCESS_KEY_ID',trim($value));
                }

                if($type == 'aws_secret_access_key'){
                    overWriteEnvFile('AWS_SECRET_ACCESS_KEY',trim($value));
                }

                if($type == 'aws_default_region'){
                    overWriteEnvFile('AWS_DEFAULT_REGION',trim($value));
                }

                if($type == 'ses_region'){
                    overWriteEnvFile('AWS_SES_REGION',trim($value));
                }

            }
        }

        return redirect()->back()->with('success', 'Mail Configuration has been updated Successfully');
    }

    public function test(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            \Mail::raw('This is a test email from ' . get_setting('site_name') . ' to verify your email configuration is working correctly.', function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Test Email from ' . get_setting('site_name'));
                
                // Set return path if configured
                if (config('mail.return_path')) {
                    $message->returnPath(config('mail.return_path'));
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully! Please check your inbox (and spam folder).'
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to send email: ' . $e->getMessage();
            
            if (strpos($e->getMessage(), 'Connection could not be established') !== false) {
                $errorMessage = 'Connection failed. Please check your mail server settings.';
            } elseif (strpos($e->getMessage(), 'authentication') !== false) {
                $errorMessage = 'Authentication failed. Please check your username and password.';
            } elseif (strpos($e->getMessage(), 'verify') !== false && config('mail.default') === 'ses') {
                $errorMessage = 'Email/domain not verified in Amazon SES. Please verify it first.';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ]);
        }
    }
}
