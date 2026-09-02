<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Audit\AuditService;

/**
 * TwoFactorService — all business logic for TOTP-based 2FA.
 *
 * Responsibilities:
 *  - Generate a TOTP secret (base-32 encoded)
 *  - Build the otpauth:// QR code URL for authenticator apps
 *  - Verify a submitted TOTP code (±1 period drift tolerance)
 *  - Enable 2FA: store secret, mark unconfirmed
 *  - Confirm 2FA: verify first code, activate, generate + store recovery codes
 *  - Disable 2FA: clear all 2FA fields atomically
 *  - Regenerate recovery codes
 *  - Write audit log for every state change
 *
 * The controller only decides response shape (JSON payload / HTTP status).
 */
final class TwoFactorService
{
    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Begin 2FA setup.
     * Generates a TOTP secret, stores it encrypted on the user row,
     * and returns the QR code URL for the authenticator app.
     *
     * @return array{secret: string, qr_code: string}
     * @throws \RuntimeException if 2FA is already active
     */
    public function enable(User $user): array
    {
        if ($user->two_factor_enabled) {
            throw new \RuntimeException('Two-factor authentication is already enabled.');
        }

        $secret = $this->generateSecret();

        $user->forceFill([
            'two_factor_secret'  => encrypt($secret),
            'two_factor_enabled' => false, // Not active until confirmed
        ])->saveQuietly();

        AuditService::log('two_factor_setup_started', $user);

        return [
            'secret'   => $secret,
            'qr_code'  => $this->buildQrCodeUrl($user->email, $secret),
        ];
    }

    /**
     * Confirm 2FA setup by verifying the first code.
     * Activates 2FA and returns one-time recovery codes.
     *
     * @return array{recovery_codes: string[]}
     * @throws \RuntimeException if the code is invalid or no secret is stored
     */
    public function confirm(User $user, string $code): array
    {
        if (empty($user->two_factor_secret)) {
            throw new \RuntimeException('2FA setup has not been initiated.');
        }

        $secret = decrypt($user->two_factor_secret);

        if (! $this->verifyCode($secret, $code)) {
            throw new \DomainException('Invalid authenticator code.');
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_enabled'        => true,
            'two_factor_confirmed_at'   => now(),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->saveQuietly();

        AuditService::log('two_factor_enabled', $user);

        return ['recovery_codes' => $recoveryCodes];
    }

    /**
     * Disable 2FA entirely and wipe all related fields.
     */
    public function disable(User $user): void
    {
        $user->forceFill([
            'two_factor_enabled'        => false,
            'two_factor_secret'         => null,
            'two_factor_confirmed_at'   => null,
            'two_factor_recovery_codes' => null,
        ])->saveQuietly();

        AuditService::log('two_factor_disabled', $user);
    }

    /**
     * Regenerate recovery codes (existing codes become invalid immediately).
     *
     * @return array{recovery_codes: string[]}
     */
    public function regenerateCodes(User $user): array
    {
        $codes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($codes)),
        ])->saveQuietly();

        AuditService::log('two_factor_recovery_codes_regenerated', $user);

        return ['recovery_codes' => $codes];
    }

    /**
     * Verify a TOTP code against the user's stored secret.
     * Used during the login challenge step.
     */
    public function verifyChallenge(User $user, string $code): bool
    {
        if (empty($user->two_factor_secret)) {
            return false;
        }

        return $this->verifyCode(decrypt($user->two_factor_secret), $code);
    }

    // ── Private TOTP implementation ───────────────────────────────────────────

    /**
     * Generate a random 32-character base-32 TOTP secret.
     */
    private function generateSecret(int $length = 32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret   = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Build an otpauth:// URI compatible with Google Authenticator, Authy, etc.
     */
    private function buildQrCodeUrl(string $email, string $secret): string
    {
        $appName = config('app.name', 'VJ CPA CRM');
        $label   = rawurlencode("{$appName}:{$email}");
        $issuer  = rawurlencode($appName);

        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Verify a 6-digit TOTP code with ±1 period (30s) drift tolerance.
     */
    private function verifyCode(string $secret, string $code): bool
    {
        $counter = (int) floor(time() / 30);

        foreach ([-1, 0, 1] as $drift) {
            if ($this->hotp($secret, $counter + $drift) === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compute an HOTP value from a base-32 secret and counter.
     * RFC 4226 / RFC 6238 compliant implementation.
     */
    private function hotp(string $secret, int $counter): string
    {
        // Decode base-32 secret to binary
        $base32  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret  = strtoupper(rtrim($secret, '='));
        $binary  = '';
        $bits    = '';

        foreach (str_split($secret) as $char) {
            $pos   = strpos($base32, $char);
            $bits .= str_pad(decbin($pos !== false ? $pos : 0), 5, '0', STR_PAD_LEFT);
        }

        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $binary .= chr((int) bindec($byte));
            }
        }

        // HMAC-SHA1 of the counter (big-endian 8 bytes)
        $message = pack('N*', 0) . pack('N*', $counter);
        $hash    = hash_hmac('sha1', $message, $binary, true);

        // Dynamic truncation
        $offset = ord($hash[19]) & 0x0f;
        $otp    = (
            ((ord($hash[$offset])     & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8)  |
            (ord($hash[$offset + 3])  & 0xff)
        ) % 1_000_000;

        return str_pad((string) $otp, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate 8 random recovery codes in XXXXXX-XXXXXX format.
     *
     * @return string[]
     */
    private function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(3)))
                . '-'
                . strtoupper(bin2hex(random_bytes(3)));
        }
        return $codes;
    }
}
