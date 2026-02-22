@extends('layouts.admin1')

@section('content')
<div class="row ">
                            <div class="col-12 col-lg-10 m-auto">
                                <form class="multisteps-form__form mb-8" action="{{ route('superadmin.tw_meters.store') }}"
                                    method="post"> {{-- Removed fixed height --}}
                                    @csrf
                                    <div class="card multisteps-form__panel p-3 border-radius-xl bg-white  js-active"
                                        data-animation="FadeIn" id="pd1">
                                        <h5 class="font-weight-bolder mb-0">เลือกผู้ใช้งานสำหรับมิเตอร์</h5>
                                        <div class="multisteps-form__content">
                                            <div class="row mt-3">
                                                <div class="col-12 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                                        <label class="form-check-label" for="selectAllUsers">เลือกทั้งหมด</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="user-list-container">
                                                        @forelse ($users as $userOption)
                                                            <div class="form-check">
                                                                <input class="form-check-input user-checkbox" type="checkbox"
                                                                    name="user_ids[]" value="{{ $userOption->id }}"
                                                                    id="user_{{ $userOption->id }}"
                                                                    {{ (is_array(old('user_ids')) && in_array($userOption->id, old('user_ids'))) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="user_{{ $userOption->id }}">
                                                                    {{ $userOption->firstname }} {{ $userOption->lastname }} ({{ $userOption->username }}) - {{ $userOption->email }}
                                                                </label>
                                                            </div>
                                                        @empty
                                                            <p class="text-center text-muted">ไม่พบผู้ใช้งานในระบบ</p>
                                                        @endforelse
                                                    </div>
                                                    @error('user_ids')
                                                        <div class="text-danger mt-2">({{ $message }})</div>
                                                    @enderror
                                                    @error('user_ids.*')
                                                        <div class="text-danger mt-2">({{ $message }})</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="button-row d-flex mt-4">
                                                <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next" data-id="2"
                                                    type="button" title="Next">ถัดไป</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card multisteps-form__panel p-3 border-radius-xl bg-white"
                                        data-animation="FadeIn" id="pd2">
                                        <h5 class="font-weight-bolder">ข้อมูลมิเตอร์ (สำหรับผู้ใช้งานที่เลือก)</h5>
                                        <div class="multisteps-form__content">
                                            <div class="row mt-3">
                                                <div class="col-12 col-sm-4">
                                                    <label for="meter_code_prefix">คำนำหน้าเลขมิเตอร์</label>
                                                    @error('meter_code_prefix')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <input type="text" class="form-control required" name="meter_code_prefix"
                                                        id="meter_code_prefix" value="{{ old('meter_code_prefix', 'MTR-') }}" placeholder="เช่น MTR-">
                                                    <div class="form-text">ระบบจะสร้างเลขมิเตอร์อัตโนมัติ: [คำนำหน้า]-[ตัวอักษรสุ่ม]</div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label>เลขมิเตอร์ตัวอย่าง</label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        id="meternumber_display" value="{{ $meternumber }}">
                                                    <div class="form-text">เลขมิเตอร์จริงจะถูกสร้างเมื่อบันทึก</div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label for="factory_no">รหัสมิเตอร์จากโรงงาน</label>
                                                    @error('factory_no')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <input type="text" class="form-control required" name="factory_no"
                                                        id="factory_no" value="{{ old('factory_no', $factory_no) }}">
                                                </div>

                                                <div class="col-12 col-sm-3">
                                                    <label for="metertype_id">ประเภทมิเตอร์
                                                        @error('metertype_id')
                                                            <span class="text-danger h-8">({{ $message }})</span>
                                                        @enderror
                                                    </label>
                                                    <select class="form-control required" name="metertype_id"
                                                        id="metertype_id">
                                                        <option value="">เลือก...</option>
                                                        @foreach ($meterTypes as $meter_type)
                                                            <option value="{{ $meter_type->id }}" {{ old('metertype_id') == $meter_type->id ? 'selected' : '' }}>
                                                                {{ $meter_type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <label>ราคาต่อหน่วย (บาท)</label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        name="counter_unit" id="counter_unit" value="">
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <label>ค่ารักษามิเตอร์ (บาท) </label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        name="reserve_price" id="reserve_price" value="">
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <label>ขนาดมิเตอร์ </label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        name="metersize" id="metersize" value="">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="undertake_zone_id">พื้นที่จัดเก็บ
                                                        @error('undertake_zone_id')
                                                            <span class="text-danger h-8">({{ $message }})</span>
                                                        @enderror
                                                    </label>
                                                    <select class="form-control required" name="undertake_zone_id"
                                                        id="undertake_zone_id" onchange="getSubzone()">
                                                        <option value="">เลือก...</option>
                                                        @foreach ($zones as $zone)
                                                            <option value="{{ $zone->id }}" {{ old('undertake_zone_id') == $zone->id ? 'selected' : '' }}>
                                                                {{ $zone->zone_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="undertake_subzone_id">เส้นทางจัดเก็บ
                                                        @error('undertake_subzone_id')
                                                            <span class="text-danger h-8">({{ $message }})</span>
                                                        @enderror
                                                    </label>
                                                    <select class="form-control" name="undertake_subzone_id"
                                                        id="undertake_subzone_id">
                                                        <option value="">เลือก...</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label for="initial_reading">เลขจดมิเตอร์เริ่มต้น</label>
                                                    @error('initial_reading')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <input type="number" step="0.01" class="form-control required" name="initial_reading"
                                                        id="initial_reading" value="{{ old('initial_reading', 0) }}">
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label for="current_active_reading">เลขจดมิเตอร์ปัจจุบัน</label>
                                                    @error('current_active_reading')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <input type="number" step="0.01" class="form-control required" name="current_active_reading"
                                                        id="current_active_reading" value="{{ old('current_active_reading', 0) }}">
                                                    <div class="form-text">สำหรับรอบบิลแรก เลขจดปัจจุบันจะเป็นค่าเริ่มต้นของรอบบิลถัดไป</div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label for="acceptance_date">วันที่ขอใช้น้ำ</label>
                                                    @error('acceptance_date')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <input type="date" class="form-control required" name="acceptance_date"
                                                        id="acceptance_date" value="{{ old('acceptance_date', $now) }}">
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label for="payment_id">วิธีชำระเงิน</label>
                                                    @error('payment_id')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <select name="payment_id" id="payment_id" class="form-control required">
                                                        <option value="">เลือก...</option>
                                                        @foreach ($paymentTypes as $payment_type)
                                                            <option value="{{ $payment_type->id }}" {{ old('payment_id') == $payment_type->id ? 'selected' : '' }}>
                                                                {{ $payment_type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label for="discounttype_id">ประเภทผู้ได้ส่วนลด</label>
                                                    @error('discounttype_id')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <select name="discounttype_id" id="discounttype_id" class="form-control">
                                                        <option value="">เลือก...</option>
                                                        {{-- @foreach ($discountTypes as $discount_type)
                                                            <option value="{{ $discount_type->id }}" {{ old('discounttype_id') == $discount_type->id ? 'selected' : '' }}>
                                                                {{ $discount_type->name }}
                                                            </option>
                                                        @endforeach --}}
                                                        <option value="1" selected>ไม่มีส่วนลด</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label for="recorder_id">ผู้บันทึก</label>
                                                    @error('recorder_id')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <select name="recorder_id" id="recorder_id" class="form-control">
                                                        <option value="">เลือก...</option>
                                                        @foreach (\App\Models\User::all() as $recorderOption)
                                                            <option value="{{ $recorderOption->id }}" {{ old('recorder_id', Auth::id()) == $recorderOption->id ? 'selected' : '' }}>
                                                                {{ $recorderOption->firstname }} {{ $recorderOption->lastname }} ({{ $recorderOption->username }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="status">สถานะมิเตอร์</label>
                                                    @error('status')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <select name="status" id="status" class="form-control required">
                                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                                        <option value="disconnected" {{ old('status') == 'disconnected' ? 'selected' : '' }}>ตัดน้ำ</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="owe_count">จำนวนรอบค้างชำระ</label>
                                                    @error('owe_count')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <input type="number" class="form-control" name="owe_count"
                                                        id="owe_count" value="{{ old('owe_count', 0) }}" min="0">
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="cutmeter" id="cutmeter" value="1" {{ old('cutmeter') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="cutmeter">ตัดน้ำ</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for="comment">หมายเหตุ</label>
                                                    <textarea class="form-control" name="comment" id="comment" rows="3">{{ old('comment') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="button-row d-flex mt-4">
                                                <button class="btn bg-gradient-light mb-0 js-btn-prev" data-id="1"
                                                    type="button" title="Prev">ย้อนกลับ</button>
                                                <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next" data-id="3"
                                                    type="button" title="Next">ถัดไป</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card multisteps-form__panel p-3 border-radius-xl bg-white"
                                        data-animation="FadeIn" id="pd3">
                                        <h5 class="font-weight-bolder">บันทึกข้อมูล</h5>
                                        <div class="multisteps-form__content text-center">
                                            <p class="mb-4">ตรวจสอบข้อมูลทั้งหมดก่อนบันทึก</p>
                                            <button class="btn btn-success ms-auto mb-0"
                                                type="submit" title="บันทึกข้อมูล">บันทึกข้อมูล</button>

                                            <div class="button-row d-flex mt-4">
                                                <button class="btn bg-gradient-light mb-0 js-btn-prev" data-id="2"
                                                    type="button" title="Prev">ย้อนกลับ</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
  <div class="card">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Add New Staff Member</h1>
        </div>
        <div class="card-body">
         
            <form action="{{ route('superadmin.staff.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="user_id" class="form-label">Select User:</label>
                    <select id="user_id" name="user_id" class="js-example-basic-single form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Select User --</option>
                        @foreach ($usersToAssign as $userOption)
                            <option value="{{ $userOption->id }}" {{ old('user_id') == $userOption->id ? 'selected' : '' }}>
                                {{ $userOption->firstname }} {{ $userOption->lastname }} ({{ $userOption->username }}) - {{ $userOption->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="role_name" class="form-label">Assign Role:</label>
                    <select id="role_name" name="role_name" class="js-example-basic-single form-select @error('role_name') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        @foreach ($assignableRoles as $role)
                            <option value="{{ $role->name }}" {{ old('role_name') == $role->name ? 'selected' : '' }}>
                                {{ $role->display_name ?? ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">Assign Role</button>
                    <a href="{{ route('superadmin.staff.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
@endsection
