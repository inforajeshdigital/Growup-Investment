<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/** Creates/updates the single login user from ADMIN_* values in .env. */
class AdminUser
{
    /** @return string Human-readable result */
    public static function syncFromEnv(bool $resetPassword = false): string
    {
        $email = config('growup.admin.email');
        $password = config('growup.admin.password');

        if (! $email || ! $password) {
            return 'ADMIN_EMAIL / ADMIN_PASSWORD are not set in .env - login user not created.';
        }
        if (strlen($password) < 8) {
            return 'ADMIN_PASSWORD must be at least 8 characters - login user not created.';
        }

        $user = User::query()->first();

        if (! $user) {
            User::create([
                'name' => config('growup.admin.name'),
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            return "Login user created: {$email}";
        }

        if ($resetPassword) {
            $user->update([
                'name' => config('growup.admin.name'),
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            return "Login user updated: {$email}";
        }

        return "Login user already exists ({$user->email}) - not changed.";
    }
}
