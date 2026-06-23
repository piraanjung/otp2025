@extends('layouts.super-admin')
@section('nav-main')
    Import Excels
@endsection
@section('nav-main-url')
    {{-- {{route('adsettings.dashboard')}} --}}
@endsection
@section('nav-current')
    Meter Types Table
@endsection
@section('nav-current-title')
    Meter Types Table
@endsection
@section('content')
    <div class="row mb-5">
        <div class="col-lg-3">
            <div class="card position-sticky top-1">
                <ul class="nav flex-column bg-white border-radius-lg border border-warning m-1 p-1">
                    <li class="nav-item">
                        <a class="nav-link text-body" data-scroll="" href="#province-data">
                            <div class="icon me-2">
                              <i class="fa-solid fa-camera"></i>
                            </div>
                            <span class="text-sm">Provinces Data</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-body" data-scroll="" href="#district-data">
                            <div class="icon me-2">
                               <i class="fa-solid fa-camera"></i>
                            </div>
                            <span class="text-sm">Districts Data</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-body" data-scroll="" href="#tambon-data">
                            <div class="icon me-2">
                              <i class="fa-solid fa-camera"></i>
                            </div>
                            <span class="text-sm">Tambons Data</span>
                        </a>
                    </li>
                    
                     <li class="nav-item pt-2">
                        <a class="nav-link text-body" data-scroll="" href="#organization-data">
                            <div class="icon me-2">
                              <i class="fa-solid fa-camera"></i>
                            </div>
                            <span class="text-sm">Organizations Data</span>
                        </a>
                    </li>

                     <li class="nav-item pt-2">
                        <a class="nav-link text-body" data-scroll="" href="#users-data">
                            <div class="icon me-2">
                              <i class="fa-solid fa-camera"></i>
                            </div>
                            <span class="text-sm">Users Data</span>
                        </a>
                    </li>
                   
                </ul>
            
                <ul class="nav flex-column bg-white border-radius-lg border border-warning m-1 p-1">
                   
                     <li class="nav-item pt-2">
                        <a class="nav-link text-body" data-scroll="" href="#tw-zone-data">
                            <div class="icon me-2">
                              <i class="fa-solid fa-camera"></i>
                            </div>
                            <span class="text-sm">Tabwater Zones Data</span>
                        </a>
                    </li>
                     <li class="nav-item pt-2">
                        <a class="nav-link text-body" data-scroll="" href="#tw-zoneblock-data">
                            <div class="icon me-2">
                              <i class="fa-solid fa-camera"></i>
                            </div>
                            <span class="text-sm">Tabwater ZoneBlocks Data</span>
                        </a>
                    </li>
                   
                </ul>
                <ul class="nav flex-column bg-white border-radius-lg border border-warning m-1 p-1">
                   
                     <li class="nav-item pt-2">
                        <a class="nav-link text-body" data-scroll="" href="#tw-meters-data">
                            <div class="icon me-2">
                              <i class="fa-solid fa-camera"></i>
                            </div>
                            <span class="text-sm">Meters Data</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-9 mt-lg-0 mt-4">
            <!-- Card Profile -->
            <div class="card" id="province-data">
                <div class="card-header">
                    <h5>Provinces Data</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Import Provinces (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Import Provinces Data</h5>
                            <form action="{{ route('admin.settings.import.provinces') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="provinces_excel_file" class="form-control-label">Choose Provinces Excel
                                        File:</label>
                                    <input type="file" name="provinces_excel_file" class="form-control"
                                        id="provinces_excel_file" accept=".xls,.xlsx" required>
                                </div>
                                <button type="submit" class="btn btn-sm bg-gradient-danger my-sm-auto mt-2 mb-0">Import
                                    Provinces</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Export Provinces (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Export Provinces Data</h5>
                            <div class="form-group">
                                <label for="provinces_excel_file" class="form-control-label">Export Provinces Excel</label>
                                <a href="{{ route('admin.settings.export.provinces') }}"
                                    class="form-control btn btn-sm bg-gradient-success my-sm-auto mt-2 mb-0">Download Provinces Excel</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- Card Basic Info -->
            <div class="card mt-4" id="district-data">
                <div class="card-header">
                    <h5>Districts</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Import Provinces (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Import Districts Data</h5>
                            <form action="{{ route('admin.settings.import.districts')}}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="provinces_excel_file" class="form-control-label">Choose Districts Excel File:</label>
                                    <input type="file" name="districts_excel_file" class="form-control" id="districts_excel_file" accept=".xls,.xlsx" required>
                                </div>
                                <button type="submit" class="btn btn-sm bg-gradient-danger my-sm-auto mt-2 mb-0">Import Districts</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Export Provinces (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Export Districts Data</h5>
                            <div class="form-group">
                                <label for="provinces_excel_file" class="form-control-label">Export Districts Excel</label>
                                <a href="{{ route('admin.settings.export.districts') }}"
                                    class="form-control btn btn-sm bg-gradient-success my-sm-auto mt-2 mb-0">Download Districts Excel</a>
                            </div>

                        </div>
                    </div>
                </div>
      
            </div>
            <!-- Card Change Password -->
            <div class="card mt-4" id="tambon-data">
                <div class="card-header">
                    <h5>Tambon</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Import tambons (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Import Tambons Data</h5>
                            <form action="{{ route('admin.settings.import.tambons')}}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="tambons_excel_file" class="form-control-label">Choose Tambons Excel File:</label>
                                    <input type="file" name="tambons_excel_file" class="form-control" id="tambons_excel_file" accept=".xls,.xlsx" required>
                                </div>
                                <button type="submit" class="btn btn-sm bg-gradient-danger my-sm-auto mt-2 mb-0">Import Tambons</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Export Provinces (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Export Tambons Data</h5>
                            <div class="form-group">
                                <label for="provinces_excel_file" class="form-control-label">Export Tambons Excel</label>
                                <a href="{{ route('admin.settings.export.tambons') }}"
                                    class="form-control btn btn-sm bg-gradient-success my-sm-auto mt-2 mb-0">Download Tambons Excel</a>
                            </div>

                        </div>
                    </div>
                </div>
      
            </div>

             <div class="card mt-4" id="tw-zone-data">
                <div class="card-header">
                    <h5>Tabwater Zone</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5>Import Tabwater Zone Data</h5>
                            <form action="{{ route('admin.settings.import.tw_zones')}}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="tw_zones_excel_file" class="form-control-label">Choose Tabwater Zones Excel File:</label>
                                    <input type="file" name="tw_zones_excel_file" class="form-control" id="tw_zones_excel_file" accept=".xls,.xlsx" required>
                                </div>
                                <button type="submit" class="btn btn-sm bg-gradient-danger my-sm-auto mt-2 mb-0">Import Tabwater Zones</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Export Provinces (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Export Tabwater Zones Data</h5>
                            <div class="form-group">
                                <label for="provinces_excel_file" class="form-control-label">Export Tabwater Zones Excel</label>
                                <a href="{{ route('admin.settings.export.tw_zones') }}"
                                    class="form-control btn btn-sm bg-gradient-success my-sm-auto mt-2 mb-0">Download Tabwater Zones Excel</a>
                            </div>

                        </div>
                    </div>
                </div>
      
            </div>
            <div class="card mt-4" id="tw-zoneblock-data">
                <div class="card-header">
                    <h5>Tabwater Zone Block</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5>Import Tabwater Zone Block Data</h5>
                            <form action="{{ route('admin.settings.import.tw_zoneblocks')}}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="tw_zoneblocks_excel_file" class="form-control-label">Choose Tabwater Zone Block Excel File:</label>
                                    <input type="file" name="tw_zoneblocks_excel_file" class="form-control" id="tw_zoneblocks_excel_file" accept=".xls,.xlsx" required>
                                </div>
                                <button type="submit" class="btn btn-sm bg-gradient-danger my-sm-auto mt-2 mb-0">Import Tabwater Zone Block</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Export Provinces (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Export Tabwater Zone Block Data</h5>
                            <div class="form-group">
                                <label for="provinces_excel_file" class="form-control-label">Export Tabwater Zone Block Excel</label>
                                <a href="{{ route('admin.settings.export.tw_zoneblocks') }}"
                                    class="form-control btn btn-sm bg-gradient-success my-sm-auto mt-2 mb-0">Download Tabwater Zone Block Excel</a>
                            </div>

                        </div>
                    </div>
                </div>
      
            </div>
             <div class="card mt-4" id="organization-data">
                <div class="card-header">
                    <h5>Organizations Block</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5>Import Organizations Data</h5>
                            <form action="{{ route('admin.settings.import.organizations')}}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="organizations_excel_file" class="form-control-label">Choose Organizations Excel File:</label>
                                    <input type="file" name="organizations_excel_file" class="form-control" id="organizations_excel_file" accept=".xls,.xlsx" required>
                                </div>
                                <button type="submit" class="btn btn-sm bg-gradient-danger my-sm-auto mt-2 mb-0">Import Organizations</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Export Provinces (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Export Organizations Data</h5>
                            <div class="form-group">
                                <label for="provinces_excel_file" class="form-control-label">Export Organizations Excel</label>
                                <a href="{{ route('admin.settings.export.organizations') }}"
                                    class="form-control btn btn-sm bg-gradient-success my-sm-auto mt-2 mb-0">Download Organizations Excel</a>
                            </div>

                        </div>
                    </div>
                </div>
      
            </div>

             <div class="card mt-4" id="users-data">
                <div class="card-header">
                    <h5>Organizations Block</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5>Import Users Data</h5>
                            <form action="{{ route('admin.settings.import.users')}}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="users_excel_file" class="form-control-label">Choose users Excel File:</label>
                                    <input type="file" name="users_excel_file" class="form-control" id="users_excel_file" accept=".xls,.xlsx" required>
                                </div>
                                <button type="submit" class="btn btn-sm bg-gradient-danger my-sm-auto mt-2 mb-0">Import users</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Export Provinces (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Export users Data</h5>
                            <div class="form-group">
                                <label for="provinces_excel_file" class="form-control-label">Export users Excel</label>
                                <a href="{{ route('admin.settings.export.users') }}"
                                    class="form-control btn btn-sm bg-gradient-success my-sm-auto mt-2 mb-0">Download users Excel</a>
                            </div>

                        </div>
                    </div>
                </div>
      
            </div>

            <div class="card mt-4" id="tw-meters-data">
                <div class="card-header">
                    <h5>Meters</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5>Import Meters Data</h5>
                            <form action="{{ route('admin.settings.import.tw_meters')}}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="tw_meters_excel_file" class="form-control-label">Choose Meters Excel File:</label>
                                    <input type="file" name="tw_meters_excel_file" class="form-control" id="tw_meters_excel_file" accept=".xls,.xlsx" required>
                                </div>
                                <button type="submit" class="btn btn-sm bg-gradient-danger my-sm-auto mt-2 mb-0">Import Meters</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            {{-- ส่วนสำหรับ Export Provinces (จากตัวอย่างก่อนหน้า) --}}
                            <h5>Export Meters Data</h5>
                            <div class="form-group">
                                <label for="provinces_excel_file" class="form-control-label">Export Meters Excel</label>
                                <a href="{{ route('admin.settings.export.tw_meters') }}"
                                    class="form-control btn btn-sm bg-gradient-success my-sm-auto mt-2 mb-0">Download Meters Excel</a>
                            </div>

                        </div>
                    </div>
                </div>
      
            </div>
          
        </div>
    </div>
    {{-- ส่วนแสดงข้อความแจ้งเตือน (Success/Error) --}}


@endsection






