@extends('layouts.admin1')

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
    <div class="row">
        <div class="row-12">
            <div class="card">
                <div class="card-body">
                    <form class="col-3 sm-auto" action="{{ route('admin.invoice_period.update', $invoice_period->id) }}" method="post">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>ปีงบประมาณ</label>
                            <input class="form-control text-center" type="text" name="budgetyear_name"
                                value="{{ $invoice_period['budgetyear']->budgetyear }}" placeholder="" readonly>
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
