@extends('layouts.admin1')

@section('nav-payment', 'active')
@section('nav-header', 'จัดการใบแจ้งหนี้')
@section('nav-main')
    <a href="{{ route('invoice.index') }}"> ออกใบแจ้งหนี้</a>
@endsection
@section('nav-current', 'ชำระเงินแล้ว')
@section('nav-topic', 'ชำระเงินแล้ว')

@section('styles')
<style>
    /* ธีม Modern Clean */
    @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap');

    .modern-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        font-family: 'Prompt', sans-serif;
        overflow: hidden;
    }
    .card-header-modern {
        background: #fff;
        padding: 20px 30px;
        border-bottom: 1px solid #f0f0f0;
    }
    .table-modern thead th {
        border-top: none;
        border-bottom: 2px solid #f0f0f0;
        font-size: 0.85rem;
        color: #8898aa;
        text-transform: uppercase;
        font-weight: 600;
        padding: 15px;
        background-color: #fff;
    }
    .table-modern tbody td {
        vertical-align: middle;
        padding: 15px;
        border-bottom: 1px solid #f8f9fa;
        color: #525f7f;
        font-size: 0.95rem;
    }
    .table-modern tbody tr:hover {
        background-color: #fcfcfc;
    }
    
    /* Avatar สำหรับ User */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        margin-right: 12px;
        box-shadow: 0 4px 10px rgba(56, 239, 125, 0.3);
    }

    /* ตัวเลขเงิน */
    .amount-text {
        font-weight: 600;
        color: #2dce89;
    }
    .meter-badge {
        background-color: #f6f9fc;
        color: #8898aa;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-family: monospace;
    }
</style>
@endsection

@section('content')
    <div class="card modern-card table-responsive">
        <div class="card-header-modern">
            <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-history text-success me-2"></i>ประวัติการชำระเงิน</h4>
        </div>
        <div class="card-body p-0">
            <div id="has_invoice">
                <table class="table table-modern mb-0 w-100 dataTable">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">#</th>
                            <th class="text-center" width="10%">เลขบิล</th>
                            <th width="25%">ผู้ใช้น้ำ / รหัสมิเตอร์</th>
                            <th class="text-center" width="15%">ที่อยู่</th>
                            <th class="text-end" width="10%">ก่อนหน้า</th>
                            <th class="text-end" width="10%">ปัจจุบัน</th>
                            <th class="text-end" width="10%">หน่วยใช้</th>
                            <th class="text-end" width="10%">ยอดเงิน</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody id="app">
                        <?php $i = 1; ?>
                        @foreach ($invoices_paid as $invoice)
                            @php
                                // ดึงความสัมพันธ์ (เปลี่ยน usermeterinfos -> tw_meter_infos)
                                $meterInfo = $invoice->tw_meter_infos ?? null;
                                $user = $meterInfo ? $meterInfo->user : null;
                                $meterType = $meterInfo ? $meterInfo->meter_type : null;
                                
                                // ชื่อ user และ Avatar
                                $firstname = $user->firstname ?? '-';
                                $lastname = $user->lastname ?? '';
                                $firstLetter = mb_substr($firstname, 0, 1);

                                // คำนวณ (ใช้ Logic เดิม)
                                $meter_net = $invoice->currentmeter - $invoice->lastmeter;
                                
                                // ป้องกัน Error กรณีไม่มี meter_type
                                $pricePerUnit = $meterType->price_per_unit ?? 0;
                                
                                // หมายเหตุ: ถ้าใน Database มี column 'totalpaid' ควรใช้ $invoice->totalpaid แทนการคูณใหม่
                                $total = $meter_net * $pricePerUnit; 
                            @endphp

                            <tr data-id="{{ $invoice->inv_no ?? '' }}" class="data">
                                <td class="text-center text-muted">{{ $i++ }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $invoice->inv_no ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle">{{ $firstLetter }}</div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $firstname }} {{ $lastname }}</div>
                                            <small class="text-muted">มิเตอร์: {{ $meterInfo->meternumber ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-secondary">
                                    <i class="fas fa-map-marker-alt me-1 text-danger opacity-50"></i>
                                    {{ $meterInfo->meter_address ?? '-' }}
                                </td>
                                <td class="text-end">
                                    <span class="meter-badge">{{ number_format($invoice->lastmeter) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="meter-badge">{{ number_format($invoice->currentmeter) }}</span>
                                </td>
                                <td class="text-end fw-bold text-secondary">
                                    {{ number_format($meter_net) }}
                                </td>
                                <td class="text-end">
                                    <span class="amount-text">{{ number_format($total, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($invoice->comment)
                                        <i class="fas fa-info-circle text-info" data-bs-toggle="tooltip" title="{{ $invoice->comment }}"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="6" class="text-end fw-bold">รวมทั้งหมด:</th>
                            <th class="text-end pr-0 fw-bold"></th> <th class="text-end pr-0 fw-bold text-success"></th> <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // เปิด Tooltip
            $('[data-bs-toggle="tooltip"]').tooltip();

            $('.dataTable').DataTable({
                "pagingType": "full_numbers", // เปลี่ยนปุ่มเปลี่ยนหน้าให้สวยขึ้น
                "lengthMenu": [
                    [10, 25, 50, 150, -1],
                    [10, 25, 50, 150, "ทั้งหมด"]
                ],
                "language": {
                    "search": "ค้นหา:",
                    "lengthMenu": "แสดง _MENU_ รายการ",
                    "info": "หน้า _PAGE_ จาก _PAGES_",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "zeroRecords": "ไม่พบข้อมูลที่ค้นหา",
                    "paginate": {
                        "first": "«",
                        "last": "»",
                        "next": "›",
                        "previous": "‹"
                    }
                },
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api();
 
                    // ฟังก์ชันแปลงค่าเป็นตัวเลข
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };
                    
                    var nf = new Intl.NumberFormat('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    var nf_int = new Intl.NumberFormat('th-TH');

                    // คำนวณ Column ที่ 6 (หน่วยใช้) และ 7 (ยอดเงิน)
                    // *หมายเหตุ: Index เริ่มจาก 0
                    
                    // --- รวมหน่วยน้ำ (Index 6) ---
                    total_unit = api.column(6).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                    pageTotal_unit = api.column(6, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                    $(api.column(6).footer()).html(nf_int.format(pageTotal_unit));

                    // --- รวมยอดเงิน (Index 7) ---
                    total_price = api.column(7).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                    pageTotal_price = api.column(7, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                    
                    $(api.column(7).footer()).html(
                        nf.format(pageTotal_price) + ' <small class="text-muted">(' + nf.format(total_price) + ')</small>'
                    );
                }
            });
        });
    </script>
@endsection