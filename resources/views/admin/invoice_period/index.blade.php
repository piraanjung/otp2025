@extends('layouts.admin1')

@section('mainheader')
สร้างรอบบิล
@endsection

@section('invoice_period')
    active
@endsection
@section('nav')
<a href="{{url('/invoice_period')}}"> สร้างรอบบิล</a>
@endsection
@section('content')
@if ($message = Session::get('message'))
<div class="alert alert-warning alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>{{ $message }}</strong>
</div>
@endif


@if ($budgetyearCount == 0)

    <div class="col-md-5 col-sm-6 col-12">
        <div class="info-box bg-warning">
        <span class="info-box-icon"><i class="fas fa-comments"></i></span>

        <div class="info-box-content">
            {{-- <span class="info-box-text">Events</span> --}}
            <span class="info-box-number text-center font-weight-bold">ยังไม่ได้สร้างปีงบประมาณ</span>

            <div class="progress">
            <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description text-center">
            <a href="{{ url('/budgetyear') }}" class="btn btn-info btn-sm">สร้างปีงบประมาณ</a>
            </span>
        </div>
        <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>

@else
    <div class="card p-2">
        <div class="card-header text-right">
            <a href="{{url('/invoice_period/create')}}" class="btn btn-primary">สร้างรอบบิล</a>
        </div>
        <div class="card-content">
            <table class="table example">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>รอบบิล</th>
                        <th>ปีงบประมาณ</th>
                        <th>วันที่เริ่มรอบบิล</th>
                        <th>วันที่สิ้นสุดรอบบิล</th>
                        <th>สถานะ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i =1;?>
                    @foreach ($invoice_periods as $invoice_period)
                    <tr class="{{$invoice_period->status == 'active' ? 'bg-success': ''}}">
                        <th>{{$i++}}</th>
                        <th>{{$invoice_period->inv_period_name}}</th>
                        <th>{{$invoice_periods[0]->budgetyear->budgetyear}}</th>
                        <th>{{$invoice_period->startdate}}</th>
                        <th>{{$invoice_period->enddate}}</th>
                        <th>
                            <span class="right badge {{$invoice_period->status == 'active' ? 'badge-success' : 'badge-primary'}}">
                                {{$invoice_period->status == 'inactive' ? 'สิ้นสุดรอบบิล' : 'รอบบิลปัจจุบัน'}}
                            </span>
                        </th>
                        <th>
                            @if ($invoice_period->status != 'inactive' || $i == 2)
                            <a href="{{url('/invoice_period/edit/'.$invoice_period->id)}}" class="btn btn-warning">แก้ไขข้อมูล</a>
                            <a href="javascript:void(0)" data-invoice_period={{ $invoice_period->id }} class="btn btn-danger delbtn">ลบ</a>
                            @endif
                        </th>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endif
@endsection

@section('script')
    <script>
        $(document).ready(()=>{
            setTimeout(()=>{
            $('.alert').toggle('slow')
          }, 1500)
        })

        $('.delbtn').click(()=>{
            let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ ?')
            if(res === true){
                //หาว่ามี invoice ผูกกับ รอบบิลนี้หรือเปล่า
                //ถ้ามี ไม่ให้ลบรอบบิลนี้ ต้องไปลบ invoice ที่ถูกให้หมดก่อน
                let inv_period_id = $('.delbtn').data('invoice_period')
                $.get('/api/invoice/checkInvoice/'+inv_period_id)
                .done(function(data){
                    if(data > 0){
                        alert('ไม่สามารถลบข้อมูลได้ \n เนื่องจากมีใบแจ้งหนี้ผูกกับรอบบิลนี้อยู่')
                    }else{
                        window.location.href = './invoice_period/delete/'+inv_period_id
                    }
                });
            }
        })


    </script>
@endsection
