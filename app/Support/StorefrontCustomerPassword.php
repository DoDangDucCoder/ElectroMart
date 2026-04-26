<?php

namespace App\Support;

use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Support\Facades\Hash;

class StorefrontCustomerPassword
{
    public static function verifyAndUpgrade(Customer $customer, string $password): bool
    {
        $stored = (string) $customer->password;

        if ($stored === '') {
            return false;
        }

        $legacyDigest = md5($password);
        $valid = false;
        $needsUpgrade = false;

        if (static::checkHash($password, $stored)) {
            $valid = true;
            $needsUpgrade = Hash::needsRehash($stored);
        } elseif (static::checkHash($legacyDigest, $stored)) {
            $valid = true;
            $needsUpgrade = true;
        } elseif (static::isMd5Hash($stored) && hash_equals(strtolower($stored), $legacyDigest)) {
            $valid = true;
            $needsUpgrade = true;
        }

        if ($valid && $needsUpgrade) {
            static::upgrade($customer, $password);
        }

        return $valid;
    }

    public static function isReusableLegacyHash(?string $hash): bool
    {
        $hash = trim((string) $hash);

        $algo = password_get_info($hash)['algo'];

        return $hash !== '' && (static::isMd5Hash($hash) || ($algo !== null && $algo !== 0));
    }

    private static function checkHash(string $password, string $hash): bool
    {
        $algo = password_get_info($hash)['algo'];

        if ($algo === null || $algo === 0) {
            return false;
        }

        try {
            return Hash::check($password, $hash);
        } catch (\Throwable) {
            return false;
        }
    }

    private static function isMd5Hash(string $hash): bool
    {
        return preg_match('/^[a-f0-9]{32}$/i', $hash) === 1;
    }

    private static function upgrade(Customer $customer, string $password): void
    {
        $meta = $customer->meta ?? [];
        $meta['legacy_password_upgraded_at'] = now()->toISOString();

        $customer->forceFill([
            'password' => Hash::make($password),
            'meta' => $meta,
        ])->save();
    }
}
