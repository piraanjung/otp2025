@extends('layouts.admin1')

@section('nav_invoice_period', 'active')
@section('nav-header', 'จัดการรอบบิล')
@section('nav-current', 'แก้ไขรอบบิล')
@section('nav-topic', 'แก้ไขข้อมูล')

@section('style')
    {{-- Import Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    {{-- Datepicker Dependencies --}}
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}"></script>

    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f8f9fe;
        }

        /* --- Modern Card Style --- */
        .modern-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.05);
            background-color: #fff;
            overflow: hidden;
            max-width: 600px; /* จำกัดความกว้างไม่ให้ยืดเกินไป */
            margin: 0 auto;   /* จัดกึ่งกลาง */
        }
        
        .card-header-modern {
            background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%);
            padding: 20px 30px;
            color: white;
            border-bottom: none;
        }

        /* --- Modern Input Style --- */
        .form-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #525f7f;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #e9ecef;
            padding: 12px 15px;
            font-size: 0.95rem;
            color: #32325d;
            box-shadow: none;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 3px rgba(94, 114, 228, 0.1);
        }

        .form-control[readonly] {
            background-color: #f6f9fc;
            opacity: 1;
        }

        /* --- Modern Datepicker CSS Overrides --- */
        .datepicker.dropdown-menu {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 15px;
            font-family: 'Prompt', sans-serif;
            margin-top: 10px;
        }

        .datepicker table {
            width: 100%;
        }

        /* Header (Month/Year) */
        .datepicker table tr td span.month, 
        .datepicker table tr td span.year {
            border-radius: 8px;
            margin: 2px;
        }
        .datepicker table tr td span.month:hover,
        .datepicker table tr td span.year:hover {
            background: #f6f9fc;
        }
        .datepicker table tr td span.active {
            background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%) !important;
            color: #fff;
            box-shadow: 0 4px 10px rgba(94, 114, 228, 0.3);
            text-shadow: none;
        }

        /* Navigation Arrows */
        .datepicker th.prev, 
        .datepicker th.next {
            border-radius: 50%;
            width: 35px;
            height: 35px;
            cursor: pointer;
            transition: background 0.2s;
            color: #5e72e4;
        }
        .datepicker th.prev:hover, 
        .datepicker th.next:hover {
            background-color: #f4f5f7;
        }

        /* Day Cells */
        .datepicker th.dow {
            color: #8898aa;
            font-weight: 600;
            font-size: 0.8rem;
            padding-bottom: 10px;
        }

        .datepicker td.day {
            border-radius: 50%; /* วงกลม */
            height: 35px;
            width: 35px;
            font-weight: 400;
            color: #32325d;
            border: none !important; /* ลบ Border หนาๆ */
        }

        .datepicker td.day:hover {
            background: #f6f9fc;
            color: #32325d;
        }

        /* Active Day (Selected) */
        .datepicker td.active.day, 
        .datepicker td.active.day:hover {
            background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%) !important;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(94, 114, 228, 0.3);
            text-shadow: none;
        }

        /* Today */
        .datepicker td.today.day {
            background: rgba(94, 114, 228, 0.1);
            color: #5e72e4;
        }
        
        .datepicker td.today.day:hover {
            background: rgba(94, 114, 228, 0.2);
            color: #5e72e4;
        }
        
        /* New & Old Days (Previous/Next Month) */
        .datepicker td.old, 
        .datepicker td.new {
            color: #adb5bd;
            background: transparent !important;
        }

        /* Input Group Icon */
        .input-group-text {
            border: 1px solid #e9ecef;
            background-color: white;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .form-control.has-icon {
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="modern-card">
            {{-- Header --}}
            <div class="card-header-modern">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-edit mr-2"></i> แก้ไขรอบบิล (Edit Invoice Period)</h5>
            </div>

            {{-- Body --}}
            <div class="card-body p-4">
                <form action="{{ route('admin.invoice_period.update', $invoice_period->id) }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- ปีงบประมาณ --}}
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label>ปีงบประมาณ</label>
                                <input class="form-control" type="text" 
                                    value="{{ $invoice_period->budgetyear->budgetyear_name }}" readonly>
                                <input type="hidden" name="budgetyear_id" value="{{ $invoice_period->budgetyear_id }}">
                            </div>
                        </div>

                        {{-- ชื่อรอบบิล --}}
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label>ชื่อรอบบิล (เช่น มกราคม)</label>
                                <input class="form-control" type="text" name="inv_p_name"
                                    value="{{ $invoice_period->only_name ?? $invoice_period->inv_p_name }}" 
                                    placeholder="ระบุชื่อรอบบิล" required>
                                {{-- ถ้าแยกปีไว้ใน Controller ก็ใส่ input แยก หรือรวมกันตาม logic เดิม --}}
                            </div>
                        </div>

                        {{-- วันที่เริ่ม - สิ้นสุด (จัดให้อยู่คู่กัน) --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label>วันที่เริ่มรอบบิล</label>
                                <div class="input-group">
                                    <input class="form-control has-icon datepicker text-center" type="text" name="startdate"
                                        value="{{ $invoice_period->startdate }}" placeholder="วว/ดด/ปปปป" autocomplete="off" required>
                                    <span class="input-group-text"><i class="far fa-calendar-alt text-muted"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label>วันสิ้นสุดรอบบิล</label>
                                <div class="input-group">
                                    <input class="form-control has-icon datepicker text-center" type="text" name="enddate"
                                        value="{{ $invoice_period->enddate }}" placeholder="วว/ดด/ปปปป" autocomplete="off" required>
                                    <span class="input-group-text"><i class="far fa-calendar-alt text-muted"></i></span>
                                </div>
                            </div>
                        </div>

                        {{-- สถานะ --}}
                        <div class="col-12 mb-4">
                            <div class="form-group">
                                <label>สถานะ</label>
                                <select name="status" id="status" class="form-control form-select">
                                    <option value="active" {{ $invoice_period->status == 'active' ? 'selected' : '' }}>
                                        🟢 รอบบิลปัจจุบัน (Active)
                                    </option>
                                    @if ($invoice_period->status == 'inactive')
                                    <option value="inactive" {{ $invoice_period->status == 'inactive' ? 'selected' : '' }}>
                                        🔴 สิ้นสุดรอบบิล (Inactive)
                                    </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.invoice_period.index') }}" class="btn btn-light shadow-sm mr-2 text-muted">
                            <i class="fas fa-arrow-left mr-1"></i> ย้อนกลับ
                        </a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm" style="background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%); border:none; border-radius: 25px;">
                            <i class="fas fa-save mr-2"></i> บันทึกการแก้ไข
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                todayBtn: false, // ปิดปุ่ม Today แบบเดิม เพราะมันไม่สวย
                language: 'th',
                autoclose: true, // เลือกเสร็จปิดเลย สะดวกกว่า
                orientation: "bottom auto"
            });
        })
    </script>
@endsection