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
    Shown on the public About page and in the Company Profile PDF, together with the logo, name, tagline,
    address and contact details above. Empty fields are not shown. For lists, write one item per line.
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
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="about_us">About Us</label>
                <textarea id="about_us" name="about_us" rows="5" class="form-control @error('about_us', 'profile') is-invalid @enderror" placeholder="Who you are, what you do, since when...">{{ $p('about_us') }}</textarea>
                @error('about_us', 'profile')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="mission">Our Mission</label>
                <textarea id="mission" name="mission" rows="3" class="form-control">{{ $p('mission') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="vision">Our Vision</label>
                <textarea id="vision" name="vision" rows="3" class="form-control">{{ $p('vision') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="services">Services (one per line)</label>
                <textarea id="services" name="services" rows="5" class="form-control" placeholder="Investment Planning&#10;Portfolio Management&#10;...">{{ $p('services') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="highlights">Why Choose Us (one per line)</label>
                <textarea id="highlights" name="highlights" rows="5" class="form-control" placeholder="Transparent process&#10;Dedicated support&#10;...">{{ $p('highlights') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="key_people">Directors / Key People (one per line)</label>
                <textarea id="key_people" name="key_people" rows="3" class="form-control" placeholder="Name - Director&#10;Name - Manager">{{ $p('key_people') }}</textarea>
                <div class="form-text">Write as "Name - Designation".</div>
            </div>
            <div class="col-md-6">
                <div class="row g-3">
                    <div class="col-sm-7">
                        <label class="form-label" for="cin_no">CIN (Company Reg. No.)</label>
                        <input type="text" id="cin_no" name="cin_no" value="{{ $p('cin_no') }}" class="form-control text-uppercase">
                    </div>
                    <div class="col-sm-5">
                        <label class="form-label" for="established_year">Established</label>
                        <input type="text" id="established_year" name="established_year" value="{{ $p('established_year') }}" class="form-control" placeholder="e.g. 2021">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="whatsapp">WhatsApp Number</label>
                        <input type="text" id="whatsapp" name="whatsapp" value="{{ $p('whatsapp') }}" class="form-control" placeholder="10-digit mobile number">
                    </div>
                </div>
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
