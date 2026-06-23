@extends('layouts.admin1')

@section('nav-reports-owe', 'active')
@section('nav-header', 'รายงาน')
@section('nav-main')
    <a href="{{ route('reports.meter_record_history') }}"> สมุดจดเลขอ่านมาตรวัดน้ำ(ป.31)</a>
@endsection
@section('nav-topic', 'ตารางสมุดจดเลขอ่านมาตรวัดน้ำ(ป.31)')

@section('style')
    {{-- CSS Libraries --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        /* จัดการตารางให้สวยงาม */
        .table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        
        /* สีพื้นหลังสำหรับแยกประเภทข้อมูล */
        .bg-orange-light { background-color: #ffe6d5 !important; }
        .bg-green-light { background-color: #e8f5e9 !important; }
        .bg-header { background-color: #FFD3B6 !important; font-weight: bold; text-align: center; }

        /* ปรับแต่ง DataTables */
        .dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody {
            overflow-y: hidden !important; /* ซ่อน scroll แนวตั้งถ้าไม่จำเป็น */
        }

        /* Select2 Fix */
        .select2-container { width: 100% !important; }
        .select2-selection__rendered { line-height: 31px !important; }
        .select2-container .select2-selection--single { height: 35px !important; }

        /* CSS สำหรับการสั่งพิมพ์ */
        @media print {
            @page { size: landscape; margin: 5mm; }
            body { -webkit-print-color-adjust: exact; }
            .no-print, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dt-buttons, form {
                display: none !important;
            }
            .card { border: none !important; box-shadow: none !important; }
            .card-header, .card-body { padding: 0 !important; margin: 0 !important; }
            
            table { font-size: 10px !important; width: 100%; }
            th, td { padding: 2px 4px !important; border: 1px solid #000 !important; }
            
            /* บังคับให้แสดงสีพื้นหลังตอนปริ้น */
            .bg-header { background-color: #FFD3B6 !important; }
            .bg-orange-light { background-color: #ffe6d5 !important; }
            .bg-green-light { background-color: #e8f5e9 !important; }
        }
    </style>
@endsection

@section('content')
    {{-- Preloader --}}
    <div class="preloader-wrapper">
        <button class="btn btn-primary btn-sm mb-2" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status"></span>
            กำลังโหลดข้อมูล...
        </button>
    </div>

    {{-- Filter Card --}}
    <div class="card mb-3 no-print">
        <div class="card-body">
            <form action="{{ route('reports.meter_record_history') }}" method="get" id="searchForm">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-4 col-sm-12 mb-3">
                        <label class="form-label">ปีงบประมาณ</label>
                        <select class="form-control js-example-tokenizer" name="budgetyear[]" id="budgetyear" multiple>
                            @foreach ($budgetyears as $budgetyear)
                                <option value="{{ $budgetyear->id }}"
                                    {{ in_array($budgetyear->id, collect($budgetyear_selected_array)->toArray()) ? 'selected' : '' }}>
                                    {{ $budgetyear->budgetyear_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4 col-sm-12 mb-3">
                        <label class="form-label">หมู่ที่ / โซน</label>
                        <select class="form-control js-example-tokenizer" id="zone" name="zone[]" multiple>
                            <option value="all" {{ in_array('all', $zone_id_array) ? 'selected' : '' }}>ทั้งหมด</option>
                            @foreach ($zones as $zone)
                                <option value="{{ $zone->id }}"
                                    {{ in_array($zone->id, $zone_id_array) ? 'selected' : '' }}>
                                    {{ $zone->zone_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-sm-12 mb-3">
                        <div class="d-flex gap-2">
                            <button type="submit" name="submitBtn" value="search" class="btn btn-success flex-fill">
                                <i class="fa fa-search"></i> ค้นหา
                            </button>
                            <button type="submit" name="submitBtn" value="export_excel" class="btn btn-info flex-fill">
                                <i class="fa fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card">
        <div class="card-header pb-0 text-center">
            <h5>ทะเบียนผู้ใช้น้ำประปาและสมุดจดเลขมาตรวัดน้ำ (ป.31)</h5>
            <div class="text-sm text-muted">
                ปีงบประมาณ: {{ implode(', ', $budgetyears->whereIn('id', $budgetyear_selected_array)->pluck('budgetyear_name')->toArray()) }} 
                | พื้นที่: {{ in_array('all', $zone_id_array) ? 'ทั้งหมด' : implode(', ', $zones->whereIn('id', $zone_id_array)->pluck('zone_name')->toArray()) }}
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table id="oweTable" class="table table-bordered table-striped nowrap" style="width:100%">
                    <thead>
                        {{-- Row 1: Main Headers --}}
                        <tr>
                            <th rowspan="2" class="bg-header" style="vertical-align: middle;">#</th>
                            <th rowspan="2" class="bg-header" style="vertical-align: middle;">รหัส</th>
                            <th rowspan="2" class="bg-header" style="vertical-align: middle;">ชื่อ - สกุล</th>
                            <th rowspan="2" class="bg-header" style="vertical-align: middle;">ที่อยู่</th>
                            <th rowspan="2" class="bg-header" style="vertical-align: middle;">หมู่</th>
                            <th rowspan="2" class="bg-header" style="vertical-align: middle;">เส้นทาง</th>
                            <th rowspan="2" class="bg-header bg-orange-light" style="vertical-align: middle;">ยอดยกมา</th>
                            
                            @foreach ($inv_period_list as $inv_period)
                                <th colspan="3" class="bg-header" style="border-bottom: 2px solid #aaa;">
                                    {{ $inv_period->inv_p_name }}
                                </th>
                            @endforeach
                        </tr>
                        {{-- Row 2: Sub Headers --}}
                        <tr>
                            @foreach ($inv_period_list as $inv_period)
                                <th class="text-center text-xs">จดวันที่</th>
                                <th class="text-center text-xs">เลขจด</th>
                                <th class="text-center text-xs bg-green-light">หน่วย</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach ($usermeterinfos as $user)
                            @php
                                $u = $user->user; // ดึง User Object จาก Relation
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i++ }}</td>
                                <td class="text-center font-weight-bold">{{ $user->meter_id }}</td>
                                <td>{{ $u->prefix . $u->firstname . ' ' . $u->lastname }}</td>
                                <td>{{ $u->address }}</td>
                                <td class="text-center">{{ optional($u->user_zone)->zone_name ?? '-' }}</td>
                                <td class="text-center">{{ optional($u->user_subzone)->subzone_name ?? '-' }}</td>
                                
                                {{-- ยอดยกมา --}}
                                <td class="text-end bg-orange-light font-weight-bold">
                                    {{ number_format($user->bringForward) }}
                                </td>

                                {{-- Loop รอบบิล --}}
                                @foreach ($user->infos as $info)
                                    <td class="text-center text-xs text-muted">-</td> {{-- วันที่จด (ถ้าไม่มีข้อมูล ใส่ -) --}}
                                    <td class="text-end">
                                        {{ $info['currentmeter'] > 0 ? number_format($info['currentmeter']) : '' }}
                                    </td>
                                    <td class="text-end bg-green-light font-weight-bold">
                                        {{ $info['water_used'] > 0 ? number_format($info['water_used']) : '' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- Scripts Libraries --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. ซ่อน Preloader
            $('.preloader-wrapper').addClass('fade-out-animation');

            // 2. ตั้งค่า Select2
            $(".js-example-tokenizer").select2({
                tags: true,
                tokenSeparators: [',', ' '],
                width: '100%'
            });

            // 3. ตั้งค่า DataTable
            var table = $('#oweTable').DataTable({
                dom: 'Bfrtip', // เพิ่มปุ่ม Buttons
                scrollX: true, // เปิด Scroll แนวนอน (สำคัญมากสำหรับ ป.31)
                scrollY: '65vh', // ความสูงตาราง
                scrollCollapse: true,
                paging: true,
                fixedColumns: {
                    left: 2 // ตรึง 2 คอลัมน์ซ้าย (ชื่อ, เลขมิเตอร์) ถ้าใช้ library fixedColumns
                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "ทั้งหมด"]
                ],
                ordering: false, // ปิดการเรียงลำดับ (เพื่อให้เรียงตามสายจดที่ส่งมาจาก Controller)
                pageLength: 50, // ค่าเริ่มต้นแสดง 50 แถว
                buttons: [
                    'pageLength',
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel"></i> Export Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'รายงานสมุดจดเลขอ่านมาตรวัดน้ำ(ป.31)'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        className: 'btn btn-secondary btn-sm',
                        title: '', // ไม่ใส่หัวข้อ Auto เพราะเรามี Header ในตารางแล้ว
                        customize: function (win) {
                            $(win.document.body).css('font-size', '10pt');
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ],
                language: {
                    search: "ค้นหาในตาราง:",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    infoEmpty: "ไม่มีข้อมูล",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });

            // 4. Custom Search Logic (ถ้าต้องการค้นหาราย Column เหมือนเดิม)
            // หมายเหตุ: ปกติ DataTable มี Search รวมอยู่แล้ว การทำ Search แยกแต่ละช่อง
            // อาจทำให้รกสำหรับตารางที่มี Column เยอะแบบนี้ แต่ถ้าต้องการใส่คืนก็ทำได้ที่นี่ครับ
        });
    </script>
@endsection