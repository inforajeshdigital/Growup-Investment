@extends('layouts.app')

@section('title', 'Bill Preview')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h4 mb-0">Preview</h1>
        <div class="text-muted small">Check the details. The bill number is given when you click Generate.</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('bills.create', ['edit' => 1]) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
        <form method="POST" action="{{ route('bills.store') }}" onsubmit="this.querySelector('button').disabled = true;">
            @csrf
            <button class="btn btn-git"><i class="bi bi-check2-circle"></i> Confirm &amp; Generate Bill</button>
        </form>
    </div>
</div>

@if (! empty($draft['income_proof_original_name']))
    <div class="alert alert-light border small"><i class="bi bi-paperclip"></i> Income proof file attached: {{ $draft['income_proof_original_name'] }}</div>
@endif

<div class="paper">
    @include('bills._receipt')
</div>
@endsection
