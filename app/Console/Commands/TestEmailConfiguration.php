<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailConfiguration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {to : The email address to send test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $to = $this->argument('to');
        
        $this->info('Testing email configuration...');
        $this->info('Mail Driver: ' . config('mail.default'));
        $this->info('From Address: ' . config('mail.from.address'));
        $this->info('From Name: ' . config('mail.from.name'));
        $this->info('From Domain: ' . (config('mail.from_domain') ?: 'Not set'));
        $this->info('Return Path: ' . (config('mail.return_path') ?: 'Using FROM address'));
        
        if (config('mail.default') === 'smtp') {
            $this->info('SMTP Host: ' . config('mail.mailers.smtp.host'));
            $this->info('SMTP Port: ' . config('mail.mailers.smtp.port'));
            $this->info('SMTP Encryption: ' . config('mail.mailers.smtp.encryption'));
        } elseif (config('mail.default') === 'ses') {
            $this->info('AWS Region: ' . config('services.ses.region', env('AWS_DEFAULT_REGION')));
            $this->info('SES Region: ' . (env('AWS_SES_REGION') ?: 'Using AWS Region'));
        }
        
        $this->info('');
        $this->info('Sending test email to: ' . $to);
        
        try {
            Mail::raw('This is a test email from ' . get_setting('site_name') . ' to verify your email configuration is working correctly.', function ($message) use ($to) {
                $message->to($to)
                    ->subject('Test Email from ' . get_setting('site_name'));
                
                // Set return path if configured
                if (config('mail.return_path')) {
                    $message->returnPath(config('mail.return_path'));
                }
            });
            
            $this->info('✅ Test email sent successfully!');
            $this->info('Please check your inbox (and spam folder) for the test email.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email!');
            $this->error('Error: ' . $e->getMessage());
            
            if (strpos($e->getMessage(), 'Connection could not be established') !== false) {
                $this->warn('This usually means your SMTP settings are incorrect or the server is unreachable.');
            } elseif (strpos($e->getMessage(), 'authentication') !== false) {
                $this->warn('This usually means your username/password is incorrect.');
            } elseif (strpos($e->getMessage(), 'verify') !== false && config('mail.default') === 'ses') {
                $this->warn('This usually means your email address or domain is not verified in Amazon SES.');
            }
            
            return Command::FAILURE;
        }
    }
}
