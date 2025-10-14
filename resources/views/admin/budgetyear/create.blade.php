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
@section('content')
    <form action="{{ route('admin.budgetyear.store') }}" id="form" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="col-lg-3 col-md-5 auth-form mx-auto my-auto">
                    <div class="card">
                        <div class="card-body">

                            <div class="form-group">
                                <label for="budgetyear">ปีงบประมาณ
                                    @error('budgetyear')
                                        <span class="text-sm text-alert">({{ $message }}) </span>
                                    @enderror
                                </label>
                                <input type="text" class="form-control text-center" id="budgetyear" name="budgetyear"
                                    placeholder="ตัวอย่าง 2563">
                            </div>
                            <div class="form-group">
                                <label for="startdate">วันที่เริ่มปีงบประมาณ
                                    @error('start')
                                        <span class="text-sm text-alert">({{ $message }}) </span>
                                    @enderror
                                </label>
                                <input class="form-control text-center datepicker" readonly type="text" name="start"
                                    id="start">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword2">วันที่สิ้นสุดปีงบประมาณ
                                    @error('end')
                                        <span class="text-sm text-alert">({{ $message }}) </span>
                                    @enderror
                                </label>
                                <input class="form-control text-center datepicker" readonly type="text" name="end"
                                    id="end">
                            </div>

                            <div class="form-group" readyonly>
                                <label for="status">สถานะ</label>
                                <select name="status" id="status" class="form-control text-center">
                                    <option value="active" selected>ปีงบประมาณปัจจุบัน</option>
                                </select>
                            </div>
                            <button type="submit" class="btn  btn-success d-table mx-auto">บันทึก</button>

                        </div>

                    </div>
                </div>
            </div>
    </form>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd/m/yyyy',
                // todayBtn: true,
                language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
                thaiyear: true //Set เป็นปี พ.ศ.
            }).datepicker(); //กำหนดเป็นวันปัจุบัน

            let d = new Date();
            let date = d.getDate();
            let month = d.getMonth() + 1;
            let year = d.getFullYear() + 543
            $('#start').val(`${date}/${month}/${year}`)
            $('#end').val(`${date}/${month}/${year}`)
            $('#status').attr('readonly')
        })
    </script>
@endsection
