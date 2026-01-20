@extends('layouts.keptkaya')

@section('title_page', 'ศูนย์รวมรายงาน (Reports Center)')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">เลือกประเภทรายงาน</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row p-4">
                        
                        {{-- 1. รายงานประจำวัน --}}
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-calendar-day text-info me-2"></i>รายงานรับเงินประจำวัน</h6>
                                    <p class="text-xs text-secondary">สำหรับฝ่ายการเงิน/บัญชี ตรวจสอบยอดสิ้นวัน</p>
                                    <form action="{{ route('keptkayas.reports.generate') }}" method="GET" target="_blank">
                                        <input type="hidden" name="report_type" value="daily_collection">
                                        <div class="input-group input-group-static mb-3">
                                            <label>เลือกวันที่</label>
                                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-info w-100">ออกรายงาน</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- 2. รายงานค้างชำระ --}}
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-user-clock text-danger me-2"></i>รายงานลูกหนี้ค้างชำระ</h6>
                                    <p class="text-xs text-secondary">สำหรับฝ่ายจัดเก็บ/เร่งรัดหนี้สิน</p>
                                    <form action="{{ route('keptkayas.reports.generate') }}" method="GET" target="_blank">
                                        <input type="hidden" name="report_type" value="arrears">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="input-group input-group-static mb-3">
                                                    <label>ปีงบประมาณ</label>
                                                    <input type="number" name="fiscal_year" class="form-control" value="{{ $fiscalYear }}">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="input-group input-group-static mb-3">
                                                    <label>โซน</label>
                                                    <select name="zone_id" class="form-control">
                                                        <option value="">ทุกโซน</option>
                                                        @foreach($zones as $z)
                                                            <option value="{{ $z->id }}">{{ $z->zone_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-danger w-100">ออกรายงาน</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- 3. รายงานสรุปผู้บริหาร --}}
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-chart-pie text-success me-2"></i>สรุปยอดจัดเก็บรายโซน</h6>
                                    <p class="text-xs text-secondary">สำหรับผู้บริหาร ดูภาพรวมประสิทธิภาพ</p>
                                    <form action="{{ route('keptkayas.reports.generate') }}" method="GET" target="_blank">
                                        <input type="hidden" name="report_type" value="zone_summary">
                                        <div class="input-group input-group-static mb-3">
                                            <label>ปีงบประมาณ</label>
                                            <input type="number" name="fiscal_year" class="form-control" value="{{ $fiscalYear }}">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-success w-100">ออกรายงาน</button>
                                    </form>
                                </div>
                            </div>
                        </div>


                         <div class="row p-4 pt-0"> {{-- เปิด row ใหม่ --}}
    
    {{-- 4. ทะเบียนคุมใบเสร็จ --}}
    <div class="col-md-6 mb-3">
        <div class="card border">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-list-ol text-primary me-2"></i>ทะเบียนคุมใบเสร็จรับเงิน</h6>
                <p class="text-xs text-secondary">ตรวจสอบความต่อเนื่องของเลขที่ใบเสร็จ (Audit Trail)</p>
                <form action="{{ route('keptkayas.reports.generate') }}" method="GET" target="_blank">
                    <input type="hidden" name="report_type" value="receipt_control">
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group input-group-static mb-3">
                                <label>ตั้งแต่วันที่</label>
                                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-01') }}" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group input-group-static mb-3">
                                <label>ถึงวันที่</label>
                                <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">ออกรายงาน</button>
                </form>
            </div>
        </div>
    </div>

    {{-- 5. ใบนำส่งเงิน --}}
    <div class="col-md-6 mb-3">
        <div class="card border">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-file-invoice-dollar text-warning me-2"></i>ใบนำส่งเงิน (Remittance)</h6>
                <p class="text-xs text-secondary">สรุปยอดประจำวันเพื่อนำฝากธนาคาร/ส่งคลัง</p>
                <form action="{{ route('keptkayas.reports.generate') }}" method="GET" target="_blank">
                    <input type="hidden" name="report_type" value="remittance">
                    <div class="input-group input-group-static mb-3">
                        <label>เลือกวันที่นำส่ง</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-warning w-100">ออกรายงาน</button>
                </form>
            </div>
        </div>
    </div>

</div>
{{-- 6. รายชื่อจุดเก็บขยะ --}}
<div class="col-md-6 mb-3">
    <div class="card border">
        <div class="card-body">
            <h6 class="card-title"><i class="fas fa-map-marked-alt text-info me-2"></i>รายชื่อจุดเก็บขยะ (Service Points)</h6>
            <p class="text-xs text-secondary">ข้อมูลพิกัดและตำแหน่งถัง สำหรับฝ่ายปฏิบัติการ/คนขับรถ</p>
            <form action="{{ route('keptkayas.reports.generate') }}" method="GET" target="_blank">
                <input type="hidden" name="report_type" value="service_points">
                <div class="input-group input-group-static mb-3">
                    <label>เลือกโซน (เว้นว่างหากต้องการทั้งหมด)</label>
                    <select name="zone_id" class="form-control">
                        <option value="">ทุกโซน</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id }}">{{ $z->zone_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-info w-100">ออกรายงาน</button>
            </form>
        </div>
    </div>
</div>

{{-- 7. รายงานถังชำรุด --}}
<div class="col-md-6 mb-3">
    <div class="card border">
        <div class="card-body">
            <h6 class="card-title"><i class="fas fa-tools text-danger me-2"></i>รายงานถังขยะชำรุด/แจ้งซ่อม</h6>
            <p class="text-xs text-secondary">รายการถังที่มีสถานะ Damaged เพื่อวางแผนซ่อมแซม</p>
            <form action="{{ route('keptkayas.reports.generate') }}" method="GET" target="_blank">
                <input type="hidden" name="report_type" value="damaged_bins">
                <div class="my-4">
                    {{-- ดันปุ่มลงมาหน่อยเพราะไม่มี input --}}
                </div>
                <button type="submit" class="btn btn-sm btn-danger w-100 mt-2">ออกรายงาน</button>
            </form>
        </div>
    </div>
</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection