@extends('layouts.keptkaya')

@section('page-topic', 'รายละเอียดค่าถังขยะรายปี')
@section('nav-header', 'รับชำระค่าจัดเก็บถังขยะรายปี')
@section('route-header')
 {{ route('keptkayas.annual_payments.index') }}
@endsection
@section('nav-main', 'ชำระเงิน')
    
@section('style')
    <style>
        .month-item {
            transition: all 0.2s ease;
        }
        
        /* 1. จ่ายแล้ว (Paid) - สีเขียว */
        .month-item.paid {
            opacity: 0.9;
            background-color: #f0fdf4; 
            border-color: #bbf7d0 !important;
        }
        .month-item.paid label {
            cursor: default;
            color: #166534 !important;
            font-weight: bold;
        }

        /* 2. ก่อนเป็นสมาชิก (Before Member) - สีเทาเข้ม */
        .month-item.before-member {
            opacity: 0.6;
            background-color: #e9ecef; 
            border-color: #dee2e6 !important;
        }
        .month-item.before-member label {
            cursor: not-allowed;
            color: #6c757d !important;
        }

        /* 3. รอคิวจ่าย (Waiting Waterfall) - สีเทาจางๆ */
        .month-item.waiting-queue {
            opacity: 0.6;
            background-color: #fff;
            border-color: #e9ecef !important;
            cursor: not-allowed;
        }
        .month-item.waiting-queue label {
            color: #adb5bd !important;
        }

        .summary-value { font-weight: 600; color: #344767; }
        .total-amount-display { font-size: 1.5rem; font-weight: 700; color: #cb0c9f; }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 mx-auto">
            
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-0 font-weight-bold">รายละเอียดการชำระเงิน</h6>
                    <p class="text-xs text-secondary mb-0">จัดการข้อมูลและบันทึกการรับเงิน</p>
                </div>
                <a href="{{ route('keptkayas.annual_payments.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                    <i class="fas fa-arrow-left me-2"></i> กลับหน้ารายการ
                </a>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
                    <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
                    <span class="alert-text"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="row">
                {{-- Left Column (User Info) --}}
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">ข้อมูลสมาชิก</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-4">
                                <div class="avatar avatar-lg me-3 bg-gradient-primary border-radius-lg shadow">
                                    <i class="fas fa-user fa-lg text-white"></i>
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">{{ $wasteBinSubscription->wasteBin->user->firstname ?? '-' }} {{ $wasteBinSubscription->wasteBin->user->lastname ?? '' }}</h6>
                                    <p class="text-xs text-secondary mb-0">
                                        <i class="fas fa-trash-alt me-1"></i> 
                                        {{ $wasteBinSubscription->wasteBin->bin_code ?? 'ไม่ระบุรหัสถัง' }}
                                    </p>
                                </div>
                            </div>
                            <hr class="horizontal dark my-3">
                            <ul class="list-group">
                                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center"><i class="fas fa-calendar-alt text-white opacity-10"></i></div>
                                        <div class="d-flex flex-column"><h6 class="mb-1 text-dark text-sm">ปีงบประมาณ</h6></div>
                                    </div>
                                    <div class="d-flex align-items-center text-dark text-sm font-weight-bold">{{ $wasteBinSubscription->fiscal_year  }}</div>
                                </li>
                                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3 bg-gradient-info shadow text-center"><i class="fas fa-coins text-white opacity-10"></i></div>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">ค่าธรรมเนียมรายปี</h6>
                                            <span class="text-xs">เดือนละ {{ number_format($wasteBinSubscription->month_fee, 0) }} ฿</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center text-dark text-sm font-weight-bold">{{ number_format($wasteBinSubscription->annual_fee, 2) }} ฿</div>
                                </li>
                                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3 bg-gradient-success shadow text-center"><i class="fas fa-check text-white opacity-10"></i></div>
                                        <div class="d-flex flex-column"><h6 class="mb-1 text-dark text-sm">ชำระแล้ว</h6></div>
                                    </div>
                                    <div class="d-flex align-items-center text-success text-sm font-weight-bold">{{ number_format($wasteBinSubscription->total_paid_amt, 2) }} ฿</div>
                                </li>
                                <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3 bg-gradient-danger shadow text-center"><i class="fas fa-exclamation text-white opacity-10"></i></div>
                                        <div class="d-flex flex-column"><h6 class="mb-1 text-dark text-sm">ค้างชำระ</h6></div>
                                    </div>
                                    <div class="d-flex align-items-center text-danger text-sm font-weight-bold">{{ number_format($wasteBinSubscription->annual_fee - $wasteBinSubscription->total_paid_amt, 2) }} ฿</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Right Column (Payment Form) --}}
                <div class="col-lg-8">
                    <div class="card h-100">
                        @if($isBinActiveForAnnualCollection)
                            <div class="card-header pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">เลือกเดือนที่ต้องการชำระ</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllMonths">
                                        <label class="form-check-label font-weight-bold" for="selectAllMonths">เลือกชำระทั้งหมด</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="paymentForm" action="{{ route('keptkayas.annual_payments.store_payment', $wasteBinSubscription->id) }}" method="POST">
                                    @csrf
                                    
                                    {{-- Month Grid --}}
                                    <div class="row g-2 mb-4">
                                        @foreach($paymentSchedule as $monthData)
                                            @php
                                                $checkboxId = 'month_' . $monthData['month_num'] . '_' . $monthData['year'];
                                                
                                                // Check Status from Server
                                                $isActive = $monthData['active'] == 1; 
                                                $isPaid = $monthData['is_paid'] && $monthData['paid_amount'] >= $monthData['due_amount'];
                                                
                                                // Server-Disabled: คือเดือนที่ User ยุ่งไม่ได้ (จ่ายแล้ว หรือ ยังไม่เป็นสมาชิก)
                                                // เราต้องส่ง Flag นี้ให้ JS รู้ เพื่อที่ JS จะได้ไม่ไปยุ่งกับมันตอนทำ Waterfall
                                                $isServerDisabled = $isPaid || !$isActive;

                                                // Class CSS for Display
                                                $itemClass = '';
                                                if ($isPaid) $itemClass = 'paid';
                                                elseif (!$isActive) $itemClass = 'before-member';

                                                $monthNameShort = \Carbon\Carbon::createFromDate(null, $monthData['month_num'], 1)->locale('th')->isoFormat('MMM');
                                                $thaiYear = $monthData['year'] + 543;
                                            @endphp

                                            <div class="col-6 col-sm-4 col-md-3">
                                                <div class="form-check p-2 border rounded-2 month-item {{ $itemClass }}">
                                                    <input class="form-check-input month-checkbox ms-1" type="checkbox"
                                                        id="{{ $checkboxId }}"
                                                        name="selected_months[]"
                                                        value="{{ $monthData['month_num'] . '|' . $monthData['year'] }}"
                                                        data-due-amount="{{ $monthData['due_amount'] }}"
                                                        
                                                        {{-- Data Attribute สำคัญ: บอก JS ว่าอันนี้ Server สั่งล็อค --}}
                                                        data-server-disabled="{{ $isServerDisabled ? 'true' : 'false' }}"
                                                        
                                                        {{ $isServerDisabled ? 'disabled' : '' }}
                                                        {{ $isPaid ? 'checked' : '' }}
                                                    >
                                                    
                                                    <label class="form-check-label ms-2 w-75" for="{{ $checkboxId }}">
                                                        <span class="d-block text-sm font-weight-bold">
                                                            {{ $monthNameShort }} {{ $thaiYear }}
                                                        </span>

                                                        @if($isPaid)
                                                            <span class="text-xs text-success"><i class="fas fa-check-circle me-1"></i>จ่ายแล้ว</span>
                                                        @elseif(!$isActive)
                                                            <span class="text-xs text-secondary">ยังไม่เป็นสมาชิก</span>
                                                        @else
                                                            <span class="text-xs text-muted">{{ number_format($monthData['due_amount'], 0) }} ฿</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <hr class="horizontal dark my-4">
                                    
                                    {{-- Payment Inputs --}}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-static mb-4">
                                                <label for="payment_date" class="font-weight-bold">วันที่รับชำระ</label>
                                                <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <div class="input-group input-group-static">
                                                <label for="notes">หมายเหตุ (ถ้ามี)</label>
                                                <input type="text" class="form-control" id="notes" name="notes" placeholder="ระบุเลขที่ใบเสร็จเล่มอื่น หรือบันทึกเพิ่มเติม">
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex flex-column align-items-end justify-content-center bg-gray-100 border-radius-lg p-3">
                                            <span class="text-sm text-secondary font-weight-bold mb-2">ยอดเงินที่ต้องชำระรวม</span>
                                            <div class="d-flex align-items-baseline">
                                                <span class="h4 text-primary font-weight-bolder me-2" id="display_amount">0.00</span>
                                                <span class="text-sm text-secondary">บาท</span>
                                            </div>
                                            <input type="hidden" id="amount_paid" name="amount_paid" value="0.00">
                                            <input type="hidden" name="amount_paid_from_js_calc" id="amount_paid_from_js_calc" value="0.00">
                                            <button type="submit" class="btn bg-gradient-primary w-100 mt-3 shadow-lg">
                                                <i class="fas fa-save me-2"></i> บันทึกการรับเงิน
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        @else
                            {{-- กรณีบริการถูกระงับ --}}
                            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-5">
                                <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mb-3">
                                    <i class="fas fa-ban opacity-10" aria-hidden="true"></i>
                                </div>
                                <h5 class="font-weight-bolder">บริการนี้ถูกระงับชั่วคราว</h5>
                                <p class="mb-0 text-sm text-secondary">ถังขยะนี้ไม่ได้เปิดใช้งานบริการรายปี จึงไม่สามารถรับชำระเงินได้</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = Array.from(document.querySelectorAll('.month-checkbox'));
        const selectAllCheckbox = document.getElementById('selectAllMonths');
        const amountPaidInput = document.getElementById('amount_paid');
        const amountPaidFromJsCalcInput = document.getElementById('amount_paid_from_js_calc');
        const displayAmount = document.getElementById('display_amount');

        // ฟังก์ชัน Waterfall Logic: บังคับเลือกเรียงเดือน
        function updateWaterfallState() {
            let allowNext = true; // เริ่มต้นอนุญาตให้เดือนแรก (ที่จ่ายได้) กดได้เสมอ

            checkboxes.forEach((cb) => {
                const isServerDisabled = cb.dataset.serverDisabled === 'true'; // เป็นเดือนที่จ่ายแล้ว หรือ ยังไม่เป็นสมาชิก
                
                if (isServerDisabled) {
                    // ถ้า Server ล็อคมาแล้ว (เช่น จ่ายแล้ว หรือ ไม่ใช่สมาชิก)
                    // เราไม่ต้องทำอะไรกับมัน แค่ข้ามไป
                    // แต่สถานะ allowNext ยังคงเดิม (ถ้าผ่านเดือนจ่ายแล้วมา ก็ยังอนุญาตเดือนถัดไปได้)
                    return; 
                }

                const parentDiv = cb.closest('.month-item');

                if (allowNext) {
                    // ถ้าเดือนนี้ได้รับอนุญาตให้กดได้
                    cb.disabled = false;
                    parentDiv.classList.remove('waiting-queue');
                    
                    // Logic สำคัญ: ถ้าเดือนนี้ "ถูกติ๊ก" -> เดือนหน้าถึงจะกดได้
                    // ถ้าเดือนนี้ "ไม่ถูกติ๊ก" -> เดือนหน้าห้ามกด
                    allowNext = cb.checked;
                } else {
                    // ถ้าเดือนนี้ยังไม่ถึงคิว
                    cb.disabled = true;
                    cb.checked = false; // เคลียร์ค่าออก
                    parentDiv.classList.add('waiting-queue');
                    
                    // ส่งต่อสถานะห้ามกดไปเดือนถัดไป
                    allowNext = false; 
                }
            });
        }

        function updateAmountPaid() {
            let totalAmount = 0;
            let hasPayable = false;
            let allPayableChecked = true;

            checkboxes.forEach(cb => {
                const isServerDisabled = cb.dataset.serverDisabled === 'true';

                // คิดเงินเฉพาะอันที่กดเลือกได้ และ ถูกเลือก
                if (!cb.disabled && cb.checked && !isServerDisabled) {
                    totalAmount += parseFloat(cb.dataset.dueAmount);
                }

                // Check Logic for Select All Status
                if (!isServerDisabled) {
                    hasPayable = true;
                    if (!cb.checked) allPayableChecked = false;
                }
            });

            // Update UI Select All
            if (selectAllCheckbox) {
                if (!hasPayable) {
                    selectAllCheckbox.disabled = true;
                    selectAllCheckbox.checked = true; 
                } else {
                    selectAllCheckbox.disabled = false;
                    selectAllCheckbox.checked = allPayableChecked;
                }
            }

            // Update Amount Display
            const formattedTotal = totalAmount.toFixed(2);
            amountPaidInput.value = formattedTotal;
            amountPaidFromJsCalcInput.value = formattedTotal;
            if(displayAmount) {
                displayAmount.innerText = new Intl.NumberFormat('th-TH', { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                }).format(totalAmount);
            }
        }

        // --- Event Listeners ---

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateWaterfallState(); // ล็อค/ปลดล็อคตัวถัดไป
                updateAmountPaid();     // คำนวณเงิน
            });
        });

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                // สั่งติ๊กเรียงลงมาตาม Waterfall
                let allowTick = true;
                
                checkboxes.forEach(cb => {
                    const isServerDisabled = cb.dataset.serverDisabled === 'true';
                    if (!isServerDisabled) {
                        if (isChecked && allowTick) {
                            cb.checked = true;
                        } else {
                            cb.checked = false;
                        }
                    }
                });

                updateWaterfallState(); // Validate อีกรอบ
                updateAmountPaid();
            });
        }

        // Init Logic
        updateWaterfallState();
        updateAmountPaid();

        // Auto print receipt logic (คงไว้เหมือนเดิม)
        const lastPaymentDate = "{{ session('last_payment_date') }}";
        if (lastPaymentDate) {
            const subscriptionId = "{{ $wasteBinSubscription->id }}";
            const printUrl = `/annual-payments/${subscriptionId}/receipt/${lastPaymentDate}`;
            window.open(printUrl, '_blank');
        }
    });
</script>
@endsection