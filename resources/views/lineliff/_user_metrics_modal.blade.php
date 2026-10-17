<div class="modal fade" id="userMetricsModal" tabindex="-1" aria-labelledby="userMetricsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 1.5em; border: none;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="userMetricsModalLabel">ตั้งค่าข้อมูลร่างกาย</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.update_metrics') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">เพศ</label>
                                <select name="gender" class="form-select rounded-pill">
                                    <option value="male" {{ Auth::user()->gender == 'male' ? 'selected' : '' }}>ชาย
                                    </option>
                                    <option value="female" {{ Auth::user()->gender == 'female' ? 'selected' : '' }}>หญิง
                                    </option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">อายุ (ปี)</label>
                                <input type="number" name="age" class="form-control rounded-pill"
                                    value="{{ Auth::user()->age }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">น้ำหนัก (kg)</label>
                                <input type="number" step="0.1" name="weight" class="form-control rounded-pill"
                                    value="{{ Auth::user()->weight }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">ส่วนสูง (cm)</label>
                                <input type="number" name="height" class="form-control rounded-pill"
                                    value="{{ Auth::user()->height }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit"
                            class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
