<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('settings.edit', ['company' => CompanySetting::current()]);
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
