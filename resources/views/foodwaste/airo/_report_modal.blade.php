<div class="modal fade" id="reportIssueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-octagon-fill"></i> แจ้งปัญหาถังหมัก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('foodwaste.airo.report_issue') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">เลือกปัญหาที่ท่านพบ เพื่อรับคำแนะนำเบื้องต้นและแจ้งเจ้าหน้าที่</p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">หัวข้อปัญหา</label>
                        <select name="issue_type" class="form-select border-0 bg-light rounded-3" required>
                            <option value="">-- โปรดเลือกปัญหา --</option>
                            {{-- <option value="smell">ถังหมักมีกลิ่นเหม็นเน่า</option>
                            <option value="maggots">พบหนอน/แมลงเยอะเกินไป</option>
                            <option value="wet">ถังแฉะ มีน้ำเยิ้ม</option>
                            <option value="mold">พบราสีดำ (กังวลว่าเป็นเชื้อราไม่ดี)</option>
                            <option value="other">อื่นๆ (โปรดระบุ)</option> --}}
                            @foreach ($issueTypes as $issueType)
                                <option value="{{ $issueType->id }}">{{ $issueType->name }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div id="auto-advice" class="alert alert-warning d-none"></div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">รายละเอียดเพิ่มเติม</label>
                        <textarea name="description" class="form-control border-0 bg-light rounded-3" rows="3" placeholder="อธิบายอาการเพิ่มเติม..."></textarea>
                    </div>

                    <div class="alert alert-info py-2 rounded-3 border-0 shadow-sm" style="font-size: 0.8rem;">
                        <i class="bi bi-info-circle-fill"></i> เมื่อส่งข้อมูลแล้ว เจ้าหน้าที่จะรีบตรวจสอบและตอบกลับผ่าน LINE ครับ
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold py-2 shadow-sm">ส่งข้อมูลแจ้งปัญหา</button>
                </div>
            </form>
        </div>
    </div>
</div>
