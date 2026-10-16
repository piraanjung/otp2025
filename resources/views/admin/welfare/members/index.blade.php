@extends('layouts.welfare') 
@section('title_page', 'รายชื่อสมาชิกกองทุนฌาปนกิจ') 

@section('content') 
<div class="container-fluid py-4"> 
    <!-- Header Title & Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4"> 
        <div> 
            <h2 class="fw-bold text-dark mb-1">👥 สมาชิกกองทุนฌาปนกิจ</h2> 
            <p class="text-muted">บริหารจัดการและตรวจสอบรายชื่อสมาชิกที่ได้รับสิทธิ์กองทุนสวัสดิการ</p> 
        </div> 
        <div>
            <a href="{{ route('admin.welfare.members.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm me-2"> 
                <i class="fas fa-user-plus me-1"></i> เพิ่มสมาชิกเข้ากองทุน 
            </a>
            <a href="{{ route('admin.welfare.dashboard') }}" class="btn btn-light rounded-pill px-4 shadow-sm"> 
                <i class="fas fa-arrow-left me-1"></i> กลับหน้าแดชบอร์ด 
            </a> 
        </div>
    </div> 

    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"> 
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }} 
        </div> 
    @endif 
    @if(session('error')) 
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4"> 
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }} 
        </div> 
    @endif 

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.welfare.members.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 ps-3 rounded-start-pill"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-light rounded-end-pill px-2" placeholder="ค้นหาด้วยชื่อสมาชิก หรือเบอร์โทรศัพท์..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button type="submit" class="btn btn-dark rounded-pill px-4 shadow-sm">
                        ค้นหาข้อมูล
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.welfare.members.index') }}" class="btn btn-outline-secondary rounded-pill px-3 ms-1">
                            รีเซ็ต
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Members Table Card -->
    <div class="card border-0 shadow-sm rounded-4"> 
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center"> 
            <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>รายชื่อสมาชิกกองทุนทั้งหมด</h5> 
        </div> 
        <div class="table-responsive"> 
            <table class="table align-middle mb-0"> 
                <thead class="bg-light text-muted"> 
                    <tr> 
                        <th class="ps-4">รหัส / ชื่อ-นามสกุล</th> 
                        <th>เบอร์โทรศัพท์</th> 
                        <th>ประเภทสมาชิก</th> 
                        <th class="text-center">สถานะกองทุน</th> 
                        <th>วันที่ลงทะเบียน</th> 
                        <th class="text-center">จัดการ</th> 
                    </tr> 
                </thead> 
                <tbody> 
                    @forelse($members as $member) 
                    <tr> 
                        <td class="ps-4"> 
                            <div class="fw-bold text-dark">{{ $member->user->firstname." ".$member->user->lastname ?? 'ไม่พบข้อมูลผู้ใช้' }}</div> 
                            <small class="text-muted">บ้านเลขที่ {{ $member->user->address." ".$member->user->user_zone->zone_name }}</small> 
                        </td> 
                        <td>{{ $member->user->phone ?? '-' }}</td> 
                        <td> 
                            @if($member->membership_type === 'both') 
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3">ขายขยะ + สมทบฌาปนกิจ</span> 
                            @elseif($member->membership_type === 'contribution_only') 
                                <span class="badge bg-info-subtle text-info rounded-pill px-3">สมทบฌาปนกิจอย่างเดียว</span> 
                            @else 
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">สะสมยอดขยะอย่างเดียว</span> 
                            @endif 
                        </td> 
                        <td class="text-center"> 
                            @if($member->status === 'active') 
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">ปกติ (Active)</span> 
                            @elseif($member->status === 'suspended') 
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3">ระงับชั่วคราว</span> 
                            @else 
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">พ้นสภาพ</span> 
                            @endif 
                        </td> 
                        <td class="text-muted small"> 
                            {{ $member->registered_at ? \Carbon\Carbon::parse($member->registered_at)->format('d/m/Y') : $member->created_at->format('d/m/Y') }} 
                        </td> 
                        <td class="text-center"> 
                            <form action="{{ route('admin.welfare.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('คุณต้องการยกเลิกสิทธิ์สมาชิกกองทุนนี้ใช่หรือไม่?');" style="display:inline-block;"> 
                                @csrf 
                                @method('DELETE') 
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3"> 
                                    <i class="fas fa-trash-alt"></i> ยกเลิกสิทธิ์ 
                                </button> 
                            </form> 
                        </td> 
                    </tr> 
                    @empty 
                    <tr> 
                        <td colspan="6" class="text-center py-5 text-muted"> 
                            <i class="fas fa-users-slash fa-2x mb-3 text-black-50"></i><br> 
                            ยังไม่มีข้อมูลสมาชิกในกองทุนฌาปนกิจ <br>
                            <a href="{{ route('admin.welfare.members.create') }}" class="btn btn-sm btn-primary rounded-pill mt-3 px-4"> 
                                เพิ่มสมาชิกเข้ากองทุนตอนนี้ 
                            </a> 
                        </td> 
                    </tr> 
                    @endforelse 
                </tbody> 
            </table> 
        </div> 
        <div class="card-footer bg-white border-0 py-3"> 
            {{ $members->withQueryString()->links() }} 
        </div> 
    </div> 
</div> 
@endsection