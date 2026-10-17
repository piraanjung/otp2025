@extends('layouts.keptkaya')

@section('content')
    
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>ประวัติการขายขยะ: {{ $user->prefix }}{{ $user->firstname }} {{ $user->lastname }}</h4>
        <a href="{{ route('keptkayas.recycle-bank.members') }}" class="btn btn-secondary btn-sm">ย้อนกลับ</a>
    </div>

    <!-- ข้อมูลสรุปสมาชิก -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>ที่อยู่:</strong> {{ $user->address }}</div>
                <div class="col-md-4"><strong>โซน / โซนย่อย:</strong> {{ optional($user->user_zone)->name }} / {{ optional($user->user_subzone)->name }}</div>
                <div class="col-md-2"><strong>ยอดเงินคงเหลือ:</strong> <span class="text-success">{{ number_format(optional($user->recycleBankAccount)->balance ?? 0, 2) }}</span> บาท</div>
                <div class="col-md-2"><strong>แต้มคงเหลือ:</strong> <span class="text-primary">{{ number_format(optional($user->recycleBankAccount)->points ?? 0) }}</span> แต้ม</div>
            </div>
        </div>
    </div>

    <!-- ตัวเลือกปีงบประมาณ -->
    <form method="GET" action="{{ route('keptkayas.recycle-bank.history', $user->id) }}" class="row g-3 mb-4 align-items-center">
        <div class="col-auto">
            <label for="fiscal_year" class="col-form-label fw-bold">เลือกปีงบประมาณ (พ.ศ.):</label>
        </div>
        <div class="col-auto">
            <select name="fiscal_year" id="fiscal_year" class="form-select" onchange="this.form.submit()">
                @foreach($fiscalYears as $year)
                    <option value="{{ $year }}" {{ $selectedFiscalYear == $year ? 'selected' : '' }}>
                        {{ $year + 543 }} (1 ต.ค. {{ $year - 1 + 543 }} - 30 ก.ย. {{ $year + 543 }})
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <!-- รายการบิลการขายขยะ -->
    <div class="accordion" id="transactionAccordion">
        @forelse($transactions as $index => $trans)
            <div class="accordion-item mb-2 border">
                <h2 class="accordion-header" id="heading{{ $trans->id }}">
                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $trans->id }}">
                        <div class="d-flex justify-content-between w-100 me-3">
                            <span><strong>เลขที่บิล:</strong> {{ $trans->kp_u_trans_no }} | <strong>วันที่:</strong> {{ optional($trans->transaction_date)->format('d/m/Y H:i') }}</span>
                            <span>
                                <span class="badge bg-success me-2">เงินรวม: {{ number_format($trans->total_amount, 2) }} บาท</span>
                                <span class="badge bg-primary">แต้มรวม: {{ number_format($trans->total_points) }} แต้ม</span>
                            </span>
                        </div>
                    </button>
                </h2>
                <div id="collapse{{ $trans->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#transactionAccordion">
                    <div class="accordion-body">
                        <h6>รายการขยะที่ขาย</h6>
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr class="table-light">
                                    <th>รายการขยะ</th>
                                    <th class="text-end">จำนวน</th>
                                    <th class="text-center">หน่วย</th>
                                    <th class="text-end">ราคา/หน่วย</th>
                                    <th class="text-end">รวมเงิน (บาท)</th>
                                    <th class="text-end">แต้ม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trans->details as $detail)
                                <tr>
                                    <td>{{ optional($detail->item)->name ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($detail->amount_in_units, 2) }}</td>
                                    <td class="text-center">{{ optional($detail->unit)->name ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($detail->price_per_unit, 2) }}</td>
                                    <td class="text-end">{{ number_format($detail->amount, 2) }}</td>
                                    <td class="text-end">{{ number_format($detail->points) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-end text-muted small">
                            ผู้บันทึก: {{ optional($trans->recorder)->firstname }} {{ optional($trans->recorder)->lastname }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning text-center">
                ไม่พบประวัติการขายขยะในปีงบประมาณ {{ $selectedFiscalYear + 543 }}
            </div>
        @endforelse
    </div>
</div>
    
@endsection