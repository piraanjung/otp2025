<div class="modal fade" id="issueListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-card-list"></i> ติดตามปัญหาที่แจ้ง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                @forelse($myIssues as $issue)
                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 15px;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span
                                    class="badge rounded-pill
                                    {{ $issue->status == 'pending' ? 'bg-secondary' : ($issue->status == 'in_progress' ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ $issue->status == 'pending' ? 'รอดำเนินการ' : ($issue->status == 'in_progress' ? 'กำลังตรวจสอบ' : 'แก้ไขแล้ว') }}
                                </span>
                                <small class="text-muted"
                                    style="font-size: 0.7rem;">{{ $issue->created_at->diffForHumans() }}</small>
                            </div>
                            <h6 class="fw-bold mb-1">
                                {{ $issue->issueType->name }}
                            </h6>

                            <p class="small text-muted mb-2">{{ $issue->description }}</p>

                            @if($issue->staff_comment)
                                <div class="p-2 rounded-3 mt-2" style="background: #e6fffa; border-left: 4px solid #38b2ac;">
                                    <small class="fw-bold text-success d-block"><i class="bi bi-chat-dots-fill"></i>
                                        คำแนะนำจากเจ้าหน้าที่:</small>
                                    <span class="small text-dark">{{ $issue->staff_comment }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">ยังไม่มีประวัติการแจ้งปัญหา</p>
                    </div>
                @endforelse
            </div>
            <div class="modal-footer border-0 bg-light">
                <button class="btn btn-danger w-100 rounded-pill fw-bold" data-bs-toggle="modal"
                    data-bs-target="#reportIssueModal" data-bs-dismiss="modal">
                    แจ้งเรื่องใหม่
                </button>
            </div>
        </div>
    </div>
</div>
