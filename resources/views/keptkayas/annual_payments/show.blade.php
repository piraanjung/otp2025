@extends('layouts.keptkaya')

@section('page-topic', 'รายละเอียดค่าถังขยะรายปี')
@section('nav-header', 'รับชำระค่าจัดเก็บถังขยะรายปี')
@section('route-header')
 {{ route('keptkayas.annual_payments.index') }}
@endsection
@section('nav-main', 'รายละเอียดค่าถังขยะรายปี')
    
@section('style')
    <style>
        form-check-input:disabled ~ .form-check-label, .form-check-input[disabled] ~ .form-check-label {
            cursor: default;
            opacity:    1;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>รายละเอียดค่าถังขยะรายปี</h6>
                    <a href="{{ route('keptkayas.annual_payments.index') }}" class="btn btn-link text-secondary px-0 mb-0">
                        <i class="fas fa-arrow-left me-1"></i> กลับ
                    </a>
                </div>
                <div class="card-body p-3">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                            <span class="alert-text text-white"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                            <span class="alert-text text-white"><strong>เกิดข้อผิดพลาด!</strong> โปรดตรวจสอบข้อมูล</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            
                            <div class="card h-100">
                                <div class="card-header pb-0 p-3">
                                    <h6 class="text-uppercase text-body  font-weight-bolder">ข้อมูลการสมัครสมาชิก</h6>
                                </div>
                                <div class="card-body p-3">
                                    <ul class="list-group">
                                        <li class="list-group-item border-0 d-flex align-items-center px-0 mb-2">
                                        <div class="avatar me-3">
                                            <img src="{{asset('/soft-ui/assets/img/theme/dropbox.png')}}" alt="kal" class="border-radius-lg shadow">
                                        </div>
                                        <div class="d-flex align-items-start flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm"> {{ $wasteBinSubscription->wasteBin->user->firstname ?? 'N/A' }} {{ $wasteBinSubscription->wasteBin->user->lastname ?? '' }}</h6>
                                            <p class="mb-0 text-xs">Hi! I need more information..</p>
                                        </div>
                                        <a class="btn btn-link pe-3 ps-0 mb-0 ms-auto" href="javascript:;">Reply</a>
                                        </li>
                                    </ul>
                                    <ul class="list-group">
                                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">ปีงบประมาณ:</strong> &nbsp; {{ $wasteBinSubscription->fiscal_year }}     </li>                                   
                                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">ค่าธรรมเนียมรายปี:</strong> &nbsp; {{ number_format($wasteBinSubscription->annual_fee, 2) }} ฿ </li>
                                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">ค่าธรรมเนียมรายเดือน:</strong> &nbsp; {{ number_format($wasteBinSubscription->month_fee, 2) }} ฿ </li>
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">ชำระแล้วรวม:</strong> &nbsp; {{ number_format($wasteBinSubscription->total_paid_amt, 2) }} ฿</li>
                                    <li class="list-group-item border-0 ps-0 pb-0 text-sm"><strong class="text-dark">ค้างชำระ:</strong> &nbsp; <span class="text-danger font-weight-bold">{{ number_format($wasteBinSubscription->annual_fee - $wasteBinSubscription->total_paid_amt, 2) }} ฿</span>
                                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">สถานะ:</strong> &nbsp;
                                            @php
                                                $statusClass = '';
                                                switch($wasteBinSubscription->status) {
                                                    case 'paid': $statusClass = 'success'; break;
                                                    case 'partially_paid': $statusClass = 'warning'; break;
                                                    case 'overdue': $statusClass = 'danger'; break;
                                                    default: $statusClass = 'secondary'; break;
                                                }
                                            @endphp
                                            <span class="badge badge-sm bg-gradient-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $wasteBinSubscription->status)) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div><!--col-md-4-->
                        <div class="col-8">
                            <div class="card">
                                @if($isBinActiveForAnnualCollection)
                                    <div class="card-header">
                                        <h6 class="">บันทึกการชำระเงิน</h6>
                                    </div>
                                    <div class="card-body">
                                        <form id="paymentForm" action="{{ route('keptkayas.annual_payments.store_payment', $wasteBinSubscription->id) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="form-label">เลือกเดือนที่ต้องการชำระ:</label>
                                                    <div class="form-check form-check-inline mb-3">
                                                        <input class="form-check-input" type="checkbox" id="selectAllMonths">
                                                        <label class="form-check-label" for="selectAllMonths">เลือกทั้งหมด</label>
                                                    </div>
                                                    <div class="row">
                                                        @foreach($paymentSchedule as $monthData)
                                                            @php
                                                                $checkboxId = 'month_' . $monthData['month_num'] . '_' . $monthData['year'];
                                                                $isDisabled = $monthData['is_paid'] && $monthData['paid_amount'] >= $monthData['due_amount'];
                                                                $isChecked = false; // By default, no month is checked
                                                            @endphp
                                                            <div class="col-6 col-md-4 col-lg-4 mb-2">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input month-checkbox" type="checkbox"
                                                                        id="{{ $checkboxId }}"
                                                                        name="selected_months[]"
                                                                        value="{{ $monthData['month_num'] . '|' . $monthData['year'] }}"
                                                                        data-due-amount="{{ $monthData['due_amount'] }}"
                                                                        {{ $isDisabled ? 'disabled checked' : '' }}
                                                                        {{ $isChecked ? 'checked' : '' }}>
                                                                    <label class="form-check-label {{ $isDisabled ? 'text-success' : '' }}" for="{{ $checkboxId }}">
                                                                        {{ $monthData['month_name'] }} {{ $monthData['year'] }}
                                                                        @if($isDisabled)
                                                                            <div class="text-success text-xs">(ชำระแล้ว)</div>
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="input-group input-group-static mb-3">
                                                        <label for="amount_paid">จำนวนเงินที่ชำระ (฿)</label>
                                                        <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid" value="0.00" required readonly>
                                                        <small class="text-muted">คำนวณอัตโนมัติตามเดือนที่เลือก</small>
                                                    </div>
                                                    <input type="hidden" name="amount_paid_from_js_calc" id="amount_paid_from_js_calc" value="0.00">
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group input-group-static mb-3">
                                                        <label for="payment_date">วันที่ชำระ</label>
                                                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="input-group input-group-static">
                                                    <label for="notes">หมายเหตุ</label>
                                                    <textarea class="form-control" id="notes" name="notes" rows="1"></textarea>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn bg-gradient-primary">บันทึกการชำระเงิน</button>
                                            </div>
                                        </form>
                                @else
                                    <div class="alert alert-warning text-white" role="alert">
                                        <span class="alert-icon text-white"><i class="fas fa-exclamation-triangle"></i></span>
                                        <span class="alert-text"><strong>บริการถูกยกเลิก!</strong> ถังขยะนี้ไม่ได้ใช้งานสำหรับบริการเก็บขยะรายปีแล้ว จึงไม่สามารถบันทึกการชำระเงินได้</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthCheckboxes = document.querySelectorAll('.month-checkbox');
        const amountPaidInput = document.getElementById('amount_paid');
        const amountPaidFromJsCalcInput = document.getElementById('amount_paid_from_js_calc');
        const selectAllMonthsCheckbox = document.getElementById('selectAllMonths');
        const paymentForm = document.getElementById('paymentForm'); // Get the form element

        function updateAmountPaid() {
            let totalAmount = 0;
            monthCheckboxes.forEach(checkbox => {
                if (checkbox.checked && !checkbox.disabled) {
                    totalAmount += parseFloat(checkbox.dataset.dueAmount);
                }
            });
            amountPaidInput.value = totalAmount.toFixed(2);
            amountPaidFromJsCalcInput.value = totalAmount.toFixed(2);
        }

        selectAllMonthsCheckbox.addEventListener('change', function() {
            monthCheckboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = selectAllMonthsCheckbox.checked;
                }
            });
            updateAmountPaid();
        });

        monthCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (!this.checked) {
                    selectAllMonthsCheckbox.checked = false;
                } else {
                    const allChecked = Array.from(monthCheckboxes).every(cb => cb.checked || cb.disabled);
                    if (allChecked) {
                        selectAllMonthsCheckbox.checked = true;
                    }
                }
                updateAmountPaid();
            });
        });

        // Auto-open print receipt if last_payment_date is present in session
        const lastPaymentDate = "{{ session('last_payment_date') }}";
        if (lastPaymentDate) {
            const subscriptionId = "{{ $wasteBinSubscription->id }}";
            const printUrl = `/annual-payments/${subscriptionId}/receipt/${lastPaymentDate}`;
            window.open(printUrl, '_blank');
        }

        updateAmountPaid();
    });
</script>
@endsection
{{-- <div class="col-md-6">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder">ตารางการชำระเงินรายเดือน</h6>
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เดือน/ปี</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ยอดที่ต้องชำระ (฿)</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ชำระแล้ว (฿)</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">สถานะ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paymentSchedule as $monthData)
                                            @if (!$isBinActiveForAnnualCollection && $monthData['is_paid'])
                                            <tr>
                                                <td><p class="text-xs font-weight-bold mb-0">{{ $monthData['month_name'] }} {{ $monthData['year'] }}</p></td>
                                                <td class="text-center"><p class="text-xs font-weight-bold mb-0">{{ number_format($monthData['due_amount'], 2) }}</p></td>
                                                <td class="text-center"><p class="text-xs font-weight-bold mb-0">{{ number_format($monthData['paid_amount'], 2) }}</p></td>
                                                <td class="text-center">
                                                    @if($monthData['is_paid'])
                                                        <span class="badge badge-sm bg-gradient-success">ชำระแล้ว</span>
                                                    @else
                                                        <span class="badge badge-sm bg-gradient-warning">ค้างชำระ</span>
                                                    @endif
                                                </td>
                                            </tr>
                                                        
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div> --}}