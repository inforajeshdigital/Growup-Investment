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
@endsection
