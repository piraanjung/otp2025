@extends('layouts.keptkaya')

@section('mainheader', 'จัดการรอบจัดเก็บค่าขยะรายปี')
@section('nav-header', 'ค่าขยะรายปี')
@section('nav-current', 'รอบการจัดเก็บ')
@section('nav-annual-batch', 'active') {{-- อย่าลืมไปเพิ่มใน Layout --}}

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white border-0 pt-4 px-4">
                <div>
                    <h6 class="mb-0 font-weight-bold text-dark">ประวัติรอบการจัดเก็บรายปี</h6>
                    <p class="text-xs text-secondary mb-0">รายการปีงบประมาณที่ถูกประมวลผลแล้ว</p>
                </div>
                
                {{-- ปุ่มเปิด Modal --}}
                <button type="button" class="btn btn-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#createBatchModal">
                    <i class="fas fa-plus me-2"></i> สร้างรอบจัดเก็บใหม่
                </button>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">ปีงบประมาณ</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">จำนวนถัง (ใบ)</th>
                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ยอดหนี้รวม (บาท)</th>
                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เก็บได้แล้ว (บาท)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ความคืบหน้า</th>
                                <th class="text-center text-secondary opacity-7">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $batch)
                            <tr>
                                <td class="ps-4">
                                    <h6 class="mb-0 text-sm">ปี {{ $batch->fiscal_year }}</h6>
                                    <span class="text-xs text-secondary">สร้างเมื่อ: {{ \Carbon\Carbon::parse($batch->created_at)->format('d/m/Y') }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-sm font-weight-bold">{{ number_format($batch->total_bins) }}</span>
                                </td>
                                <td class="align-middle text-end">
                                    <span class="text-dark text-sm font-weight-bold">{{ number_format($batch->total_expected_revenue, 2) }}</span>
                                </td>
                                <td class="align-middle text-end">
                                    <span class="text-success text-sm font-weight-bold">{{ number_format($batch->total_collected, 2) }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    @php
                                        $percent = $batch->total_expected_revenue > 0 ? ($batch->total_collected / $batch->total_expected_revenue) * 100 : 0;
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-center px-2">
                                        <span class="me-2 text-xs font-weight-bold">{{ number_format($percent, 1) }}%</span>
                                        <div>
                                            <div class="progress" style="width: 100px;"> <div class="progress-bar bg-gradient-{{ $percent == 100 ? 'success' : 'info' }}" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $percent }}%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    {{-- ปุ่มลบ (Rollback) เฉพาะกรณีที่ยังเก็บเงินได้ 0 บาท --}}
                                    @if($batch->total_collected == 0)
                                        <form action="{{ route('keptkayas.annual_batch.destroy', $batch->fiscal_year) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('ยืนยันการลบข้อมูลปี {{ $batch->fiscal_year }} ทั้งหมด? \n(การกระทำนี้ไม่สามารถกู้คืนได้)')">
                                                <i class="far fa-trash-alt me-2"></i> ลบข้อมูล
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-light text-secondary border">เริ่มมีการชำระเงินแล้ว</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <i class="fas fa-folder-open fa-3x text-secondary mb-3 opacity-5"></i>
                                        <h6 class="text-secondary">ยังไม่มีข้อมูลรอบการจัดเก็บ</h6>
                                        <p class="text-xs text-secondary">กรุณากดปุ่ม "สร้างรอบจัดเก็บใหม่" เพื่อเริ่มต้น</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: เลือกปีงบประมาณเพื่อสร้างข้อมูล --}}
<div class="modal fade" id="createBatchModal" tabindex="-1" role="dialog" aria-labelledby="createBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="createBatchModalLabel">สร้างรอบจัดเก็บค่าขยะรายปี</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('keptkayas.annual_batch.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info text-white" role="alert">
                        <strong>คำแนะนำ:</strong> ระบบจะดึงข้อมูลถังขยะที่เปิดบริการ "เก็บรายปี" และคำนวณยอดเงินตามเรทราคาของปีที่เลือก
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="fiscal_year" class="form-label">เลือกปีงบประมาณ</label>
                        <select name="fiscal_year" id="fiscal_year" class="form-select" required>
                            <option value="" disabled selected>-- กรุณาเลือกปี --</option>
                            @foreach($budgetYears as $year)
                                {{-- ซ่อนปีที่สร้างไปแล้ว --}}
                                @unless($batches->contains('fiscal_year', $year->budgetyear_name))
                                    <option value="{{ $year->budgetyear_name }}">{{ $year->budgetyear_name }} ({{ $year->budgetyear_name ?? 'ไม่มีชื่อ' }})</option>
                                @endunless
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn bg-gradient-primary">ยืนยันการประมวลผล</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection