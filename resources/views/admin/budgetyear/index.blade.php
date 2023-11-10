@extends('layouts.admin1')

@section('mainheader')
ตั้งค่าปีงบประมาณ
@endsection
@section('nav')
<a href="{{url('/budgetyear')}}"> รายการปีงบประมาณ</a>
@endsection
@section('budgetyear-show')
    show
@endsection
@section('nav-budgetyear')
    active
@endsection

@section('content')

<div class="card">
    <div class="card-header text-right">
        <a href="{{route('admin.budgetyear.create')}}" class="btn btn-primary">สร้างปีงบประมาณ</a>
    </div>
    <div class="card-content">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ปีงบประมาณ</th>
                    <th>วันที่เริ่มปีงบประมาณ</th>
                    <th>วันที่สิ้นสุดปีงบประมาณ</th>
                    <th>สถานะ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                  <?php $i =1;?>
                @foreach ($budgetyears as $budgetyear)
                <tr>
                    <th>{{$i++}}</th>
                    <th>{{$budgetyear->budgetyear_name}}</th>
                    <th>{{$budgetyear->startdate}}</th>
                    <th>{{$budgetyear->enddate}}</th>
                    <th>
                        <span class="right badge {{$budgetyear->status == 'active'? 'badge-success' : ''}}">
                            {{$budgetyear->status == 'inactive' ? 'สิ้นสุดปีงบประมาณ' : 'ปีงบประมาณปัจจุบัน'}}
                        </span>
                    </th>

                    <th>
                        @if ($budgetyear->status != 'inactive')
                        <a href="{{route('admin.budgetyear.edit',$budgetyear->id)}}" class="btn btn-warning">แก้ไขข้อมูล</a>
                        <a href="javascript:void(0)" data-budgetyear_id ="{{$budgetyear->id}}" class="btn btn-danger delbtn"> ลบ</a>
                        @endif
                    </th>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('script')
    <script>
        $('.delbtn').click(()=>{
            let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ ?');
            if(res === false){
                console.log('res')

                return false;
            }else{
                //หาว่ามีรอบบิลผูกกับปีงบประมาณที่จะลบหรือเปล่า
                //ถ้ามีจะไม่ยอมให้ลบ ต้องไปไล่ลบรอบบิลที่ผูกทั้งหมดก่อน
                let budgetyear_id = $('.delbtn').data('budgetyear_id');
                 console.log(budgetyear_id);
                $.get(`/api/invoice_period/check_invoice_period_by_budgetyear/${budgetyear_id}`)
                .done(function(data){
                    if(data > 0){
                        alert('ไม่สามารถทำการลบได้ \n เนื่องจากมีรอบบิลผูกกับ ปีงบประมาณนี้อยู่')
                    }else{
                        window.location.href = './invoice_period/delete/'+budgetyear_id
                    }

                    // if(data)
                });
            }

        });

    </script>
@endsection
