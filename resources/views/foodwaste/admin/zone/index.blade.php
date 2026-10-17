@extends('layouts.admin1')

@section('mainheader')
    พื้นที่จดมิเตอร์น้ำประปา
@endsection
@section('zone')
    active
@endsection
@section('nav')
    <a href="{{ url('/users') }}"> พื้นที่จดมิเตอร์น้ำประปา</a>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.zone.create') }}" class="btn btn-primary my-4 col-2 "><i
                    class="fas fa-plus-circle"></i>สร้างพื้นที่จดมิเตอร์</a>
            <div class="table-responsive">
                <div class="row">
                    @foreach ($zones as $item)
                        <div class="col-12 col-sm-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"> {{ $item->zone_name }}</h3>

                                    <div class="card-tools">
                                        <a href="{{ route('admin.subzone.edit', $item->id) }}" class="badge bg-gradient-primary mr-1"
                                            placeholder="เพิ่มเส้นทาง">เพิ่มเส้นทาง</a>

                                            <a href="{{ route('admin.subzone.edit' , $item->id) }}"
                                                class="badge bg-gradient-warning">แก้ไข</a>
                                                <form action="{{route('admin.zone.destroy', $item->id)}}" method="post">
                                                    @csrf
                                                    @method("delete")
                                                    <button data-zone_id={{ $item->id }} type="submit"
                                                        class="badge bg-gradient-danger">ลบ</button>
                                                </form>

                                            <a type="button" class="badge bg-gradient-light" data-card-widget="collapse"><i
                                                    class="fas fa-minus"></i></a>
                                    </div>
                                    <!-- /.card-tools -->
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <ul>
                                        @foreach ($item->subzone as $subzone)
                                            <li><span class="text-primary"><i class="fas fa-road"></i></span>
                                                {{ $subzone['subzone_name'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.delbtn').click(function() {
            //หา ว่า subzone มีใน user_meter_infos table ไหม
            var res = false
            var zone_id = $(this).data('zone_id');

                    $.get('api/zone/delete/' + zone_id, function(data) {
                        console.log(data)
                        if (data == 1) {
                            alert('ทำการลบข้อมูลเรียบร้อยแล้ว')
                            location.reload();
                        }
                    })


        });

        $(document).ready(() => {
            setTimeout(() => {
                $('.alert').toggle('slow')
            }, 2000)
        })
    </script>
@endsection
