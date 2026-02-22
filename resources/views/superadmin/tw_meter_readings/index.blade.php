@extends('layouts.admin1')
@section('nav-main')
    Manage Meter Readings
@endsection
@section('nav-main-url')
    {{route('superadmin.budget_years.index')}}
@endsection
@section('nav-current')
Meter Readings
@endsection
@section('nav-current-title')
    Meter Readings
@endsection


@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Meter Readings Management</h1>
        {{-- <a href="{{ route('superadmin.tw_meter_readings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Reading
        </a> --}}
    </div>



    {{-- Filter Form --}}
    {{-- <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filter Readings</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.tw_meter_readings.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="period_id" class="form-label">Period:</label>
                        <select name="period_id" id="period_id" class="form-select">
                            <option value="">All Periods</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->period_name }} ({{ $period->budgetYear->year ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="meter_id" class="form-label">Meter Code:</label>
                        <select name="meter_id" id="meter_id" class="form-select">
                            <option value="">All Meters</option>
                            @foreach ($meters as $meter)
                                <option value="{{ $meter->id }}" {{ request('meter_id') == $meter->id ? 'selected' : '' }}>
                                    {{ $meter->meter_code }} - {{ $meter->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary me-2">Apply Filter</button>
                        <a href="{{ route('superadmin.tw_meter_readings.index') }}" class="btn btn-outline-secondary">Clear Filter</a>
                    </div>
                </div>
            </form>
        </div>
    </div> --}}

    {{-- Global Summary Section --}}
    <div class="card mb-4 shadow-sm border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Overall Meter Reading Summary</h5>
        </div>
        <div class="card-body">
            @if (!$selectedPeriodId)
                <div class="alert alert-info text-center" role="alert">
                    Please select a <strong>Period</strong> in the filter above to see overall reading status.
                </div>
            @else
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded h-100 d-flex flex-column justify-content-center align-items-center">
                            <i class="bi bi-people-fill h3 text-primary"></i>
                            <h6 class="mb-1">Total Active Meters:</h6>
                            {{-- <a href="{{ route('superadmin.tw_meters.index', ['status' => 'active']) }}" class="btn btn-lg btn-outline-primary mt-2"> --}}
                                {{ $totalActiveMetersCount }}
                            {{-- </a> --}}
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded h-100 d-flex flex-column justify-content-center align-items-center">
                            <i class="bi bi-check-circle-fill h3 text-success"></i>
                            <h6 class="mb-1">Recorded for Period:</h6>
                            <a href="{{ route('superadmin.tw_meter_readings.index', ['period_id' => $selectedPeriodId]) }}" class="btn btn-lg btn-outline-success mt-2">
                                {{ $totalRecordedMetersCount }}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded h-100 d-flex flex-column justify-content-center align-items-center">
                            <i class="bi bi-x-circle-fill h3 text-danger"></i>
                            <h6 class="mb-1">Unrecorded for Period:</h6>
                            {{-- <a href="{{ route('superadmin.tw_meters.unrecorded_by_subzone', ['period_id' => $selectedPeriodId]) }}" class="btn btn-lg btn-outline-danger mt-2">
                                {{ $totalUnrecordedMetersCount }}
                            </a> --}}
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded h-100 d-flex flex-column justify-content-center align-items-center">
                            <i class="bi bi-currency-dollar h3 text-warning"></i>
                            <h6 class="mb-1">Total Invoiced Amount:</h6>
                            {{-- This would require summing all total_paid from invoices for the selected period --}}
                            {{-- For now, this is a placeholder. Calculation would be complex if not pre-aggregated. --}}
                            <span class="btn btn-lg btn-outline-warning mt-2">
                                {{ number_format($subzoneSummaries->sum('total_amount'), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Grouped Meters by Subzone --}}
    <h2 class="mb-4">Meters by Undertake Subzone</h2>
    @if (!$selectedPeriodId)
        <div class="alert alert-info" role="alert">
            Please select a <strong>Period</strong> in the filter above to see detailed counts of recorded/unrecorded meters.
        </div>
    @endif
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-1 g-4"> {{-- Grid layout for subzone cards --}}
        @forelse ($subzoneSummaries as $subzoneId => $summary)

            @php
                $subzone = $summary['subzone_object'];
                $zone = $summary['zone_object'];
                $metersInSubzone = $summary['meters_in_subzone_list']; // List of meters for detailed display
            @endphp

            <div class="card mt-4">
                        <div class="card-header col-12">
                            <div class="card">
                                <span class="mask bg-gradient-dark opacity-9 border-radius-xl"></span>
                                <div class="card-body p-3 position-relative">
                                    <div class="row">
                                        <div class="col-8 text-start">

                                            <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                                @if($zone)
                                                   {{ $zone->zone_name }}
                                                @endif
                                            </h5>
                                            <span class="text-white text-sm">เส้นทาง :
                                                {{ $subzone->zone_block_name }}</span>
                                        </div>
                                        <div class="col-4">

                                            <p class="text-white text-sm text-end font-weight-bolder mt-auto mb-0">สมาชิก
                                                {{ $summary['total_meters'] }} คน</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-7">
                                    <div class="row">
                                        <p class="my-auto text-bold col-12 col-md-6">ยังไม่บันทึกข้อมูลมิเตอร์</p>
                                        <p class="text-secondary h5 my-auto col-12 col-md-3  text-xl-end">
                                            {{ $summary['unrecorded_meters'] }} <sup> คน</sup>
                                        </p>
                                        <div class="col-12 col-md-3">
                                         
                                            <a href="{{ route('tw_meter_readings.unrecorded_form', [
                                                'undertake_zone_block_id' => $subzone['id'],
                                                'period_id' => $selectedPeriodId,
                                                'page' => 'create'
                                            ]) }}"
                                                class=" btn btn-sm w-100  mb-0  {{ $summary['unrecorded_meters'] == 0 ? 'disabled' : 'bg-gradient-success' }}">เพิ่มข้อมูล
                                            </a>
                                        </div>
                                    </div>

                                    <hr class="horizontal dark">

                                    <div class="row">
                                        <p class="my-auto text-bold col-12 col-md-6">บันทึกข้อมูลแล้ว</p>
                                        <p class="text-secondary h5 my-auto col-12 col-md-3  text-xl-end">
                                            {{ $summary['recorded_meters'] }} <sup> คน</sup>
                                        </p>
                                        <div class="col-12 col-md-3">

                                            <a style=""
                                                href="{{ route('tw_meter_readings.batch_edit_recorded_form', [
                                                    'undertake_zone_block_id' => $subzone['id'],
                                                    'period_id' => $selectedPeriodId,
                                                ]) }}"
                                                class=" btn btn-sm w-100  mb-0  btn-sm {{ $summary['recorded_meters'] == 0 ? 'disabled' : 'bg-gradient-success' }}">
                                                แก้ไขข้อมูล
                                            </a>
                                        </div>
                                    </div>
                                    <hr class="horizontal dark">
                                        <div class="row">
                                            <p class="my-auto text-bold col-12 col-md-6">ชำระเงินแล้ว</p>
                                            <p class="text-secondary h5 my-auto col-12 col-md-3  text-xl-end">
                                                 0<sup> คน</sup>
                                            </p>
                                            <div class="col-12 col-md-3">
                                                {{-- <a href="{{ url('payment/paymenthistory/' . $current_inv_period->id . '/' . $zone['zone_info']['undertake_subzone_id']) }}"
                                                    class="foatright btn btn-sm w-100 mb-0 {{ $zone['paidTotalCount'] == 0 ? 'disabled' : 'bg-gradient-success' }} ">
                                                    ดูข้อมูล
                                                </a> --}}
                                            </div>
                                        </div>

                                        <hr class="horizontal dark">

                                        {{-- <div class="row">
                                            <p class="my-auto text-bold col-12 col-md-6">เพิ่มผู้ใช้น้ำระหว่างรอบบิล</p>
                                            <p class="text-secondary h5 my-auto col-12 col-md-3  text-xl-end">
                                                {{ $zone['user_notyet_inv_info'] }} <sup> คน</sup>
                                            </p>
                                            <div class="col-12 col-md-3">

                                                <a href="{{ route('invoice.zone_create', [
                                                    'zone_id' => $zone['zone_info']['undertake_subzone_id'],
                                                    'curr_inv_prd' => $current_inv_period->id,
                                                    'page' => 'betweenInvoice',
                                                ]) }}"
                                                    class="foatright btn btn-sm w-100  mb-0  {{ $zone['user_notyet_inv_info'] == 0 ? 'disabled' : 'bg-gradient-success' }}">เพิ่มข้อมูล
                                                </a>
                                            </div>
                                        </div> --}}
                                        <hr class="horizontal dark">

                                        <div class="row">
                                            <p class="my-auto text-bold col-12 col-md-6">Export Excel</p>
                                            <p class="text-secondary h5 my-auto col-12 col-md-3  text-xl-end">
                                                
                                            </p>
                                            <div class="col-12 col-md-3">

                                                {{-- <a href="{{ route('invoice.export_excel', [
                                                    'zone_id' => $zone['zone_info']['undertake_subzone_id'],
                                                    'curr_inv_prd' => $current_inv_period->id,
                                                ]) }}"
                                                    class="foatright btn btn-sm w-100  mb-0 bg-gradient-info">Export
                                                </a> --}}
                                            </div>
                                        </div>
                                </div>
                                <div class="col-5">
                                    <div class="card shadow h-80">
                                        <div class="card-header pb-0 p-3">
                                            <h6 class="mb-0">ข้อมูลการชำระเงินประจำรอบบิล</h6>
                                        </div>
                                        <div class="card-body pb-0 p-3">
                                            <ul class="list-group">
                                                <li class="list-group-item border-0 d-flex align-items-center px-0 mb-0">
                                                    <div class="w-100">
                                                        <div class="d-flex mb-2">
                                                            <span
                                                                class="me-2 text-sm font-weight-bold text-dark">จำนวนที่ต้องชำระ</span>
                                                            <span
                                                                class="ms-auto text-sm font-weight-bold">
                                                                {{ number_format($summary['total_amount'], 2) }}
                                                                บาท</span>
                                                        </div>
                                                        <div>
                                                            <div class="progress progress-md">
                                                                <div class="progress-bar bg-primary w-100"
                                                                    role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-group-item border-0 d-flex align-items-center px-0 mb-0">
                                                    <div class="w-100">
                                                        <div class="d-flex mb-2">
                                                            <span
                                                                class="me-2 text-sm font-weight-bold text-dark">ชำระเงินแล้ว</span>
                                                            <span
                                                                class="ms-auto text-sm font-weight-bold">
                                                                {{-- {{ number_format($zone['paidTotalAmount'], 2) }} --}}
                                                                บาท</span>
                                                        </div>
                                                        <div>
                                                            <div class="progress progress-md">
                                                                <div class="progress-bar bg-success"
                                                               
                                                                    {{-- style="width:{{ 
                                                                    $zone['total_paid'] == 0 ? 0 :
                                                                    number_format(($zone['paidTotalAmount'] / $zone['total_paid']) * 100, 2) }}%" --}}
                                                                    role="progressbar" aria-valuenow="60"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-group-item border-0 d-flex align-items-center px-0 mb-0">
                                                    <div class="w-100">
                                                        <div class="d-flex mb-2">
                                                            <span
                                                                class="me-2 text-sm font-weight-bold text-dark">ยังไม่ชำระเงิน</span>
                                                            <span
                                                                class="ms-auto text-sm font-weight-bold">
                                                                {{-- {{ number_format($zone['total_paid'] - $zone['paidTotalAmount'], 2) }} --}}
                                                                บาท</span>
                                                        </div>
                                                        <div>
                                                            <div class="progress progress-md">
                                                                <div class="progress-bar bg-warning"
                                                                    {{-- style="width:{{ 
                                                                    $zone['total_paid'] == 0 ? 0 :
                                                                    number_format((($zone['total_paid'] - $zone['paidTotalAmount']) / $zone['total_paid']) * 100, 2) }}%" --}}
                                                                    role="progressbar" aria-valuenow="90"
                                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            {{ $subzone->zone_block_name ?? 'Unassigned Subzone' }}
                            @if($zone)
                                <small class="text-white-50 d-block">({{ $zone->zone_name }})</small>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- --- แสดงข้อมูลสรุปพร้อมลิงก์ปุ่ม --- --}}
                        <p class="card-text">
                            <i class="bi bi-person-fill"></i> Total Meters:
                            {{-- <a href="{{ route('superadmin.tw_meters.index', ['undertake_subzone_id' => $subzoneId]) }}" class="btn btn-sm btn-outline-primary ms-2"> --}}
                                {{ $summary['total_meters'] }}
                            {{-- </a> --}}
                        </p>
                        @if ($selectedPeriodId) {{-- แสดง Recorded/Unrecorded/Water/Amount เฉพาะเมื่อมีการเลือก Period --}}
                            <p class="card-text">
                                <i class="bi bi-check-circle-fill text-success"></i> Recorded:
                                <a href="{{ route('superadmin.tw_meter_readings.index', ['period_id' => $selectedPeriodId, 'undertake_subzone_id' => $subzoneId]) }}" class="btn btn-sm btn-outline-success ms-2">
                                    {{ $summary['recorded_meters'] }}
                                </a>
                            </p>
                            <p class="card-text">
                                <i class="bi bi-x-circle-fill text-danger"></i> Unrecorded:
                                <a href="{{ route('tw_meter_readings.unrecorded_form', ['period_id' => $selectedPeriodId, 'undertake_subzone_id' => $subzoneId]) }}" class="btn btn-sm btn-outline-danger ms-2">
                                    {{ $summary['unrecorded_meters'] }}
                                </a>
                            </p>
                            <p class="card-text">
                                <i class="bi bi-droplet-fill text-primary"></i> Water Used:
                                {{-- <a href="{{ route('superadmin.tw_invoices.index', ['period_id' => $selectedPeriodId, 'undertake_subzone_id' => $subzoneId]) }}" class="btn btn-sm btn-outline-primary ms-2">
                                    {{ number_format($summary['total_water_used'], 2) }} units
                                </a> --}}
                            </p>
                            <p class="card-text">
                                <i class="bi bi-currency-dollar text-warning"></i> Total Amount:
                                {{-- <a href="{{ route('superadmin.tw_invoices.index', ['period_id' => $selectedPeriodId, 'undertake_subzone_id' => $subzoneId]) }}" class="btn btn-sm btn-outline-warning ms-2">
                                    {{ number_format($summary['total_amount'], 2) }} THB
                                </a> --}}
                            </p>
                        @else
                            <p class="card-text text-muted">
                                <i class="bi bi-info-circle-fill"></i> Select a period to see reading and invoice summary.
                            </p>
                        @endif
                        <hr>
                        {{-- <h6 class="mb-2">Meters in this Subzone:</h6>
                        <ul class="list-group list-group-flush">
                            @foreach ($metersInSubzone as $meter)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <h6 class="mb-0">{{ $meter->meter_code }} - {{ $meter->customer_name }}</h6>
                                        <small class="text-muted">
                                            Type: {{ $meter->meterType->name ?? 'N/A' }} |
                                            Last Reading: {{ number_format($meter->current_active_reading, 2) }}
                                        </small>
                                    </div>
                                    <a href="{{ route('superadmin.tw_meter_readings.create', ['meter_id' => $meter->id, 'period_id' => $selectedPeriodId]) }}" class="btn btn-sm btn-outline-success" title="Add Reading">
                                        <i class="bi bi-pencil-square"></i> Add Reading
                                    </a>
                                </li>
                            @endforeach
                        </ul> --}}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center" role="alert">
                    No active meters found or grouped by subzone.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Original Meter Readings Table --}}
    <h2 class="mt-5 mb-3">All Recorded Readings</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Meter Code</th>
                    <th>Period</th>
                    <th>Reading Date</th>
                    <th>Prev. Reading</th>
                    <th>Current Reading</th>
                    <th>Water Used</th>
                    <th>Comment</th>
                    <th>Recorder</th>
                    <th style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($meterReadings as $reading)
                    <tr>
                        <td>{{ $reading->id }}</td>
                        <td>{{ $reading->meter->meter_code ?? 'N/A' }}</td>
                        <td>{{ $reading->period->period_name ?? 'N/A' }}</td>
                        <td>{{ $reading->reading_date->format('Y-m-d') }}</td>
                        <td>{{ number_format($reading->previous_reading_value, 2) }}</td>
                        <td>{{ number_format($reading->reading_value, 2) }}</td>
                        <td>{{ number_format($reading->reading_value - $reading->previous_reading_value, 2) }}</td>
                        <td>{{ Str::limit($reading->comment, 50) }}</td>
                        <td>{{ $reading->recorder->username ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('superadmin.tw_meter_readings.show', $reading->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('superadmin.tw_meter_readings.edit', $reading->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('superadmin.tw_meter_readings.destroy', $reading->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this meter reading? This will affect meter current active reading.')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No meter readings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $meterReadings->links() }}
    </div>

    
</div>
@endsection
