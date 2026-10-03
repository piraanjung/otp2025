@extends('inventory.inv_master')
@section('title', 'บันทึกจ่ายพัสดุ')
@section('header_title', 'บันทึกจ่ายพัสดุและตัดสต็อก (Ref: ' . $refNo . ')')

@section('content')
<div class="container-fluid">
    <form action="{{ route('inventory.withdraw.dispense_process', $refNo) }}" method="POST">
        @csrf
        <div class="row">

            <!-- 📌 คอลัมน์ซ้าย: แผงควบคุมและปุ่มกดต่างๆ (ซ่อนเวลาสั่งปริ้นท์) -->
            <div class="col-md-3 d-print-none mb-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0"><i class="material-icons-round align-middle fs-6 me-1">settings</i>
                            จัดการใบเบิก</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">เลขที่ใบเบิก: <strong class="text-dark">{{ $refNo }}</strong>
                        </p>
                        <hr>

                        <!-- ปุ่มพิมพ์เอกสาร -->
                        <div class="d-grid gap-2 mb-3">
                            <button type="button" class="btn btn-outline-dark shadow-sm text-start"
                                onclick="window.print();">
                                <i class="material-icons-round align-middle me-2">print</i> พิมพ์ใบหยิบ/เซ็นรับ (A4)
                            </button>
                        </div>

                        <!-- ปุ่มบันทึกตัดสต็อก -->
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-success shadow-sm text-start"
                                onclick="return confirm('ยืนยันการจ่ายพัสดุและตัดสต็อกตามจำนวนนี้?');">
                                <i class="material-icons-round align-middle me-2">check_circle</i> ยืนยันการจ่ายพัสดุ
                            </button>
                        </div>

                        <!-- ปุ่มกลับ -->
                        <div class="d-grid gap-2">
                            <a href="{{ route('inventory.history') }}" class="btn btn-secondary shadow-sm text-start">
                                <i class="material-icons-round align-middle me-2">arrow_back</i> กลับหน้าประวัติ
                            </a>
                        </div>

                        <hr class="my-4">
                        <div class="alert alert-warning small mb-0">
                            <i class="material-icons-round align-middle fs-6">info</i>
                            เจ้าหน้าที่สามารถปรับลดจำนวนจ่ายจริง หรือกดยกเลิกรายการได้ที่ตารางด้านขวาก่อนกดยืนยัน
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📄 คอลัมน์ขวา: หน้ากระดาษจำลองขนาด A4 (วนลูปแยกตาม Workflow) -->
            <div class="col-md-9">

                @foreach($workflowGroups as $group)
                <div id="print-printable-area"
                    class="a4-document card shadow-sm p-4 bg-white mx-auto @if(!$loop->last) mb-5 page-break @endif"
                    style="max-width: 210mm; min-height: 297mm;">

                    <!-- หัวกระดาษ A4 -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-1">ใบหยิบพัสดุและบันทึกจ่ายพัสดุ (FIFO)</h4>
                        <p class="text-primary fw-bold mb-1">สายการอนุมัติ: {{ $group['workflow_name'] }}</p>
                        <p class="text-muted small mb-0">เลขที่อ้างอิง: <strong>{{ $refNo }}</strong> | วันที่พิมพ์:
                            {{ date('d/m/Y H:i') }}</p>
                    </div>

                    <!-- 💡 ส่วนแสดงคำแนะนำการหยิบพัสดุ FIFO (เฉพาะรายการใน Workflow นี้) -->
                    <div class="card border-info mb-4 shadow-sm">
                        <div class="card-header bg-info text-white d-flex align-items-center py-2">
                            <i class="material-icons-round me-2 fs-5">lightbulb</i>
                            <h6 class="mb-0 fw-bold">แนะนำแนวทางการหยิบพัสดุ (ระบบ FIFO)</h6>
                        </div>
                        <div class="card-body bg-light p-3">
                            @foreach($group['transactions'] as $tx)
                                @if(isset($fifoPlans[$tx->id]))
                                    @php $plan = $fifoPlans[$tx->id]; @endphp
                                    <div class="card mb-2 border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="fw-bold text-primary mb-2">
                                                <i class="material-icons-round align-middle fs-6">inventory_2</i>
                                                รายการ: {{ $plan['item_name'] }}
                                            </div>

                                            @if(!$plan['is_stock_enough'])
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="material-icons-round align-middle fs-6">warning</i> คำเตือน:
                                                    สต็อกรวมในคลังไม่เพียงพอต่อจำนวนที่ขอเบิก!
                                                </div>
                                            @endif

                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0 bg-white small">
                                                    <thead class="table-secondary text-center">
                                                        <tr>
                                                            <th width="10%">ลำดับ</th>
                                                            <th width="25%">เลขที่ล็อต (Lot No.)</th>
                                                            <th width="30%">พื้นที่จัดเก็บ (Location)</th>
                                                            <th width="15%">วันหมดอายุ</th>
                                                            <th width="10%">หยิบ</th>
                                                            <th width="10%">คงเหลือ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($plan['plans'] as $index => $p)
                                                            <tr>
                                                                <td class="text-center fw-bold text-info">#{{ $index + 1 }}</td>
                                                                <td class="text-center fw-bold">{{ $p['lot_number'] }}</td>
                                                                <td><span
                                                                        class="badge bg-warning text-dark">{{ $p['location_name'] }}</span>
                                                                </td>
                                                                <td class="text-center">{{ $p['expire_date'] ?? '-' }}</td>
                                                                <td class="text-center fw-bold text-success">{{ $p['take_qty'] }}
                                                                </td>
                                                                <td class="text-center text-muted">{{ $p['remaining_after'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- ตารางรายการเบิกและตัดสต็อกจริง (เฉพาะกลุ่ม Workflow นี้) -->
                    <div class="table-responsive mb-4">
                        <!-- ตารางรายการเบิกและตัดสต็อกจริง (ระบุล็อตหน้างาน) -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle small">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="40%">รายการพัสดุ </th>
                                        <th width="10%">ขอเบิก</th>
                                        <th width="12%">จ่ายจริงรวม</th>
                                        <th width="10%">หน่วย</th>
                                        <th width="10%" class="d-print-none">ยกเลิก</th>
                                        {{-- <th width="28%">หมายเหตุ</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group['transactions'] as $txIndex => $tx)
                                    <tr>
                                        <td class="text-center">{{ $txIndex + 1 }}</td>
                                        <td>
                                            <span class="fw-bold text-primary">{{ $tx->item->name ?? '-' }}</span> <br>
                                            <small class="text-muted">รหัส: {{ $tx->item->code ?? '-' }}</small>
                                            <input type="hidden"
                                                name="items[{{ $loop->parent->index }}_{{ $txIndex }}][transaction_id]"
                                                value="{{ $tx->id }}">
                                        </td>
                                        <td class="text-center fw-bold">
                                            {{ $tx->quantity }}
                                        </td>
                                        <td>
                                            <!-- ช่องกรอกจำนวนรวม หรือจะให้ระบบคำนวณจากล็อตด้านล่าง -->
                                            <span
                                                class="text-success fw-bold text-center d-block">{{ $tx->quantity }}</span>
                                        </td>
                                        <td class="text-center">{{ $tx->item->unit ?? 'หน่วย' }}</td>
                                        <td class="text-center d-print-none">
                                            <div class="form-check form-switch d-flex justify-content-center"
                                                title="ติ๊กเพื่อยกเลิกรายการนี้">
                                                <input class="form-check-input" type="checkbox"
                                                    name="items[{{ $loop->parent->index }}_{{ $txIndex }}[cancel]"
                                                    value="1" style="cursor: pointer;">
                                            </div>
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <!-- 💡 ตารางย่อยให้เจ้าหน้าที่คลังคีย์จำนวนที่หยิบแยกตามล็อตจริงหน้างาน -->
                                            <div class="p-2 bg-light border rounded">
                                                <small
                                                    class="fw-bold text-dark d-none d-print-block">ระบุล็อตที่หยิบจริง:</small>
                                                @php
                                                    // ดึงล็อตทั้งหมดของสินค้านี้ที่มีในสต็อก
                                                    $allLots = \App\Models\InvItemDetail::where('inv_item_id_fk', $tx->inv_item_id_fk)
                                                        ->where('current_qty', '>', 0)
                                                        ->with('location')
                                                        ->get();
                                                @endphp

                                                @if($allLots->isEmpty())
                                                    <span class="text-danger small">⚠️ ไม่มีสต็อกในระบบ</span>
                                                @else
                                                <table class="table table-sm table-borderless mb-0">
                                                    <thead class="text-muted" style="font-size: 11px;">
                                                        <tr>
                                                            <th>Lot No. (Location)</th>
                                                            <th width="35%">หยิบจริง</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($allLots as $lot)
                                                            <tr>
                                                                <td>
                                                                    <small class="fw-bold">Lot:
                                                                        {{ $lot->lot_number }}</small>
                                                                    <br><small
                                                                        class="text-muted">({{ $lot->location->name ?? 'ไม่ระบุโซน' }}
                                                                        | คงเหลือ: {{ $lot->current_qty }})</small>
                                                                    <!-- ส่ง ID ของ Lot ไปด้วย -->
                                                                    <input type="hidden"
                                                                        name="items[{{ $loop->parent->parent->index }}_{{ $txIndex }}[lots][{{ $lot->id }}][lot_id]"
                                                                        value="{{ $lot->id }}">
                                                                </td>
                                                                <td>
                                                                    <!-- ช่องกรอกจำนวนที่หยิบจากล็อตนี้จริงๆ หน้างาน -->
                                                                    <input type="number"
                                                                        name="items[{{ $loop->parent->parent->index }}_{{ $txIndex }}[lots][{{ $lot->id }}][qty]"
                                                                        class="form-control form-control-sm text-center picked"
                                                                        value="{{ isset($fifoPlans[$tx->id]) ? collect($fifoPlans[$tx->id]['plans'])->where('lot_number', $lot->lot_number)->first()['take_qty'] ?? 0 : 0 }}"
                                                                        min="0" max="{{ $lot->current_qty }}" step="1">sss
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @endIF
                                            </div>

                                            <div class="mt-2">
                                                <textarea
                                                    name="items[{{ $loop->parent->index }}_{{ $txIndex }}][comment]"
                                                    class="form-control form-control-sm" rows="1"
                                                    placeholder="หมายเหตุเพิ่มเติม..."></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ✍️ ส่วนสำหรับเซ็นชื่อรับพัสดุ -->
                    <div class="row mt-5 pt-4">
                        <div class="col-6 text-center">
                            <p class="small mb-5">
                                ลงชื่อ......................................................ผู้จ่ายพัสดุ<br>
                                (<span style="display:inline-block; width:120px;"></span>)<br>
                                วันที่ _____ / _________ / _________</p>
                        </div>
                        <div class="col-6 text-center">
                            <p class="small mb-5">
                                ลงชื่อ......................................................ผู้รับพัสดุ / ผู้เบิก<br>
                                (<span
                                    style="display:inline-block; width:120px; text-align:center;">{{ optional($transaction->requester)->prefix . '' . optional($transaction->requester)->firstname . ' ' . optional($transaction->requester)->lastname ?? '' }}</span>)<br>
                                วันที่ _____ / _________ / _________</p>
                        </div>
                    </div>

                </div>
                @endforeach

            </div>

        </div>
    </form>
</div>

<!-- 🖨️ CSS ควบคุมการพิมพ์ -->
<style>
    @media print {
        body * {
            visibility: hidden !important;
        }

        #print-printable-area,
        #print-printable-area * {
            visibility: visible !important;
        }

        .a4-document {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            max-width: none !important;
            box-shadow: none !important;
            border: none !important;
            padding: 10mm !important;
            margin: 0 !important;
            min-height: auto !important;
        }

        .page-break {
            page-break-after: always !important;
            break-after: page !important;
        }

        .a4-document:last-child {
            page-break-after: avoid !important;
            break-after: avoid !important;
        }

        body {
            background-color: #fff !important;
            color: #000 !important;
            font-size: 13px !important;
        }

        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }

        .card-header {
            background-color: #f1f3f5 !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
        }

        .badge {
            border: 1px solid #000;
            color: #000 !important;
            background: transparent !important;
        }

        input.form-control,
        textarea.form-control {
            border: none !important;
            border-bottom: 1px solid #000 !important;
            border-radius: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            text-align: center;
        }
    }
</style>

@endsection

@section('scripts')
    <script>
    // ใช้ event delegate หรือผูกกับ input ที่มีคลาส .picked
    $(document).on('input', '.picked', function() {
    // จำกัดขอบเขตให้คำนวณเฉพาะภายในตาราง (table) หรือฟอร์มย่อยของรายการนั้น
    let totalVal = 0;
    
    // หาตารางที่ input ตัวนี้อยู่ แล้ววนลูปหาเฉพาะช่อง .picked ในตารางนั้น
    $(this).closest('table').find('.picked').each(function() {
        let val = parseFloat($(this).val()) || 0;
        totalVal += val;
    });

    console.log("ยอดรวมเฉพาะรายการนี้: ", totalVal);
});
</script>
@endsection