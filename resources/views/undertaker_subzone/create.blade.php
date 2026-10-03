@extends('layouts.admin1') 

@section('nav-header') 
เพิ่มพื้นที่จดมิเตอร์น้ำประปา 
@endsection 

@section('topic') 
เพิ่มพื้นที่จดมิเตอร์น้ำประปา 
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

<form action="{{ url('admin/undertaker_subzone/store') }}" method="post"> 
    @csrf 
    <div class="row"> 
        <div class="col-md-3"> 
            <div class="form-group">
                <label>เลือกพนักงาน:</label>
                <select class="form-control" name="twman_id" id="twman_id" required> 
                    <option value="">-- กรุณาเลือกพนักงาน --</option>
                    @foreach ($tw_mans as $twman) 
                        <option value="{{ $twman->id }}">{{ $twman->firstname . ' ' .$twman->lastname }}</option> 
                    @endforeach 
                </select> 
            </div>

            @foreach ($tw_mans as $twman) 
            <div class="card card-widget card-warning card-twman card-outline mt-2 hidden" data-id="{{ $twman->id }}" id="card-twman{{ $twman->id }}"> 
                <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 text-center"> 
                    <a href="javascript:;" class="d-block"> 
                        <img src="{{ asset('adminlte/dist/img/user1-128x128.jpg') }}" class="img-fluid border-radius-lg"> 
                    </a> 
                </div> 
                <div class="card-body pt-2"> 
                    <span class="text-muted text-center d-block mb-2 font-weight-bold" style="font-size: 0.9rem;">พื้นที่รับผิดชอบปัจจุบัน</span>
                    <ul class="nav flex-column"> 
                        @forelse ($twman->undertaker_subzone as $item) 
                        <li class="nav-item"> 
                            <span class="nav-link py-1"> 
                                {{ $item->subzone->zone->zone_name ?? '-' }} 
                                <span class="float-right badge bg-info">เส้น: {{ $item->subzone->subzone_name ?? '-' }}</span> 
                            </span> 
                        </li> 
                        @empty 
                        <li class="text-center text-muted text-sm py-2">ยังไม่มีพื้นที่รับผิดชอบ</li>
                        @endforelse 
                    </ul> 
                </div> 
            </div> 
            @endforeach 
        </div><!--col-md-3--> 

        <div class="col-md-9"> 
            <div class="card card-primary card-outline"> 
                <div class="card-header"> 
                    <div class="card-title h-4"> เลือกเส้นทางจัดเก็บค่าน้ำประปาใหม่ </div> 
                    <div class="card-tools"> 
                        <input type="submit" class="btn btn-primary" value="บันทึก"> 
                    </div> 
                </div> 
                <div class="card-body p-2"> 
                    <div class="row"> 
                        @forelse ($subzone as $item) 
                        <div class="col-4 mb-2"> 
                            <div class="card card-outline card-primary h-100 mb-0"> 
                                <div class="card-header py-2"> 
                                    <h3 class="card-title font-weight-bold" style="font-size: 0.95rem;"> {{ $item->zone_name }}</h3> 
                                    <div class="card-tools"></div> 
                                </div> 
                                <!-- /.card-header --> 
                                <div class="card-body p-1"> 
                                    <div class="row align-items-center"> 
                                        <div class="col-md-9 text-center pt-2 pb-2"> 
                                            <span class="text-primary"><i class="fas fa-road"></i></span> 
                                            <span class="h6 font-weight-bold">{{ $item->subzone_name }}</span> 
                                        </div> 
                                        <div class="col-md-3 text-center"> 
                                            <input type="checkbox" name="on[{{ $item->zone_id . '-' . $item->subzone_id }}]" style="width: 25px; height: 25px; cursor: pointer;" id="{{ 'sz' . $item->subzone_id }}"> 
                                        </div> 
                                    </div> 
                                </div> 
                                <!-- /.card-body --> 
                            </div> 
                        </div> 
                        @empty 
                        <div class="col-12 text-center py-4 text-muted">
                            ไม่มีเส้นทางที่สามารถเลือกได้ (ถูกเลือกครบหมดแล้ว)
                        </div>
                        @endforelse 
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
            $('.card-twman').addClass('hidden'); 
        } else { 
            $('.card-twman').addClass('hidden');$(`#card-twman${id}`).removeClass('hidden'); 
        } 
    }); 
</script> 
@endsection