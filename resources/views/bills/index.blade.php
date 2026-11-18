@extends('layouts.app')

@section('title', 'All Bills')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">All Bills</h1>
    <a href="{{ route('bills.create') }}" class="btn btn-git"><i class="bi bi-plus-lg"></i> Generate New Bill</a>
</div>

<form method="GET" action="{{ route('bills.index') }}" class="mb-3">
    <div class="input-group" style="max-width: 460px;">
        <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Search by investor name or bill number">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
        @if ($search !== '')<a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">Clear</a>@endif
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Bill Number</th>
                    <th>Investor Name</th>
                    <th>Mobile Number</th>
                    <th class="text-end">Investment Amount</th>
                    <th>Bill Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($bills as $bill)
                <tr>
                    <td class="fw-semibold">{{ $bill->bill_no }}</td>
                    <td>{{ $bill->investor_name }}</td>
                    <td>{{ $bill->mobile }}</td>
                    <td class="text-end">&#8377; {{ number_format($bill->amount, 2) }}</td>
                    <td>{{ $bill->bill_date->format('d-m-Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('bills.show', $bill) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View Bill</a>
                        <a href="{{ route('bills.pdf', $bill) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No bills found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $bills->links('pagination::bootstrap-5') }}</div>
@endsection
