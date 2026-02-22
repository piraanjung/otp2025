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
@section('style')
<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}">
</script>
@endsection
@section('invoice_period')
    active
@endsection
@section('nav')
    <a href="{{ url('/invoice_period') }}"> สร้างรอบบิล</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <form action="{{ route('admin.invoice_period.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label>ปีงบประมาณ</label>
                            <input class="form-control text-center bg-gray-200" type="text"
                                value="{{ $budgetyear->budgetyear_name }}" placeholder="" readonly>
                            <input class="form-control text-center" type="text" name="budgetyear_id"
                                value="{{ $budgetyear->id }}" hidden>
                        </div>
                        <div class="form-group">
                            <label>รอบบิลประจำเดือน</label>
                            <div class="row">
                                <div class="col-5">
                                    <input class="form-control text-center" type="text" name="inv_period_name"
                                        value="{{ date('m') }}" id="inv_period_name" placeholder="01">
                                </div>
                                <div class="col-2 text-center h3">-</div>
                                <div class="col-5">
                                    <input class="form-control text-center bg-gray-200" type="text" name="inv_period_name_year"
                                        value="{{ substr(date('Y') + 543, 2) }}" readonly>
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

        $('.submit_btn').click(function() {
            $('.card').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>')
        })
    </script>
@endsection
