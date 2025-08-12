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
@section('invoice_period')
    active
@endsection
@section('nav')
    <a href="{{ url('/invoice_period') }}"> สร้างรอบบิล</a>
@endsection

@section('content')
    <div class="card">

        <div class="card-header h4">สร้างรอบบิล</div>
        <div class="card-body">
            <div class="preloader-wrapper hidden">
                <button class="btn btn-primary btn-sm mb-2" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    กำลังสร้างข้อมูล รอบบิล...
                </button>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <form action="{{ route('admin.invoice_period.store') }}" method="post" onsubmit="return check()">
                        @csrf
                        <div class="form-group">
                            <label>ปีงบประมาณ</label>
                            <input class="form-control text-center bg-gray-200" type="text"
                                value="{{ $budgetyear->budgetyear_name }}" placeholder="" readonly>
                            <input class="form-control text-center" type="text" name="budgetyear_id"
                                value="{{ $budgetyear->id }}" hidden>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-5">
                                    <label>ประจำเดือน</label>

                                    <input class="form-control text-center" type="text" name="inv_period_name"
                                        value="{{ date('m') }}" id="inv_period_name" placeholder="01">
                                </div>
                             
                                <div class="col-5">
                                    <label>พ.ศ.</label>

                                    <input class="form-control text-center bg-gray-200" type="text" name="inv_period_name_year"
                                        value="{{ $budgetyear->budgetyear_name  }}" readonly>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label>วันที่เริ่มรอบบิล</label>
                            <input class="form-control text-center datepicker" type="text" name="startdate"
                                id="startdate">
                        </div>
                        <div class="form-group">
                            <label>วันสิ้นสุดรอบบิล</label>
                            <input class="form-control text-center datepicker" type="text" name="enddate" id="enddate">
                        </div>

                        <button type="submit" class="btn btn-success submit_btn">บันทึก</button>
                    </form>
                </div>
            </div>

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
                thaiyear: true //Set เป็นปี พ.ศ.
            }).datepicker(); //กำหนดเป็นวันปัจุบัน

            let d = new Date();
            let date = d.getDate();
            let month = d.getMonth();
            let year = d.getFullYear()

            var fistDay = new Date(year, month, 1);
            var lastDay = new Date(year, month + 1, 0);
            console.log(fistDay.toDateString('dd/mm/yyyy'))
            let fistDayResult = fistDay.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'numeric',
                day: 'numeric',
            })
            let lastDayResult = lastDay.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'numeric',
                day: 'numeric',
            })

            $('#startdate').val(fistDayResult)
            $('#enddate').val(lastDayResult)
            $('#status').attr('readonly')
        })

        $('#inv_period_name').blur(function() {
            let val = $(this).val()
            if (val.length == 1 && val > 0) {
                $(this).val(`0${val}`)
            }
            if (val == 0) {
                alert('กรุณาใส่หมายเลข 01 - 12')
            }
        })



        function check(){
            $('.preloader-wrapper').removeClass('hidden')
            return true
        }
    </script>
@endsection
