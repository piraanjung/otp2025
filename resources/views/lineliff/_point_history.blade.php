            <div class="modal fade" id="pointHistoryModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content" style="border-radius: 1.5rem; border: none;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-success"><i class="bi bi-clock-history"></i>
                                ประวัติแต้มสะสม</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            @php
                                // 1. เช็คก่อนว่ามี Preference หรือไม่ (ใช้ Optional หรือ Null Coalescing)
                                $waste_pref = $user->foodwastePreference;
                                $waste_preference_id = $waste_pref ? $waste_pref->id : null;

                                // 2. ถ้ามี ID ค่อยไปดึง Transaction ถ้าไม่มีให้เป็น Collection ว่าง
                                $transactions = $waste_preference_id
                                    ? App\Models\FoodWaste\FoodWasteTransaction::where('fw_pref_id_fk', $waste_preference_id)
                                        ->latest()->take(10)->get()
                                    : collect(); // ส่ง Collection ว่างไปเพื่อให้ Loop @foreach ไม่พัง
                            @endphp

                            @if($transactions->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p>ยังไม่มีประวัติการรับแต้ม</p>
                                </div>
                            @else
                                <div class="timeline">
                                    @foreach($transactions as $trx)
                                        <div class="d-flex align-items-center mb-3 p-3 bg-light" style="border-radius: 1rem;">
                                            <div class="flex-shrink-0">
                                                @if($trx->points > 0)
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </div>
                                                @else
                                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="bi bi-dash-lg"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $trx->note }}</div>
                                                <div class="small text-muted">{{ $trx->created_at->format('d M Y | H:i') }}
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold {{ $trx->points > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ ($trx->points > 0 ? '+' : '') . $trx->points }}
                                                </div>
                                                <div class="small text-muted" style="font-size: 0.7rem;">PTS</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light w-100 rounded-pill fw-bold"
                                data-bs-dismiss="modal">ปิดหน้าต่าง</button>
                        </div>
                    </div>
                </div>
            </div>
