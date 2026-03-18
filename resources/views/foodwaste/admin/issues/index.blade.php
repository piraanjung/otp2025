
@extends('layouts.foodwaste')
@section('content')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill"></i> ระบบจัดการแจ้งปัญหา (Issues)</h3>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">รหัส</th>
                                <th>ผู้แจ้ง</th>
                                <th>ผู้รับผิดชอบ</th>
                                <th>หมวดหมู่ปัญหา</th>
                                <th>เวลาที่แจ้ง</th>
                                <th>สถานะ</th>
                                <th class="pe-4 text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($issues as $issue)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#{{ $issue->id }}</td>
                                    <td>
                                        <i class="bi bi-person-circle text-secondary"></i>
                                        {{ $issue->user->firstname." ".$issue->user->lastname ?? 'ผู้ใช้ทั่วไป' }}
                                    </td>
                                    <td>
                                        <!-- 🌟 แสดงชื่อคนรับผิดชอบในตาราง -->
                                        @if($issue->assigned_staff_id)
                                            <span class="badge bg-info text-dark rounded-pill px-2">
                                                <i class="bi bi-person-badge"></i> {{ $issue->assignedStaff->user->firstname." ".$issue->assignedStaff->user->lastname ?? 'ไม่มีข้อมูล' }}
                                            </span>
                                        @else
                                            <span class="text-muted small">- ยังไม่ระบุ -</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-truncate fw-bold text-dark" style="max-width: 150px;">
                                            {{ $issue->issueType->name ?? 'ไม่ได้ระบุหมวดหมู่' }}
                                        </div>
                                    </td>
                                    <td class="small text-muted">{{ $issue->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($issue->status == 'pending')
                                            <span class="badge bg-danger rounded-pill px-3">🔴 รอดำเนินการ</span>
                                        @elseif($issue->status == 'in_progress')
                                            <span class="badge bg-warning text-dark rounded-pill px-3">🟡 กำลังตรวจสอบ</span>
                                        @elseif($issue->status == 'resolved')
                                            <span class="badge bg-success rounded-pill px-3">🟢 แก้ไขแล้ว</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill px-3">⚪ ยกเลิก</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-center">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                            data-bs-toggle="modal" data-bs-target="#issueModal{{ $issue->id }}">
                                            ดูรายละเอียด/อัปเดต
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal รายละเอียดปัญหา -->
                                <div class="modal fade" id="issueModal{{ $issue->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 rounded-4 shadow">

                                            <form action="{{ route('foodwaste.admin.issues.update', $issue->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-light border-bottom-0 rounded-top-4">
                                                    <h5 class="modal-title fw-bold text-danger">รายละเอียดปัญหา #{{ $issue->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body p-4">
                                                    <!-- กล่องข้อมูลปัญหา -->
                                                    <div class="p-3 bg-light rounded-3 border mb-4">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-2">
                                                                <label class="text-muted small">หมวดหมู่ปัญหา:</label>
                                                                <p class="fw-bold mb-0 text-dark">{{ $issue->issueType->name ?? 'ไม่ได้ระบุหมวดหมู่' }}</p>
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label class="text-muted small">ผู้แจ้ง / เวลา:</label>
                                                                <p class="mb-0 text-dark small">
                                                                    {{ $issue->user->firstname ?? 'ผู้ใช้ทั่วไป' }} /
                                                                    {{ $issue->created_at->format('d/m/Y H:i') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <hr class="my-2">
                                                        <label class="text-muted small">คำอธิบายรายละเอียดปัญหา:</label>
                                                        <p class="mb-0 text-dark">{{ $issue->description }}</p>
                                                    </div>

                                                    <!-- ส่วนของเจ้าหน้าที่ -->
                                                    <h6 class="fw-bold mb-3" style="color: #c2185b;"><i class="bi bi-tools me-2"></i>ส่วนการจัดการของเจ้าหน้าที่</h6>

                                                    <!-- 🌟 Dropdown เลือกผู้รับผิดชอบ (Staff) -->
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">มอบหมายผู้รับผิดชอบ (Assign to)</label>
                                                        <select name="assigned_staff_id" class="form-select border-secondary bg-white">
                                                            <option value="">-- ยังไม่ระบุผู้รับผิดชอบ --</option>
                                                            @foreach($staffs as $staff)
                                                                <option value="{{ $staff->user_id }}" {{ $issue->assigned_staff_id == $staff->user_id ? 'selected' : '' }}>
                                                                    {{ $staff->user->firstname }} {{ $staff->user->lastname }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <!-- Dropdown เลือกสถานะ -->
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">อัปเดตสถานะปัญหา <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-select border-primary bg-white" required>
                                                            <option value="pending" {{ $issue->status == 'pending' ? 'selected' : '' }}>🔴 รอดำเนินการ</option>
                                                            <option value="in_progress" {{ $issue->status == 'in_progress' ? 'selected' : '' }}>🟡 กำลังตรวจสอบ/แก้ใข</option>
                                                            <option value="resolved" {{ $issue->status == 'resolved' ? 'selected' : '' }}>🟢 แก้ไขเรียบร้อยแล้ว</option>
                                                            <option value="cancel" {{ $issue->status == 'cancel' ? 'selected' : '' }}>⚪ ยกเลิก</option>
                                                        </select>
                                                    </div>

                                                    <!-- ช่องคอมเมนต์ -->
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">บอกเจ้าหน้าที่ที่รับผิดชอบ</label>
                                                        <textarea name="admin_note" rows="4" class="form-control bg-white border">{{ $issue->admin_note }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">ระบุการแก้ไข หรือคำแนะนำที่จะส่งให้ผู้ใช้รับทราบ...</label>
                                                        <textarea name="staff_comment" rows="4" class="form-control bg-white border">{{ $issue->staff_comment }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background-color: #c2185b; border-color: #c2185b;">บันทึกการอัปเดต</button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-emoji-smile text-success" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3 text-muted fw-bold">ระบบทำงานปกติ</h5>
                                        <p class="text-muted small">ไม่มีการแจ้งปัญหาจากผู้ใช้งานในขณะนี้</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


