@extends('layouts.welfare') 
@section('title_page', 'ตรวจสอบสิทธิ์กองทุนสวัสดิการ')

@section('content') 
<div class="container-fluid py-4"> 
    <div class="row justify-content-center"> 
        <div class="col-md-8"> 
            <div class="card border-0 shadow-sm rounded-4 mb-4"> 
                <div class="card-header bg-white py-3 border-0"> 
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-shield-alt text-primary me-2"></i>ตรวจสอบสิทธิ์กองทุนสวัสดิการฌาปนกิจของฉัน</h5> 
                </div> 
                <div class="card-body p-4"> 
                    @if(!$isMember)
                        <div class="alert alert-warning border-0 rounded-4 p-4 text-center">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2 text-warning"></i>
                            <h5 class="fw-bold">ยังไม่เป็นสมาชิกกองทุน</h5>
                            <p class="text-muted mb-3">{{ $message }}</p>
                        </div>
                    @else
                        <!-- สถานะสมาชิก -->
                        <div class="p-3 bg-light rounded-4 mb-4 d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">สถานะสมาชิกกองทุน</small>
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> ปกติ (Active)
                                </span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">ประเภทสิทธิ์</small>
                                <strong class="text-dark">
                                    @if($welfareMember->membership_type === 'both') ขายขยะ + สมทบฌาปนกิจ
                                    @else สมทบฌาปนกิจอย่างเดียว @endif
                                </strong>
                            </div>
                        </div>

                        <!-- ผลตรวจสอบสิทธิ์ -->
                        @if($isEligible)
                            <div class="alert alert-success border-0 rounded-4 p-4 mb-4">
                                <h4 class="fw-bold text-success mb-2"><i class="fas fa-check-circle me-2"></i>คุณมี "สิทธิ์" ได้รับการคุ้มครองและเบิกจ่ายสวัสดิการ</h4>
                                <p class="mb-0 text-muted small">คุณมีคุณสมบัติครบถ้วนตามเกณฑ์ สามารถยื่นคำร้องขอรับเงินสวัสดิการได้ทันที</p>
                            </div>
                        @else
                            <div class="alert alert-danger border-0 rounded-4 p-4 mb-4">
                                <h4 class="fw-bold text-danger mb-2"><i class="fas fa-clock me-2"></i>ยังไม่ผ่านเกณฑ์รับสวัสดิการ</h4>
                                <p class="mb-0 text-muted small">คุณยังสะสมเงื่อนไขไม่ครบตามที่กองทุนกำหนด</p>
                            </div>
                        @endif

                        <!-- ตารางเกณฑ์เปรียบเทียบ -->
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th>เงื่อนไขการรับสิทธิ์</th>
                                        <th class="text-center">เกณฑ์ขั้นต่ำ</th>
                                        <th class="text-center">ข้อมูลของคุณ</th>
                                        <th class="text-center">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>ระยะเวลาเป็นสมาชิก</td>
                                        <td class="text-center">{{ $minMonths }} เดือน</td>
                                        <td class="text-center fw-bold">{{ $monthsActive }} เดือน</td>
                                        <td class="text-center">
                                            @if($monthsActive >= $minMonths) <span class="text-success"><i class="fas fa-check"></i> ผ่าน</span>
                                            @else <span class="text-danger"><i class="fas fa-times"></i> ยังไม่ถึง</span> @endif
                                        </td>
                                    </tr>
                                    @if($welfareMember->membership_type !== 'contribution_only')
                                    <tr>
                                        <td>น้ำหนักขยะรีไซเคิลสะสม</td>
                                        <td class="text-center">{{ $config->min_total_weight ?? 50 }} กก.</td>
                                        <td class="text-center fw-bold">{{ $totalWeight }} กก.</td>
                                        <td class="text-center">
                                            @if($totalWeight >= ($config->min_total_weight ?? 50)) <span class="text-success"><i class="fas fa-check"></i> ผ่าน</span>
                                            @else <span class="text-danger"><i class="fas fa-times"></i> ยังไม่ถึง</span> @endif
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('member.welfare.tracking') }}" class="text-decoration-none">
                                <i class="fas fa-history me-1"></i> ติดตามสถานะคำร้องของฉัน
                            </a>
                            @if($isEligible)
                                <a href="{{ route('member.welfare.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> ไปหน้ายื่นคำร้องขอรับสวัสดิการ
                                </a>
                            @else
                                <button class="btn btn-secondary rounded-pill px-4" disabled>ยังไม่สามารถยื่นคำร้องได้</button>
                            @endif
                        </div>
                    @endif
                </div> 
            </div> 
        </div> 
    </div> 
</div> 
@endsection