@extends('layouts.app')

@section('title', 'Company Settings')

@php
    $f = fn ($key) => old($key, $company->{$key});
    $logoUri = \App\Models\CompanySetting::imageDataUri($company->logo_path, true);
    $signUri = \App\Models\CompanySetting::imageDataUri($company->signature_path);
@endphp

@section('content')
<h1 class="h4 mb-1">Company Settings</h1>
<p class="text-muted small mb-3">These details are printed on every new bill automatically. Changes do not affect bills that were already generated.</p>

<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="card">
    @csrf
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Company Logo</label>
                <div class="border rounded p-2 mb-2 text-center bg-light"><img src="{{ $logoUri }}" alt="Logo" style="max-height: 90px; max-width: 100%;"></div>
                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="form-control @error('logo') is-invalid @enderror">
                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if ($company->logo_path)
                    <div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="remove_logo"><label class="form-check-label small" for="remove_logo">Use default GIT logo</label></div>
                @else
                    <div class="form-text">Using the default GIT logo. Upload to replace (PNG/JPG, max 2 MB).</div>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label">Signature Image (optional)</label>
                <div class="border rounded p-2 mb-2 text-center bg-light" style="min-height: 108px;">
                    @if ($signUri)<img src="{{ $signUri }}" alt="Signature" style="max-height: 90px; max-width: 100%;">@else<span class="text-muted small d-inline-block pt-4">No signature uploaded</span>@endif
                </div>
                <input type="file" name="signature" accept="image/png,image/jpeg,image/webp" class="form-control @error('signature') is-invalid @enderror">
                @error('signature')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if ($company->signature_path)
                    <div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_signature" value="1" id="remove_signature"><label class="form-check-label small" for="remove_signature">Remove signature</label></div>
                @endif
            </div>

            <div class="col-md-8">
                <label class="form-label" for="company_name">Company Name *</label>
                <input type="text" id="company_name" name="company_name" value="{{ $f('company_name') }}" class="form-control @error('company_name') is-invalid @enderror" required>
                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="tagline">Tagline</label>
                <input type="text" id="tagline" name="tagline" value="{{ $f('tagline') }}" class="form-control">
                <div class="form-text">Shown under an uploaded logo.</div>
            </div>
            <div class="col-12">
                <label class="form-label" for="address">Company Address *</label>
                <textarea id="address" name="address" rows="2" class="form-control @error('address') is-invalid @enderror" required>{{ $f('address') }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" value="{{ $f('mobile') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ $f('email') }}" class="form-control @error('email') is-invalid @enderror">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="website">Website (optional)</label>
                <input type="text" id="website" name="website" value="{{ $f('website') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="pan_no">PAN Number</label>
                <input type="text" id="pan_no" name="pan_no" value="{{ $f('pan_no') }}" class="form-control text-uppercase">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="gst_no">GST Number (optional)</label>
                <input type="text" id="gst_no" name="gst_no" value="{{ $f('gst_no') }}" class="form-control text-uppercase">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="authorized_person">Authorized Person Name</label>
                <input type="text" id="authorized_person" name="authorized_person" value="{{ $f('authorized_person') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="header_text">Receipt Header Text</label>
                <input type="text" id="header_text" name="header_text" value="{{ $f('header_text') }}" class="form-control" placeholder="Investment Receipt">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="footer_text">Receipt Footer Text</label>
                <input type="text" id="footer_text" name="footer_text" value="{{ $f('footer_text') }}" class="form-control" placeholder="e.g. This is a computer generated receipt.">
            </div>
        </div>
    </div>
    <div class="card-footer bg-white text-end">
        <button class="btn btn-git px-4"><i class="bi bi-save"></i> Save Settings</button>
    </div>
</form>

{{-- ============ Company profile / About page ============ --}}
@php $p = fn ($key) => old($key, $company->{$key}); @endphp
<h2 class="h5 mt-5 mb-1" id="profile">Company Profile (About Page)</h2>
<p class="text-muted small mb-3">
    Used for the public About page and the Company Profile PDF (cover page + numbered sections), with the logo
    and contact details from above. A section with empty text is left out and the numbering adjusts.
</p>

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge {{ $company->about_published ? 'text-bg-success' : 'text-bg-secondary' }}">
                {{ $company->about_published ? 'Public' : 'Hidden (only you can see it)' }}
            </span>
            <input type="text" id="aboutUrl" value="{{ route('about') }}" class="form-control form-control-sm" style="max-width: 320px;" readonly>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="copyAboutUrl"><i class="bi bi-clipboard"></i> Copy Link</button>
            <a href="{{ route('about') }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-up-right"></i> View Page</a>
            <a href="{{ route('about.pdf') }}" class="btn btn-sm btn-git"><i class="bi bi-file-earmark-pdf"></i> Download Profile PDF</a>
            <a href="https://wa.me/?text={{ rawurlencode($company->company_name.' - Company Profile: '.route('about')) }}" target="_blank" rel="noopener" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i> Share on WhatsApp</a>
            <button type="button" class="btn btn-sm btn-outline-dark d-none" id="nativeShare"><i class="bi bi-share"></i> Share</button>
        </div>
        @unless ($company->about_published)
            <div class="form-text mt-2">Tick "Make About page public" below and save, before you share the link.</div>
        @endunless
    </div>
</div>

<form method="POST" action="{{ route('settings.profile.update') }}" class="card mb-3">
    @csrf
    <div class="card-body">
        <h3 class="h6 text-uppercase text-muted mb-3">Cover Page</h3>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="profile_title">Profile Title</label>
                <textarea id="profile_title" name="profile_title" rows="2" class="form-control">{{ $p('profile_title') }}</textarea>
                <div class="form-text">Press Enter where you want the title to break into a new line.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="profile_subtitle">Subtitle</label>
                <input type="text" id="profile_subtitle" name="profile_subtitle" value="{{ $p('profile_subtitle') }}" class="form-control" placeholder="CORPORATE COMPANY PROFILE">
                <label class="form-label mt-2" for="profile_focus_line">Focus Line</label>
                <input type="text" id="profile_focus_line" name="profile_focus_line" value="{{ $p('profile_focus_line') }}" class="form-control" placeholder="INVESTMENT • SHARE MARKET • TRADING">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="profile_tagline">Tagline</label>
                <textarea id="profile_tagline" name="profile_tagline" rows="2" class="form-control">{{ $p('profile_tagline') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="cover_note">Cover Note</label>
                <input type="text" id="cover_note" name="cover_note" value="{{ $p('cover_note') }}" class="form-control" placeholder="Prepared for clients, investors and business partners">
            </div>
            <div class="col-12">
                <label class="form-label" for="associations_summary">Business Associations (cover page text)</label>
                <textarea id="associations_summary" name="associations_summary" rows="2" class="form-control">{{ $p('associations_summary') }}</textarea>
            </div>
        </div>

        <hr class="my-4">
        <h3 class="h6 text-uppercase text-muted mb-3">Sections</h3>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="about_us">1. Company Overview</label>
                <textarea id="about_us" name="about_us" rows="6" class="form-control @error('about_us', 'profile') is-invalid @enderror">{{ $p('about_us') }}</textarea>
                <div class="form-text">Leave an empty line between paragraphs.</div>
                @error('about_us', 'profile')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="business_focus">2. Business Focus</label>
                <textarea id="business_focus" name="business_focus" rows="4" class="form-control font-monospace small">{{ $p('business_focus') }}</textarea>
                <div class="form-text">One per line: <code>TITLE | Description</code></div>
            </div>
            <div class="col-12">
                <label class="form-label" for="associations">3. Official Business Associations</label>
                <textarea id="associations" name="associations" rows="3" class="form-control font-monospace small">{{ $p('associations') }}</textarea>
                <div class="form-text">One per line: <code>Company Name | Description</code></div>
            </div>
            <div class="col-12">
                <label class="form-label" for="associations_note">Associations Note (small text)</label>
                <textarea id="associations_note" name="associations_note" rows="2" class="form-control">{{ $p('associations_note') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label" for="vision">4. Our Vision</label>
                <textarea id="vision" name="vision" rows="3" class="form-control">{{ $p('vision') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label" for="mission">5. Our Mission</label>
                <textarea id="mission" name="mission" rows="5" class="form-control">{{ $p('mission') }}</textarea>
                <div class="form-text">One point per line (shown as bullet points).</div>
            </div>
            <div class="col-12">
                <label class="form-label" for="core_values">6. Core Values</label>
                <textarea id="core_values" name="core_values" rows="6" class="form-control font-monospace small">{{ $p('core_values') }}</textarea>
                <div class="form-text">One per line: <code>TITLE | Description</code> (shown 3 per row)</div>
            </div>
            <div class="col-12">
                <label class="form-label" for="commitment">7. Our Commitment</label>
                <textarea id="commitment" name="commitment" rows="4" class="form-control">{{ $p('commitment') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label" for="disclaimer">8. Important Information</label>
                <textarea id="disclaimer" name="disclaimer" rows="4" class="form-control">{{ $p('disclaimer') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label" for="enquiries_text">Corporate Enquiries</label>
                <textarea id="enquiries_text" name="enquiries_text" rows="2" class="form-control">{{ $p('enquiries_text') }}</textarea>
                <div class="form-text">Address, email, website and mobile from the company details above are added below this text automatically.</div>
            </div>
        </div>

        <hr class="my-4">
        <h3 class="h6 text-uppercase text-muted mb-3">Extra details (About page)</h3>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="cin_no">CIN (Company Reg. No.)</label>
                <input type="text" id="cin_no" name="cin_no" value="{{ $p('cin_no') }}" class="form-control text-uppercase">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="established_year">Established</label>
                <input type="text" id="established_year" name="established_year" value="{{ $p('established_year') }}" class="form-control" placeholder="e.g. 2021">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="whatsapp">WhatsApp Number</label>
                <input type="text" id="whatsapp" name="whatsapp" value="{{ $p('whatsapp') }}" class="form-control" placeholder="10-digit mobile number">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="facebook">Facebook Link</label>
                <input type="text" id="facebook" name="facebook" value="{{ $p('facebook') }}" class="form-control" placeholder="facebook.com/...">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="instagram">Instagram Link</label>
                <input type="text" id="instagram" name="instagram" value="{{ $p('instagram') }}" class="form-control" placeholder="instagram.com/...">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="linkedin">LinkedIn Link</label>
                <input type="text" id="linkedin" name="linkedin" value="{{ $p('linkedin') }}" class="form-control" placeholder="linkedin.com/company/...">
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="about_published" value="1" id="about_published" @checked(old('about_published', $company->about_published))>
                    <label class="form-check-label fw-semibold" for="about_published">Make About page public (anyone with the link can open it)</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white text-end">
        <button class="btn btn-git px-4"><i class="bi bi-save"></i> Save Company Profile</button>
    </div>
</form>

@push('scripts')
<script>
(function () {
    var url = document.getElementById('aboutUrl').value;
    document.getElementById('copyAboutUrl').addEventListener('click', function () {
        var btn = this;
        var done = function () { btn.innerHTML = '<i class="bi bi-check2"></i> Copied'; setTimeout(function () { btn.innerHTML = '<i class="bi bi-clipboard"></i> Copy Link'; }, 2000); };
        if (navigator.clipboard) { navigator.clipboard.writeText(url).then(done); }
        else { var i = document.getElementById('aboutUrl'); i.select(); document.execCommand('copy'); done(); }
    });
    if (navigator.share) {
        var s = document.getElementById('nativeShare');
        s.classList.remove('d-none');
        s.addEventListener('click', function () { navigator.share({ title: document.title, url: url }).catch(function () {}); });
    }
})();
</script>
@endpush

{{-- ============ SMTP (Gmail) ============ --}}
<h2 class="h5 mt-5 mb-1" id="smtp">Email (SMTP) Settings</h2>
<p class="text-muted small mb-3">Used to send the OTP for "Forgot password". Gmail is filled in by default.</p>

<div class="card mb-3">
    <form method="POST" action="{{ route('settings.mail.update') }}">
        @csrf
        <div class="card-body">
            <div class="alert alert-info small mb-3">
                <strong>Gmail setup:</strong> turn on 2-Step Verification on the Gmail account, then create an
                <strong>App Password</strong> at <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">myaccount.google.com/apppasswords</a>.
                Paste that 16-letter app password below. Your normal Gmail password will not work.
            </div>
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label" for="smtp_host">SMTP Host *</label>
                    <input type="text" id="smtp_host" name="smtp_host" value="{{ old('smtp_host', $mail->host) }}" class="form-control @error('smtp_host', 'mail') is-invalid @enderror">
                    @error('smtp_host', 'mail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="smtp_port">Port *</label>
                    <input type="number" id="smtp_port" name="smtp_port" value="{{ old('smtp_port', $mail->port) }}" class="form-control @error('smtp_port', 'mail') is-invalid @enderror">
                    @error('smtp_port', 'mail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="smtp_encryption">Encryption *</label>
                    @php $enc = old('smtp_encryption', $mail->encryption); @endphp
                    <select id="smtp_encryption" name="smtp_encryption" class="form-select">
                        <option value="tls" @selected($enc === 'tls')>TLS (port 587)</option>
                        <option value="ssl" @selected($enc === 'ssl')>SSL (port 465)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="smtp_username">Gmail Address (username) *</label>
                    <input type="email" id="smtp_username" name="smtp_username" value="{{ old('smtp_username', $mail->username) }}" class="form-control @error('smtp_username', 'mail') is-invalid @enderror" placeholder="yourname@gmail.com" autocomplete="off">
                    @error('smtp_username', 'mail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="smtp_password">Gmail App Password {{ $mail->smtpPassword() ? '' : '*' }}</label>
                    <input type="password" id="smtp_password" name="smtp_password" class="form-control @error('smtp_password', 'mail') is-invalid @enderror" placeholder="{{ $mail->smtpPassword() ? 'Saved - leave blank to keep' : 'xxxx xxxx xxxx xxxx' }}" autocomplete="new-password">
                    @error('smtp_password', 'mail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="from_address">From Email (optional)</label>
                    <input type="email" id="from_address" name="from_address" value="{{ old('from_address', $mail->from_address) }}" class="form-control @error('from_address', 'mail') is-invalid @enderror" placeholder="Same as Gmail address">
                    @error('from_address', 'mail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="from_name">From Name (optional)</label>
                    <input type="text" id="from_name" name="from_name" value="{{ old('from_name', $mail->from_name) }}" class="form-control" placeholder="{{ $company->company_name }}">
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="small {{ $mail->isConfigured() ? 'text-success' : 'text-muted' }}">
                <i class="bi {{ $mail->isConfigured() ? 'bi-check-circle' : 'bi-dash-circle' }}"></i>
                {{ $mail->isConfigured() ? 'SMTP is set up' : 'SMTP not set up yet' }}
            </span>
            <button class="btn btn-git px-4"><i class="bi bi-save"></i> Save SMTP Settings</button>
        </div>
    </form>
</div>

<div class="card">
    <form method="POST" action="{{ route('settings.mail.test') }}" class="card-body">
        @csrf
        <label class="form-label" for="test_to">Send a test email</label>
        <div class="input-group" style="max-width: 520px;">
            <input type="email" id="test_to" name="test_to" value="{{ old('test_to', $user->email) }}" class="form-control @error('test_to', 'mail') is-invalid @enderror" required>
            <button class="btn btn-outline-primary" @disabled(! $mail->isConfigured())><i class="bi bi-send"></i> Send Test</button>
            @error('test_to', 'mail')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </form>
</div>

{{-- ============ Login account ============ --}}
<h2 class="h5 mt-5 mb-1" id="account">Login Account</h2>
<p class="text-muted small mb-3">The "Forgot password" OTP is sent to this email, so use a real inbox you can open.</p>

<form method="POST" action="{{ route('settings.account.update') }}" class="card mb-4"
      data-confirm="Update the login email / password? You will use the new details next time you log in." data-confirm-button="Yes, update">
    @csrf
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="login_email">Login Email *</label>
                <input type="email" id="login_email" name="login_email" value="{{ old('login_email', $user->email) }}" class="form-control @error('login_email', 'account') is-invalid @enderror" required>
                @error('login_email', 'account')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="current_password">Current Password *</label>
                <input type="password" id="current_password" name="current_password" class="form-control @error('current_password', 'account') is-invalid @enderror" autocomplete="current-password" required>
                @error('current_password', 'account')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" class="form-control @error('new_password', 'account') is-invalid @enderror" autocomplete="new-password" placeholder="Leave blank to keep the current one">
                @error('new_password', 'account')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="new_password_confirmation">Confirm New Password</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" autocomplete="new-password">
            </div>
        </div>
    </div>
    <div class="card-footer bg-white text-end">
        <button class="btn btn-git px-4"><i class="bi bi-person-check"></i> Update Login Account</button>
    </div>
</form>
@endsection
