@extends('layouts.welfare')
@section('title_page', 'กองทุนสวัสดิการ')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">🏢 กองทุนสวัสดิการฌาปนกิจ</h2>
                <p class="text-muted">บริหารจัดการเงินกำไรจากขยะเพื่อสวัสดิการชุมชน</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addPayoutModal">
                <i class="fas fa-plus me-1"></i> บันทึกขออนุมัติจ่ายสวัสดิการ
            </button>
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

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-success text-white h-100">
                    <div class="card-body p-4 text-center">
                        <h6 class="opacity-75 mb-2 text-uppercase fw-bold" style="font-size: 0.8rem;">ยอดเงินกองทุนคงเหลือ
                        </h6>
                        <h2 class="display-6 fw-bold mb-0">฿ {{ number_format($fundBalance, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-body p-4 text-center border-start border-primary border-5 rounded-4">
                        <h6 class="text-muted mb-2 text-uppercase fw-bold" style="font-size: 0.8rem;">กำไรสะสมจากการขายขยะ
                        </h6>
                        <h2 class="fw-bold text-primary mb-0">฿ {{ number_format($totalProfit, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-body p-4 text-center border-start border-danger border-5 rounded-4 text-danger">
                        <h6 class="text-muted mb-2 text-uppercase fw-bold" style="font-size: 0.8rem;">จ่ายสวัสดิการไปแล้ว
                        </h6>
                        <h2 class="fw-bold mb-0">- ฿ {{ number_format($totalPaid, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>ประวัติการขออนุมัติเบิกจ่าย</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="ps-4">วันที่</th>
                            <th>สมาชิก</th>
                            <th>ผู้เสียชีวิต</th>
                            <th>ทายาทผู้รับเงิน</th>
                            <th class="text-end">จำนวนเงิน</th>
                            <th class="text-center">เอกสาร/หลักฐาน</th>
                            <th class="text-center">สถานะอนุมัติ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayouts as $payout)
                            <tr>
                                <td class="ps-4 text-muted">{{ $payout->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $payout->user->firstname." ". $payout->user->lastname}}</div>
                                    <small class="text-muted">ID: #{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}</small>
                                    <div class="text-muted">{{ $payout->user->address." ".$payout->user->user_zone->zone_name }}</div>
                                </td>
                                                              <td>
                                    <div class="fw-bold text-dark">{{ $payout->deceased_name }}</div>
                                    <small class="text-muted">ID: #{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}</small>
                                </td>
                                <td>{{ $payout->beneficiary_name }}</td>
                                <td class="text-end fw-bold text-danger">฿ {{ number_format($payout->amount, 2) }}</td>
                                <td class="text-center">
                                    {{-- <a href="{{ route('admin.welfare.approval.pdf', $payout->id) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="fas fa-file-alt"></i> ใบเสนออนุมัติ
                                    </a> --}}
                                     <a href="{{ route('admin.welfare.payout.approval_page', $payout->id) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="fas fa-file-alt"></i> ใบเสนออนุมัติ
                                    </a>
                                    
                                    @if($payout->status === 'paid')
                                        <a href="{{ route('admin.welfare.payout.voucher', $payout->id) }}" target="_blank"
                                            class="btn btn-sm btn-outline-dark rounded-pill">
                                            <i class="fas fa-print"></i> พิมพ์ใบสำคัญ
                                        </a>
                                    @endif
                                    @if($payout->death_certificate_img)
                                        <a href="{{ asset('storage/' . $payout->death_certificate_img) }}" target="_blank"
                                            class="btn btn-sm btn-outline-info rounded-pill">
                                            <i class="fas fa-image"></i> ใบมรณบัตร
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($payout->status === 'paid')
                                        <span
                                            class="badge bg-success-subtle text-success rounded-pill px-3">อนุมัติ/จ่ายเรียบร้อย</span>
                                    @elseif($payout->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">ไม่อนุมัติ</span>
                                    @else
                                        <button class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm" data-bs-toggle="modal"
                                            data-bs-target="#approveModal{{ $payout->id }}">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $payout->currentStep->role_name ?? 'รอการอนุมัติ' }}
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal พิจารณาอนุมัติ Step -->
                            @if($payout->status === 'pending_approval')
                                <div class="modal fade" id="approveModal{{ $payout->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <div class="modal-header border-0">
                                                <h5 class="fw-bold mb-0">พิจารณาอนุมัติขั้นตอน
                                                    (#{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }})</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.welfare.payout.approve', $payout->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                 <div><strong>สมาชิก:</strong>
                                                                    {{ $payout->user->firstname." ". $payout->user->lastname}}</div>
                                                                <div><strong>ผู้เสียชีวิต:</strong>
                                                                    {{ $payout->deceased_name }}</div>
                                                                <div><strong>ทายาทผู้รับเงิน:</strong>
                                                                    {{ $payout->beneficiary_name }}</div>
                                                                <div><strong>จำนวนเงิน:</strong> ฿
                                                                    {{ number_format($payout->amount, 2) }}</div>
                                                                <div><strong>ขั้นตอนปัจจุบัน:</strong> <span
                                                                        class="badge bg-primary">{{ $payout->currentStep->role_name ?? '-' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold small">ประวัติการอนุมัติก่อนหน้า:</h6>
                                                            <ul class="list-group list-group-flush small">
                                                                @forelse($payout->logs as $log)
                                                                    <li class="list-group-item px-0 bg-transparent">
                                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                                        <strong>{{ $log->step->role_name ?? 'ผู้ยื่นคำร้อง' }}:</strong>
                                                                        {{ $log->actionBy->name }}
                                                                        <div class="text-muted" style="font-size: 0.75rem;">
                                                                            {{ $log->created_at->format('d/m/Y H:i') }}
                                                                            {{ $log->note ? "($log->note)" : '' }}</div>
                                                                    </li>
                                                                @empty
                                                                    <li class="list-group-item px-0 bg-transparent text-muted">
                                                                        ยังไม่มีประวัติการอนุมัติ</li>
                                                                @endforelse
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label small fw-bold text-muted">แนบไฟล์สแกนใบเสนออนุมัติที่มีลายเซ็น
                                                            (ถ้ามี)</label>
                                                        <input type="file" name="signed_doc"
                                                            class="form-control rounded-3 border-0 bg-light" accept=".pdf,image/*">
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label small fw-bold text-muted">ความเห็น /
                                                            หมายเหตุ</label>
                                                        <textarea name="note" class="form-control rounded-3 border-0 bg-light"
                                                            rows="2" placeholder="ระบุความเห็นหรือหมายเหตุเพิ่มเติม..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 d-flex justify-content-between">
                                                    <button type="submit" name="action" value="reject"
                                                        class="btn btn-outline-danger rounded-pill px-4">
                                                        <i class="fas fa-times me-1"></i> ปฏิเสธ / ส่งกลับ
                                                    </button>
                                                    <button type="submit" name="action" value="approve"
                                                        class="btn btn-success rounded-pill px-4 shadow-sm">
                                                        <i class="fas fa-check me-1"></i> ยืนยันอนุมัติขั้นตอนนี้
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">ยังไม่มีประวัติการเบิกจ่ายสวัสดิการ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $recentPayouts->links() }}
            </div>
        </div>
    </div>

    <!-- Modal สำหรับยื่นคำร้องเบิกจ่าย (ปรับปรุงให้เลือกสมาชิกกองทุนก่อน) -->
    <div class="modal fade" id="addPayoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0">
                    <h5 class="fw-bold mb-0">บันทึกขออนุมัติจ่ายเงินสวัสดิการ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.welfare.payout.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        

                        <!-- เลือกสมาชิกกองทุนเพื่อตรวจสอบสิทธิ์ -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">เลือกสมาชิกกองทุน (เจ้าของสิทธิ์)</label>
                            <select name="user_id" id="welfareUserSelect" class="form-select rounded-3 bg-light border-0"
                                required>
                                <option value="">-- ค้นหา / เลือกรายชื่อสมาชิกกองทุน --</option>
                                @foreach($welfareMembers as $member)
                                    <option value="{{ $member->user->id }}">
                                        {{ $member->user->name ?? ($member->user->firstname . ' ' . $member->user->lastname) }}
                                        (โทร: {{ $member->user->phone ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- กล่องแสดงผลการตรวจสอบสิทธิ์แบบเรียลไทม์ -->
                        <div id="eligibilityResultBox" class="mb-3" style="display: none;">
                            <div class="p-3 rounded-3 small" id="eligibilityContent"></div>
                        </div>

                        <!-- ระบุชื่อผู้เสียชีวิต (ถ้าเป็นตัวสมาชิกเอง หรือคนในครอบครัว) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">ชื่อผู้เสียชีวิต (กรณีเสียชีวิต)</label>
                            <input type="text" name="deceased_name" id="deceasedNameInput"
                                class="form-control rounded-3 bg-light border-0" required
                                placeholder="ระบุชื่อ-นามสกุลผู้เสียชีวิต">
                            <small class="text-muted" style="font-size: 0.75rem;">*หากตัวสมาชิกเสียชีวิตเอง
                                สามารถพิมพ์ซ้ำชื่อสมาชิก หรือระบุชื่อบุคคลในครอบครัวที่คุ้มครอง</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">ชื่อทายาทผู้รับเงิน</label>
                            <input type="text" name="beneficiary_name" class="form-control rounded-3 bg-light border-0"
                                required placeholder="ระบุชื่อทายาทผู้รับเงินแทน">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">จำนวนเงิน (บาท)</label>
                            <input type="number" name="amount"
                                class="form-control rounded-3 bg-light border-0 fw-bold text-primary" required
                                placeholder="5000.00" value="5000">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">หลักฐานใบมรณบัตร (แนบรูปถ่าย)</label>
                            <input type="file" name="death_certificate_img" class="form-control rounded-3 bg-light border-0"
                                accept="image/*">
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">หมายเหตุ / ข้อมูลเพิ่มเติม</label>
                            <textarea name="note" class="form-control rounded-3 bg-light border-0" rows="2"
                                placeholder="เช่น เลขที่หนังสือเทศบาล..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" id="submit-btn"
                            class="btn btn-primary rounded-pill px-4 shadow-sm">ยื่นเรื่องเข้าสู่ระบบอนุมัติ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
    document.getElementById('welfareUserSelect').addEventListener('change', function() {
        let userId = this.value;
        let resultBox = document.getElementById('eligibilityResultBox');
        let contentBox = document.getElementById('eligibilityContent');
        let submitBtn = document.getElementById('submitPayoutBtn');

        if (!userId) {
            resultBox.style.display = 'none';
            return;
        }

        // แสดงสถานะกำลังตรวจสอบ
        resultBox.style.display = 'block';
        contentBox.className = 'p-3 rounded-3 small bg-light text-muted';
        contentBox.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> กำลังตรวจสอบเกณฑ์สิทธิ์ของสมาชิก...';

        // ยิง AJAX ไปที่ฟังก์ชัน checkEligibility ใน WelfareController
        fetch(`/admin/welfare/check-eligibility/${userId}`)
            .then(response => response.json())
            .then(data => {
                let details = data.details;
                if (data.is_eligible) {
                    contentBox.className = 'p-3 rounded-3 small bg-success-subtle text-success border border-success-subtle';
                    contentBox.innerHTML = `
                        <div class="fw-bold mb-1"><i class="fas fa-check-circle me-1"></i> ผ่านเกณฑ์รับสวัสดิการกองทุน!</div>
                        <div>• ระยะเวลาสมาชิก: <strong>${details.months_active} เดือน</strong> (เกณฑ์ขั้นต่ำ ${details.required_months} เดือน)</div>
                        <div>• น้ำหนักขยะสะสม: <strong>${details.total_weight} กก.</strong> (เกณฑ์ขั้นต่ำ ${details.required_weight} กก.)</div>
                    `;
                    // เปิดให้กดปุ่มบันทึกได้
                    if(submitBtn) submitBtn.disabled = false;
                } else {
                    contentBox.className = 'p-3 rounded-3 small bg-danger-subtle text-danger border border-danger-subtle';
                    contentBox.innerHTML = `
                        <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> ไม่ผ่านเกณฑ์สวัสดิการกองทุน!</div>
                        <div>• ระยะเวลาสมาชิก: ${details.months_active} เดือน (ต้องการ ${details.required_months} เดือน)</div>
                        <div>• น้ำหนักขยะสะสม: ${details.total_weight} กก. (ต้องการ ${details.required_weight} กก.)</div>
                        <div class="mt-1 text-dark fw-bold">* สมาชิกรายนี้ยังไม่ผ่านเงื่อนไขการสะสมขยะหรืออายุสมาชิก</div>
                    `;
                    // ป้องกันไม่ให้กดบันทึก (ถ้าต้องการบังคับเข้มงวด) หรือปล่อยให้เตือนเฉยๆ
                    // $('#submit-btn').addClass('hidden')
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultBox.style.display = 'none';
            });
    });
</script>
@endsection