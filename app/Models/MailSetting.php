<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

/** SMTP settings (single row) entered from the Settings page. Default: Gmail. */
class MailSetting extends Model
{
    protected $fillable = ['host', 'port', 'encryption', 'username', 'from_address', 'from_name'];

    protected $hidden = ['password'];

    public static function current(): self
    {
        return static::query()->first()
            ?? static::create(['host' => 'smtp.gmail.com', 'port' => 587, 'encryption' => 'tls']);
    }

    public function setSmtpPassword(string $plain): void
    {
        $this->attributes['password'] = Crypt::encryptString($plain);
    }

    /** Decrypted password, or null if missing / APP_KEY changed. */
    public function smtpPassword(): ?string
    {
        if (empty($this->attributes['password'])) {
            return null;
        }

        try {
            return Crypt::decryptString($this->attributes['password']);
        } catch (\Throwable) {
            return null;
        }
    }

    public function isConfigured(): bool
    {
        return $this->host && $this->port && $this->username && $this->smtpPassword();
    }

    /** Point Laravel's mailer at these SMTP settings for this request. */
    public function apply(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                // "smtps" = SSL on connect (port 465); "smtp" = STARTTLS (port 587)
                'scheme' => $this->encryption === 'ssl' ? 'smtps' : 'smtp',
                'host' => $this->host,
                'port' => (int) $this->port,
                'username' => $this->username,
                'password' => $this->smtpPassword(),
                'timeout' => 20,
                'local_domain' => parse_url((string) config('app.url'), PHP_URL_HOST) ?: null,
            ],
            'mail.from.address' => $this->from_address ?: $this->username,
            'mail.from.name' => $this->from_name ?: CompanySetting::current()->company_name,
        ]);

        Mail::purge('smtp');
    }
}
