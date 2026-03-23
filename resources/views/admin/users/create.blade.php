@extends('layouts.super-admin')

@section('title_page', 'เพิ่มผู้ใช้งานใหม่')

@section('content')
    <div class="container-fluid py-4">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show text-white mx-4" role="alert">
                <span class="alert-text"><strong>เกิดข้อผิดพลาด!</strong> กรุณาตรวจสอบข้อมูลในช่องที่มีสีแดง</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="row">
                {{-- ฝั่งซ้าย: ข้อมูลผู้ใช้งาน --}}
                <div class="col-lg-8">
                    <div class="card mb-4 shadow-sm border-0">
                        <div class="card-header pb-0 bg-transparent border-0">
                            <h6 class="fw-bold"><i class="fas fa-id-card text-primary me-2"></i>
                                ลงทะเบียนสมาชิกและเปิดสิทธิ์บริการ</h6>
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

                            {{-- 🏠 พื้นที่และที่อยู่ --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary fw-bold">โซน (Zone)</label>
                                    <select class="form-select border-primary @error('zone_id') is-invalid @enderror"
                                        id="zone_id" name="zone_id" required>
                                        <option value="">-- เลือกโซน --</option>
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->zone_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary fw-bold">ซอย/ชุมชนย่อย (Subzone)</label>
                                    <select class="form-select border-primary @error('subzone_id') is-invalid @enderror"
                                        id="subzone_id" name="subzone_id" required>
                                        <option value="">-- กรุณาเลือกโซนก่อน --</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">เลขที่บ้าน / รายละเอียดที่อยู่เพิ่มเติม</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                        name="address" placeholder="เช่น 123/4 หมู่ 1" value="12">
                                </div>
                            </div>

                            <div class="row p-3 bg-gray-100 rounded-3">
                                <div class="col-md-4">
                                    <label class="text-xxs text-uppercase fw-bold text-muted">ตำบล/แขวง</label>
                                    <div class="p-2 border-bottom">{{ $defaultAddress['tambon'] }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-xxs text-uppercase fw-bold text-muted">อำเภอ/เขต</label>
                                    <div class="p-2 border-bottom">{{ $defaultAddress['district'] }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-xxs text-uppercase fw-bold text-muted">จังหวัด</label>
                                    <div class="p-2 border-bottom">{{ $defaultAddress['province'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ฝั่งขวา: การเปิดบริการ (Sticky Sidebar) --}}
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 100;">
                        <div class="card-header pb-0 bg-transparent border-0">
                            <h6 class="fw-bold"><i class="fas fa-check-circle text-success me-2"></i> เปิดใช้งานบริการ</h6>
                        </div>
                        <div class="card-body">
                            <div
                                class="form-check form-switch mb-3 p-3 bg-gray-100 rounded-3 border-start border-success border-4">
                                <input class="form-check-input ms-0 chk-service" type="checkbox" name="svc_recycle"
                                    id="svc_recycle" checked>
                                <label class="form-check-label fw-bold ms-4" for="svc_recycle">ธนาคารขยะรีไซเคิล</label>
                                <p class="text-xxs mb-0 ms-4 text-muted">เปิดบัญชีสะสมทรัพย์และแต้ม</p>
                            </div>

                            <div
                                class="form-check form-switch mb-3 p-3 bg-gray-100 rounded-3 border-start border-info border-4">
                                <input class="form-check-input ms-0 chk-service" type="checkbox" name="svc_food_waste"
                                    id="svc_food_waste">
                                <label class="form-check-label fw-bold ms-4" for="svc_food_waste">ธนาคารขยะเปียก</label>
                                <p class="text-xxs mb-0 ms-4 text-muted">ระบบถังหมักปุ๋ย AiroBact</p>
                            </div>

                            <div
                                class="form-check form-switch mb-4 p-3 bg-gray-100 rounded-3 border-start border-warning border-4">
                                <input class="form-check-input ms-0 chk-service" type="checkbox" name="svc_annual_trash"
                                    id="svc_annual_trash" checked>
                                <label class="form-check-label fw-bold ms-4" for="svc_annual_trash">ค่าขยะรายปี
                                    (เทศบาล)</label>
                                <p class="text-xxs mb-0 ms-4 text-muted">สิทธิ์ยกเว้น/จ่ายค่าขยะรายเดือน</p>
                            </div>

                            <button type="submit"
                                class="btn bg-gradient-primary w-100 py-3 mb-2">บันทึกและเปิดสิทธิ์</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">ยกเลิก</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // 1. Cascading Select: Zone -> Subzone
            $('#zone_id').on('change', function () {
                var zoneId = $(this).val();
                var $subzoneSelect = $('#subzone_id');

                $subzoneSelect.empty().append('<option value="">-- กำลังโหลด... --</option>');

                if (zoneId) {
                    $.ajax({
                        url: `{{ url('admin/subzone/${zoneId}/getSubzone ') }}`,
                        type: "GET",
                        success: function (data) {
                            console.log('da', data)
                            $subzoneSelect.empty().append('<option value="">-- เลือกซอย/ชุมชนย่อย --</option>');
                            $.each(data, function (key, value) {
                                $subzoneSelect.append('<option value="' + value.id + '">' + value.subzone_name + '</option>');
                            });
                        }
                    });
                } else {
                    $subzoneSelect.empty().append('<option value="">-- กรุณาเลือกโซนก่อน --</option>');
                }
            });
        });
    </script>
@endsection
