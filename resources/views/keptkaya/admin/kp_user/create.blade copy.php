@extends('layouts.keptkaya')

@section('title_page')
    ข้อมูลผู้ใช้งานระบบ
@endsection

@section('url')
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">แอดมิน</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">ผู้ใช้งานระบบ</li>
@endsection
@section('style')
    <style>
        .hidden {
            display: none;
        }

        .show {
            display: block;
        }
    </style>
@endsection
@section('content')
  <div class="mb-3">
                           
    <form action="{{ route('keptkaya.admin.kp_user.store_multi_users') }}" method="post">
         @csrf
          <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                <label class="form-check-label" for="selectAllUsers">เลือกทั้งหมด</label>
                            </div>
                        </div>
        <input type="submit" value="บันทึก" class="btn btn-info">
                        <textarea name="user_selected_data_json" cols="100" id="user_selected_data_json" class=""></textarea>
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>ชื่อ-สกุล</th>
                    <th>zone</th>
                    <th>zone_block</th>
                    <th>usergroup</th>
                    <th>bin_count</th>
                    <th>as_tbank</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>
                        <select class="form-control form-control-sm kp-usergroup-select" id="kp_usergroup_selected_for_all"> {{-- Unique name for user ID --}}
                                                    <option value="">เลือก...</option>
                                                    @foreach ($kp_usergroups as $kp_usergroup)
                                                        <option value="{{ $kp_usergroup->id }}">
                                                            {{ $kp_usergroup->usergroup_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                    </th>
                    <th></th>
                    <th>
                         <select class="form-control form-control-sm kp-as-tbank-select"> 
                               
                        <option value="1">สมาชิกธนาคารขยะ</option>
                        <option value="0">สมาชิกจัดเก็บขยะรายปี</option> 
                         </select>
                    </th>
                </tr>

                
            </thead>
             <tbody>
                                    @forelse ($users as $key => $user)
                                        <tr data-user-id="{{ $user->id }}" class="user-row"> {{-- Add data-user-id to row --}}
                                            <td>
                                                <input class="form-check-input user-checkbox" type="checkbox"
                                                    name="selected_user_ids[]" {{-- Changed name to avoid conflict with other inputs --}}
                                                    value="{{ $user->id }}"
                                                    id="user_checkbox_{{ $user->id }}" {{-- Unique ID for checkbox --}}
                                                    {{ (is_array(old('selected_user_ids')) && in_array($user->id, old('selected_user_ids'))) ? 'checked' : '' }}>
                                                <label class="form-check-label d-none" for="user_checkbox_{{ $user->id }}"></label> {{-- Hidden label for accessibility --}}
                                            </td>
                                            <td>
                                                <h6 class="mb-0 text-dark font-weight-bold text-sm">
                                                    {{ $user->firstname . " " . $user->lastname }}
                                                    <small class="d-block text-muted">({{ $user->username }})</small>
                                                </h6>
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm kp-tzone-select" name="user[{{$user->id}}][kp_tzone_idfk]"> {{-- Unique name for user ID --}}
                                                    <option value="">เลือก...</option>
                                                    @foreach ($zones as $zone)
                                                        <option value="{{ $zone->id }}" {{ ($user->zone_id == $zone->id) ? 'selected' : '' }}>
                                                            {{ $zone->zone_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm kp-tzoneblock-select" name="user[{{$user->id}}][kp_tzoneblock_idfk]"> {{-- Unique name for user ID --}}
                                                    <option value="">เลือก...</option>
                                                    {{-- Options will be populated by JavaScript based on Zone selection --}}
                                                    {{-- Initial population for existing user --}}
                                                    @if($user->zone_block_id)
                                                        <option value="{{ $user->zone_block_id }}" selected>{{ $user->user_zone_block->zone_block_name ?? 'N/A' }}</option>
                                                    @endif
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm kp-usergroup-select" name="user[{{$user->id}}][kp_usergroup_idfk]"> {{-- Unique name for user ID --}}
                                                    <option value="">เลือก...</option>
                                                    @foreach ($kp_usergroups as $kp_usergroup)
                                                        <option value="{{ $kp_usergroup->id }}" {{ (old("user.{$user->id}.kp_kp_usergroup_idfk") == $kp_usergroup->id || ($user->kp_usergroup_id == $kp_usergroup->id)) ? 'selected' : '' }}>
                                                            {{ $kp_usergroup->usergroup_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="user[{{$user->id}}][bin_count]" class="form-control form-control-sm kp-bin-count-input" value="{{ old("user.{$user->id}.bin_count", 1) }}" min="1"></td> {{-- Unique name and default 1 --}}
                                            <td>
                                                <select class="form-control form-control-sm kp-as-tbank-select" name="user[{{$user->id}}][as_tbank]"> 
                                                    @if (isset( $user->user_kp_infos->as_tbank))
                                                        <option value="1" {{ $user->user_kp_infos->as_tbank == 1 ? 'selected' : '' }}>สมาชิกธนาคารขยะ</option>
                                                        <option value="0" {{ collect($user->user_kp_infos)->isEmpty() | $user->user_kp_infos->as_tbank == 0 ? 'selected' : '' }}>สมาชิกจัดเก็บขยะรายปี</option> 
                                                    @else
                                                    <option value="1">สมาชิกธนาคารขยะ</option>
                                                    <option value="0" selected>สมาชิกจัดเก็บขยะรายปี</option> 
                                                    @endif
                                                    
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">ไม่พบผู้ใช้งานที่ยังไม่เป็นสมาชิก Keptkaya</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
    </form>

    <div class="row mb-3">
        <div class="col-12">
            <label for="user_select">เลือกผู้ใช้งานที่มีอยู่</label>
            <select name="user_select" id="user_select" class="form-control">
                <option value="">-- เลือกผู้ใช้งาน --</option>
                {{-- Example: Assuming $users is passed from backend --}}
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->firstname }} {{ $user->lastname }} (ID: {{ $user->id_card }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="multisteps-form mb-5">
                        <div class="row">
                            <div class="col-12 col-lg-8 mx-auto my-5">
                                <div class="multisteps-form__progress">
                                    <button class="multisteps-form__progress-btn js-active" id="b1" data-id="1"
                                        type="button" title="User Info">
                                        <span>ข้อมูลผู้ใช้งาน</span>
                                    </button>
                                    <button class="multisteps-form__progress-btn" id="b2" data-id="2" type="button"
                                        title="Address">ข้อมูลขอใช้บริการจัดเก็บขยะ</button>
                                    <button class="multisteps-form__progress-btn" id="b3" data-id="3" type="button"
                                        title="System Info">ตั้งค่าเข้าใช้งานระบบ</button>
                                    <button class="multisteps-form__progress-btn" id="b4" data-id="4" type="button"
                                        title="Socials">บันทึก</button>

                                </div>
                            </div>
                        </div>

                        <div class="row ">
                            <div class="col-12 col-lg-10 m-auto">
                                <form class="multisteps-form__form mb-8"
                                    action="{{ route('keptkaya.admin.kp_user.store') }}" method="post"
                                    style="height: 492px;">
                                    @csrf
                                    <div class="card multisteps-form__panel p-3 border-radius-xl bg-white  js-active"
                                        data-animation="FadeIn" id="pd1">
                                        <h5 class="font-weight-bolder mb-0">ข้อมูลผู้ใช้งาน</h5>
                                        <div class="multisteps-form__content">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="card card-primary card-outline">
                                                        <div class="card-body box-profile">
                                                            <div class="text-center">
                                                                <img class="avatar avatar-xl position-relative"
                                                                    src="{{ asset('soft-ui/assets/img/bruce-mars.jpg') }}"
                                                                    alt="User profile picture">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="row">
                                                        <div class="col-12 col-sm-2">
                                                            <label for="feGender">คำนำหน้า
                                                                @error('prefix_select')

                                                                    <div class="text-danger h-8">({{ $message }})</div>
                                                                @enderror
                                                            </label>
                                                            <select name="prefix_select" id="prefix_select"
                                                                class="form-control">
                                                                <option value="">เลือก..</option>
                                                                <option value="คุณ">คุณ</option>
                                                                <option value="นาย">นาย</option>
                                                                <option value="นาง">นาง</option>
                                                                <option value="นส.">นส.</option>
                                                                <option value="other">อื่นๆ</option>
                                                            </select>
                                                            <input type="text" class="form-control hidden mt-1"
                                                                id="prefix_text" name="prefix_text" value=""
                                                                placeholder="ระบุ...">
                                                        </div>
                                                        <div class="col-12 col-sm-4">
                                                            <label for="feFirstName">ชื่อ
                                                                @error('firstname')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="firstname"
                                                                name="firstname" value="test">
                                                        </div>
                                                        <div class="col-12 col-sm-4">
                                                            <label for="feFirstName">สกุล
                                                                @error('lastname')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="lastname"
                                                                name="lastname" value="testlastname">
                                                        </div>

                                                        <div class="col-12 col-sm-2">
                                                            <label for="feGender">เพศ
                                                                @error('gender')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <select name="gender" id="gender" class="form-control">
                                                                <option value="0">เลือก..</option>
                                                                <option value="m" selected>ชาย</option>
                                                                <option value="w">หญิง</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <label for="feID_card">เลขบัตรประจำตัวประชาชน
                                                                @error('id_card')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="id_card"
                                                                name="id_card" value="1111111">
                                                        </div>

                                                        <div class="col-12 col-sm-6">
                                                            <label for="fePhone">เบอร์โทรศัพท์
                                                                @error('phone')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="phone" name="phone"
                                                                value="09888888">
                                                        </div>
                                                        <div class="col-12 col-sm-3">
                                                            <label for="feInputAddress">ที่อยู่
                                                                @error('address')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="address"
                                                                name="address" value="1/2">
                                                        </div>
                                                        <div class="col-12 col-sm-2">
                                                            <label>หมู่ที่
                                                                @error('zone_id')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <select class="form-control" name="zone_id" id="zone_id">
                                                                <option>เลือก...</option>
                                                                @foreach ($zones as $zone)
                                                                    <option value="{{ $zone->id }}" selected>
                                                                        {{ $zone->zone_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-sm-3">
                                                            <label>จังหวัด
                                                                @error('province_code')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <select class="form-control bg-gray-200" name="province_code"
                                                                id="province_code" onchange="getDistrict()">
                                                                <option value="35" selected>ยโสธร</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-sm-2">
                                                            <label>อำเภอ
                                                                @error('district_code')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <select class="form-control bg-gray-200" name="district_code"
                                                                id="district_code" onchange="getTambon()">
                                                                <option value="3508">เลิงนกทา</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-sm-2">
                                                            <label>ตำบล
                                                                @error('tambon_code')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <select class="form-control bg-gray-200" name="tambon_code"
                                                                id="tambon_code" onchange="getZone()">
                                                                <option value="350805">ห้องแซง</option>

                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="button-row d-flex mt-2">
                                                <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next" data-id="2"
                                                    type="button" title="Next">Next</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card multisteps-form__panel p-3 border-radius-xl bg-white"
                                        data-animation="FadeIn" id="pd2">
                                        <h5 class="font-weight-bolder">ข้อมูลขอใช้บริการจัดเก็บขยะ</h5>
                                        <div class="multisteps-form__content">
                                            <div class="row mt-3">
                                                <div class="col-12 col-sm-6">
                                                    <label>เลขที่ผู้ขอใช้บริการจัดเก็บขยะ</label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        name="new_meter_id" value="{{ $usernumber }}">
                                                </div>
                                                <div class="col-12 col-sm-6">

                                                </div>

                                                <div class="col-12 col-sm-3">
                                                    <label>ประเภทผู้ใช้งาน
                                                        @error('usergroup')
                                                            <span class="text-danger h-8">({{ $message }})</span>
                                                        @enderror
                                                    </label>
                                                    <select class="form-control" name="usergroup" id="usergroup">
                                                        <option value="">เลือก...</option>
                                                        @foreach ($kp_usergroups as $usergroup)
                                                            <option value="{{ $usergroup->id }}">
                                                                {{ $usergroup->usergroup_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <label>ราคาต่อหน่วย (บาท/ถัง)</label>
                                                    <input type="text" class="form-control" readonly
                                                        name="rate_payment_per_year" id="rate_payment_per_year" value="">
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <label>จำนวนถังที่ใช้(ถัง) </label>
                                                    @error('bin_quantity')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <input type="text" class="form-control" name="bin_quantity"
                                                        id="bin_quantity" value="">
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <label>เป็นเงิน(บาท/ปี) </label>
                                                    <input type="text" class="form-control" name="payment_per_year"
                                                        id="payment_per_year" readonly>
                                                </div>


                                            </div>
                                            <div class="button-row d-flex mt-5 mb-5">
                                                <button class="btn bg-gradient-light mb-0 js-btn-prev" data-id="1"
                                                    type="button" title="Prev">Prev</button>
                                                <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next" data-id="3"
                                                    type="button" title="Next">Next</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card multisteps-form__panel p-3 border-radius-xl bg-white"
                                        data-animation="FadeIn" id="pd3">
                                        <h5 class="font-weight-bolder">ตั้งค่าเข้าใช้งานระบบ</h5>
                                        <div class="multisteps-form__content ">
                                            <div class="mt-3 row">
                                                <div class="col-6">
                                                    <label>User name</label>
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="username" value="{{ $username }}">
                                                </div>
                                                <div class="col-6">
                                                    <label>Password</label>
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="password" value="{{$password}}">
                                                </div>
                                                <div class="col-6">
                                                    <label>Email</label>
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="email" value="{{$username." @hs.com"}}">
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="button-row d-flex mt-4 col-12">
                                                    <button class="btn bg-gradient-light mb-0 js-btn-prev" type="button"
                                                        data-id="2" title="Prev">Prev</button>
                                                    <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next"
                                                        data-id="4" type="button" title="Next">Next</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card multisteps-form__panel p-3 border-radius-xl bg-white"
                                        data-animation="FadeIn" id="pd4">
                                        <h5 class="font-weight-bolder">บันทึกข้อมูล</h5>
                                        <div class="multisteps-form__content text-center">
                                            <button class="btn btn-success ms-auto mb-0 bg-red-500 hover:bg-green-300"
                                                type="submit" title="บันทึกข้อมูล">บันทึกข้อมูล</button>

                                            <div class="button-row d-flex mt-4">
                                                <button class="btn bg-gradient-light mb-0 js-btn-prev" data-id="3"
                                                    type="button" title="Prev">Prev</button>

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        $('button.js-btn-next').click(function (e) {
            var id = parseInt($(this).data('id'));
            console.log(id)
            changePageInfo(id)
        })
        $('button.js-btn-prev').click(function (e) {
            var id = parseInt($(this).data('id'));
            changePageInfo(id)
        })
        $('button.multisteps-form__progress-btn').click(function (e) {
            var id = parseInt($(this).data('id'));
            changePageInfo(id)
        })

        function changePageInfo(id) {
            $(`#b${id}`).addClass('js-active')
            $("#pd" + id).addClass('js-active')

            if (id === 1) {
                if ($(`#b2`).hasClass('js-active')) {
                    $('#b2').removeClass('js-active')
                }
                if ($(`#b3`).hasClass('js-active')) {
                    $('#b3').removeClass('js-active')
                }
                if ($(`#b4`).hasClass('js-active')) {
                    $('#b4').removeClass('js-active')
                }
                $('#pd2').removeClass('js-active')
                $('#pd3').removeClass('js-active')
                $('#pd4').removeClass('js-active')
            } else if (id === 2) {
                if ($(`#b3`).hasClass('js-active')) {
                    $('#b3').removeClass('js-active')
                }
                if ($(`#b4`).hasClass('js-active')) {
                    $('#b4').removeClass('js-active')
                }
                $('#pd3').removeClass('js-active')
                $('#pd4').removeClass('js-active')
            } else if (id === 3) {
                if ($(`#b4`).hasClass('js-active')) {
                    $('#b4').removeClass('js-active')
                }
                $('#b3').addClass('js-active')
                $('#b2').addClass('js-active')
                $('#b1').addClass('js-active')

                $('#pd1').removeClass('js-active')
                $('#pd2').removeClass('js-active')
                $('#pd4').removeClass('js-active')
            } else { //pd4
                $('#pd3').removeClass('js-active')
                $('#pd2').removeClass('js-active')
                $('#pd1').removeClass('js-active')
                $('#b1').addClass('js-active')
                $('#b2').addClass('js-active')
                $('#b3').addClass('js-active')

            }
        }

        // $(document).ready(function() {
        //     $('.datepicker').datepicker({
        //         format: 'dd/mm/yyyy',
        //         todayBtn: true,
        //         language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
        //         // thaiyear: true              //Set เป็นปี พ.ศ.
        //     }).datepicker(); //กำหนดเป็นวันปัจุบัน
        // });
        $('#prefix_select').change(function () {
            if ($(this).val() === "other") {
                $('#prefix_text').removeClass('hidden')
            } else {
                if (!$('#prefix_text').hasClass('hidden')) {
                    $('#prefix_text').addClass('hidden')
                }
            }
        })

        $('#usergroup').change(function () {
            let id = $(this).val()
            $.get(`/admin/usergroup/${id}/infos`).done(function (data) { //server
                $(`#rate_payment_per_year`).val(data.rate_payment_per_year);
                $(`#bin_quantity`).val(1);
                $('#payment_per_year').val(data.rate_payment_per_year)

            })
        });

        $(`#bin_quantity`).keyup(function () {
            let bin_quantity = $(this).val();
            let total = $('#rate_payment_per_year').val() * bin_quantity;
            $('#payment_per_year').val(total)
        })

        function getDistrict() {
            var id = $("#province_code").val();
            $.get("/district/getDistrict/" + id).done(function (data) {
                var text = "<option>--Select--</option>";
                data.forEach(function (val) {
                    text += "<option seleted value='" + val.district_code + "'>" + val.district_name +
                        "</option>";
                });
                $("#district_code").html(text);
            });
        }

        function getTambon() {
            var id = $("#district_code").val();
            $.get("/tambon/getTambon/" + id).done(function (data) {
                console.log(data)
                var text = "<option>--Select--</option>";
                data.forEach(function (val) {
                    text += "<option seleted value='" + val.tambon_code + "'>" + val.tambon_name +
                        "</option>";
                });
                $("#tambon_code").html(text);
            });
        }

        function getZone() {
            var id = $("#tambon_code").val();
            $.get("../../../zone/getZone/" + id).done(function (data) {
                var text = "<option>--Select--</option>";
                data.forEach(function (val) {
                    text += "<option seleted value='" + val.id + "'>" + val.zone_name + "</option>";
                });
                $("#zone_id").html(text);
            });
        }

        function getSubzone() {
            var zone_id = $("#undertake_zone_id").val();
            $.get(`/admin/subzone/${zone_id}/getSubzone`).done(function (data) {
                var text = "<option>เลือก...</option>";
                console.log('data', data)
                if (data.length == 0) {
                    text += "<option value='0'>-</option>";
                } else {
                    data.forEach(function (val) {
                        text += "<option seleted value='" + val.id + "'>" + val.subzone_name + "</option>";
                    });
                }
                $("#undertake_subzone_id").html(text);
            });
        }

        $('select').change(() => {
            checkValues()
        });

        $('input').keyup(() => {
            checkValues()
        });

        function checkValues() {
            let res = true
            $("#undertake_subzone_id").removeClass("border-danger rounded")
            $("#metertype").removeClass("border-danger rounded")
            $("#undertake_zone_id").removeClass("border-danger rounded")

            if ($("#metertype").val() === "เลือก...") {

                $("#metertype").addClass("border-danger rounded")
                res = false;
            }

            if ($("#undertake_zone_id").val() === "เลือก...") {
                console.log($("#metertype").val())

                $("#undertake_zone_id").addClass("border-danger rounded")
                res = false;
            } else {
                if ($("#undertake_subzone_id").val() === "เลือก...") {
                    $("#undertake_subzone_id").addClass("border-danger rounded")
                    res = false;
                }
            }
            return res;

        }

        $('#kp_usergroup_selected_for_all').change(function(){
            let selected_val = $(this).val();
            console.log(selected_val)
            $('.kp-usergroup-select').val(selected_val)
        })
       
        // New function to fetch user data and populate form fields
        $('#user_select').change(function () {
            let userId = $(this).val();
            if (userId) {
                // Assuming an API endpoint like /admin/users/{id}/info to fetch user details
                // You will need to create this endpoint in your Laravel backend
                $.get(`/keptkaya/admin/kp_user/${userId}/info`).done(function (userData) {
                    // Populate prefix
                    if (userData.prefix_select === 'คุณ' || userData.prefix_select === 'นาย' || userData.prefix_select === 'นาง' || userData.prefix_select === 'นส.') {
                        $('#prefix_select').val(userData.prefix_select);
                        $('#prefix_text').addClass('hidden');
                    } else {
                        $('#prefix_select').val('other');
                        $('#prefix_text').removeClass('hidden').val(userData.prefix_select);
                    }

                    $('#firstname').val(userData.firstname);
                    $('#lastname').val(userData.lastname);
                    $('#gender').val(userData.gender);
                    $('#id_card').val(userData.id_card);
                    $('#phone').val(userData.phone);
                    $('#address').val(userData.address);

                    // For location fields, you might need to trigger changes
                    // to populate sub-dropdowns correctly
                    $('#province_code').val(userData.province_code);
                    if (userData.province_code) {
                        $.get(`/district/getDistrict/${userData.province_code}`).done(function (data) {
                            var text = "<option>--Select--</option>";
                            data.forEach(function (val) {
                                text += "<option value='" + val.district_code + "'>" + val.district_name + "</option>";
                            });
                            $("#district_code").html(text).val(userData.district_code);
                            if (userData.district_code) {
                                $.get(`/tambon/getTambon/${userData.district_code}`).done(function (data) {
                                    var text = "<option>--Select--</option>";
                                    data.forEach(function (val) {
                                        text += "<option value='" + val.tambon_code + "'>" + val.tambon_name + "</option>";
                                    });
                                    $("#tambon_code").html(text).val(userData.tambon_code);
                                    if (userData.tambon_code) {
                                        $.get(`../../../zone/getZone/${userData.tambon_code}`).done(function (data) {
                                            var text = "<option>--Select--</option>";
                                            data.forEach(function (val) {
                                                text += "<option value='" + val.id + "'>" + val.zone_name + "</option>";
                                            });
                                            $("#zone_id").html(text).val(userData.zone_id);
                                        });
                                    }
                                });
                            }
                        });
                    }

                    // Populate username and email for pd3
                    $('#username').val(userData.username);
                    // Password should not be pre-filled for security
                    $('#email').val(userData.email);

                }).fail(function () {
                    console.error("Failed to fetch user data.");
                    // Optionally clear fields or show an error message
                });
            } else {
                // Clear all fields if "Select User" is chosen
                $('#prefix_select').val('');
                $('#prefix_text').addClass('hidden').val('');
                $('#firstname').val('');
                $('#lastname').val('');
                $('#gender').val('0');
                $('#id_card').val('');
                $('#phone').val('');
                $('#address').val('');
                $('#province_code').val('35'); // Default to Yasothon
                getDistrict(); // Clear and repopulate district/tambon/zone

                $('#usergroup').val('');
                $('#rate_payment_per_year').val('');
                $('#bin_quantity').val('');
                $('#payment_per_year').val('');
                $('#new_meter_id').val('{{ $usernumber }}'); // Reset to default new meter ID

                $('#username').val('{{ $username }}');
                $('#password').val('{{ $password }}'); // Reset to default new password
                $('#email').val('{{ $username."@hs.com" }}');
            }
        });

        $(document).ready(function () {
            $('#user_select').select2();
        });

        

    </script>
   <script>
    $(document).ready(function() {
            const $selectAllUsersCheckbox = $('#selectAllUsers');
            const $userCheckboxes = $('.user-checkbox');
            const $userSelectedDataJson = $('#user_selected_data_json');
            const $userRows = $('.user-row'); // Each table row representing a user

            let selectedUsersData = {}; // Object to store data of selected users by user ID

            // --- Function to update the hidden textarea ---
            function updateSelectedUsersDataJson() {
                const selectedUsersArray = [];
                $userCheckboxes.each(function() {
                    if ($(this).is(':checked')) {
                        const userId = $(this).val();
                        const $row = $(this).closest('.user-row');

                        const userData = {
                            user_id: userId,
                            kp_tzone_idfk: $row.find('select[name$="[kp_tzone_idfk]"]').val(),
                            kp_tzoneblock_idfk: $row.find('select[name$="[kp_tzoneblock_idfk]"]').val(),
                            kp_usergroup_idfk: $row.find('select[name$="[kp_usergroup_idfk]"]').val(),
                            bin_count: $row.find('.kp-bin-count-input').val(), // Specific class for bin_count
                            as_tbank: $row.find('select[name$="[as_tbank]"]').val(),
                            // You can add more text fields if needed, e.g., user's name for display/logging
                            // Example: user_name: $row.find('h6.mb-0').text().trim().split('(')[0].trim()
                        };
                        selectedUsersArray.push(userData);
                    }
                });
                $userSelectedDataJson.val(JSON.stringify(selectedUsersArray));
            }

            // --- Event Listeners ---

            // 1. Select All Checkbox
            $selectAllUsersCheckbox.on('change', function() {
                const isChecked = $(this).is(':checked');
                $userCheckboxes.prop('checked', isChecked);
                updateSelectedUsersDataJson(); // Update data when select all changes
            });

            // 2. Individual User Checkbox
            $userCheckboxes.on('change', function() {
                // Update "Select All" checkbox state
                if ($userCheckboxes.length > 0 && $userCheckboxes.filter(':checked').length === $userCheckboxes.length) {
                    $selectAllUsersCheckbox.prop('checked', true);
                } else {
                    $selectAllUsersCheckbox.prop('checked', false);
                }
                updateSelectedUsersDataJson(); // Update data when individual checkbox changes
            });

            // 3. Any input/select within a row changes (to update its data if selected)
            $userRows.find('select, input[type="text"], input[type="number"]').on('change keyup', function() {
                const $checkbox = $(this).closest('.user-row').find('.user-checkbox');
                $checkbox.prop('checked', true)
                if ($checkbox.is(':checked')) {
                    updateSelectedUsersDataJson(); // Only update if the row's checkbox is checked
                }
            });

            // 4. Initial call to set state (e.g., if old() values are present)
            updateSelectedUsersDataJson();
            // Also, update select all checkbox state on load
            if ($userCheckboxes.length > 0 && $userCheckboxes.filter(':checked').length === $userCheckboxes.length) {
                $selectAllUsersCheckbox.prop('checked', true);
            }


            // --- Cascading Dropdowns for Zone Block (based on Zone) ---
            // Assuming you have an AJAX endpoint like /admin/subzone/{zone_id}/getSubzone
            $('.kp-tzone-select').on('change', function() {
                const $zoneSelect = $(this);
                const zoneId = $zoneSelect.val();
                const $zoneBlockSelect = $zoneSelect.closest('.user-row').find('.kp-tzoneblock-select'); // Find related zone block select

                if (zoneId) {
                    $.get(`/superadmin/zone_blocks/${zoneId}/get-zone-block`)
                        .done(function(data) {
                            let options = '<option value="">เลือก...</option>';
                            if (data.length > 0) {
                                data.forEach(function(item) {
                                    options += `<option value="${item.id}">${item.zone_block_name}</option>`;
                                });
                            } else {
                                options += `<option value="">ไม่พบ Zone Block</option>`;
                            }
                            $zoneBlockSelect.html(options);
                        })
                        .fail(function() {
                            console.error("Failed to fetch zone blocks for zone ID:", zoneId);
                            $zoneBlockSelect.html('<option value="">เกิดข้อผิดพลาด</option>');
                        });
                } else {
                    $zoneBlockSelect.html('<option value="">เลือก...</option>');
                }
                updateSelectedUsersDataJson(); // Update data after zone/zoneblock changes
            });

            // Trigger change on load for any pre-selected zones (e.g. from old values or existing user data)
            // $('.kp-tzone-select').each(function() {
            //     if ($(this).val()) {
            //         $(this).trigger('change');
            //     }
            // });
        });
    </script>
@endsection