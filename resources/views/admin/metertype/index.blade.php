@extends('layouts.admin1')

@section('mainheader')
    ขนาดมิเตอร์
@endsection
@section('nav')
    <a href="{{ url('tabwatermeter') }}"> ขนาดมิเตอร์</a>
@endsection
@section('tabwatermeter')
    active
@endsection
@section('content')
    <div class="row">

        <div class="col-12">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom flex">
                    <a href="{{ route('admin.metertype.create') }}" class="btn btn-primary fa-pull-right">เพิ่มข้อมูล</a>
                </div>
                @if (collect($metertypes)->isEmpty())
                    <h4 class="text-center mt-3">ยังไม่มีข้อมูลขนาดมิเตอร์</h4>
                @else
                    <div class="card-body pb-3 text-center">
                        <?php $order = 1; ?>

                        <div class="card">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                ประเภทมิเตอร์</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                ขนาดมิเตอร์ (นิ้ว)</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                ราคาต่อหน่วย (บาท)</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                วันที่บันทึก</th>
                                            <th class=""></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($metertypes as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2">
                                                        <div>
                                                            <img src="https://demos.creative-tim.com/soft-ui-design-system-pro/assets/img/logos/small-logos/logo-spotify.svg"
                                                                class="avatar avatar-sm rounded-circle me-2">
                                                        </div>
                                                        <div class="my-auto">
                                                            <h6 class="mb-0 text-xs">{{ $item->meter_type_name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $item->metersize }}</p>
                                                </td>
                                                <td>
                                                    <span class="badge badge-dot me-4">
                                                        <span class="text-dark text-xs">{{ $item->price_per_unit }}</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-dot me-4">
                                                        <span
                                                            class="text-dark text-xs">{{ date_format($item->created_at, date('d-m-Y')) }}</span>
                                                    </span>
                                                </td>

                                                <td class="align-middle">
                                                    <div class="dropstart float-lg-end ms-auto pe-0">
                                                        <a href="javascript:;" class="cursor-pointer" id="dropdownTable2"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa fa-ellipsis-h text-secondary" aria-hidden="true"></i>
                                                        </a>
                                                        <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                            aria-labelledby="dropdownTable2" style="">
                                                            <li><a class="dropdown-item" href="{{ route('admin.metertype.edit', $item->id) }}">แก้ไขข้อมูล</a></li>
                                                            <li><a class="dropdown-item" href="#">ลบข้อมูล</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
                    .done(function(data) {
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
