<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class AdminAuthenticator
{
    public static function resolveUser(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return null;
        }

        $storedPassword = (string) $user->getRawOriginal('password');
        $storedPasswordIsHash = self::isPasswordHash($storedPassword);

        if ($storedPasswordIsHash && Hash::check($password, $storedPassword)) {
            return $user;
        }

        $legacyDigest = md5($password);

        if (hash_equals($legacyDigest, $storedPassword)
            || ($storedPasswordIsHash && Hash::check($legacyDigest, $storedPassword))) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();

            return $user->fresh();
        }

        return null;
    }

    protected static function isPasswordHash(string $value): bool
    {
        return password_get_info($value)['algo'] !== null;
    }
}
