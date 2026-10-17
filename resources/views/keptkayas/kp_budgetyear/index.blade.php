@extends('layouts.keptkaya')

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
            <a href="{{route('keptkayas.kp_budgetyear.create')}}" class="btn btn-primary">สร้างปีงบประมาณ</a>
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
                    <?php $i = 1;?>
                    @foreach ($budgetyears as $budgetyear)
                        <tr>
                            <th>{{$i++}}</th>
                            <th>{{$budgetyear->budgetyear_name}}</th>
                            <th>{{$budgetyear->startdate}}</th>
                            <th>{{$budgetyear->enddate}}</th>
                            <th>
                                <span class="right badge {{$budgetyear->status == 'active' ? 'badge-success' : ''}}">
                                    {{$budgetyear->status == 'inactive' ? 'สิ้นสุดปีงบประมาณ' : 'ปีงบประมาณปัจจุบัน'}}
                                </span>
                            </th>
                            <td class="align-middle">
                                <ul class="navbar-nav">
                                    <li class="nav-item dropdown">
                                        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                                            <i class="far fa-comments"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"
                                            style="left: inherit; right: 0px;">

                                            <ul class="navbar-nav">
                                                <li><a class="dropdown-item text-center"
                                                        href="{{ route('keptkayas.kp_budgetyear.edit', $budgetyear->id) }}">แก้ไขข้อมูล</a>
                                                </li>
                                                <li>
                                                    @if ($budgetyear->deleted == 0)
                                                        <form
                                                            action="{{ route('keptkayas.kp_budgetyear.destroy', $budgetyear->id) }}"
                                                            method="Post">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a class="dropdown-item text-center test"
                                                                href="javascript:test()">ลบขัอมูล</a>
                                                        </form>
                                                    @endif

                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>

                            </td>

                            {{-- <th class="d-flex">
                                @if ($budgetyear->status != 'inactive')
                                <a href="{{route('keptkayas.kp_budgetyear.edit',$budgetyear->id )}}"
                                    class="btn btn-warning">แก้ไขข้อมูล</a>
                                <form action="{{ route('keptkayas.kp_budgetyear.destroy', $budgetyear->id) }}" method="post"
                                    class="ml-auto">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger delbtn">ลบ</button>
                                </form>
                                @endif
                            </th> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $('.delbtn').click(() => {
            let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ ?');
            if (res === false) {
                console.log('res')

                return false;
            } else {
                //หาว่ามีรอบบิลผูกกับปีงบประมาณที่จะลบหรือเปล่า
                //ถ้ามีจะไม่ยอมให้ลบ ต้องไปไล่ลบรอบบิลที่ผูกทั้งหมดก่อน
                let budgetyear_id = $('.delbtn').data('budgetyear_id');
                console.log(budgetyear_id);
                $.get(`/api/invoice_period/check_invoice_period_by_budgetyear/${budgetyear_id}`)
                    .done(function (data) {
                        if (data > 0) {
                            alert('ไม่สามารถทำการลบได้ \n เนื่องจากมีรอบบิลผูกกับ ปีงบประมาณนี้อยู่')
                        } else {
                            window.location.href = './invoice_period/delete/' + budgetyear_id
                        }

                        // if(data)
                    });
            }

        });

    </script>
@endsection