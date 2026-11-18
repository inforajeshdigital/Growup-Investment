<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanySetting extends Model
{
    protected $fillable = [
        'logo_path', 'company_name', 'address', 'mobile', 'email', 'pan_no', 'gst_no',
        'website', 'tagline', 'header_text', 'footer_text', 'authorized_person', 'signature_path',
    ];

    /** The single settings row (created if missing). */
    public static function current(): self
    {
        return static::query()->first()
            ?? static::create([
                'company_name' => 'GROWUP INVESTMENT TRADING PRIVATE LIMITED',
                'tagline' => 'YOUR WEALTH IS OUR CONCERN',
                'header_text' => 'Investment Receipt',
            ]);
    }

    /** Values copied onto each bill so that old bills keep their original details. */
    public function snapshot(): array
    {
        return $this->only($this->fillable);
    }

    /**
     * Return a stored image as a data URI, so the same markup works on screen,
     * in print and in the PDF without exposing files publicly.
     */
    public static function imageDataUri(?string $storagePath, bool $fallbackToDefaultLogo = false): ?string
    {
        if ($storagePath && Storage::disk('local')->exists($storagePath)) {
            $file = Storage::disk('local')->path($storagePath);
        } elseif ($fallbackToDefaultLogo) {
            $file = public_path('images/logo.png');
        } else {
            return null;
        }

        $mime = mime_content_type($file) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file));
    }
}
