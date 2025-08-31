<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TwoFactorAuthService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generate a new secret key for the user
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Generate QR code for 2FA setup
     */
    public function generateQrCode(User $user): string
    {
        $companyName = get_setting('site_name', 'eForum');
        $companyEmail = $user->email;
        $secretKey = $user->two_factor_secret;

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            $companyName,
            $companyEmail,
            $secretKey
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            )
        );

        return $writer->writeString($qrCodeUrl);
    }

    /**
     * Verify the OTP code
     */
    public function verifyCode(User $user, string $code): bool
    {
        return $this->google2fa->verifyKey($user->two_factor_secret, $code);
    }

    /**
     * Generate recovery codes
     */
    public function generateRecoveryCodes(): Collection
    {
        return Collection::times(8, function () {
            return Str::random(10) . '-' . Str::random(10);
        });
    }

    /**
     * Enable 2FA for user
     */
    public function enableTwoFactor(User $user): void
    {
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);
    }

    /**
     * Disable 2FA for user
     */
    public function disableTwoFactor(User $user): void
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }

    /**
     * Verify recovery code
     */
    public function verifyRecoveryCode(User $user, string $code): bool
    {
        $codes = json_decode($user->two_factor_recovery_codes, true) ?? [];
        
        if (in_array($code, $codes)) {
            // Remove used recovery code
            $codes = array_values(array_diff($codes, [$code]));
            $user->update(['two_factor_recovery_codes' => json_encode($codes)]);
            
            return true;
        }
        
        return false;
    }

    /**
     * Check if user needs 2FA verification
     */
    public function requiresVerification(User $user): bool
    {
        return $user->two_factor_enabled && !session('2fa_verified');
    }

    /**
     * Mark 2FA as verified for current session
     */
    public function markAsVerified(): void
    {
        session(['2fa_verified' => true, '2fa_verified_at' => now()]);
    }

    /**
     * Check if 2FA verification has expired
     */
    public function verificationExpired(): bool
    {
        $verifiedAt = session('2fa_verified_at');
        
        if (!$verifiedAt) {
            return true;
        }
        
        // Expire after 2 hours
        return now()->diffInMinutes($verifiedAt) > 120;
    }
}
