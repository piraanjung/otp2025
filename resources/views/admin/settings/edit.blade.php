@extends('layouts.adminlte')

@section('mainheader')
แก้ไขตั้งค่าทั่วไป
@endsection
@section('nav')
<a href="{{'settings'}}">ตั้งค่า</a>
@endsection
@section('settings')
active
@endsection
@section('style')
<style>
    .hidden {
        display: none
    }

</style>
@endsection

@section('content')
<form class="m-2" method="post" action="{{ url('settings/create_and_update') }}" enctype="multipart/form-data">
  @csrf
  <div class="card">
    <div class="card-header">
      <input type="submit" class="btn btn-info col-2" value="บันทึก">
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-5 col-sm-3">
                <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist"
                    aria-orientation="vertical">
                    <a class="nav-link active" id="organization_name-tab" data-toggle="pill" href="#organization_name"
                        role="tab" aria-controls="organization_name" aria-selected="true">ชื่อหน่วยองค์กรและหน่วยงาน</a>
                    <a class="nav-link " id="logo-tab" data-toggle="pill" href="#logo" role="tab" aria-controls="logo"
                        aria-selected="false">ตราสัญลักษณ์</a>
                    <a class="nav-link" id="address-tab" data-toggle="pill" href="#address" role="tab"
                        aria-controls="address" aria-selected="false">ที่อยู่</a>
                    <a class="nav-link" id="sign-tab" data-toggle="pill" href="#sign" role="tab" aria-controls="sign"
                        aria-selected="false">ลายเซ็นต์</a>
                    <a class="nav-link" id="meternumber-tab" data-toggle="pill" href="#meternumber" role="tab"
                        aria-controls="meternumber" aria-selected="false">รหัสเลขมิเตอร์</a>
                    <a class="nav-link" id="inv_period-tab" data-toggle="pill" href="#inv_period" role="tab"
                        aria-controls="inv_period" aria-selected="false">เกี่ยวกับใบแจ้งหนี้</a>
                </div>
            </div>
            
            <div class="col-7 col-sm-9">
                <div class="tab-content" id="vert-tabs-tabContent">
                    <div class="tab-pane text-left fade  active show " id="organization_name" role="tabpanel"
                        aria-labelledby="organization_name-tab">
                        {{-- ชื่อองค์กร --}}
                        @include('settings.organization_name', $organizations)
                    </div>

                    <div class="tab-pane fade  text-center" id="logo" role="tabpanel" aria-labelledby="logo-tab">
                        {{-- ตราสัญลักษณ์  --}}
                       
                        @include('settings.logo')
                    </div>
                    <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                        {{-- ที่อยู่ --}}
                        @include('settings.address')
                    </div>
                    <div class="tab-pane fade" id="sign" role="tabpanel" aria-labelledby="sign-tab">
                        {{-- ลายเซ็นต์ --}}
                        @include('settings.sign')
                    </div>
                    <div class="tab-pane fade" id="meternumber" role="tabpanel" aria-labelledby="meternumber-tab">
                        {{-- ลายเซ็นต์ --}}
                        @include('settings.meternumber')
                    </div>
                    <div class="tab-pane fade" id="inv_period" role="tabpanel" aria-labelledby="inv_period-tab">
                        {{-- ลายเซ็นต์ --}}
                        @include('settings.inv_period')
                    </div>

                </div>
            </div>
        </div>
    </div>
  </div>
</form>
@endsection

