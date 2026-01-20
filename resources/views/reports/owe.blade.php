@extends('layouts.admin1')

@section('nav-reports-owe', 'active')
@section('nav-header', 'รายงาน')
@section('nav-main')
    <a href="{{ route('reports.owe') }}"> ผู้ค้างชำระค่าน้ำประปา</a>
@endsection
@section('nav-topic', 'ตารางผู้ค้างชำระค่าน้ำประปา')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .table { border-collapse: collapse; }
        th, th sup { text-align: center; padding: 3px !important; }
        
        /* ปรับสี Border ให้ดูนุ่มนวลขึ้น */
        tbody tr.group { border-left: 3px solid #17c1e8; border-right: 3px solid #17c1e8; }
        tbody tr.tr_even { border-left: 3px solid #ea0606; border-right: 3px solid #ea0606; }

        .dataTables_length, .dt-buttons, .dataTables_filter, .select_row_all, .deselect_row_all, .create_user {
            display: inline-flex; margin-right: 5px; margin-bottom: 10px;
        }
        .hidden { display: none; }
        
        /* แก้ปัญหา Select2 หด */
        .select2-container { width: 100% !important; }
        .select2-selection__rendered { line-height: 31px !important; }
        .select2-container .select2-selection--single { height: 35px !important; }
        .select2-selection__arrow { height: 34px !important; }
    </style>
@endsection

@section('content')
    <div class="preloader-wrapper">
        <button class="btn btn-primary btn-sm mb-2" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status"></span>
            กำลังโหลดข้อมูล...
        </button>
    </div>

    @if (collect($owes)->isNotEmpty())
    <div class="row mb-4">
        {{-- ส่วนค้นหา --}}
        <div class="col-lg-8 col-md-12">
            <div class="card h-100">
                <form action="{{ route('reports.owe_search') }}" method="post" onsubmit="return validateAndSubmit()">
                    @csrf
                    <div class="card-body row">
                        <div class="col-md-3 col-6 mb-2">
                            <label>ปีงบประมาณ</label>
                            <select class="form-control js-example-tokenizer" name="budgetyear[]" id="budgetyear" multiple>
                                @foreach ($budgetyears as $budgetyear)
                                    <option value="{{ $budgetyear->id }}" {{ in_array($budgetyear->id, $budgetyears_selected) ? 'selected' : '' }}>
                                        {{ $budgetyear->budgetyear_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <label>รอบบิล</label>
                            <select class="form-control js-example-tokenizer" name="inv_period[]" id="inv_period" multiple>
                                <option value="all" {{ isset($selected_inv_periods[0]) && $selected_inv_periods[0] == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                @foreach ($inv_periods as $inv_period)
                                    <option value="{{ $inv_period->id }}" {{ in_array($inv_period->id, collect($selectedInvPeriodID)->toArray()) ? 'selected' : '' }}>
                                        {{ $inv_period->inv_p_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <label>หมู่ที่</label>
                            <select class="form-control js-example-tokenizer" id="zone" name="zone[]" multiple>
                                <option value="all" {{ isset($zone_selected[0]) && $zone_selected[0] == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ in_array($zone->id, $zone_selected) ? 'selected' : '' }}>{{ $zone->zone_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <label>เส้นทาง</label>
                            <select class="form-control js-example-tokenizer" id="subzone" name="subzone[]" multiple>
                                <option value="all" {{ isset($subzone_selected[0]) && $subzone_selected[0] == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                @foreach ($subzones as $subzone)
                                    <option value="{{ $subzone->id }}" {{ in_array($subzone->id, $subzone_selected) ? 'selected' : '' }}>{{ $subzone->subzone_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 text-end mt-2">
                            <button type="submit" name="searchBtn" value="true" class="btn btn-success btn-sm"> 
                                <i class="fa fa-search"></i> ค้นหา
                            </button>
                        </div>

                        <div class="col-12 pt-2 mt-2 row border-top">
                            <div class="col-md-6 col-12 mt-2">
                                <button type="submit" name="excelBtn" value="overview" class="btn btn-info w-100"> 
                                    <i class="fa fa-file-excel"></i> Excel (ผลรวม)
                                </button>
                            </div>
                            <div class="col-md-6 col-12 mt-2">
                                <button type="submit" name="excelBtn" value="details" class="btn btn-warning w-100">
                                    <i class="fa fa-file-excel"></i> Excel (รายละเอียด)
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ส่วนสรุปยอด --}}
        <div class="col-lg-4 col-md-12 mt-3 mt-lg-0">
            @php
                // Logic การคำนวณ (ปรับปรุงให้ไม่ Error)
                $vat_rate = 0.07;
                $display_vat_include = false; // เปลี่ยนเป็น true ถ้าใน DB ยอด paid รวม VAT แล้ว แต่ต้องการถอด VAT โชว์
                
                // ค่าน้ำ
                $crudetotal_vat = 0; // ตาม Code เก่าคุณใส่ 0 ไว้
                $crudetotal_total = $crudetotal_sum; 

                // ค่ารักษา
                $reservemeter_vat = 0; // ตาม Code เก่า
                $reservemeter_total = $reservemeter_sum;

                $grand_total = $crudetotal_total + $reservemeter_total;
            @endphp

            <div class="card mb-2">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6 text-sm">ค้างค่าใช้น้ำ</div>
                        <div class="col-6 text-end fw-bold">{{ number_format($crudetotal_total, 2) }} ฿</div>
                        <div class="col-12 text-end text-xs text-muted">
                            (เนื้อ: {{ number_format($crudetotal_sum, 2) }} + Vat: {{ number_format($crudetotal_vat, 2) }})
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6 text-sm">ค้างค่ารักษามิเตอร์</div>
                        <div class="col-6 text-end fw-bold">{{ number_format($reservemeter_total, 2) }} ฿</div>
                        <div class="col-12 text-end text-xs text-muted">
                            (เนื้อ: {{ number_format($reservemeter_sum, 2) }} + Vat: {{ number_format($reservemeter_vat, 2) }})
                        </div>
                    </div>
                </div>
            </div>
            <div class="card bg-gradient-success">
                <div class="card-body p-3 text-white">
                    <div class="row">
                        <div class="col-6 font-weight-bolder">รวมทั้งสิ้น</div>
                        <div class="col-6 text-end font-weight-bolder h5 text-white">{{ number_format($grand_total, 2) }} ฿</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ตารางข้อมูล --}}
    <div class="card">
        <div class="card-body table-responsive">
            <form action="{{ route('admin.owepaper.print') }}" method="POST" id="printForm" target="_blank">
                @csrf
                <input type="hidden" name="from_view" value="owepaper">

                <div class="d-flex justify-content-end mb-2">
                    <button type="submit" class="btn btn-info btn-sm hidden" id="print_multi_inv">
                        <i class="fa fa-print"></i> พิมพ์ใบแจ้งหนี้ที่เลือก
                    </button>
                </div>

                <table class="table table-hover align-items-center mb-0" id="example">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                            <th class="text-center"><i class="fa fa-check-square"></i></th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เลขมิเตอร์</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">แจ้งเตือน(ครั้ง)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อ-สกุล</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ที่อยู่</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">โซน/เส้นทาง</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">รอบบิลค้าง</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เป็นเงิน</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Vat</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">บริการ</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $index_row = 0; @endphp
                        @foreach ($owes as $owe)
                            {{-- ใช้ optional() หรือตรวจสอบข้อมูลก่อน เพื่อป้องกัน error หน้าขาว --}}
                            @php
                                $first_owe = $owe['owe_infos']->first();
                                $meter_info = $first_owe ? $first_owe->tw_meter_infos : null;
                                $user = $meter_info ? $meter_info->user : null;
                            @endphp

                            @if($user)
                            <tr>
                                <td class="text-center">{{ ++$index_row }}</td>
                                <td class="text-center">
                                    <input type="checkbox" class="invoice_id form-check-input" 
                                           name="meter_id[{{ $owe['meter_id_fk'] }}]">
                                    <i class="fa fa-plus-circle text-success ms-2 findInfo" 
                                       style="cursor: pointer;"
                                       data-user_id="{{ $owe['user_id'] }}"></i>
                                </td>
                                <td>{{ $owe['meter_id_fk'] }}</td>
                                <td class="text-center">{{ $owe['printed_time'] }}</td>
                                <td>
                                    {{ $user->prefix . $user->firstname . " " . $user->lastname }}
                                </td>
                                <td>
                                    <small>{{ $user->address }}</small>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-gradient-light text-dark mb-1">{{ optional($user->user_zone)->zone_name }}</span>
                                        <span class="badge bg-gradient-light text-dark">
                                            {{ $user->subzone_id == 13 ? 'เส้นหมู่13' : optional($user->user_subzone)->subzone_name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    @foreach ($owe['owe_infos'] as $item)
                                        <span class="badge bg-danger mb-1">{{ $item->invoice_period->inv_p_name }}</span><br>
                                    @endforeach
                                </td>
                                <td class="text-end">{{ number_format($owe['paid'], 2) }}</td>
                                <td class="text-end">{{ number_format($owe['vat'], 2) }}</td>
                                <td class="text-end">{{ number_format($owe['owe_count']*10, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($owe['totalpaid'], 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    @else
        <div class="card mt-4">
            <div class="card-body text-center p-5">
                <i class="fa fa-check-circle text-success fa-3x mb-3"></i>
                <h3>ไม่พบข้อมูลการค้างชำระ</h3>
                @if(isset($owe_inv_periods) && !empty($owe_inv_periods))
                    <p class="text-muted">
                        เงื่อนไข: หมู่ {{ implode(', ', $owe_zones ?? []) }} 
                        <br>ประจำเดือน {{ $owe_inv_periods }}
                    </p>
                @endif
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

    <script>
        // กำหนดตัวแปร Preloader
        let preloaderwrapper = document.querySelector('.preloader-wrapper');

        $(document).ready(function() {
            // Select2 Init
            $(".js-example-tokenizer").select2({
                tags: true,
                tokenSeparators: [',', ' '],
                width: '100%' // บังคับเต็มจอ
            });

            // DataTable Init
            var table = $('#example').DataTable({
                "lengthMenu": [[10, 25, 50, 150, -1], [10, 25, 50, 150, "All"]],
                "language": {
                    "search": "ค้นหา:",
                    "lengthMenu": "แสดง _MENU_ แถว",
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "paginate": { "next": ">", "previous": "<" }
                }
            });

            // ปุ่มเลือกทั้งหมด
            $(`<div class="deselect_row_all ms-2">
                    <button type="button" class="btn btn-secondary btn-sm hidden" id="deselect-all">ยกเลิกทั้งหมด</button>
                </div>
                <div class="select_row_all ms-2">
                    <button type="button" class="btn btn-outline-success btn-sm" id="select_row_all">เลือกทั้งหมดในหน้านี้</button>
                </div>`).insertAfter('.dataTables_length');

            // ซ่อน Preloader
            if(preloaderwrapper) preloaderwrapper.style.display = 'none';
        });

        // ฟังก์ชัน Submit
        function validateAndSubmit(){
            if(preloaderwrapper) preloaderwrapper.style.display = 'flex';
            return true;
        }

        // Logic เลือกแถวในตาราง
        $(document).on('click', 'tbody tr td:not(:first-child)', function() { 
            // คลิกที่ไหนก็ได้ในแถว (ยกเว้นช่องแรก)
            let tr = $(this).closest('tr');
            tr.toggleClass('selected table-active');
            
            let checkbox = tr.find('input[type=checkbox]');
            checkbox.prop('checked', tr.hasClass('selected'));

            togglePrintButton();
        });

        // Logic ปุ่ม Select All
        $(document).on('click', '#select_row_all', function() {
            $("tbody tr").addClass('selected table-active');
            $('tbody tr').find('.invoice_id').prop('checked', true);
            
            $('#deselect-all').removeClass('hidden');
            $(this).addClass('hidden');
            togglePrintButton();
        });

        $(document).on('click', '#deselect-all', function() {
            $("tbody tr").removeClass('selected table-active');
            $('tbody tr').find('.invoice_id').prop('checked', false);
            
            $('#select_row_all').removeClass('hidden');
            $(this).addClass('hidden');
            togglePrintButton();
        });

        function togglePrintButton() {
            if ($('tbody tr.selected').length > 0) {
                $('#print_multi_inv').removeClass('hidden');
            } else {
                $('#print_multi_inv').addClass('hidden');
            }
        }

        // Dropdown Cascading (Zone -> Subzone)
        $(document).on('change', "#zone", function() {
            let zone_ids = $(this).val();
            // ตรวจสอบว่าเลือก All หรือไม่
            if(zone_ids.includes('all')) return;

            $.post(`../api/subzone`, { zone_id: zone_ids, _token: '{{ csrf_token() }}' })
                .done(function(data) {
                    let text = `<option value="all">ทั้งหมด</option>`;
                    data.forEach(element => {
                        text += `<option value="${element.id}">${element.subzone_name}</option>`;
                    });
                    $('#subzone').html(text);
                });
        });

        // Dropdown Cascading (BudgetYear -> Period)
        $(document).on('change', "#budgetyear", function() {
            let budgetyear_ids = $(this).val();
            $.post(`../api/invoice_period/inv_period_lists_post`, { budgetyear_id: budgetyear_ids, _token: '{{ csrf_token() }}' })
                .done(function(data) {
                    let text = `<option value="all">ทั้งหมด</option>`;
                    data.forEach(element => {
                        text += `<optgroup label="ปีงบ ${element.budgetyear_name}">`;
                        element.invoice_period.forEach(ele => {
                            text += `<option value="${ele.id}">${ele.inv_p_name}</option>`;
                        });
                        text += `</optgroup>`;
                    });
                    $('#inv_period').html(text);
                });
        });

        // Expand Detail Info (Child Row)
        $('body').on('click', '.findInfo', function(e) {
            e.stopPropagation(); // ป้องกันไม่ให้ไป Trigger การเลือกแถว
            let icon = $(this);
            let user_id = icon.data('user_id');
            let tr = icon.closest('tr');
            let table = $('#example').DataTable();
            let row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                icon.removeClass('fa-minus-circle text-info').addClass('fa-plus-circle text-success');
            } else {
                icon.removeClass('fa-plus-circle text-success').addClass('fa-spinner fa-spin text-warning');
                
                $.get(`../../api/users/user/${user_id}`)
                .done(function(data) {
                    row.child(owe_by_user_id_format(data)).show();
                    tr.addClass('shown');
                    icon.removeClass('fa-spinner fa-spin text-warning').addClass('fa-minus-circle text-info');
                })
                .fail(function(){
                    alert('ไม่สามารถดึงข้อมูลได้');
                    icon.removeClass('fa-spinner fa-spin text-warning').addClass('fa-plus-circle text-success');
                });
            }
        });

        function owe_by_user_id_format(d) {
            if(!d || !d[0] || !d[0].usermeterinfos || !d[0].usermeterinfos[0]) return '<div class="alert alert-warning">ไม่พบข้อมูลใบแจ้งหนี้</div>';

            let invoices = d[0].usermeterinfos[0].invoice || [];
            let html = `
            <div class="p-3 bg-light border rounded">
                <h6 class="text-info">ประวัติการค้างชำระ</h6>
                <table class="table table-sm table-bordered bg-white">
                    <thead class="bg-gradient-info text-white">
                        <tr>
                            <th>วันที่</th>
                            <th>รอบบิล</th>
                            <th class="text-end">ก่อนจด</th>
                            <th class="text-end">หลังจด</th>
                            <th class="text-end">หน่วย</th>
                            <th class="text-end">ค่าน้ำ</th>
                            <th class="text-end">รวม</th>
                            <th class="text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>`;
            
            if(invoices.length === 0) {
                 html += `<tr><td colspan="8" class="text-center">ไม่พบรายการค้างชำระ</td></tr>`;
            } else {
                invoices.forEach(el => {
                    if (el.status === 'owe' || el.status === 'invoice') {
                        let statusBadge = el.status === 'owe' 
                            ? '<span class="badge bg-danger">ค้างชำระ</span>' 
                            : '<span class="badge bg-warning text-dark">รอแจ้งหนี้</span>';
                        
                        html += `
                            <tr>
                                <td>${el.updated_at_th || '-'}</td>
                                <td>${el.invoice_period ? el.invoice_period.inv_p_name : '-'}</td>
                                <td class="text-end">${el.lastmeter}</td>
                                <td class="text-end">${el.currentmeter}</td>
                                <td class="text-end">${el.water_used}</td>
                                <td class="text-end">${el.paid}</td>
                                <td class="text-end fw-bold">${el.totalpaid}</td>
                                <td class="text-center">${statusBadge}</td>
                            </tr>
                        `;
                    }
                });
            }
            
            html += `</tbody></table></div>`;
            return html;
        }
    </script>
@endsection