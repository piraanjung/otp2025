@extends('layouts.admin1')

@section('nav-payment', 'active')
@section('nav-header', 'จัดการใบเสร็จรับเงิน')
@section('nav-main')
    <a href="{{ route('invoice.index') }}">รับชำระค่าน้ำประปา</a>
@endsection

@section('nav-topic')
    รายชื่อผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าน้ำประปา
    @foreach ($selected_subzone_name_array as $item)
        <span class="badge badge-info text-white">{{ $item }}</span>
    @endforeach
@endsection

@section('style')
    <style>
        /* General Utils */
        .selected { background-color: #e3f2fd !important; }
        .displayblock { display: block; }
        .displaynone, .hidden { display: none; }
        .cursor-pointer { cursor: pointer; }
        .sup { color: blue; font-size: 0.8em; }
        .fs-7 { font-size: 0.7rem; }
        
        /* Modal & Layout */
        .modal-dialog { max-width: 90rem; margin: 30px auto; }
        @media (min-width: 568px) {
            .modal-dialog { max-width: 90rem; margin-right: auto; margin-left: auto; }
        }
        
        /* Table Styling */
        .table { border-collapse: collapse; width: 100%; }
        .table thead th {
            padding: 0.75rem 0.5rem;
            text-transform: capitalize;
            border-bottom: 2px solid #dee2e6;
            color: #343a40;
            text-align: center;
            vertical-align: middle;
            background-color: #f8f9fa;
            font-weight: 600;
        }
        tfoot th { background: #e9ecef !important; font-weight: bold; }
        
        .input-search-by-title {
            border-radius: 4px;
            height: 2rem;
            border: 1px solid #ced4da;
            width: 100%;
            padding: 0 5px;
        }

        /* Status & Icons */
        .text-cutmeter { background-color: #fbdcf4 !important; color: #b71c1c; }
        .total { color: blue; font-weight: bold; }
        .subtotal { color: black; border-bottom: 1px solid black; }
        
        .icon-shape {
            width: 48px !important; height: 48px !important;
            background-position: 50%; border-radius: .75rem;
            display: flex; align-items: center; justify-content: center;
        }

        /* Preloader */
        .preloader-wrapper {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.8); z-index: 9999;
            display: flex; justify-content: center; align-items: center;
        }

        /* ทำให้เมาส์เป็นรูปมือเมื่อชี้ที่แถวในตาราง ยกเว้นช่องแรก */
#invoiceTable tbody tr td:not(:first-child) {
    cursor: pointer;
}

/* สี Highlight เมื่อคลิกเลือกแถว */
tr.selected td {
    background-color: #e3f2fd !important; /* สีฟ้าอ่อน */
}
    </style>
@endsection

@section('content')
<div class="preloader-wrapper">
    <button class="btn btn-primary btn-sm" type="button" disabled>
        <span class="spinner-border spinner-border-sm" role="status"></span>
        Loading...
    </button>
</div>

<div class="row">
    <div class="col-12 mb-3">
        <form action="{{ route('payment.index') }}" method="get">
            @csrf
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-12 mb-2">
                            <h6><i class="fas fa-search"></i> ค้นหาจากเส้นทางจดมิเตอร์</h6>
                        </div>
                        
                        <div class="col-md-2 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="check-input-select-all"
                                    id="check-input-select-all" {{ $select_all == true ? 'checked' : '' }}>
                                <label class="form-check-label font-weight-bold" for="check-input-select-all">เลือกทั้งหมด</label>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="row">
                                @foreach ($subzones as $key => $subzone)
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input subzone_checkbox" type="checkbox"
                                                name="subzone_id_lists[]" value="{{ $subzone['id'] }}"
                                                {{ isset($subzone_selected) && in_array($subzone['id'], is_array($subzone_selected) ? $subzone_selected : json_decode($subzone_selected)) ? 'checked' : '' }}>
                                            <label class="form-check-label text-primary" style="font-size: 0.9rem;">
                                                {{ $subzone->zone->zone_name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-2 text-center">
                            <button type="submit" class="btn btn-primary btn-block w-100">
                                <i class="fas fa-search"></i> ค้นหา
                            </button>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <label>รอบบิล ปีงบประมาณ {{ $current_budgetyear[0]->budgetyear_name ?? '' }}</label>
                            <select name="inv_period_id" class="form-control">
                                <option value="0">ทั้งหมด</option>
                                @foreach ($current_budgetyear[0]->invoice_period ?? [] as $inv_period)
                                    <option value="{{ $inv_period->id }}" {{ request('inv_period_id') == $inv_period->id ? 'selected' : '' }}>
                                        {{ $inv_period->inv_p_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-12 mb-3">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">ยอดทั้งหมดในหน้านี้</p>
                                <h5 class="font-weight-bolder mb-0 mt-2">
                                    <span id="main_total_water_used">0.00</span> <small class="text-success text-sm">หน่วย</small>
                                </h5>
                                <h5 class="font-weight-bolder mb-0">
                                    <span id="main_total_paid">0.00</span> <small class="text-success text-sm">บาท</small>
                                </h5>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-chart-bar-32 text-white text-lg" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-start border-primary border-3">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-primary">ยอดที่เลือก (เตรียมชำระ)</p>
                                <h5 class="font-weight-bolder mb-0 mt-2">
                                    <span id="main_total_water_used_selected">0.00</span> <small class="text-success text-sm">หน่วย</small>
                                </h5>
                                <h5 class="font-weight-bolder mb-0 text-primary" style="font-size: 1.5rem;">
                                    <span id="main_total_paid_selected">0.00</span> <small>บาท</small>
                                </h5>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="ni ni-check-bold text-white text-lg" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('payment.store_by_inv_no') }}" method="POST" id="bulkPaymentForm">
                    @csrf
                    
                    <button type="submit" class="btn btn-info mb-3 hidden" id="submitbtn" onclick="return confirm('คุณต้องการบันทึกการรับชำระเงินหลายรายการใช่หรือไม่?')">
                        <i class="fas fa-save"></i> บันทึกการชำระเงินหลายรายการ
                    </button>

                    <div class="table-responsive">
                        <table class="table table-hover align-items-center mb-0" id="invoiceTable">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%"><input type="checkbox" class="form-check-input" id="check_all"></th>
                                    <th>เลขผู้ใช้น้ำ</th>
                                    <th>เลขใบแจ้งหนี้</th>
                                    <th>ชื่อ-สกุล</th>
                                    <th>เลขมิเตอร์</th>
                                    <th>ที่อยู่</th>
                                    <th>เส้นทาง</th>
                                    <th>ใช้น้ำ (หน่วย)</th>
                                    <th>ต้องชำระ (บาท)</th>
                                    <th>ค้าง (รอบบิล)</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $meter)
                                    {{-- ตรวจสอบว่ามีหนี้ในตาราง tw_invoices หรือไม่ --}}
                                    @if($meter->tw_invoices->isEmpty())
                                        @continue
                                    @endif

                                    @php
                                        // คำนวณค่าต่างๆ
                                        $sumTotalPaid = $meter->tw_invoices->sum('totalpaid');
                                        $sumWaterUsed = $meter->tw_invoices->sum('water_used');
                                        // เก็บ ID ของ Invoice ทั้งหมดไว้ส่งไปตัดยอด
                                        $invIds = $meter->tw_invoices->pluck('id')->implode('|');
                                    @endphp

                                    <tr class="{{ $meter->cutmeter == 1 ? 'text-cutmeter' : '' }}">
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input main_checkbox" name="datas[]"
                                                value="{{ $meter->meter_id . '|' . $invIds }}"
                                                data-main_checkbox_totalpaid="{{ $sumTotalPaid }}"  
                                                data-main_total_water_used_selected="{{ $sumWaterUsed }}">
                                        </td>
                                        <td class="text-center">{{ str_pad($meter->meter_id, 5, '0', STR_PAD_LEFT) }}</td>
                                        
                                        <td class="popup text-center cursor-pointer">
                                            @foreach ($meter->tw_invoices as $inv)
                                                <div class="badge bg-light text-dark border mb-1" style="font-weight:normal;">
                                                    {{ $inv->inv_no ?? 'IV'.str_pad($inv->id, 6, '0', STR_PAD_LEFT) }}
                                                </div><br>
                                            @endforeach
                                        </td>
                                        
                                        <td class="popup cursor-pointer">
                                            {{ optional($meter->user)->prefix . optional($meter->user)->firstname . ' ' . optional($meter->user)->lastname }}
                                        </td>
                                        
                                        <td class="popup meternumber text-center cursor-pointer font-weight-bold"
                                            data-meter_id="{{ $meter->meter_id }}">
                                            {{ $meter->meternumber }}
                                        </td>
                                        
                                        <td class="popup cursor-pointer text-sm">
                                            {{ $meter->meter_address }} {{ optional($meter->undertake_zone)->zone_name }}
                                        </td>
                                        
                                        <td class="popup cursor-pointer text-center">
                                            {{ optional($meter->undertake_subzone)->subzone_name }}
                                        </td>

                                        <td class="popup cursor-pointer text-end">
                                            {{ number_format($sumWaterUsed, 2) }}
                                        </td>
                                       
                                        <td class="popup cursor-pointer text-end font-weight-bold text-primary">
                                            {{ number_format($sumTotalPaid, 2) }}
                                        </td>
                                        
                                        <td class="popup cursor-pointer text-center">
                                            <span class="badge badge-secondary">{{ $meter->tw_invoices->count() }}</span>
                                        </td>
                                        
                                        <td class="text-center">
                                            @if ($meter->cutmeter == 1)
                                                <span class="badge bg-gradient-danger">ตัดมิเตอร์</span>
                                            @else
                                                <span class="badge bg-gradient-warning text-dark">ค้างชำระ</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-end">รวมทั้งหน้า:</th>
                                    <th class="text-end" id="footer_water_used">0.00</th>
                                    <th class="text-end" id="footer_total_paid">0.00</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('payment.store') }}" method="post" onsubmit="return check()">
    @csrf
    <div class="modal fade" id="modal-success">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title font-weight-bolder" id="feFirstName"></h5>
                        <span class="text-sm text-muted" id="feInputAddress"></span>
                    </div>
                    <button type="button" class="btn-close text-dark close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="activity">
                        <div class="row">
                            <div class="col-12">
                                {{-- Hidden Fields --}}
                                <input type="hidden" name="mode" id="mode" value="payment">
                                <input type="hidden" name="inv_no" id="inv_no">
                                <input type="hidden" name="user_id" id="user_id">
                                <input type="hidden" name="meter_id" id="meter_id">
                                <input type="hidden" name="reserve_meter_sum" id="reserve_meter_sum">
                                <input type="hidden" name="paidsum" id="paidsum"> <input type="hidden" name="vat7" id="vat7">
                                <input type="hidden" name="mustpaid" id="mustpaid"> <div id="payment_res">
                                    </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 col-md-3 text-center border-end">
                                <div class="card shadow-none">
                                    <div class="card-body">
                                        <h6>สแกน QR CODE เพื่อชำระเงิน</h6>
                                        <div id="qrcode" class="my-3"></div>
                                        <textarea id="qrcode_text" style="opacity: 0; width:1px; height:1px;"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="mb-3 border-bottom pb-2">สรุปยอดที่ต้องชำระ</h6>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-sm">รวมค่าใช้น้ำ:</span>
                                                    <span class="font-weight-bold"><span class="paidsum">0.00</span> <sup class="sup">บาท</sup></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-sm">Vat 7%:</span>
                                                    <span class="font-weight-bold"><span class="vat7">0.00</span> <sup class="sup">บาท</sup></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-sm">ค่ารักษามิเตอร์:</span>
                                                    <span class="font-weight-bold"><span class="reserve_meter">0.00</span> <sup class="sup">บาท</sup></span>
                                                </div>
                                                <hr class="my-2">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-lg font-weight-bold">ยอดสุทธิ:</span>
                                                    <span class="text-lg font-weight-bold text-primary">
                                                        <span class="mustpaid">0.00</span> <sup class="sup">บาท</sup>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon icon-shape bg-gradient-dark text-center border-radius-md text-white">
                                                        <i class="fas fa-coins text-lg"></i>
                                                    </div>
                                                    <div class="ms-3 w-100">
                                                        <p class="text-sm mb-0 font-weight-bold">รับเงินมา</p>
                                                        <input type="number" class="form-control text-center font-weight-bold fs-5 cash_from_user" 
                                                               name="cash_from_user" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card mb-3">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon icon-shape bg-gradient-success text-center border-radius-md text-white">
                                                        <i class="fas fa-hand-holding-usd text-lg"></i>
                                                    </div>
                                                    <div class="ms-3 w-100">
                                                        <p class="text-sm mb-0 font-weight-bold">เงินทอน</p>
                                                        <input type="text" class="form-control text-center font-weight-bold fs-5 border-success cashback" 
                                                               disabled value="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-success btn-lg w-100 submitbtn reciept_money_btn hidden">
                                            <i class="fas fa-check-circle"></i> ยืนยันการชำระเงิน
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/jquery.qrcode@1.0.3/jquery.qrcode.min.js"></script>
    <script>
        let table;
        // ประกาศตัวแปร Global เพื่อเก็บข้อมูล Invoice ที่โหลดมา
        let invoice_local = []; 

        $(document).ready(function() {
            // ซ่อน Preloader
            $('.preloader-wrapper').fadeOut();

            // 1. Initialize DataTable
            table = $('#invoiceTable').DataTable({
                responsive: true,
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, 150, -1], [10, 25, 50, 150, "ทั้งหมด"]],
                "language": {
                    "search": "ค้นหา:",
                    "lengthMenu": "แสดง _MENU_ แถว",
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
                    "paginate": { "next": ">", "previous": "<" }
                },
                "order": [], // ไม่ให้เรียง Auto
                
                // คำนวณยอดรวมที่ Footer ของตารางหลัก
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();
                    let intVal = function(i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };

                    // คำนวณ column ที่ 7 (ใช้น้ำ) และ 8 (ยอดเงิน)
                    let totalWater = api.column(7, {page:'current'}).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                    let totalMoney = api.column(8, {page:'current'}).data().reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Update UI
                    $('#footer_water_used').html(formatNumber(totalWater));
                    $('#footer_total_paid').html(formatNumber(totalMoney));
                    
                    $('#main_total_water_used').html(formatNumber(totalWater));
                    $('#main_total_paid').html(formatNumber(totalMoney));
                }
            });

            // 2. Custom Search Headers
            $('#invoiceTable thead tr').clone(true).appendTo('#invoiceTable thead');
            $('#invoiceTable thead tr:eq(1) th').each(function(i) {
                var title = $(this).text();
                $(this).removeClass('sorting sorting_asc');
                if (i > 0 && i < 7) { // ใส่ช่องค้นหาเฉพาะ Column 1-6
                    $(this).html('<input type="text" class="input-search-by-title" placeholder="ค้นหา" />');
                    $('input', this).on('keyup change', function() {
                        if (table.column(i).search() !== this.value) {
                            table.column(i).search(this.value).draw();
                        }
                    });
                } else {
                    $(this).html('');
                }
            });
        });

        // --------------------------------------------------------
        //  Event Handlers
        // --------------------------------------------------------

        // คลิกแถวเพื่อเปิด Modal ชำระเงิน
        $('body').on('click', '#invoiceTable tbody tr', function(e) {
    
    // 1. ป้องกันไม่ให้ Modal เด้ง ถ้าคลิกที่ Checkbox หรือ ช่องแรกสุด (Column 0)
    // เพราะช่องแรกเอาไว้ติ๊กเลือกหลายรายการ
    if ($(e.target).is('input[type="checkbox"]') || $(e.target).closest('td').index() === 0) {
        return; 
    }

    let tr = $(this); // แถวที่ถูกคลิก

    // 2. ดึง meter_id จาก class 'meternumber' ที่อยู่ในแถวนั้น
    // สังเกต: เราต้องหา td ที่มี class meternumber ในแถวที่เราคลิก
    let meternumber = tr.find('td.meternumber').data('meter_id');

    if (typeof meternumber === 'undefined') {
        return; // ถ้าหาไม่เจอ หรือเป็นแถวที่ไม่มีข้อมูล ให้จบการทำงาน
    }

    // 3. Highlight แถวที่เลือก
    $('#invoiceTable tbody tr').removeClass('selected');
    tr.addClass('selected');

    // 4. เรียกฟังก์ชันเปิด Modal
    findReciept(meternumber);
});

        // คลิก Checkbox Select All ใน Modal
        $(document).on('click', '#checkAll', function() {
            let state = $(this).prop('checked');
            $('.modalcheckbox').prop('checked', state);
            checkboxclicked(); // สำคัญ: ต้องเรียกคำนวณใหม่ทันที
        });

        // คลิก Checkbox รายการย่อยใน Modal
        $(document).on('change', '.modalcheckbox', function() {
            checkboxclicked();
        });

        // คำนวณเงินทอน
        $('.cash_from_user').on('keyup change', function() {
            let mustpaid = parseFloat($('#mustpaid').val()) || 0;
            let cash = parseFloat($(this).val()) || 0;
            let cashback = cash - mustpaid;

            if(cash >= 0) $('.cashback').val(formatNumber(cashback));
            else $('.cashback').val('');

            // แสดง/ซ่อนปุ่มบันทึก
            if (cash >= mustpaid && mustpaid > 0) {
                $('.submitbtn').removeClass('hidden').show();
            } else {
                $('.submitbtn').addClass('hidden').hide();
            }
        });

        // Checkbox หน้าหลัก (Main Table)
        $('#check_all').on('click', function() {
            let isChecked = $(this).is(':checked');
            $('.main_checkbox').prop('checked', isChecked);
            sum_payment_selected_main_checkbox();
        });

        $('.main_checkbox').on('change', function() {
            sum_payment_selected_main_checkbox();
        });

        // --------------------------------------------------------
        //  Functions
        // --------------------------------------------------------

        function findReciept(meter_id) {
            // Reset ค่า
            $('#payment_res').html('<div class="text-center p-4"><div class="spinner-border text-primary"></div><br>กำลังโหลดข้อมูล...</div>');
            $('.cash_from_user').val('');
            $('.cashback').val('');
            $('.submitbtn').addClass('hidden');
            $('#qrcode').empty();
            
            // เรียกข้อมูลจาก Server
            $.get(`/api/invoice/get_user_invoice/${meter_id}/inv_and_owe`).done(function(invoices) {
                // *** สำคัญ: Assign ค่าลง Global variable ***
                invoice_local = invoices; 

                if (invoices.length > 0) {
                    renderModalTable(invoices); // สร้าง HTML ตาราง
                    checkboxclicked(); // คำนวณยอดเริ่มต้น
                    $('#modal-success').modal('show');
                } else {
                    alert('ไม่พบข้อมูลหนี้ค้างชำระ');
                    $('#payment_res').html('');
                }
            }).fail(function() {
                $('#payment_res').html('<div class="alert alert-danger">เกิดข้อผิดพลาดในการดึงข้อมูล</div>');
            });
        }

        function renderModalTable(invoices) {
            let rows = '';
            
            invoices.forEach((el, index) => {
                let status = el.status == 'owe' ? '<span class="text-danger font-weight-bold">ค้างชำระ</span>' : 'ออกใบแจ้งหนี้';
                let invNo = el.inv_no ? el.inv_no : 'IV' + String(el.id).padStart(6, '0');
                
                rows += `
                    <tr>
                        <td class="text-center">
                            <div class="form-check justify-content-center">
                                <input type="checkbox" checked class="form-check-input invoice_id modalcheckbox"
                                    data-inv_id="${el.id}" name="payments[${index}][on]">
                            </div>
                        </td>
                        <td class="text-center">${invNo}</td>
                        <td class="text-center">${el.tw_meter_infos.meternumber}</td>
                        <td class="text-center">${el.invoice_period ? el.invoice_period.inv_p_name : '-'}</td>
                        <td class="text-end">${el.lastmeter}</td>
                        <td class="text-end">${el.currentmeter}</td>
                        <td class="text-end">${el.water_used}</td>
                        <td class="text-end">
                            <span id="paid_text${el.id}">${parseFloat(el.paid).toFixed(2)}</span>
                            <input type="hidden" id="paid${el.id}" value="${el.paid}">
                            <input type="hidden" name="payments[${index}][total]" value="${el.paid}">
                            <input type="hidden" name="payments[${index}][iv_id]" value="${el.id}">
                        </td>
                        <td class="text-end"><span id="reserve_text${el.id}">${parseFloat(el.reserve_meter).toFixed(2)}</span><input type="hidden" id="reserve_meter${el.id}" value="${el.reserve_meter}"></td>
                        <td class="text-end"><span id="vat_text${el.id}">${parseFloat(el.vat).toFixed(2)}</span><input type="hidden" id="vat${el.id}" value="${el.vat}"></td>
                        <td class="text-end font-weight-bold"><span id="total${el.id}">${parseFloat(el.totalpaid).toFixed(2)}</span></td>
                        <td class="text-center">${status}</td>
                    </tr>
                `;
            });

            let html = `
                <div class="card border">
                    <div class="card-header bg-secondary text-white py-2">
                         <h6 class="mb-0 text-white">รายการค้างชำระ (${invoices.length} รายการ)</h6>
                    </div>
                    <div class="table-responsive" style="max-height: 300px;">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center"><input type="checkbox" id="checkAll" checked></th>
                                    <th>เลขใบแจ้งหนี้</th>
                                    <th>เลขมิเตอร์</th>
                                    <th>รอบบิล</th>
                                    <th class="text-end">ครั้งก่อน</th>
                                    <th class="text-end">ปัจจุบัน</th>
                                    <th class="text-end">หน่วยใช้</th>
                                    <th class="text-end">ค่าน้ำ</th>
                                    <th class="text-end">บำรุง</th>
                                    <th class="text-end">Vat</th>
                                    <th class="text-end">รวมเป็นเงิน</th>
                                    <th class="text-center">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                </div>
            `;
            
            $('#payment_res').html(html);

            // Set Header Info
            let user = invoices[0].tw_meter_infos.user;
            let address = `${user.address || ''} ${user.user_zone?.user_zone_name || ''} ${user.user_tambon?.tambon_name || ''}`;
            
            $('#feFirstName').text(`${user.prefix}${user.firstname} ${user.lastname}`);
            $('#feInputAddress').text(address);
            $('#user_id').val(user.id); // หรือ meter_id แล้วแต่ logic
            $('#meter_id').val(invoices[0].tw_meter_infos.meter_id);
        }

        // ฟังก์ชันคำนวณเงิน (Core Logic)
        function checkboxclicked() {
            let totalSum = 0;
            let vatSum = 0;
            let paidSum = 0;
            let reserveSum = 0;
            let count = 0;

            $('.modalcheckbox:checked').each(function() {
                let id = $(this).data('inv_id');
                // ดึงค่า
                totalSum += parseFloat($(`#total${id}`).text()) || 0;
                paidSum += parseFloat($(`#paid${id}`).val()) || 0;
                vatSum += parseFloat($(`#vat${id}`).val()) || 0;
                reserveSum += parseFloat($(`#reserve_meter${id}`).val()) || 0;
                count++;
            });

            // Update UI & Hidden Inputs
            $('.paidsum').text(formatNumber(paidSum));
            $('#paidsum').val(paidSum.toFixed(2));
            
            $('.vat7').text(formatNumber(vatSum));
            $('#vat7').val(vatSum.toFixed(2));
            
            $('.reserve_meter').text(formatNumber(reserveSum));
            $('#reserve_meter_sum').val(reserveSum.toFixed(2));

            $('.mustpaid').text(formatNumber(totalSum));
            $('#mustpaid').val(totalSum.toFixed(2));

            // Generate QR Code
            if (count > 0 && invoice_local.length > 0) {
                createQrCode({
                    meter_id: invoice_local[0].tw_meter_infos.meter_id,
                    totalpaidsum: totalSum
                });
                $('.cash_from_user').prop('readonly', false);
            } else {
                $('#qrcode').html('<div class="text-muted mt-4">กรุณาเลือกรายการ</div>');
                $('.cash_from_user').prop('readonly', true);
            }
            
            // Trigger คำนวณเงินทอนใหม่
            $('.cash_from_user').trigger('keyup');
        }

        function createQrCode(data) {
            $('#qrcode').empty();
            let amount = data.totalpaidsum.toFixed(2);
            
            // สร้าง QR Code (Text)
            let qrcode_text = `|099400035262000\n${data.meter_id}\n\n${(amount * 100).toFixed(0)}`;
            $('#qrcode_text').val(qrcode_text);
            
            // Render QR Code Image
            $('#qrcode').qrcode({
                text: qrcode_text,
                width: 150,
                height: 150
            });
            $('#qrcode').append(`<div class="mt-2 font-weight-bold">ยอด: ${formatNumber(data.totalpaidsum)} บาท</div>`);
        }

        function sum_payment_selected_main_checkbox(){
            let totalMoney = 0;
            let totalWater = 0;
            let count = 0;
            $('.main_checkbox:checked').each(function() {
                totalMoney += parseFloat($(this).data('main_checkbox_totalpaid')) || 0;
                totalWater += parseFloat($(this).data('main_total_water_used_selected')) || 0;
                count++;
            });
            $('#main_total_paid_selected').html(formatNumber(totalMoney));
            $('#main_total_water_used_selected').html(formatNumber(totalWater));

            if(count > 0) $('#submitbtn').removeClass('hidden').show();
            else $('#submitbtn').addClass('hidden').hide();
        }

        function check() {
            let mustpaid = parseFloat($('#mustpaid').val());
            let cash = parseFloat($('.cash_from_user').val());
            
            if ($('.modalcheckbox:checked').length === 0) {
                alert('กรุณาเลือกรายการชำระเงิน');
                return false;
            }
            if (isNaN(cash) || cash < mustpaid) {
                alert('จำนวนเงินที่รับมาไม่ถูกต้อง');
                return false;
            }
            return confirm('ยืนยันการบันทึกข้อมูล?');
        }

        function formatNumber(num) {
            return num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
    </script>
@endsection