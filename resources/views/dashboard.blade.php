@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('bills.create') }}" class="card h-100 text-decoration-none">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-file-earmark-plus fs-1" style="color: var(--git-blue);"></i>
                <div>
                    <div class="fw-semibold text-dark fs-5">Generate New Bill</div>
                    <div class="text-muted small">Create an investment receipt</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('bills.index') }}" class="card h-100 text-decoration-none">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-list-ul fs-1" style="color: var(--git-green);"></i>
                <div>
                    <div class="fw-semibold text-dark fs-5">All Generated Bills</div>
                    <div class="text-muted small">View, print or download</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-receipt fs-1" style="color: var(--git-red);"></i>
                <div>
                    <div class="text-muted small">Total Bills Generated</div>
                    <div class="fw-bold display-6">{{ number_format($totalBills) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($recentBills->isNotEmpty())
    <div class="card">
        <div class="card-header bg-white fw-semibold">Recent Bills</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light"><tr><th>Bill No</th><th>Investor</th><th class="text-end">Amount</th><th>Date</th><th></th></tr></thead>
                <tbody>
                @foreach ($recentBills as $bill)
                    <tr>
                        <td>{{ $bill->bill_no }}</td>
                        <td>{{ $bill->investor_name }}</td>
                        <td class="text-end">&#8377; {{ number_format($bill->amount, 2) }}</td>
                        <td>{{ $bill->bill_date->format('d-m-Y') }}</td>
                        <td class="text-end"><a href="{{ route('bills.show', $bill) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
