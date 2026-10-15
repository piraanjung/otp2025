@extends('layouts.super-admin') 

@section('content') 
<div class="container-fluid py-4"> 
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST"> 
        @csrf 
        @method('PUT') 
        <div class="row"> 
            {{-- ฝั่งซ้าย: ข้อมูลผู้ใช้งาน --}} 
            <div class="col-lg-8"> 
                <div class="card mb-4 shadow-sm border-0"> 
                    <div class="card-header pb-0 bg-transparent border-0"> 
                        <h6 class="fw-bold"><i class="fas fa-user-edit text-primary me-2"></i> แก้ไขข้อมูลสมาชิก: {{ $user->firstname }} {{$user->lastname }}</h6> 
                    </div> 
                    <div class="card-body"> 
                        <div class="row"> 
                            <div class="col-md-6 mb-3"> 
                                <label class="form-label text-sm">ชื่อผู้ใช้งาน (Username)</label> 
                                <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" required value="{{ old('username', $user->username) }}" placeholder="สำหรับเข้าสู่ระบบ"> 
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror 
                            </div> 
                            <div class="col-md-6 mb-3"> 
                                <label class="form-label text-sm">รหัสผ่านใหม่ (ปล่อยว่างหากไม่เปลี่ยน)</label> 
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="••••••••"> 
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror 
                            </div> 
                            <div class="col-md-3 mb-3"> 
                                <label class="form-label text-sm">คำนำหน้า</label> 
                                <input type="text" class="form-control" name="prefix" value="{{ old('prefix', $user->prefix ?? 'นาย') }}"> 
                            </div> 
                            <div class="col-md-4 mb-3"> 
                                <label class="form-label text-sm">ชื่อจริง</label> 
                                <input type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" required value="{{ old('firstname', $user->firstname) }}"> 
                            </div> 
                            <div class="col-md-5 mb-3"> 
                                <label class="form-label text-sm">นามสกุล</label> 
                                <input type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" required value="{{ old('lastname', $user->lastname) }}"> 
                            </div> 
                            <div class="col-md-6 mb-3"> 
                                <label class="form-label text-sm">เบอร์โทรศัพท์</label> 
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone) }}"> 
                            </div> 
                            <div class="col-md-6 mb-3"> 
                                <label class="form-label text-sm">เลขบัตรประชาชน</label> 
                                <input type="text" class="form-control @error('id_card') is-invalid @enderror" name="id_card" value="{{ old('id_card', $user->id_card) }}"> 
                            </div> 
                        </div> 

                        <hr class="horizontal dark my-4"> 

                        {{-- 🏠 พื้นที่และที่อยู่ --}} 
                        <div class="row"> 
                            <div class="col-md-6 mb-3"> 
                                <label class="form-label text-primary fw-bold">โซน (Zone)</label> 
                                <select class="form-select border-primary" id="zone_id" name="zone_id" required> 
                                    @foreach($zones as $zone) 
                                        <option value="{{ $zone->id }}" {{ old('zone_id', $user->zone_id) ==$zone->id ? 'selected' : '' }}> 
                                            {{ $zone->zone_name }} 
                                        </option> 
                                    @endforeach 
                                </select> 
                            </div> 
                            <div class="col-md-6 mb-3"> 
                                <label class="form-label text-primary fw-bold">ซอย/ชุมชนย่อย (Subzone)</label> 
                                <select class="form-select border-primary" id="subzone_id" name="subzone_id" required> 
                                    @foreach($subzones as $sub) 
                                        <option value="{{ $sub->id }}" {{ old('subzone_id', $user->subzone_id) ==$sub->id ? 'selected' : '' }}> 
                                            {{ $sub->subzone_name }} 
                                        </option> 
                                    @endforeach 
                                </select> 
                            </div> 
                            <div class="col-md-12 mb-3"> 
                                <label class="form-label">เลขที่บ้าน / รายละเอียดที่อยู่เพิ่มเติม</label> 
                                <input type="text" class="form-control" name="address" value="{{ old('address', $user->address) }}"> 
                            </div> 
                        </div> 

                        {{-- แสดงที่อยู่จาก Org (Readonly) --}} 
                        <div class="row p-3 bg-gray-100 rounded-3"> 
                            <div class="col-md-4"> 
                                <label class="text-xxs fw-bold text-muted">ตำบล/แขวง</label> 
                                <div class="border-bottom py-1">{{ $defaultAddress['district'] ?? '-' }}</div> 
                            </div> 
                            <div class="col-md-4"> 
                                <label class="text-xxs fw-bold text-muted">อำเภอ/เขต</label> 
                                <div class="border-bottom py-1">{{ $defaultAddress['amphure'] ?? '-' }}</div> 
                            </div> 
                            <div class="col-md-4"> 
                                <label class="text-xxs fw-bold text-muted">จังหวัด</label> 
                                <div class="border-bottom py-1">{{ $defaultAddress['province'] ?? '-' }}</div> 
                            </div> 
                        </div> 
                    </div> 
                </div> 
            </div> 

            {{-- ฝั่งขวา: การจัดการบริการ (Sticky Sidebar) --}} 
            <div class="col-lg-4"> 
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 100;"> 
                    <div class="card-header pb-0 bg-transparent border-0"> 
                        <h6 class="fw-bold"><i class="fas fa-check-circle text-success me-2"></i> สถานะบริการในปัจจุบัน</h6> 
                    </div> 
                    <div class="card-body"> 
                        {{-- 1. ธนาคารขยะรีไซเคิล --}} 
                        <div class="form-check form-switch mb-3 p-3 bg-gray-100 rounded-3 border-start border-success border-4"> 
                            <input class="form-check-input ms-0" type="checkbox" name="svc_recycle" id="svc_recycle" value="1" {{ $user->recycleBankAccount ? 'checked' : '' }} {{ auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Recycle Bank Staff']) ? '' : 'disabled' }}> 
                            <label class="form-check-label fw-bold ms-4" for="svc_recycle">ธนาคารขยะรีไซเคิล</label> 
                        </div> 
                        {{-- 2. ธนาคารขยะเปียก --}} 
                        <div class="form-check form-switch mb-3 p-3 bg-gray-100 rounded-3 border-start border-info border-4"> 
                            <input class="form-check-input ms-0" type="checkbox" name="svc_food_waste" id="svc_food_waste" value="1" {{ $user->foodWasteBankAccount ? 'checked' : '' }} {{ auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'FoodWaste Staff']) ? '' : 'disabled' }}> 
                            <label class="form-check-label fw-bold ms-4" for="svc_food_waste">ธนาคารขยะเปียก</label> 
                        </div> 
                        {{-- 3. จัดการขยะรายปี --}} 
                        <div class="form-check form-switch mb-4 p-3 bg-gray-100 rounded-3 border-start border-warning border-4"> 
                            <input class="form-check-input ms-0" type="checkbox" name="svc_annual_trash" id="svc_annual_trash" value="1" {{ $user->annualTrashSubscription ? 'checked' : '' }} {{ auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Annual Trash Staff']) ? '' : 'disabled' }}> 
                            <label class="form-check-label fw-bold ms-4" for="svc_annual_trash">ค่าขยะรายปี (เทศบาล)</label> 
                        </div> 
                        <button type="submit" class="btn bg-gradient-primary w-100 py-3 mb-2">อัปเดตข้อมูล</button> 
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">ยกเลิก</a> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </form> 
</div> 

{{-- 💧 การ์ดแสดงรายการมิเตอร์น้ำประปาในครอบครอง --}} 
<div class="row mt-4"> 
    <div class="col-12"> 
        <div class="card shadow-sm border-0"> 
            <div class="card-header pb-0 bg-transparent d-flex justify-content-between align-items-center"> 
                <h6 class="fw-bold mb-0"> 
                    <i class="fas fa-tint text-info me-2"></i> มิเตอร์น้ำประปาในครอบครอง ({{ $user->usermeterinfos->count() }} เครื่อง) 
                </h6> 
                <button type="button" class="btn bg-gradient-info btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#addMeterModal"> 
                    <i class="fas fa-plus me-1"></i> ผูกมิเตอร์น้ำประปาใหม่ 
                </button> 
            </div> 
            <div class="card-body px-0 pt-2 pb-2"> 
                <div class="table-responsive p-0"> 
                    <table class="table align-items-center mb-0"> 
                        <thead> 
                            <tr> 
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">เลขมิเตอร์ / ชื่อระบุ</th> 
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานที่ติดตั้ง / บ้านเลขที่</th> 
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">โซน / ซอย</th> 
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ประเภทมิเตอร์</th> 
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th> 
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">จัดการ</th> 
                            </tr> 
                        </thead> 
                        <tbody> 
                            @forelse($user->usermeterinfos as $meter) 
                                <tr> 
                                    <td class="ps-4"> 
                                        <div class="d-flex flex-column"> 
                                            <h6 class="mb-0 text-sm font-weight-bold">{{ $meter->meternumber }}</h6> 
                                            <span class="text-xxs text-secondary">{{ $meter->submeter_name ?? 'มิเตอร์หลัก' }}</span> 
                                        </div> 
                                    </td> 
                                    <td> 
                                        <span class="text-xs font-weight-bold">{{ $meter->meter_address ?? '-' }}</span> 
                                    </td> 
                                    <td> 
                                        <div class="text-xs"> 
                                            <div><strong>โซน:</strong> {{ $meter->undertake_zone->zone_name ?? '-' }}</div> 
                                            <div class="text-muted"><strong>ซอย:</strong> {{ $meter->undertake_subzone->subzone_name ?? '-' }}</div> 
                                        </div> 
                                    </td> 
                                    <td> 
                                        <span class="text-xs">{{ $meter->meter_type->name ?? $meter->meter_type->meter_type_name ?? 'ประเภททั่วไป' }}</span> 
                                    </td> 
                                    <td class="align-middle text-center text-sm"> 
                                        @if($meter->status == 'active') 
                                            <span class="badge badge-sm bg-gradient-success">ใช้งานปกติ</span> 
                                        @else 
                                            <span class="badge badge-sm bg-gradient-secondary">ระงับการใช้งาน</span> 
                                        @endif 
                                    </td> 
                                    <td class="align-middle text-center"> 
                                        <a href="{{ route('admin.users.edit_invoices', $meter->meter_id ?? $meter->id) }}" class="btn btn-link text-info p-0 mb-0 me-2"> 
                                            <i class="fas fa-file-invoice"></i> ประวัติค่าน้ำ 
                                        </a> 
                                    </td> 
                                </tr> 
                            @empty 
                                <tr> 
                                    <td colspan="6" class="text-center py-4 text-muted text-sm"> ยังไม่มีมิเตอร์น้ำประปาผูกกับผู้ใช้งานคนนี้ </td> 
                                </tr> 
                            @endforelse 
                        </tbody> 
                    </table> 
                </div> 
            </div> 
        </div> 
    </div> 
</div> 

{{-- 📥 Modal สำหรับผูกมิเตอร์ประปาใหม่ --}} 
<div class="modal fade" id="addMeterModal" tabindex="-1" role="dialog" aria-labelledby="addMeterModalLabel" aria-hidden="true"> 
    <div class="modal-dialog modal-dialog-centered" role="document"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <h5 class="modal-title fw-bold" id="addMeterModalLabel">ผูกมิเตอร์น้ำประปาใหม่ให้ {{ $user->firstname }} {{$user->lastname }}</h5> 
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"> 
                    <span aria-hidden="true">&times;</span> 
                </button> 
            </div> 
            <form action="{{ route('admin.users.attach_meter', $user->id) }}" method="POST"> 
                @csrf 
                <div class="modal-body"> 
                    <div class="mb-3"> 
                        <label class="form-label text-sm fw-bold">เลขมิเตอร์</label> 
                        <input type="text" name="meternumber" value="{{  $generatedMeterId }}" readonly class="form-control"
                            style="background: #ded7d7;font-weight: bold;"
                            required placeholder="เช่น M-00123"> 
                    </div> 
                    <div class="mb-3"> 
                        <label class="form-label text-sm fw-bold">รหัสมิเตอร์ (จากโรงงาน)</label> 
                        <input type="text" name="foctory_no" value=""  class="form-control"  > 
                    </div> 
                    <div class="mb-3"> 
                        <label class="form-label text-sm">ชื่อระบุมิเตอร์ (ถ้ามี)</label> 
                        <input type="text" name="submeter_name" class="form-control" placeholder="เช่น บ้านเช่าหลังที่ 1 / ร้านค้า"> 
                    </div> 
                    <div class="mb-3"> 
                        <label class="form-label text-sm fw-bold">สถานที่ติดตั้ง / บ้านเลขที่</label> 
                        <input type="text" name="meter_address" class="form-control" required value="{{ $user->address }}" placeholder="เช่น 45/2 หมู่ 3"> 
                    </div> 
                    <div class="row"> 
                        <div class="col-md-6 mb-3"> 
                            <label class="form-label text-sm fw-bold">โซนประจำมิเตอร์</label> 
                            <select name="undertake_zone_id" id="undertake_zone_id" class="form-select" required> 
                                <option value="">-- เลือกโซน --</option> 
                                @foreach($zones as $zone) 
                                    <option value="{{ $zone->id }}" {{ $user->zone_id == $zone->id ? 'selected' : '' }}>{{ $zone->zone_name }}</option> 
                                @endforeach 
                            </select> 
                        </div> 
                        <div class="col-md-6 mb-3"> 
                            <label class="form-label text-sm fw-bold">เส้นทางจดมิเตอร์ (ซอย)</label> 
                            <select name="undertake_subzone_id" id="modal_undertake_subzone_id" class="form-select" required> 
                                <option value="">-- กรุณาเลือกโซนก่อน --</option> 
                            </select> 
                        </div> 
                        <div class="col-md-12 mb-3"> 
                            <label class="form-label text-sm fw-bold">ประเภทมิเตอร์</label> 
                            <select name="metertype_id" class="form-select" required> 
                                <option value="">-- เลือกประเภท --</option> 
                                @foreach($meterTypes ?? [] as $type) 
                                    <option value="{{ $type->id }}">{{ $type->name ?? $type->meter_type_name }}</option> 
                                @endforeach 
                            </select> 
                        </div> 
                    </div> 
                </div> 
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-link text-secondary" data-bs-dismiss="modal">ยกเลิก</button> 
                    <button type="submit" class="btn bg-gradient-info">บันทึกผูกมิเตอร์</button> 
                </div> 
            </form> 
        </div> 
    </div> 
</div> 
@endsection 

@section('script') 
<script> 
$(document).ready(function() { 
    // ฟังก์ชันโหลด Subzone ตาม Target Select Element
    function loadSubzones(zoneId, $targetSelect, selectedSubzoneId = null) { 
        if (!zoneId) return; 
        $targetSelect.empty().append('<option value="">-- กำลังโหลด... --</option>'); 

        $.ajax({ 
            url: `{{ url('admin/subzone') }}/${zoneId}/getSubzone`, 
            type: "GET", 
            success: function(data) { 
                $targetSelect.empty().append('<option value="">-- เลือกซอย/ชุมชนย่อย --</option>'); 
                $.each(data, function(key, value) { 
                    var isSelected = (value.id == selectedSubzoneId) ? 'selected' : ''; 
                    $targetSelect.append(`<option value="${value.id}" ${isSelected}>${value.subzone_name}</option>`); 
                }); 
            } 
        }); 
    } 

    // 1. สคริปต์สลับโซนสำหรับฟอร์มแก้ไขสมาชิกหลัก
    $('#zone_id').on('change', function() { 
        loadSubzones($(this).val(),$('#subzone_id')); 
    }); 

    // 2. สคริปต์สลับโซนสำหรับ Modal ผูกมิเตอร์
    $('#undertake_zone_id').on('change', function() { 
        loadSubzones($(this).val(),$('#modal_undertake_subzone_id')); 
    }); 

    // 3. โหลด Subzone เริ่มต้นของ Modal เมื่อเปิดหน้า (ถ้ามี zone_id เดิม)
    var currentModalZone = $('#undertake_zone_id').val(); 
    var currentModalSubzone = "{{ old('undertake_subzone_id', $user->subzone_id) }}"; 
    if (currentModalZone) { 
        loadSubzones(currentModalZone, $('#modal_undertake_subzone_id'), currentModalSubzone); 
    } 
}); 
</script> 
@endsection