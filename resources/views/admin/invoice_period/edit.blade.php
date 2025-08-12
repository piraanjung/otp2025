@extends('layouts.admin1')
@section('style')
<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}">
    </script>
<style>
.datepicker.dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1040 !important;
            display: none;
            float: left;
            min-width: 360px;
            list-style: none;
            background-color: #fff;
            border: 1px solid #ccc;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            -webkit-box-shadow: 0 6px 12px rgb(0 0 0 / 18%);
            -moz-box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
            box-shadow: 0 6px 12px rgb(0 0 0 / 18%);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            color: #333333;
            font-size: 13px;
            line-height: 1.42857143;
        }
        .old.day,
        td.new.day {
            text-align: center;
            background: #f0e3e3;
            border: 1px solid white;
        }

        td.day {
            text-align: center;
            background: #44ffbb;
            font-weight: bold;
            border: 2px solid white;
        }

        td.active.day {
            background: white;
            color: red;
        }

        th.dow {
            text-align: center;
            color: green;
            solid;
            border: 1px solid blue;
        }

        th.datepicker-switch {
            text-align: center;
            font-size: 1.2rem;
        }

        th.prev,
        th.next {
            text-align: center;
            font-size: 1.2rem;
        }

        span.month {
            text-align: center;
            margin: 2px;
            !;
            background: #52f0ee;
            padding: 5px;

        }

        span.year {
            border: 1px solid;
            margin: 2px;
            padding: 2px;
        }

        table.table-condensed {
            width: 100%;
        }


        th.today {
            display: none !important;
          
        }
        
</style>
@endsection
@section('nav_invoice_period')
    active
@endsection

@section('nav-header')
    ปีงบประมาณ/รอบบิล
@endsection
@section('nav-current')
    <a href="{{ url('/invoice_period') }}"> แก้ไขรอบบิล</a>
@endsection

@section('nav-topic')
    แก้ไขรอบบิล
@endsection


@section('content')
  
            <div class="card">
                <div class="card-body">
                    <form class="col-3 sm-auto" action="{{ route('admin.invoice_period.update', $invoice_period->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>ปีงบประมาณ</label>
                            <input class="form-control text-center" type="text" name="budgetyear_name"
                                value="{{ $invoice_period->budgetyear->budgetyear_name }}" placeholder="" readonly>
                            <input class="form-control text-center" type="text" name="budgetyear_id"
                                value="{{ $invoice_period->budgetyear_id }}" hidden>
                        </div>
                        <div class="form-group">
                            <label>รอบบิลประจำเดือน</label>
                            <input class="form-control text-center" type="text" name="inv_p_name"
                                value="{{ $invoice_period->inv_p_name }}" placeholder="01-63">
                        </div>
                        <div class="form-group">
                            <label>วันที่เริ่มรอบ</label>
                            <input class="form-control text-center datepicker" type="text" name="startdate"
                                value="{{ $invoice_period->startdate }}" placeholder="Select date">
                        </div>
                        <div class="form-group">
                            <label>วันสิ้นสุดรอบ</label>
                            <input class="form-control text-center datepicker" type="text" name="enddate"
                                value="{{ $invoice_period->enddate }}" placeholder="Select date">
                        </div>
                        <div class="form-group">
                            <label>สถานะ</label>
                            <select name="status" id="status" class="form-control">
                                <option value="active" {{ $invoice_period->status == 'active' ? 'selected' : '' }}>
                                    รอบบิลปัจจุบัน
                                </option>
                                @if ($invoice_period->status == 'inactive')
                                <option value="inactive" {{ $invoice_period->status == 'inactive' ? 'selected' : '' }}>
                                    สิ้นสุดรอบบิล
                                </option>
                                @endif

                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">ยืนยัน</button>
                    </form>
                </div>
            </div>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                todayBtn: true,
                language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
                // thaiyear: true              //Set เป็นปี พ.ศ.
            }).datepicker(); //กำหนดเป็นวันปัจุบัน
        })
    </script>
@endsection
