@extends('layouts.keptkaya')

@section('content')
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h3 class="mb-0">รายการใบแจ้งหนี้ค้างชำระค่าจัดเก็บขยะรายปี</h3>
            </div>
            <div class="card-body">
                @if ($invoices->isEmpty())
                    <div class="alert alert-info text-center">
                        ไม่พบรายการใบแจ้งหนี้ค้างชำระ
                    </div>
                @else
                    <form id="print-form" action="{{ route('keptkayas.annual_payments.print_selected_invoices') }}"
                        method="POST">
                        @csrf
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th><input type="checkbox" id="select-all-invoices"></th>
                                        <th>#</th>
                                        <th>ชื่อผู้ใช้งาน</th>
                                        <th>รหัสถังขยะ</th>
                                        <th>ปีงบประมาณ</th>
                                        <th>ยอดรวม</th>
                                        <th>ยอดที่ชำระแล้ว</th>
                                        <th>ยอดค้างชำระ</th>
                                        <th>สถานะ</th>
                                        <th>การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $index => $invoice)
                                        <tr>
                                            <td><input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}"
                                                    class="invoice-checkbox"></td>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $invoice->wasteBin->user->firstname }} {{ $invoice->wasteBin->user->lastname }}
                                            </td>
                                            <td>{{ $invoice->wasteBin->bin_code }}</td>
                                            <td>{{ $invoice->fiscal_year }}</td>
                                            <td>{{ number_format($invoice->annual_fee, 2) }}</td>
                                            <td>{{ number_format($invoice->total_paid_amt, 2) }}</td>
                                            <td>{{ number_format($invoice->annual_fee - $invoice->total_paid_amt, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $invoice->status == 'pending' ? 'bg-warning' : 'bg-info' }}">
                                                    {{ ucfirst($invoice->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('keptkayas.annual_payments.show', $invoice->id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="bi bi-eye"></i> ดูรายละเอียด
                                                </a>
                                                {{-- Assuming a payment route for this item --}}
                                                {{-- <a href="{{ route('keptkayas.annual_payments.edit_payment', $invoice->id) }}"
                                                    class="btn btn-success btn-sm">
                                                    <i class="bi bi-cash"></i> ชำระเงิน
                                                </a> --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                @endif
            </div>
            <div class="card-footer text-end">
                @if (!$invoices->isEmpty())
                    <button type="submit" form="print-form" class="btn btn-primary me-2">
                        <i class="bi bi-printer-fill me-1"></i> พิมพ์ใบแจ้งหนี้ที่เลือก
                    </button>
                @endif
                {{-- Assuming a route to go back to annual payments index --}}
                <a href="{{ route('keptkayas.annual_payments.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> กลับหน้าหลัก
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('select-all-invoices');
            const invoiceCheckboxes = document.querySelectorAll('.invoice-checkbox');

            selectAllCheckbox.addEventListener('change', function () {
                invoiceCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            invoiceCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else if (document.querySelectorAll('.invoice-checkbox:checked').length === invoiceCheckboxes.length) {
                        selectAllCheckbox.checked = true;
                    }
                });
            });
        });
    </script>
@endsection