<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\CompanySetting;
use App\Models\MailSetting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function edit(Request $request)
    {
        return view('settings.edit', [
            'company' => CompanySetting::current(),
            'mail' => MailSetting::current(),
            'user' => $request->user(),
        ]);
    }

    /** Company profile shown on the public /about page and in the profile PDF. */
    public function updateProfile(Request $request)
    {
        $data = $request->validateWithBag('profile', [
            'profile_title' => ['nullable', 'string', 'max:255'],
            'profile_subtitle' => ['nullable', 'string', 'max:255'],
            'profile_focus_line' => ['nullable', 'string', 'max:255'],
            'profile_tagline' => ['nullable', 'string', 'max:1000'],
            'associations_summary' => ['nullable', 'string', 'max:2000'],
            'cover_note' => ['nullable', 'string', 'max:255'],
            'about_us' => ['nullable', 'string', 'max:6000'],
            'business_focus' => ['nullable', 'string', 'max:4000'],
            'associations' => ['nullable', 'string', 'max:4000'],
            'associations_note' => ['nullable', 'string', 'max:2000'],
            'vision' => ['nullable', 'string', 'max:2000'],
            'mission' => ['nullable', 'string', 'max:4000'],
            'core_values' => ['nullable', 'string', 'max:4000'],
            'commitment' => ['nullable', 'string', 'max:4000'],
            'disclaimer' => ['nullable', 'string', 'max:4000'],
            'enquiries_text' => ['nullable', 'string', 'max:2000'],
            'cin_no' => ['nullable', 'string', 'max:30'],
            'established_year' => ['nullable', 'string', 'max:10'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
        ]);

        $data['cin_no'] = isset($data['cin_no']) ? strtoupper($data['cin_no']) : null;
        $data['about_published'] = $request->boolean('about_published');

        CompanySetting::current()->update($data);

        return redirect()->to(route('settings.edit').'#profile')->with('success', 'Company profile saved.');
    }

    /** SMTP (Gmail) settings used for password-reset OTP emails. */
    public function updateMail(Request $request)
    {
        $mail = MailSetting::current();

        $data = $request->validateWithBag('mail', [
            'smtp_host' => ['required', 'string', 'max:255'],
            'smtp_port' => ['required', 'integer', 'between:1,65535'],
            'smtp_encryption' => ['required', Rule::in(['tls', 'ssl'])],
            'smtp_username' => ['required', 'email', 'max:255'],
            'smtp_password' => [$mail->smtpPassword() ? 'nullable' : 'required', 'string', 'max:255'],
            'from_address' => ['nullable', 'email', 'max:255'],
            'from_name' => ['nullable', 'string', 'max:255'],
        ], [
            'smtp_password.required' => 'Enter the Gmail App Password.',
        ]);

        $mail->fill([
            'host' => trim($data['smtp_host']),
            'port' => $data['smtp_port'],
            'encryption' => $data['smtp_encryption'],
            'username' => trim($data['smtp_username']),
            'from_address' => $data['from_address'] ?? null,
            'from_name' => $data['from_name'] ?? null,
        ]);

        if (! empty($data['smtp_password'])) {
            // Gmail shows app passwords in groups of 4 with spaces; spaces are not part of it
            $mail->setSmtpPassword(str_replace(' ', '', $data['smtp_password']));
        }

        $mail->save();

        return redirect()->to(route('settings.edit').'#smtp')->with('success', 'SMTP settings saved. Send a test email to check them.');
    }

    public function testMail(Request $request)
    {
        $data = $request->validateWithBag('mail', ['test_to' => ['required', 'email']]);
        $mail = MailSetting::current();

        if (! $mail->isConfigured()) {
            return redirect()->to(route('settings.edit').'#smtp')->with('error', 'Save the SMTP settings first.');
        }

        try {
            $mail->apply();
            Mail::to($data['test_to'])->send(new TestMail);
        } catch (\Throwable $e) {
            Log::warning('SMTP test failed: '.$e->getMessage());

            return redirect()->to(route('settings.edit').'#smtp')
                ->with('error', 'Test email failed: '.$e->getMessage());
        }

        return redirect()->to(route('settings.edit').'#smtp')->with('success', "Test email sent to {$data['test_to']}. Check the inbox (and spam).");
    }

    /** Login email and password of the single user. */
    public function updateAccount(Request $request)
    {
        $user = $request->user();

        $data = $request->validateWithBag('account', [
            'login_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Current password is not correct.',
        ]);

        $user->email = $data['login_email'];
        if (! empty($data['new_password'])) {
            $user->password = Hash::make($data['new_password']);
        }
        $user->save();

        return redirect()->to(route('settings.edit').'#account')->with('success', 'Login account updated.');
    }

    public function update(Request $request)
    {
        $company = CompanySetting::current();

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'pan_no' => ['nullable', 'string', 'max:20'],
            'gst_no' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'string', 'max:255'],
            'header_text' => ['nullable', 'string', 'max:255'],
            'footer_text' => ['nullable', 'string', 'max:1000'],
            'authorized_person' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_signature' => ['nullable', 'boolean'],
        ]);

        $data['pan_no'] = isset($data['pan_no']) ? strtoupper($data['pan_no']) : null;
        $data['gst_no'] = isset($data['gst_no']) ? strtoupper($data['gst_no']) : null;

        // Old image files are kept on purpose: earlier bills still point to them.
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $this->storeImage($request->file('logo'));
        } elseif ($request->boolean('remove_logo')) {
            $data['logo_path'] = null;
        }

        if ($request->hasFile('signature')) {
            $data['signature_path'] = $this->storeImage($request->file('signature'));
        } elseif ($request->boolean('remove_signature')) {
            $data['signature_path'] = null;
        }

        unset($data['logo'], $data['signature'], $data['remove_logo'], $data['remove_signature']);
        $company->update($data);

        return redirect()->route('settings.edit')->with('success', 'Company settings saved.');
    }

    /** Store an uploaded image privately, shrinking large ones so PDFs stay small. */
    private function storeImage(UploadedFile $file, int $maxWidth = 800): string
    {
        $path = $file->store('company', 'local');
        $full = Storage::disk('local')->path($path);

        $info = @getimagesize($full);
        if (! $info || $info[0] <= $maxWidth || ! function_exists('imagecreatefromstring')) {
            return $path;
        }

        $src = @imagecreatefromstring(file_get_contents($full));
        if (! $src) {
            return $path;
        }

        $height = (int) round($info[1] * $maxWidth / $info[0]);
        $dst = imagecreatetruecolor($maxWidth, $height);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxWidth, $height, $info[0], $info[1]);

        // Save as PNG to keep transparency
        $pngPath = preg_replace('/\.[^.\/]+$/', '', $path).'.png';
        imagepng($dst, Storage::disk('local')->path($pngPath), 9);
        if ($pngPath !== $path) {
            Storage::disk('local')->delete($path);
        }

        return $pngPath;
    }
}
