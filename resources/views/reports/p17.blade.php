@extends('layouts.admin1')
@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-4">ทะเบียนรายตัวลูกหนี้ (ป.17) - ประเภทใช้มาตรวัดน้ำ</h3>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.p17') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">งวดประจำเดือน</label>
                    <select name="inv_period_id_fk" class="form-select">
                        <option value="">-- เลือกงวด --</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}" {{ $periodId == $period->id ? 'selected' : '' }}>
                                {{ $period->name ?? 'งวดที่ '.$period->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">ค้นหา (ชื่อ-สกุล / เลขมาตร)</label>
                    <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="พิมพ์ชื่อ สกุล หรือเลขมาตร...">
                </div>

                <div class="col-md-2">
                    <label class="form-label">เลขมาตรวัดน้ำ</label>
                    <input type="text" name="meternumber" class="form-control" value="{{ $meterNumber }}" placeholder="meternumber">
                </div>

                <div class="col-md-2">
                    <label class="form-label">รหัสผู้ใช้ (User ID)</label>
                    <input type="number" name="user_id" class="form-control" value="{{ $userId }}" placeholder="user_id">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">ค้นหา</button>
                    <a href="{{ route('reports.p17') }}" class="btn btn-secondary">ล้าง</a>
                </div>
            </form>
        </div>
    </div>

    <!-- ตารางแสดงผล ป.17 -->
    <div class="table-responsive">
       <table class="table table-bordered align-middle text-center" style="font-size: 13px;">
    <thead class="table-light">
        <tr>
            <th rowspan="2">เลขมาตร</th>
            <th rowspan="2">ชื่อ - สกุล</th>
            <th rowspan="2">บิล</th>
            <th colspan="2">เลขมาตร</th>
            <th rowspan="2">หนี้เดือนนี้</th>
            <th rowspan="2">ยกมา</th>
            <th rowspan="2">รวมหนี้</th>
            <th colspan="3" class="bg-success text-white">การชำระเดือนนี้</th>
            <th rowspan="2">คงค้างยกไป</th>
        </tr>
        <tr>
            <th>ก่อน</th><th>หลัง</th>
            <th>วันที่</th><th>หน้าบัญชี</th><th>จำนวนเงิน</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $row)
        <tr>
            <td>{{ $row->tw_meter_infos->meternumber ?? '-' }}</td>
            <td class="text-start">{{ $row->tw_meter_infos->user->firstname ?? '-' }}</td>
            <td>{{ $row->id }}</td>
            <td>{{ number_format($row->lastmeter) }}</td>
            <td>{{ number_format($row->currentmeter) }}</td>
            <td>{{ number_format($row->totalpaid, 2) }}</td>
            <td>{{ number_format($row->computed_brought_forward, 2) }}</td>
            <td class="fw-bold">{{ number_format($row->computed_total_due, 2) }}</td>
            <td>{{ $row->computed_pay_date }}</td>
            <td>{{ $row->computed_cashbook_page }}</td>
            <td class="fw-bold text-success">{{ number_format($row->computed_paid_amount, 2) }}</td>
            <td class="fw-bold text-danger">{{ number_format($row->computed_carried_forward, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection