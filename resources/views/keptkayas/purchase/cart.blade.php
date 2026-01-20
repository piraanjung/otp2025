@if($user->can('access waste bank mobile'))
    @php $layout = 'layouts.keptkaya_mobile'; @endphp
@else
    @php $layout = 'layouts.keptkaya'; @endphp
@endif

@extends($layout)
@section('nav-header', 'สรุปรายการขาย')
@section('nav-current', 'สรุปรายการ')
@section('page-topic', 'สรุปรายการขาย')

@section('content')
<div class="container-fluid px-0" style="max-width: 700px; margin: 0 auto;">
    
    {{-- Header & Change User --}}
    <div class="d-flex justify-content-between align-items-center mb-3 px-2 mt-2">
        <h4 class="fw-bold mb-0 text-dark">สรุปรายการ</h4>
        <a href="{{ route('keptkayas.purchase.select_user') }}" class="btn btn-sm btn-light rounded-pill border shadow-sm text-muted">
            <i class="bi bi-arrow-repeat me-1"></i> เปลี่ยนคนขาย
        </a>
    </div>

    {{-- Alert Messages --}}
    @if (session('success') || session('error'))
        <div class="px-2 mb-3">
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 py-2"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-warning border-0 shadow-sm rounded-3 py-2"><i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}</div>
            @endif
        </div>
    @endif

    @if (empty($cart))
        {{-- Empty State --}}
        <div class="text-center py-5">
            <div class="mb-3 text-muted opacity-25">
                <i class="fa fa-shopping-basket fa-4x"></i>
            </div>
            <h5 class="text-muted">ยังไม่มีรายการในรถเข็น</h5>
            <a href="{{ route('keptkayas.purchase.form', $seller->id) }}" class="btn btn-primary rounded-pill px-4 mt-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> เพิ่มรายการขยะ
            </a>
        </div>
    @else
        <form action="{{ route('keptkayas.purchase.save_transaction') }}" method="POST">
            @csrf
            
            {{-- 1. Seller Info Card (Design ใหม่: สะอาดตา) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3 mx-2">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">ผู้ขาย (Seller)</small>
                        <h5 class="fw-bold mb-0 text-dark">{{ $seller->firstname }} {{ $seller->lastname }}</h5>
                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ Str::limit($seller->address, 40) }}</small>
                    </div>
                </div>
            </div>

            {{-- 2. Cart Items (Table แบบ Clean) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3 mx-2 overflow-hidden">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-list-ul me-2"></i>รายการขยะรีไซเคิล</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-secondary small">
                            <tr>
                                <th class="ps-3 border-0">รายการ</th>
                                <th class="text-end border-0">ราคา/หน่วย</th>
                                <th class="text-end border-0">รวมเงิน</th>
                                <th class="text-center border-0" style="width: 50px;">ลบ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grandTotal = 0;
                                $grandPoints = 0;
                            @endphp
                            @foreach ($cart as $index => $item)
                                @php
                                    $grandTotal += $item['amount'];
                                    $grandPoints += $item['points'];
                                @endphp
                                <tr class="border-bottom border-light">
                                    <td class="ps-3 py-3">
                                        <div class="fw-bold text-dark">{{ $item['item_name'] }}</div>
                                        <div class="badge bg-light text-dark border mt-1">
                                            {{ number_format($item['amount_in_units'], 2) }} {{ $item['unit_name'] }}
                                        </div>
                                    </td>
                                    <td class="text-end text-muted small">
                                        {{ number_format($item['price_per_unit'], 2) }}
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-success">{{ number_format($item['amount'], 2) }}</div>
                                        <small class="text-warning small">+{{ number_format($item['points']) }} แต้ม</small>
                                    </td>
                                    <td class="text-center">
                                        {{-- ใช้ปุ่ม Submit แยก form ไม่ได้ใน form ใหญ่ จึงต้องใช้ button type submit พร้อม formaction หรือ แยกปุ่มออกมา --}}
                                        {{-- วิธีแก้: ใช้ Link หรือ Button ที่ trigger form delete แยก --}}
                                        <button type="submit" form="delete-form-{{ $index }}" class="btn btn-sm btn-link text-danger p-0 border-0">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Summary Section --}}
                <div class="bg-light p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">คะแนนสะสมที่จะได้รับ</span>
                        <span class="fw-bold text-warning"><i class="bi bi-star-fill me-1"></i>{{ number_format($grandPoints) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top border-white pt-2">
                        <span class="fs-5 fw-bold text-dark">ยอดสุทธิ</span>
                        <span class="fs-3 fw-bold text-success">{{ number_format($grandTotal, 2) }} <span class="fs-6 text-muted">บาท</span></span>
                    </div>
                </div>
            </div>

            {{-- 3. Payment Option (เปลี่ยนจาก Checkbox เป็น Card Selection) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 mx-2">
                <div class="card-body p-0">
                    <label class="d-flex justify-content-between align-items-center p-3 w-100 cursor-pointer" for="cash_back_switch">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                <i class="bi bi-cash-coin fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">รับเงินสดทันที</h6>
                                <small class="text-muted">ติ๊กเพื่อจ่ายเงินสดให้สมาชิก</small>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input fs-4" type="checkbox" name="cash_back" id="cash_back_switch" style="cursor: pointer;">
                        </div>
                    </label>
                </div>
            </div>

            {{-- 4. Action Buttons --}}
            <div class="d-grid gap-2 px-2 pb-4">
                <button type="submit" class="btn btn-success btn-lg rounded-4 shadow py-3">
                    <i class="bi bi-save-fill me-2"></i>ยืนยันบันทึกรายการ
                </button>
                <a href="{{ route('keptkayas.purchase.form', $seller->id) }}" class="btn btn-outline-secondary btn-lg rounded-4 border-0 py-2">
                    <i class="bi bi-arrow-left me-2"></i>กลับไปแก้ไข / เพิ่มรายการ
                </a>
            </div>
        </form>

        {{-- Hidden Forms for Delete Items (เพื่อไม่ให้ตีกับ Form หลัก) --}}
        @foreach ($cart as $index => $item)
            <form id="delete-form-{{ $index }}" action="{{ route('keptkayas.purchase.remove_from_cart', $index) }}" method="POST" class="d-none">
                @csrf @method('DELETE')
            </form>
        @endforeach

    @endif
</div>

<style>
    /* CSS ตกแต่งเพิ่มเติม */
    .cursor-pointer { cursor: pointer; }
    .form-check-input:checked { background-color: #198754; border-color: #198754; }
</style>
@endsection