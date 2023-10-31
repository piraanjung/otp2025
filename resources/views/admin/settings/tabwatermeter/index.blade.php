@extends('layouts.adminlte')

@section('mainheader')
ขนาดมิเตอร์
@endsection
@section('nav')
<a href="{{url('tabwatermeter')}}"> ขนาดมิเตอร์</a>
@endsection
@section('tabwatermeter')
active
@endsection
@section('content')
<div class="row">

    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom flex">
                <a href="{{url('tabwatermeter/create')}}" class="btn btn-primary fa-pull-right">เพิ่มข้อมูล</a>
            </div>
            @if (collect($tabwatermeters)->isEmpty())

            <h4 class="text-center mt-3">ยังไม่มีข้อมูลขนาดมิเตอร์</h4>

            @else
            <div class="card-body p-0 pb-3 text-center">
                <?php $order = 1; ?>
                <table class="table table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="border-0">#</th>
                            <th scope="col" class="border-0">ประเภทมิเตอร์</th>
                            <th scope="col" class="border-0">ขนาดมิเตอร์ (นิ้ว)</th>
                            <th scope="col" class="border-0">ราคาต่อหน่วย (บาท)</th>
                            <th scope="col" class="border-0">วันที่บันทึก</th>
                            <th scope="col" class="border-0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tabwatermeters as $item)
                        <tr>
                            <td>{{$order++}}</td>
                            <td class="text-left">{{$item->typemetername}}</td>
                            <td>{{$item->metersize}}</td>
                            <td>{{$item->price_per_unit}}</td>
                            <td>{{$item->created_at}}</td>
                            <td>
                                <a href="{{url('tabwatermeter/'.$item->id.'/edit')}}" class="btn btn-warning">แก้ไข</a>
                                <button onclick="deleteData({{$item->id}})" class="btn btn-danger">ลบ</button>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>


</div>
@endsection


@section('script')
<script>
    function deleteData(del_id) {
        var r = confirm("ต้องการลบข้อมูลใช่หรือไม่!");
        if (r == true) {
            //หาว่ามีผูกใน user_meter_infos table ไหม
            $.get('/api/tabwatermeter/checkTabwatermeterMatchedUserMeterInfos/' + del_id)
                .done(function (data) {
                    if (data == 0) {
                        $.get('/tabwatermeter/' + del_id + '/delete').then((res) => {
                            alert('ทำการลบข้อมูลเรียบร้อย')
                            setTimeout(() => {
                                window.location.reload()
                            }, 500)

                        })
                    } else {
                        alert(`ไม่สามารถลบได้เนื่องจากมีการใช้ข้อมูลขนาดมิเตอร์นี้`)
                    }
                })
        }
    }

</script>
@endsection
