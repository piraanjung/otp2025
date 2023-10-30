@extends('layouts.admin1')

@section('mainheader')
แก้ไขปีงบประมาณ
@endsection
@section('budgetyear')
    active
@endsection
@section('style')
<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
        crossorigin="anonymous"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
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
            min-width: 160px;
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
        .datepicker {
            top: 3px;
            left: 850.75px !important;
            display: block;
        }
</style>

@endsection
@section('nav')
<a href="{{url('/budgetyear')}}"> รายการปีงบประมาณ</a>
@endsection
@section('content')
<form action="{{route('admin.budgetyear.update',$budgetyear->id)}}" method="POST">
@csrf
@method('PUT')
        <div class="row no-gutters h-100">
            <div class="col-lg-3 col-md-5 auth-form mx-auto my-auto">
                <div class="card">
                    <div class="card-body">

                            <div class="form-group">
                                <label for="budgetyear">ปีงบประมาณ</label>
                                <input type="text" class="form-control text-center" id="budgetyear" name="budgetyear" value="{{$budgetyear->budgetyear}}"
                                    placeholder="ตัวอย่าง 2563">
                            </div>
                            <div class="form-group">
                                <label for="startdate">วันที่เริ่มปีงบประมาณ</label>
                                <div id="blog-overview-date-range">
                                    <input class="form-control text-center datepicker" type="text" name="startdate" id="startdate"
                                    id="blog-overview-date-range-1"  readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword2">วันที่สิ้นสุดปีงบประมาณ</label>
                                <input class="form-control text-center datepicker" type="text" name="enddate" id="enddate" value="" readonly>

                            </div>

                            <div class="form-group">
                            <label for="status">สถานะ</label>
                            <input class="form-control text-center" type="text" value="{{$budgetyear->status == 'active' ? 'ปีงบประมาณปัจจุบัน' : 'สิ้นสุดปีงบประมาณ'}}" readonly>

                            </div>
                            <button type="submit"
                                class="btn btn-success d-table mx-auto">บันทึก</button>

                    </div>

                </div>
            </div>
        </div>
</form>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            // todayBtn: true,
            language: 'th',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
            thaiyear: true              //Set เป็นปี พ.ศ.
        }).datepicker();  //กำหนดเป็นวันปัจุบัน

        let d = new Date();
        let date = d.getDate();
        let month = d.getMonth()+1;
        let year = d.getFullYear()+543
        let startdate = "<?=$budgetyear->startdate?>"
        let enddate = "<?=$budgetyear->enddate?>"
        console.log('startdate', startdate)
        let start = startdate.split('/')
        let end = enddate.split('/')
        $('#startdate').val(`${start[0]}/${parseInt(start[1])}/${start[2]}`)
        $('#enddate').val(`${end[0]}/${parseInt(end[1])}/${end[2]}`)
        $('#status').attr('readonly')
    })
</script>
@endsection
