<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanySetting extends Model
{
    /** Fields printed on receipts (copied onto each bill). */
    public const RECEIPT_FIELDS = [
        'logo_path', 'company_name', 'address', 'mobile', 'email', 'pan_no', 'gst_no',
        'website', 'tagline', 'header_text', 'footer_text', 'authorized_person', 'signature_path',
    ];

    /** Fields used only on the public About page and the company profile PDF. */
    public const PROFILE_FIELDS = [
        'about_us', 'mission', 'vision', 'services', 'highlights', 'key_people', 'cin_no',
        'established_year', 'whatsapp', 'facebook', 'instagram', 'linkedin', 'about_published',
    ];

    protected $fillable = [
        'logo_path', 'company_name', 'address', 'mobile', 'email', 'pan_no', 'gst_no',
        'website', 'tagline', 'header_text', 'footer_text', 'authorized_person', 'signature_path',
        'about_us', 'mission', 'vision', 'services', 'highlights', 'key_people', 'cin_no',
        'established_year', 'whatsapp', 'facebook', 'instagram', 'linkedin', 'about_published',
    ];

    protected function casts(): array
    {
        return ['about_published' => 'boolean'];
    }

    /** Non-empty lines of a multi-line field (services, highlights, key people). */
    public function lines(string $field): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->{$field}))));
    }

    /** Key people as [['name' => ..., 'role' => ...]], from lines like "Name - Designation". */
    public function people(): array
    {
        return array_map(function ($line) {
            $parts = preg_split('/\s+[-–|]\s+/u', $line, 2);

            return ['name' => trim($parts[0]), 'role' => trim($parts[1] ?? '')];
        }, $this->lines('key_people'));
    }

    /** Website as a full URL (adds https:// if missing). */
    public function websiteUrl(): ?string
    {
        return self::fullUrl($this->website);
    }

    public static function fullUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        return preg_match('#^https?://#i', $url) ? $url : 'https://'.$url;
    }

    /** WhatsApp chat link (Indian numbers get the 91 country code). */
    public function whatsappUrl(): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $this->whatsapp);
        if ($digits === '') {
            return null;
        }
        if (strlen($digits) === 10) {
            $digits = '91'.$digits;
        }

        return 'https://wa.me/'.$digits;
    }

    public function mapUrl(): ?string
    {
        return $this->address ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($this->address) : null;
    }

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
        return $this->only(self::RECEIPT_FIELDS);
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
