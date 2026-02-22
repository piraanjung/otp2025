@extends('layouts.admin1')

@section('mainheader')
    สร้างปีงบประมาณ
@endsection
@section('budgetyear-show')
    show
@endsection
@section('nav-header')
    <a href="{{url('/admin/budgetyear')}}">ปีงบประมาณ</a>
@endsection
@section('nav-current')
    สร้างปีงบประมาณ
@endsection
@section('nav-budgetyear')
    active
@endsection

@section('style')
    {{-- 1. อย่าลืมใส่ CSS ของ Library --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}"></script>

    <style>
        /* --- แก้ไข CSS ตีกัน --- */
        
        /* 1. Reset พื้นหลังและขอบ */
        .datepicker {
            background-color: #fff !important; /* บังคับพื้นขาว เพราะบางที Bootstrap ทำให้ใส */
            border: 1px solid #ced4da;
            padding: 10px;
            border-radius: 0.25rem;
            font-family: 'Sarabun', sans-serif;
            z-index: 9999 !important; /* ให้ลอยอยู่บนสุดเสมอ */
        }

        /* 2. แก้ปัญหาตารางเบี้ยวจาก Bootstrap */
        .datepicker table {
            margin: 0 !important;
            width: 100% !important;
        }

        .datepicker table tr td,
        .datepicker table tr th {
            border-radius: 4px;
            width: 35px; /* บังคับความกว้างช่อง */
            height: 35px; /* บังคับความสูงช่อง */
            padding: 0 !important; /* ล้าง Padding ของ Bootstrap ออก */
            vertical-align: middle !important;
        }

        /* --- ตกแต่งให้สวยงาม (Modern Look) --- */

        /* ส่วนหัว (เดือน/ปี) */
        .datepicker table tr th.datepicker-switch:hover,
        .datepicker table tr th.prev:hover,
        .datepicker table tr th.next:hover {
            background: #f0f2f5;
            cursor: pointer;
        }

        /* วันที่ถูกเลือก (Active) */
        .datepicker table tr td.active,
        .datepicker table tr td.active:hover,
        .datepicker table tr td.active.disabled,
        .datepicker table tr td.active.disabled:hover {
            background-color: #28a745 !important; /* สีเขียว */
            background-image: none !important; /* ลบ Gradient เก่าออก */
            color: #fff !important;
            text-shadow: none;
        }

        /* วันปัจจุบัน (Today) */
        .datepicker table tr td.today,
        .datepicker table tr td.today:hover {
            background-color: #e8f5e9 !important; /* เขียวอ่อน */
            background-image: none !important;
            color: #1e7e34 !important;
        }

        /* Hover วันทั่วไป */
        .datepicker table tr td.day:hover {
            background: #e9ecef;
            cursor: pointer;
        }

        /* จัด Input Group */
        .input-group-text {
            background-color: #fff;
            border-right: 0;
        }
        .datepicker-input {
            border-left: 0;
            padding-left: 0;
        }
        .datepicker-input:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
    </style>
@endsection

@section('content')
    <form action="{{ route('admin.budgetyear.store') }}" id="form" method="POST">
        @csrf
        {{-- ปรับ Layout ให้กว้างขึ้นเล็กน้อยและดูสะอาดตา --}}
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8"> 
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="text-center text-secondary mb-0">ข้อมูลปีงบประมาณ</h5>
                    </div>
                    <div class="card-body">

                        <div class="form-group mb-3">
    <label for="budgetyear_suffix" class="text-muted">ปีงบประมาณ (25xx) <span class="text-danger">*</span></label>
    
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text font-weight-bold bg-light text-dark" style="font-size: 1.25rem;">25</span>
        </div>
        
        <input type="text" 
               class="form-control form-control-lg text-center" 
               id="budgetyear_suffix" 
               maxlength="2" 
               placeholder="69" 
               required 
               autocomplete="off">
               
        <input type="hidden" name="budgetyear" id="budgetyear_full">
    </div>
    
    <small id="year-error" class="text-danger" style="display:none;"></small>
    @error('budgetyear')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

                        <div class="form-group mb-3">
                            <label for="startdate" class="text-muted">วันที่เริ่ม <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-calendar text-muted"></i></span>
                                </div>
                                <input class="form-control datepicker datepicker-input text-center" 
                                       readonly type="text" name="start" id="start" placeholder="เลือกวันที่">
                            </div>
                            @error('start')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="enddate" class="text-muted">วันที่สิ้นสุด <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-calendar text-muted"></i></span>
                                </div>
                                <input class="form-control datepicker datepicker-input text-center" 
                                       readonly type="text" name="end" id="end" placeholder="เลือกวันที่">
                            </div>
                            @error('end')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="status" class="text-muted">สถานะ</label>
                            <select name="status" id="status" class="form-control text-center bg-light" readonly>
                                <option value="active" selected>ปีงบประมาณปัจจุบัน (Active)</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success px-5 py-2 shadow-sm">
                                <i class="fa fa-save mr-1"></i> บันทึกข้อมูล
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        // --- Logic ตรวจสอบปีงบประมาณ ---
let d = new Date();
let currentYearAD = d.getFullYear(); // 2026
let currentYearTH = currentYearAD + 543; // 2569
let minSuffix = parseInt(currentYearTH.toString().slice(-2)); // ตัดเอาแค่ "69"
        $(document).ready(function() {
            // ตั้งค่า Datepicker
            $('.datepicker').datepicker({
                format: 'dd/m/yyyy',
                language: 'th',
                thaiyear: true,
                autoclose: true, // เลือกเสร็จแล้วปิดปฏิทินให้อัตโนมัติ
                orientation: "bottom auto" // บังคับให้เด้งลงข้างล่างเสมอ (ถ้ามีที่พอ)
            });

            // ตั้งค่า Default Date
            let d = new Date();
            let date = d.getDate();
            let month = d.getMonth() + 1;
            let year = d.getFullYear() + 543;
            
            // Format วันที่ให้มีเลข 0 นำหน้าถ้าน้อยกว่า 10 (สวยงามขึ้น)
            let formattedDate = (date < 10 ? '0' : '') + date;
            let formattedMonth = (month < 10 ? '0' : '') + month;
            
            let todayStr = `${formattedDate}/${formattedMonth}/${year}`;

            // ถ้าไม่มี value (คือเพิ่ง load หน้าครั้งแรก) ให้ใส่ค่า default
            if(!$('#start').val()) $('#start').val(todayStr);
            if(!$('#end').val()) $('#end').val(todayStr);
            
            // Focus ที่ช่องปีงบประมาณเป็นอันดับแรก
            $('#budgetyear').focus();
        })

        // ตั้งค่า Placeholder ให้เป็นปีปัจจุบัน
$('#budgetyear_suffix').attr('placeholder', minSuffix);

$('#budgetyear_suffix').on('input keyup', function() {
    let inputVal = $(this).val();

    // 1. ให้พิมพ์ได้แค่ตัวเลขเท่านั้น
    inputVal = inputVal.replace(/[^0-9]/g, '');
    $(this).val(inputVal);

    let errorSpan = $('#year-error');
    let fullInput = $('#budgetyear_full');

    // 2. เมื่อพิมพ์ครบ 2 หลัก
    if (inputVal.length === 2) {
        let yearSuffix = parseInt(inputVal);
        
        // เช็คเงื่อนไข: ต้องมากกว่าหรือเท่ากับปีปัจจุบัน (เช่น >= 69)
        if (yearSuffix < minSuffix) {
            $(this).addClass('is-invalid').removeClass('is-valid');
            fullInput.val(''); // ไม่บันทึกค่าถ้าผิดเงื่อนไข
            errorSpan.text(`* ต้องระบุปีไม่ต่ำกว่า 25${minSuffix}`).show();
            // ปิดปุ่ม Submit (ถ้าต้องการ)
            $('button[type="submit"]').prop('disabled', true);
        } else {
            // ผ่านเงื่อนไข
            $(this).removeClass('is-invalid').addClass('is-valid');
            fullInput.val('25' + inputVal); // รวมร่างเป็น 25xx ใส่ใน hidden input
            errorSpan.hide();
            $('button[type="submit"]').prop('disabled', false);
        }
    } else {
        // ยังพิมพ์ไม่ครบ
        $(this).removeClass('is-valid is-invalid');
        fullInput.val('');
        errorSpan.hide();
    }
});
    </script>
@endsection