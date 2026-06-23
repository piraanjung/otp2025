@extends('layouts.admin1')

@section('nav-header')
    แก้ไขพื้นที่จดมิเตอร์น้ำประปา
@endsection
@section('topic')
    แก้ไขพื้นที่จดมิเตอร์น้ำประปา
@endsection
@section('nav_undertaker-subzone')
    active
@endsection
@section('nav-current')
    <a href="{{ url('/undertaker_subzone') }}"> พื้นที่จดมิเตอร์น้ำประปา</a>
@endsection
@section('content')
    @if (Session::has('message'))
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-info"></i> {{ Session::get('message') }}</h5>

        </div>
    @endif

    <form action="{{ url('undertaker_subzone/store') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <select class="form-control" name="twman_id" id="twman_id">
                    @foreach ($tw_mans as $twman)
                        <option value="{{ $twman->id }}" selected>{{ $twman->firstname . ' ' . $twman->lastname }}</option>
                    @endforeach
                </select>
                @foreach ($tw_mans as $twman)
                <div class="card card-widget card-warning card-twman card-outline mt-2" data-id="{{ $twman->id }}"
                  id="card-twman{{ $twman->id }}">
                        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 text-center">
                            <a href="javascript:;" class="d-block">
                                <img src="{{ asset('adminlte/dist/img/user1-128x128.jpg') }}"
                                    class="img-fluid border-radius-lg">
                            </a>
                        </div>
                        <div class="card-body pt-2">
                         
                         
                                <ul class="nav flex-column">
    
                                    @foreach ($twman->undertaker_subzone as $item)
                                        <li class="nav-item">
                                            <a href="#" class="nav-link">
                                                <a href="#" class="btn btn-sm btn-danger del_subzone_btn"
                                                    data-id="{{ $item->id }}">ลบ</a>
    
                                                {{ $item->subzone->zone->zone_name }}
                                                <span class="float-right badge bg-info">เส้น:
                                                    {{ $item->subzone->subzone_name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
    
                                </ul>
                        
                        </div>
                    </div>
                  
                @endforeach
            </div><!--col-md-3-->
            <div class="col-md-9">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <div class="card-title h-4">
                            เลือกเส้นทางจัดเก็บค่าน้ำประปา
                        </div>
                        <div class="card-tools">
                            <input type="submit" class="btn btn-primary" value="บันทึก">
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            @foreach ($zone as $item)
                                <div class="col-4 mb-2">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title"> {{ $item->zone_name }}</h3>
                                            <div class="card-tools">

                                            </div>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body  p-1">
                                            <div class="row">
                                                <div class="col-md-9 text-center pt-3">
                                                    <span class="text-primary"><i class="fas fa-road"></i></span>
                                                    <span class="h5">{{ $item->subzone_name }}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="checkbox"
                                                        name="on[{{ $item->zone_id . '-' . $item->subzone_id }}]"
                                                        style="width: 30px; height:50px" id="{{ 'sz' . $item->subzone_id }}">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>
                            @endforeach
                        </div><!--col-md-9>row-->
                    </div><!--card-body-->
                </div>
            </div><!--col-md-9-->
        </div><!--row-->
    </form>
@endsection


@section('script')
    <script>
        $('#twman_id').change(function() {
            let id = $(this).val();
            if (id == "") {
                $('.card-twman').removeClass('hidden');
            } else {
                $('.card-twman').addClass('hidden');
                $(`#card-twman${id}`).removeClass('hidden');
            }
        });


        //ทำการลบ เส้นทางที่รับผิดชอบอยู่เดิม
        $('.del_subzone_btn').click(function() {
            let undertaker_subzone_id = $(this).data('id');

            var r = confirm("คุณต้องการลบ เส้นทางจัดเก็บน้ำประปาเดิม ใช่หรือไม่ !!!!");
            if (r == true) {
                $.get(`/undertaker_subzone/delete/${undertaker_subzone_id}`).done(function(data) {
                    window.location.reload();
                });
            }
        });
    </script>
@endsection
