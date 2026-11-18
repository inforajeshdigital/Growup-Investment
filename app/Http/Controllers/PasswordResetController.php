<?php

namespace App\Http\Controllers;

use App\Mail\PasswordOtpMail;
use App\Models\MailSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Forgot password: a 6-digit OTP is emailed (via the SMTP settings) and used to set a new password. */
class PasswordResetController extends Controller
{
    private const OTP_MINUTES = 10;

    private const MAX_ATTEMPTS = 5;

    private const SESSION_KEY = 'reset_email';

    public function showForgot()
    {
        return view('auth.forgot');
    }

    public function sendOtp(Request $request)
    {
        $email = Str::lower(trim($request->validate(['email' => ['required', 'email']])['email']));

        $mail = MailSetting::current();
        if (! $mail->isConfigured()) {
            return back()->withInput()->with('error', 'Email sending is not set up yet. Set up SMTP in Company Settings first.');
        }

        $sendKey = 'otp-send:'.$email;
        if (RateLimiter::tooManyAttempts($sendKey, 1)) {
            throw ValidationException::withMessages([
                'email' => 'Please wait '.RateLimiter::availableIn($sendKey).' seconds before asking for a new OTP.',
            ]);
        }
        RateLimiter::hit($sendKey, 60);

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user) {
            $otp = (string) random_int(100000, 999999);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => Hash::make($otp), 'created_at' => now()]
            );
            RateLimiter::clear('otp-verify:'.$email);

            try {
                $mail->apply();
                Mail::to($user->email)->send(new PasswordOtpMail($otp, self::OTP_MINUTES));
            } catch (\Throwable $e) {
                Log::error('Password OTP email failed: '.$e->getMessage());
                DB::table('password_reset_tokens')->where('email', $email)->delete();
                RateLimiter::clear($sendKey);

                return back()->withInput()->with('error', 'Could not send the OTP email. Please check the SMTP settings and try again.');
            }
        }

        // Same message whether or not the email exists, so nobody can guess the login email.
        $request->session()->put(self::SESSION_KEY, $email);

        return redirect()->route('password.reset.form')
            ->with('success', 'If this email is registered, an OTP has been sent to it. It is valid for '.self::OTP_MINUTES.' minutes.');
    }

    public function showReset(Request $request)
    {
        $email = $request->session()->get(self::SESSION_KEY);
        if (! $email) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset', ['email' => $email]);
    }

    public function reset(Request $request)
    {
        $email = $request->session()->get(self::SESSION_KEY);
        if (! $email) {
            return redirect()->route('password.forgot');
        }

        $data = $request->validate([
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $verifyKey = 'otp-verify:'.$email;
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (RateLimiter::tooManyAttempts($verifyKey, self::MAX_ATTEMPTS)) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            throw ValidationException::withMessages(['otp' => 'Too many wrong attempts. Please request a new OTP.']);
        }

        if (! $record || Carbon::parse($record->created_at)->addMinutes(self::OTP_MINUTES)->isPast()) {
            throw ValidationException::withMessages(['otp' => 'This OTP has expired. Please request a new one.']);
        }

        if (! Hash::check($data['otp'], $record->token)) {
            RateLimiter::hit($verifyKey, self::OTP_MINUTES * 60);
            $left = self::MAX_ATTEMPTS - RateLimiter::attempts($verifyKey);
            throw ValidationException::withMessages(['otp' => "Wrong OTP. {$left} attempt(s) left."]);
        }

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->firstOrFail();
        $user->forceFill([
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(60),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        RateLimiter::clear($verifyKey);
        $request->session()->forget(self::SESSION_KEY);

        return redirect()->route('login')->with('success', 'Password changed. Please log in with your new password.');
    }
}
