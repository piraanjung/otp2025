@extends('layouts.admin1')

@section('mainheader')
    แก้ไขปีงบประมาณ
@endsection
@section('budgetyear-show')
    show
@endsection
@section('nav-header')
    <a href="{{url('/admin/budgetyear')}}">ปีงบประมาณ</a>
@endsection
@section('nav-current')
    แก้ไขปีงบประมาณ
@endsection
@section('budgetyear')
    active
@endsection

@section('style')
    {{-- CSS ของ Library --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}"></script>

    <style>
        /* --- CSS ชุดเดียวกับหน้า Create (Clean Style) --- */
        .datepicker {
            background-color: #fff !important;
            border: 1px solid #ced4da;
            padding: 10px;
            border-radius: 0.25rem;
            font-family: 'Sarabun', sans-serif;
            z-index: 9999 !important;
        }

        .datepicker table { margin: 0 !important; width: 100% !important; }
        .datepicker table tr td, .datepicker table tr th {
            border-radius: 4px; width: 35px; height: 35px;
            padding: 0 !important; vertical-align: middle !important;
        }

        .datepicker table tr th.datepicker-switch:hover,
        .datepicker table tr th.prev:hover,
        .datepicker table tr th.next:hover,
        .datepicker table tr td.day:hover {
            background: #f0f2f5; cursor: pointer;
        }

        .datepicker table tr td.active,
        .datepicker table tr td.active:hover {
            background-color: #28a745 !important; color: #fff !important;
            background-image: none !important; text-shadow: none;
        }

        .datepicker table tr td.today {
            background-color: #e8f5e9 !important; color: #1e7e34 !important;
            background-image: none !important;
        }

        .input-group-text { background-color: #fff; border-right: 0; }
        .datepicker-input { border-left: 0; padding-left: 0; }
        .datepicker-input:focus { box-shadow: none; border-color: #ced4da; }
    </style>
@endsection

@section('content')
<form action="{{ route('admin.budgetyear.update', $budgetyear->id) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- เตรียมข้อมูลปีงบประมาณ (ตัดเอาแค่ 2 ตัวท้าย) --}}
    @php
        // เช่น 2563 -> เอาแค่ 63
        $yearSuffix = substr($budgetyear->budgetyear_name, -2);
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="text-center text-secondary mb-0">แก้ไขข้อมูลปีงบประมาณ</h5>
                </div>
                <div class="card-body">

                    <div class="form-group mb-3">
                        <label for="budgetyear_suffix" class="text-muted">ปีงบประมาณ (25xx) <span class="text-danger">*</span></label>
                        
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold bg-light text-dark" style="font-size: 1.25rem;">25</span>
                            </div>
                            
                            {{-- Input รับค่า 2 ตัวท้าย (แสดงผล 63) --}}
                            <input type="text" 
                                   class="form-control form-control-lg text-center" 
                                   id="budgetyear_suffix" 
                                   maxlength="2" 
                                   value="{{ $yearSuffix }}" 
                                   required 
                                   autocomplete="off">
                                   
                            {{-- Hidden Input ส่งค่าเต็ม (2563) กลับไป update --}}
                            <input type="hidden" name="budgetyear" id="budgetyear_full" value="{{ $budgetyear->budgetyear_name }}">
                        </div>
                        <small id="year-error" class="text-danger" style="display:none;"></small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="startdate" class="text-muted">วันที่เริ่ม <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-calendar text-muted"></i></span>
                            </div>
                            {{-- ใส่ value จาก DB โดยตรง --}}
                            <input class="form-control datepicker datepicker-input text-center" 
                                   type="text" name="startdate" id="startdate" 
                                   value="{{ $budgetyear->startdate }}" readonly>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="enddate" class="text-muted">วันที่สิ้นสุด <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-calendar text-muted"></i></span>
                            </div>
                            <input class="form-control datepicker datepicker-input text-center" 
                                   type="text" name="enddate" id="enddate" 
                                   value="{{ $budgetyear->enddate }}" readonly>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="status" class="text-muted">สถานะ</label>
                        <input class="form-control text-center bg-light" type="text" 
                               value="{{ $budgetyear->status == 'active' ? 'ปีงบประมาณปัจจุบัน (Active)' : 'สิ้นสุดปีงบประมาณ (Inactive)' }}" 
                               readonly>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success px-5 py-2 shadow-sm">
                            <i class="fa fa-save mr-1"></i> บันทึกการแก้ไข
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
    $(document).ready(function(){
        // 1. ตั้งค่า Datepicker
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'th',
            thaiyear: true,
            autoclose: true,
            orientation: "bottom auto"
        });

        // 2. Logic จัดการปีงบประมาณ (เหมือนหน้า Create)
        let d = new Date();
        let currentYearTH = d.getFullYear() + 543; 
        let minSuffix = parseInt(currentYearTH.toString().slice(-2)); // เช่น 69

        $('#budgetyear_suffix').on('input keyup', function() {
            let inputVal = $(this).val();
            inputVal = inputVal.replace(/[^0-9]/g, ''); // รับเฉพาะตัวเลข
            $(this).val(inputVal);

            let errorSpan = $('#year-error');
            let fullInput = $('#budgetyear_full');

            if (inputVal.length === 2) {
                let yearSuffix = parseInt(inputVal);
                
                // Optional: ถ้าแก้ไขข้อมูลเก่า อาจจะไม่ต้องบังคับว่าต้อง >= ปีปัจจุบันก็ได้
                // แต่ถ้าอยากบังคับเหมือนหน้า Create ให้ Uncomment เงื่อนไขนี้ครับ
                /*
                if (yearSuffix < minSuffix) {
                    $(this).addClass('is-invalid').removeClass('is-valid');
                    fullInput.val('');
                    errorSpan.text(`* ต้องระบุปีไม่ต่ำกว่า 25${minSuffix}`).show();
                    $('button[type="submit"]').prop('disabled', true);
                } else {
                */
                    // ผ่าน
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    fullInput.val('25' + inputVal);
                    errorSpan.hide();
                    $('button[type="submit"]').prop('disabled', false);
                /* } */

            } else {
                // ยังพิมพ์ไม่ครบ
                $(this).removeClass('is-valid is-invalid');
                // อย่าเพิ่งเคลียร์ค่าทิ้งถ้าเขาแค่ลบแก้ เพราะอาจจะกระทบ data เดิม
                // แต่ถ้าจะเอาชัวร์คือถ้าไม่ครบ 2 หลัก ห้าม submit
                $('button[type="submit"]').prop('disabled', true);
            }
        });

        // เปิดปุ่ม Submit ตอนโหลดหน้าเสร็จ (เพราะข้อมูลเดิมถูกต้องอยู่แล้ว)
        $('button[type="submit"]').prop('disabled', false);
    })
</script>
@endsection