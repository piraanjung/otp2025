@extends('layouts.super-admin')

@section('content')
 <h1 class="mb-0">Manage Payments</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <h6>Please fix the following errors:</h6>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Search Form --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Find Outstanding Invoices</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.payments.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search Meter Code / Customer Name:</label>
                        <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="e.g., MTR001 or John Doe">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary me-2">Search</button>
                        <a href="{{ route('superadmin.payments.index') }}" class="btn btn-outline-secondary">Clear Search</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Payment Form --}}
    <form action="{{ route('superadmin.payments.process') }}" method="POST" id="paymentForm">
        @csrf
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Select Invoices for Payment</h5>
                {{-- Global Select All Checkbox --}}
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="selectAllGlobalInvoices">
                    <label class="form-check-label" for="selectAllGlobalInvoices">Select All Meters</label>
                </div>
            </div>
            <div class="card-body">
                @forelse ($metersWithOutstandingInvoices as $meter)
                    @if ($meter->invoices->isNotEmpty())
                        <div class="card mb-3 border-secondary">
                            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    Meter: {{ $meter->id }} - {{ $meter->user->firstname }}
                                    <small class="d-block">{{ $meter->meter_address }}</small>
                                </h6>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input select-all-meter-invoices" type="checkbox" id="selectAllMeter_{{ $meter->id }}">
                                    <label class="form-check-label" for="selectAllMeter_{{ $meter->id }}">Select All for this Meter</label>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @foreach ($meter->invoices as $invoice)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input invoice-checkbox" type="checkbox" name="selected_invoices[]" value="{{ $invoice->id }}" id="invoice_{{ $invoice->id }}" data-amount="{{ $invoice->total_paid }}">
                                                <label class="form-check-label" for="invoice_{{ $invoice->id }}">
                                                    Invoice No: <strong>{{ $invoice->inv_no }}</strong> |
                                                    Period: {{ $invoice->period->period_name ?? 'N/A' }} ({{ $invoice->period->budgetYear->year ?? 'N/A' }}) |
                                                    Amount: {{ number_format($invoice->total_paid, 2) }} THB
                                                </label>
                                            </div>
                                            <span class="badge bg-warning text-dark">{{ ucfirst($invoice->status) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="alert alert-info text-center" role="alert">
                        No outstanding invoices found.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Payment Details Section --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Payment Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="payment_date" class="form-label">Payment Date:</label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="payment_type_id" class="form-label">Payment Method:</label>
                        <select name="payment_type_id" id="payment_type_id" class="form-select" required>
                            <option value="">Select Payment Method</option>
                            @foreach ($paymentTypes as $paymentType)
                                <option value="{{ $paymentType->id }}">{{ $paymentType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="total_selected_amount" class="form-label">Total Selected Amount (THB):</label>
                        <input type="text" id="total_selected_amount" class="form-control" readonly value="0.00">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="payment_amount" class="form-label">Amount Received (THB):</label>
                        <input type="number" step="0.01" name="payment_amount" id="payment_amount" class="form-control" required min="0">
                    </div>
                    <div class="col-12 mb-3">
                        <label for="comment" class="form-label">Comment:</label>
                        <textarea name="comment" id="comment" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-success btn-lg">Process Payment</button>
            </div>
        </div>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('superadmin.dashboard') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const invoiceCheckboxes = document.querySelectorAll('.invoice-checkbox');
        const totalSelectedAmountInput = document.getElementById('total_selected_amount');
        const selectAllMeterCheckboxes = document.querySelectorAll('.select-all-meter-invoices');
        const selectAllGlobalCheckbox = document.getElementById('selectAllGlobalInvoices'); // New global checkbox

        function updateSelectedAmount() {
            let totalAmount = 0;
            invoiceCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    totalAmount += parseFloat(checkbox.dataset.amount);
                }
            });
            totalSelectedAmountInput.value = totalAmount.toFixed(2);
            // Update global select all checkbox status
            const allInvoicesChecked = invoiceCheckboxes.length > 0 && Array.from(invoiceCheckboxes).every(cb => cb.checked);

            selectAllGlobalCheckbox.checked = allInvoicesChecked;
        }

        invoiceCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedAmount);
        });

        selectAllMeterCheckboxes.forEach(selectAllCheckbox => {
            selectAllCheckbox.addEventListener('change', function() {
                const meterCardBody = this.closest('.card-header').nextElementSibling;
                const checkboxesInMeter = meterCardBody.querySelectorAll('.invoice-checkbox');
                checkboxesInMeter.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedAmount(); // Recalculate total after selecting/deselecting all for a meter
            });
        });

        // New: Event listener for the global select all checkbox
        selectAllGlobalCheckbox.addEventListener('change', function() {
            selectAllMeterCheckboxes.forEach(selectAllMeterCb => {
                selectAllMeterCb.checked = this.checked; // Set individual meter select-all checkboxes
                // Trigger change event on meter select-all checkbox to update individual invoices
                // and ensure updateSelectedAmount is called
                const event = new Event('change');
                selectAllMeterCb.dispatchEvent(event);
            });
            updateSelectedAmount(); // Ensure total is updated after global selection
        });


        // Initial calculation on page load
        updateSelectedAmount();
    });
</script>
@endsection