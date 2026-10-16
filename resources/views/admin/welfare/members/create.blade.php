@extends('layouts.welfare') 
@section('title_page', 'เพิ่มสมาชิกกองทุนฌาปนกิจ') 

@section('content') 
<div class="container-fluid py-4"> 
    <div class="d-flex justify-content-between align-items-center mb-4"> 
        <div> 
            <h2 class="fw-bold text-dark mb-1">➕ ลงทะเบียนสมาชิกกองทุนฌาปนกิจ</h2> 
            <p class="text-muted">ดึงรายชื่อจากฐานข้อมูลกลาง (Master Users) ที่ยังไม่ได้เข้าร่วมกองทุน</p> 
        </div> 
        <a href="{{ route('admin.welfare.members.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm"> 
            <i class="fas fa-arrow-left me-1"></i> กลับหน้ารายชื่อสมาชิก 
        </a> 
    </div> 

    <!-- ฟอร์มค้นหา -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.welfare.members.create') }}" method="GET" class="input-group">
                <input type="text" name="search" class="form-control border-0 bg-light rounded-start-pill px-3" placeholder="ค้นหาด้วยชื่อ หรือเบอร์โทรศัพท์..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary px-4 rounded-end-pill">
                    <i class="fas fa-search me-1"></i> ค้นหา
                </button>
            </form>
        </div>
    </div>

    <!-- ตารางรายชื่อให้ติ๊กเลือก -->
    <div class="card border-0 shadow-sm rounded-4"> 
        <form action="{{ route('admin.welfare.members.bulkStore') }}" method="POST"> 
            @csrf 
            <div class="table-responsive"> 
                <table class="table align-middle mb-0"> 
                    <thead class="bg-light text-muted"> 
                        <tr> 
                            <th class="ps-4" style="width: 50px;"> 
                                <input type="checkbox" id="selectAll" class="form-check-input"> 
                            </th> 
                            <th>ชื่อ-นามสกุล</th> 
                            <th>เบอร์โทรศัพท์</th> 
                            <th>ประเภทสมาชิกกองทุนที่ต้องการสมัคร</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        @forelse($availableUsers as $user) 
                        <tr> 
                            <td class="ps-4"> 
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox"> 
                            </td> 
                            <td> 
                                <div class="fw-bold text-dark">{{ $user->firstname." ".$user->lastname }}</div> 
                                <small class="text-muted">บ้านเลขที่ {{ $user->address." ".$user->user_zone->zone_name }} </small> 
                            </td> 
                            <td>{{ $user->phone ?? '-' }}</td> 
                            <td> 
                                <select name="membership_type[{{ $user->id }}]" class="form-select form-select-sm bg-light border-0 rounded-3" style="max-width: 250px;"> 
                                    <option value="both">ขายขยะ + สมทบฌาปนกิจ (ปกติ)</option> 
                                    <option value="contribution_only">สมทบฌาปนกิจอย่างเดียว (ไม่ขายขยะ)</option> 
                                    <option value="waste_only">สะสมยอดขายขยะอย่างเดียว</option> 
                                </select> 
                            </td> 
                        </tr> 
                        @empty 
                        <tr> 
                            <td colspan="4" class="text-center py-5 text-muted">ไม่พบรายชื่อสมาชิกกลางที่สามารถเพิ่มได้ (หรือเพิ่มครบหมดแล้ว)</td> 
                        </tr> 
                        @endforelse 
                    </tbody> 
                </table> 
            </div> 
            
            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center"> 
                <div class="text-muted small">แสดงผลหน้าละ 20 รายการ</div>
                <div>
                    {{ $availableUsers->withQueryString()->links() }}
                </div>
                @if($availableUsers->isNotEmpty())
                    <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm"> 
                        <i class="fas fa-check-circle me-1"></i> ยืนยันเพิ่มสมาชิกที่เลือก 
                    </button> 
                @endif
            </div> 
        </form> 
    </div> 
</div> 

<script> 
    // Script สำหรับ Checkbox "เลือกทั้งหมดในหน้านี้"
    document.getElementById('selectAll')?.addEventListener('change', function() { 
        let checkboxes = document.querySelectorAll('.user-checkbox'); 
        checkboxes.forEach(cb => cb.checked = this.checked); 
    }); 
</script> 
@endsection