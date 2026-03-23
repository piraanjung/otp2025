@extends('layouts.super-admin')

@section('content')
<div class="container-fluid py-4">
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header pb-0">
                        <h6 class="fw-bold">📝 แก้ไขข้อมูล: {{ $user->firstname }} {{ $user->lastname }}</h6>
                    </div>
                    <div class="card-body">
                         <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-sm">ชื่อผู้ใช้งาน (Username)</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        name="username" required placeholder="สำหรับเข้าสู่ระบบ">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-sm">รหัสผ่าน (Password)</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        name="password" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label text-sm">คำนำหน้า</label>
                                    <input type="text" class="form-control" name="prefix" value="นาย">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-sm">ชื่อจริง</label>
                                    <input type="text" class="form-control @error('firstname') is-invalid @enderror"
                                        name="firstname" required value="xx">
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label text-sm">นามสกุล</label>
                                    <input type="text" class="form-control @error('lastname') is-invalid @enderror"
                                        name="lastname" required value="xxx">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-sm">เบอร์โทรศัพท์</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" value="0999999">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-sm">เลขบัตรประชาชน</label>
                                    <input type="text" class="form-control @error('id_card') is-invalid @enderror"
                                        name="id_card" value="122222222">
                                </div>
                            </div>

                            <hr class="horizontal dark my-4">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-primary fw-bold">โซน (Zone)</label>
                                <select class="form-select border-primary" id="zone_id" name="zone_id" required>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" {{ $user->zone_id == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->zone_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-primary fw-bold">ซอย/ชุมชนย่อย (Subzone)</label>
                                <select class="form-select border-primary" id="subzone_id" name="subzone_id" required>
                                    @foreach($subzones as $sub)
                                        <option value="{{ $sub->id }}" {{ $user->subzone_id == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->subzone_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ที่อยู่ (เลขที่บ้าน)</label>
                            <input type="text" class="form-control" name="address" value="{{ old('address', $user->address) }}">
                        </div>

                        {{-- แสดงที่อยู่จาก Org (Readonly) --}}
                        <div class="row p-3 bg-gray-100 rounded-3">
                            <div class="col-md-4">
                                <label class="text-xxs fw-bold">ตำบล</label>
                                <div class="border-bottom">{{ $defaultAddress['district'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-xxs fw-bold">อำเภอ</label>
                                <div class="border-bottom">{{ $defaultAddress['amphure'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-xxs fw-bold">จังหวัด</label>
                                <div class="border-bottom">{{ $defaultAddress['province'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header pb-0">
                        <h6 class="fw-bold">✅ สถานะบริการในปัจจุบัน</h6>
                    </div>
                    <div class="card-body">
                        {{-- เช็คว่ามี Account ในแต่ละตารางไหม --}}
                        <div class="form-check form-switch mb-3 p-3 bg-gray-100 rounded-3 border-start border-success border-4">
                            <input class="form-check-input ms-0" type="checkbox" name="svc_recycle" id="svc_recycle"
                                {{ $user->recycleAccount ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold ms-4" for="svc_recycle">ธนาคารขยะรีไซเคิล</label>
                        </div>

                        <div class="form-check form-switch mb-3 p-3 bg-gray-100 rounded-3 border-start border-info border-4">
                            <input class="form-check-input ms-0" type="checkbox" name="svc_food_waste" id="svc_food_waste"
                                {{ $user->foodWasteAccount ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold ms-4" for="svc_food_waste">ธนาคารขยะเปียก</label>
                        </div>

                        <div class="form-check form-switch mb-4 p-3 bg-gray-100 rounded-3 border-start border-warning border-4">
                            <input class="form-check-input ms-0" type="checkbox" name="svc_annual_trash" id="svc_annual_trash"
                                {{ $user->annualTrashSubscription ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold ms-4" for="svc_annual_trash">ค่าขยะรายปี (เทศบาล)</label>
                        </div>

                        <button type="submit" class="btn bg-gradient-primary w-100 py-3">อัปเดตข้อมูล</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    function getSubzones(zoneId, selectedSubzoneId = null) {
        if (!zoneId) return;

        var $subzoneSelect = $('#subzone_id');
        $subzoneSelect.empty().append('<option value="">-- กำลังโหลด... --</option>');

        $.ajax({
            url: `{{ url('admin/subzone') }}/${zoneId}/getSubzone`,
            type: "GET",
            success: function(data) {
                $subzoneSelect.empty().append('<option value="">-- เลือกซอย/ชุมชนย่อย --</option>');
                $.each(data, function(key, value) {
                    var isSelected = (value.id == selectedSubzoneId) ? 'selected' : '';
                    $subzoneSelect.append(`<option value="${value.id}" ${isSelected}>${value.subzone_name}</option>`);
                });
            }
        });
    }

    // เมื่อเปลี่ยน Zone
    $('#zone_id').on('change', function() {
        getSubzones($(this).val());
    });

    // โหลด Subzone ครั้งแรกเมื่อเปิดหน้า (กรณีมีข้อมูลเดิม)
    var currentZone = $('#zone_id').val();
    var currentSubzone = "{{ $user->subzone_id }}";
    if (currentZone) {
        getSubzones(currentZone, currentSubzone);
    }
});
</script>
@endsection

