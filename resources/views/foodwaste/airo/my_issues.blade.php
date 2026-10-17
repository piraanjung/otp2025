<div class="container py-4" style="max-width: 500px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">รายการแจ้งปัญหา</h4>
        <button class="btn btn-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#reportIssueModal">
            + แจ้งเรื่องใหม่
        </button>
    </div>

    @foreach($myIssues as $issue)
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 15px;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="fw-bold">
                    @if($issue->issue_type == 'smell') 🤢 กลิ่นเหม็น
                    @elseif($issue->issue_type == 'maggots') 🐛 พบหนอน
                    @else ⚠️ อื่นๆ @endif
                </span>
                <span class="badge rounded-pill
                    {{ $issue->status == 'pending' ? 'bg-secondary' : ($issue->status == 'in_progress' ? 'bg-warning text-dark' : 'bg-success') }}">
                    {{ $issue->status == 'pending' ? 'รอดำเนินการ' : ($issue->status == 'in_progress' ? 'กำลังตรวจสอบ' : 'แก้ไขแล้ว') }}
                </span>
            </div>

            <p class="small text-muted mb-2">{{ $issue->description }}</p>

            @if($issue->staff_comment)
                <div class="p-2 bg-light rounded-3 mt-2" style="border-left: 3px solid #11998e;">
                    <small class="fw-bold text-success">เจ้าหน้าที่ตอบกลับ:</small>
                    <p class="mb-0 x-small">{{ $issue->staff_comment }}</p>
                </div>
            @endif

            <div class="text-end mt-2">
                <small class="text-muted" style="font-size: 0.7rem;">{{ $issue->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>
    @endforeach
</div>
