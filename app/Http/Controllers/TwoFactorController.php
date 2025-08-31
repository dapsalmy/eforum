<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TwoFactorController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->middleware('auth');
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Show 2FA setup page
     */
    public function show()
    {
        $user = Auth::user();
        
        if ($user->two_factor_enabled) {
            return redirect()->route('user.settings')->with('info', 'Two-factor authentication is already enabled.');
        }

        // Generate new secret if not exists
        if (!$user->two_factor_secret) {
            $user->update([
                'two_factor_secret' => $this->twoFactorService->generateSecretKey()
            ]);
        }

        $qrCode = $this->twoFactorService->generateQrCode($user);
        $secret = $user->two_factor_secret;

        return view('user.two-factor.setup', compact('qrCode', 'secret'));
    }

    /**
     * Enable 2FA
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$this->twoFactorService->verifyCode($user, $request->code)) {
            return back()->withErrors(['code' => 'The verification code is invalid.']);
        }

        // Generate recovery codes
        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();
        
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => json_encode($recoveryCodes->toArray()),
        ]);

        Log::info('2FA enabled for user', ['user_id' => $user->id]);

        return redirect()->route('user.two-factor.recovery-codes');
    }

    /**
     * Show recovery codes
     */
    public function showRecoveryCodes()
    {
        $user = Auth::user();
        
        if (!$user->two_factor_enabled) {
            return redirect()->route('user.settings');
        }

        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true) ?? [];

        return view('user.two-factor.recovery-codes', compact('recoveryCodes'));
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();
        
        $user->update([
            'two_factor_recovery_codes' => json_encode($recoveryCodes->toArray()),
        ]);

        Log::info('2FA recovery codes regenerated', ['user_id' => $user->id]);

        return back()->with('success', 'Recovery codes regenerated successfully.');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $this->twoFactorService->disableTwoFactor($user);

        Log::info('2FA disabled for user', ['user_id' => $user->id]);

        return redirect()->route('user.settings')->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Show 2FA verification page
     */
    public function verify()
    {
        if (!Auth::user()->two_factor_enabled) {
            return redirect()->intended(route('home'));
        }

        return view('auth.two-factor-verify');
    }

    /**
     * Verify 2FA code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = Auth::user();
        $code = str_replace(' ', '', $request->code);

        // Try OTP code first
        if (strlen($code) === 6 && $this->twoFactorService->verifyCode($user, $code)) {
            $this->twoFactorService->markAsVerified();
            Log::info('2FA verified with OTP', ['user_id' => $user->id]);
            return redirect()->intended(route('home'));
        }

        // Try recovery code
        if (str_contains($code, '-') && $this->twoFactorService->verifyRecoveryCode($user, $code)) {
            $this->twoFactorService->markAsVerified();
            Log::warning('2FA verified with recovery code', ['user_id' => $user->id]);
            
            // Alert user that they used a recovery code
            session()->flash('warning', 'You used a recovery code. Please generate new recovery codes for security.');
            
            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['code' => 'The verification code is invalid.']);
    }
}
