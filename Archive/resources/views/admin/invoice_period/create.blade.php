@extends('layouts.admin1')

@section('inv_prd-show')
    show
@endsection

@section('nav-budgetyear-header')
    active
@endsection

@section('nav-inv_prd')
    active
@endsection

@section('nav-header')
    รอบบิล
@endsection

@section('nav-main')
    <a href="{{ route('admin.invoice_period.create') }}"> สร้างรอบบิล</a>
@endsection

@section('style')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    
    <style>
        /* --- แต่ง Datepicker ให้เป็นสไตล์ Material --- */
        .datepicker.dropdown-menu {
            font-family: 'Sarabun', sans-serif;
            border: 0;
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
            box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.14), 
                        0 6px 30px 5px rgba(0, 0, 0, 0.12), 
                        0 8px 10px -5px rgba(0, 0, 0, 0.2);
            z-index: 9999 !important;
        }

        .datepicker-days table {
            width: 100%;
        }

        /* หัวตาราง วันอาทิตย์-จันทร์ */
        .datepicker table tr th.dow {
            color: #ab47bc; /* สีม่วงธีม */
            font-weight: 500;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        /* หัวตาราง เดือน/ปี */
        .datepicker table tr th.datepicker-switch {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .datepicker table tr th.prev,
        .datepicker table tr th.next {
            color: #ab47bc;
            font-weight: bold;
            cursor: pointer;
        }

        .datepicker table tr th.prev:hover,
        .datepicker table tr th.next:hover,
        .datepicker table tr th.datepicker-switch:hover {
            background: #eee;
            border-radius: 4px;
        }

        /* ช่องวันที่ปกติ */
        .datepicker table tr td.day {
            border-radius: 4px;
            padding: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .datepicker table tr td.day:hover {
            background: #eeeeee;
        }

        /* ช่องวันที่ถูกเลือก (Active) - ทำเป็นสีม่วง Gradient */
        .datepicker table tr td.active.day,
        .datepicker table tr td.active.day:hover {
            background: linear-gradient(60deg, #ab47bc, #8e24aa) !important;
            color: #fff !important;
            box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 
                        0 7px 10px -5px rgba(156, 39, 176, 0.4);
            text-shadow: none;
            border: none;
        }

        /* วันปัจจุบัน (Today) */
        .datepicker table tr td.today,
        .datepicker table tr td.today:hover,
        .datepicker table tr td.today.disabled,
        .datepicker table tr td.today.disabled:hover {
            background-color: #fff;
            border: 1px solid #ab47bc; /* ขอบม่วง */
            color: #ab47bc;
        }
        
        /* วันเก่า/วันเดือนถัดไป */
        .datepicker table tr td.old,
        .datepicker table tr td.new {
            color: #bdbdbd;
        }
    </style>
@endsection

@section('invoice_period')
    active
@endsection

@section('nav')
    <a href="{{ url('/invoice_period') }}"> สร้างรอบบิล</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header card-header-primary">
            <h4 class="card-title mb-0">สร้างรอบบิล</h4>
            <p class="card-category">กำหนดช่วงเวลาการจดมิเตอร์และออกใบแจ้งหนี้</p>
        </div>

        <div class="card-body">
            <div class="preloader-wrapper hidden text-center mb-3">
                <button class="btn btn-primary btn-sm mb-2" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    กำลังสร้างข้อมูล รอบบิล...
                </button>
            </div>

            <div class="row justify-content-center"> <div class="col-md-6"> <form action="{{ route('admin.invoice_period.store') }}" method="post" onsubmit="return check()">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">ปีงบประมาณ</label>
                            <input class="form-control text-center bg-light" type="text"
                                value="{{ $budgetyear->budgetyear_name }}" readonly style="cursor: not-allowed;">
                            <input type="hidden" name="budgetyear_id" value="{{ $budgetyear->id }}">
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="form-label font-weight-bold">ประจำเดือน (XX)</label>
                                    <input class="form-control text-center" type="text" name="inv_period_name"
                                        value="{{ date('m') }}" id="inv_period_name" placeholder="01" maxlength="2">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="form-label font-weight-bold">พ.ศ.</label>
                                    <input class="form-control text-center bg-light" type="text" name="inv_period_name_year"
                                        value="{{ $budgetyear->budgetyear_name }}" readonly style="cursor: not-allowed;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">วันที่เริ่มรอบบิล</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <input class="form-control text-center datepicker" type="text" name="startdate" id="startdate" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label font-weight-bold">วันสิ้นสุดรอบบิล</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <input class="form-control text-center datepicker" type="text" name="enddate" id="enddate" autocomplete="off">
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">ย้อนกลับ</a>
                            <button type="submit" class="btn btn-success submit_btn ms-2">บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // ตั้งค่า Datepicker
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                todayBtn: "linked",
                language: 'th',
                thaiyear: true,
                autoclose: true, // เลือกเสร็จปิดเลย
                orientation: "bottom auto"
            });

            // คำนวณวันเริ่มต้น-สิ้นสุด ของเดือนปัจจุบัน
            let d = new Date();
            let year = d.getFullYear();
            let month = d.getMonth();

            let fistDay = new Date(year, month, 1);
            let lastDay = new Date(year, month + 1, 0);

            // แปลงเป็นรูปแบบไทย
            let fistDayResult = fistDay.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'numeric',
                day: 'numeric',
            });
            let lastDayResult = lastDay.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'numeric',
                day: 'numeric',
            });

            // ตรวจสอบว่ามีค่าอยู่แล้วหรือไม่ ถ้าไม่มีค่อยใส่ (เผื่อกรณี Edit)
            if(!$('#startdate').val()) $('#startdate').val(fistDayResult);
            if(!$('#enddate').val()) $('#enddate').val(lastDayResult);
        });

        // จัดการ Input เดือน ให้เติม 0 ข้างหน้า
        $('#inv_period_name').blur(function() {
            let val = $(this).val();
            if (val.length === 1 && val > 0) {
                $(this).val(`0${val}`);
            }
            if (val == 0 || val > 12) {
                alert('กรุณาระบุเดือนให้ถูกต้อง (01 - 12)');
                $(this).val('');
            }
        });

        function check() {
            $('.preloader-wrapper').removeClass('hidden');
            $('.submit_btn').prop('disabled', true); // กันกดซ้ำ
            return true;
        }
    </script>
@endsection