@extends('layouts.app')

@php
    $editing = isset($bill);
    $v = fn ($key, $default = '') => old($key, $d[$key] ?? $default);
@endphp

@section('title', $editing ? 'Edit Bill '.$bill->bill_no : 'Generate Bill')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">{{ $editing ? 'Edit Bill '.$bill->bill_no : 'Generate New Bill' }}</h1>
    @if ($editing)
        <a href="{{ route('bills.show', $bill) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Bill</a>
    @endif
</div>

@if ($errors->any())
    <div class="alert alert-danger">Please correct the highlighted fields.</div>
@endif

<form method="POST" action="{{ $editing ? route('bills.update', $bill) : route('bills.preview') }}" enctype="multipart/form-data" class="card" novalidate
      @if ($editing) data-confirm="Save the changes to bill {{ $bill->bill_no }}? The PDF will be created again with the new details." data-confirm-button="Yes, update" @endif>
    @csrf
    @if ($editing) @method('PUT') @endif
    <div class="card-body">
        <h2 class="h6 text-uppercase text-muted mb-3">Investor Details</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="investor_name">Investor Name *</label>
                <input type="text" id="investor_name" name="investor_name" value="{{ $v('investor_name') }}" class="form-control @error('investor_name') is-invalid @enderror" required>
                @error('investor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="nominee_name">Nominee Name</label>
                <input type="text" id="nominee_name" name="nominee_name" value="{{ $v('nominee_name') }}" class="form-control @error('nominee_name') is-invalid @enderror">
                @error('nominee_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="address">Address *</label>
                <textarea id="address" name="address" rows="2" class="form-control @error('address') is-invalid @enderror" required>{{ $v('address') }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="mobile">Mobile No *</label>
                <input type="tel" id="mobile" name="mobile" value="{{ $v('mobile') }}" class="form-control @error('mobile') is-invalid @enderror" maxlength="14" required>
                @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">Email ID</label>
                <input type="email" id="email" name="email" value="{{ $v('email') }}" class="form-control @error('email') is-invalid @enderror">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="aadhaar_no">Aadhaar Card No *</label>
                <input type="text" id="aadhaar_no" name="aadhaar_no" value="{{ $v('aadhaar_no') }}" class="form-control @error('aadhaar_no') is-invalid @enderror" inputmode="numeric" maxlength="14" placeholder="12 digits" required>
                @error('aadhaar_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="pan_no">PAN Card No *</label>
                <input type="text" id="pan_no" name="pan_no" value="{{ $v('pan_no') }}" class="form-control text-uppercase @error('pan_no') is-invalid @enderror" maxlength="10" placeholder="ABCDE1234F" required>
                @error('pan_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <hr class="my-4">
        <h2 class="h6 text-uppercase text-muted mb-3">Investment</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="amount">Investment Amount (&#8377;) *</label>
                <input type="number" id="amount" name="amount" value="{{ $v('amount') }}" step="0.01" min="1" class="form-control @error('amount') is-invalid @enderror" required>
                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-8">
                <label class="form-label" for="amount_words">Amount in Words (automatic)</label>
                <input type="text" id="amount_words" class="form-control bg-light" readonly tabindex="-1">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="duration">Duration *</label>
                <input type="text" id="duration" name="duration" value="{{ $v('duration') }}" list="durationList" class="form-control @error('duration') is-invalid @enderror" placeholder="e.g. 12 Months" required>
                <datalist id="durationList">
                    <option value="3 Months"><option value="6 Months"><option value="12 Months"><option value="24 Months"><option value="36 Months">
                </datalist>
                @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="from_date">From Date *</label>
                <input type="date" id="from_date" name="from_date" value="{{ $v('from_date') }}" class="form-control @error('from_date') is-invalid @enderror" required>
                @error('from_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="to_date">To Date *</label>
                <input type="date" id="to_date" name="to_date" value="{{ $v('to_date') }}" class="form-control @error('to_date') is-invalid @enderror" required>
                @error('to_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Filled automatically from duration (in months/years); you can change it.</div>
            </div>
        </div>

        <hr class="my-4">
        <h2 class="h6 text-uppercase text-muted mb-3">Proof &amp; Declaration</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="income_proof">Investor Income Proof *</label>
                <input type="text" id="income_proof" name="income_proof" value="{{ $v('income_proof') }}" list="proofList" class="form-control @error('income_proof') is-invalid @enderror" placeholder="e.g. ITR / Salary Slip / Bank Statement" required>
                <datalist id="proofList">
                    <option value="ITR"><option value="Salary Slip"><option value="Bank Statement"><option value="Form 16"><option value="Business Income">
                </datalist>
                @error('income_proof')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="income_proof_file">Income Proof Document (optional)</label>
                <input type="file" id="income_proof_file" name="income_proof_file" accept=".pdf,.jpg,.jpeg,.png" class="form-control @error('income_proof_file') is-invalid @enderror">
                @error('income_proof_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if ($hasDraftFile)
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" name="keep_income_proof_file" value="1" id="keep_file" checked>
                        <label class="form-check-label small" for="keep_file">Keep attached file: {{ $draftFileName }}</label>
                    </div>
                @endif
                <div class="form-text">PDF/JPG/PNG, max 5 MB. Stored privately, not printed on the bill.</div>
            </div>
            <div class="col-12">
                <label class="form-label" for="self_declaration">Self Declaration *</label>
                <textarea id="self_declaration" name="self_declaration" rows="2" class="form-control @error('self_declaration') is-invalid @enderror" required>{{ $v('self_declaration') }}</textarea>
                @error('self_declaration')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="special_note">Special Note</label>
                <textarea id="special_note" name="special_note" rows="2" class="form-control @error('special_note') is-invalid @enderror" placeholder="Optional note for this bill only">{{ $v('special_note') }}</textarea>
                @error('special_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="bill_date">Bill Date *</label>
                <input type="date" id="bill_date" name="bill_date" value="{{ $v('bill_date') }}" class="form-control @error('bill_date') is-invalid @enderror" required>
                @error('bill_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    <div class="card-footer bg-white text-end">
        @if ($editing)
            <button class="btn btn-git px-4"><i class="bi bi-save"></i> Update Bill</button>
        @else
            <button class="btn btn-git px-4"><i class="bi bi-eye"></i> Preview Bill</button>
        @endif
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
        'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    var tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    function two(n) { return n < 20 ? ones[n] : (tens[Math.floor(n / 10)] + ' ' + ones[n % 10]).trim(); }
    function num(n) {
        var parts = [], units = [[10000000, 'Crore'], [100000, 'Lakh'], [1000, 'Thousand'], [100, 'Hundred']];
        units.forEach(function (u) {
            if (n >= u[0]) { var c = Math.floor(n / u[0]); parts.push((c > 99 ? num(c) : two(c)) + ' ' + u[1]); n %= u[0]; }
        });
        if (n > 0) parts.push(two(n));
        return parts.join(' ');
    }
    function words(v) {
        v = Math.round(parseFloat(v) * 100) / 100;
        if (!v || v < 0) return '';
        var r = Math.floor(v), p = Math.round((v - r) * 100);
        return 'Rupees ' + (r ? num(r) : 'Zero') + (p ? ' and ' + num(p) + ' Paise' : '') + ' Only';
    }

    var amount = document.getElementById('amount'), out = document.getElementById('amount_words');
    function updateWords() { out.value = words(amount.value); }
    amount.addEventListener('input', updateWords); updateWords();

    // Auto-fill "To Date" from "From Date" + duration like "12 Months" / "1 Year"
    var dur = document.getElementById('duration'), from = document.getElementById('from_date'), to = document.getElementById('to_date');
    function fillTo() {
        var m = /^(\d+)\s*(month|months|year|years|yr|yrs)?\b/i.exec(dur.value.trim());
        if (!m || !from.value) return;
        var months = parseInt(m[1], 10) * (/^y/i.test(m[2] || '') ? 12 : 1);
        var d = new Date(from.value + 'T00:00:00');
        d.setMonth(d.getMonth() + months);
        d.setDate(d.getDate() - 1);
        to.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }
    dur.addEventListener('change', fillTo); from.addEventListener('change', fillTo);

    document.getElementById('pan_no').addEventListener('input', function () { this.value = this.value.toUpperCase(); });
})();
</script>
@endpush
