@extends('layouts.admin1')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">แก้ไขการจดมิเตอร์และสถานะใบแจ้งหนี้</h1>
        <a href="{{ route('superadmin.tw_meter_readings.index', ['period_id' => $period->id]) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> กลับไปสรุปการจดมิเตอร์
        </a>
    </div>


    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                รายการสำหรับแก้ไขใน Subzone: {{ $subzone->zone_block_name ?? 'N/A' }} ({{ $subzone->zone->zone_name ?? 'N/A' }})
                <br>
                สำหรับ Period: {{ $period->period_name ?? 'N/A' }} ({{ $period->budgetYear->year ?? 'N/A' }})
            </h5>
        </div>
        <div class="card-body">
            @if ($readingsToEdit->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    ไม่พบรายการจดมิเตอร์ที่สถานะเป็น "pending" สำหรับ Subzone และ Period นี้
                </div>
            @else
                <form action="{{ route('tw_meter_readings.store_batch_edit_recorded') }}" method="POST">
                    @csrf
                    <input type="hidden" name="period_id" value="{{ $period->id }}">
                    <input type="hidden" name="subzone_id" value="{{ $subzone->id }}">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัสมิเตอร์</th>
                                    <th>ลูกค้า</th>
                                    <th>เลขจดก่อนหน้า</th>
                                    <th>เลขจดปัจจุบัน <span class="text-danger">*</span></th>
                                    <th>ปริมาณน้ำ</th>
                                    <th>ยอดชำระ</th>
                                    <th>สถานะใบแจ้งหนี้ <span class="text-danger">*</span></th>
                                    <th>หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($readingsToEdit as $index => $reading)
                                    @php
                                        // Load meter, meterType, and invoice directly for display if not eager loaded initially
                                        $meter = $reading->meter;
                                        $invoice = $reading->invoice;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $meter->meter_code ?? 'N/A' }}
                                            <input type="hidden" name="readings[{{ $index }}][id]" value="{{ $reading->id }}">
                                        </td>
                                        <td>{{ $meter->customer_name ?? ($meter->user->firstname . ' ' . $meter->user->lastname ?? 'N/A') }}</td>
                                        <td>{{ number_format($reading->previous_reading_value, 2) }}</td>
                                        <td>
                                            <input type="number" step="0.01" name="readings[{{ $index }}][reading_value]"
                                                   class="form-control @error('readings.'.$index.'.reading_value') is-invalid @enderror"
                                                   value="{{ old('readings.'.$index.'.reading_value', $reading->reading_value) }}"
                                                   required min="{{ $reading->previous_reading_value }}">
                                            @error('readings.'.$index.'.reading_value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            {{ number_format($reading->reading_value - $reading->previous_reading_value, 2) }}
                                        </td>
                                        <td>
                                            {{ number_format($invoice->total_paid ?? 0, 2) }}
                                        </td>
                                        <td>
                                            <select name="readings[{{ $index }}][invoice_status]"
                                                    class="form-select @error('readings.'.$index.'.invoice_status') is-invalid @enderror" required>
                                                <option value="pending" {{ old('readings.'.$index.'.invoice_status', $invoice->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="paid" {{ old('readings.'.$index.'.invoice_status', $invoice->status ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="overdue" {{ old('readings.'.$index.'.invoice_status', $invoice->status ?? '') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                                <option value="cancelled" {{ old('readings.'.$index.'.invoice_status', $invoice->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                            @error('readings.'.$index.'.invoice_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" name="readings[{{ $index }}][comment]"
                                                   class="form-control @error('readings.'.$index.'.comment') is-invalid @enderror"
                                                   value="{{ old('readings.'.$index.'.comment', $reading->comment) }}">
                                            @error('readings.'.$index.'.comment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-success btn-lg">บันทึกการแก้ไขทั้งหมด</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
