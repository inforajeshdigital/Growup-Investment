@extends('layouts.app')

@section('title', $bill->bill_no)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Bill {{ $bill->bill_no }}</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> All Bills</a>
        <a href="{{ route('bills.print', $bill) }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Print</a>
        <a href="{{ route('bills.pdf', $bill) }}" class="btn btn-git"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
        <a href="{{ route('bills.edit', $bill) }}" class="btn btn-outline-warning"><i class="bi bi-pencil-square"></i> Edit</a>
        <form method="POST" action="{{ route('bills.destroy', $bill) }}" class="d-inline"
              data-confirm="Delete bill {{ $bill->bill_no }} of {{ $bill->investor_name }}? This cannot be undone." data-confirm-button="Yes, delete" data-confirm-variant="danger">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
        </form>
    </div>
</div>

@if ($bill->income_proof_file)
    <div class="alert alert-light border small">
        <i class="bi bi-paperclip"></i> Income proof document:
        <a href="{{ route('bills.income-proof', $bill) }}">{{ $bill->income_proof_original_name ?: 'Download' }}</a>
        <span class="text-muted">(private, only visible after login)</span>
    </div>
@endif

<div class="paper">
    @include('bills._receipt')
</div>
@endsection
