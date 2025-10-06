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
                                    <button class="multisteps-form__progress-btn" id="b2" data-id="2"
                                        type="button" title="Address">ข้อมูลขอใช้บริการจัดเก็บขยะ</button>
                                    <button class="multisteps-form__progress-btn" id="b3" data-id="3"
                                        type="button" title="System Info">ตั้งค่าเข้าใช้งานระบบ</button>
                                    <button class="multisteps-form__progress-btn" id="b4" data-id="4"
                                        type="button" title="Socials">บันทึก</button>

                                </div>
                            </div>
                        </div>

                        <div class="row ">
                            <div class="col-12 col-lg-10 m-auto">
                                <form class="multisteps-form__form mb-8"
                                    action="{{ route('keptkayas.admin.kp_user.update', $kp_user) }}" method="post"
                                    style="height: 492px;">
                                    @method('put')
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
                                                                <option value="คุณ"
                                                                    {{ $kp_user->prefix == 'คุณ' ? 'selected' : '' }}>คุณ
                                                                </option>
                                                                <option value="นาย"
                                                                    {{ $kp_user->prefix == 'นาย' ? 'selected' : '' }}>นาย
                                                                </option>
                                                                <option value="นาง"
                                                                    {{ $kp_user->prefix == 'นาง' ? 'selected' : '' }}>นาง
                                                                </option>
                                                                <option value="นส."
                                                                    {{ $kp_user->prefix == 'นส.' ? 'selected' : '' }}>นส.
                                                                </option>
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
                                                                name="firstname" value="{{ $kp_user->firstname }}">
                                                        </div>
                                                        <div class="col-12 col-sm-4">
                                                            <label for="feFirstName">สกุล
                                                                @error('lastname')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="lastname"
                                                                name="lastname" value="{{ $kp_user->lastname }}">
                                                        </div>

                                                        <div class="col-12 col-sm-2">
                                                            <label for="feGender">เพศ
                                                                @error('gender')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <select name="gender" id="gender" class="form-control">
                                                                <option value="0">เลือก..</option>
                                                                <option value="m"
                                                                    {{ $kp_user->gender == 'm' ? 'selected' : '' }}>ชาย
                                                                </option>
                                                                <option value="w"
                                                                    {{ $kp_user->gender == 'w' ? 'selected' : '' }}>หญิง
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <label for="feID_card">เลขบัตรประจำตัวประชาชน
                                                                @error('id_card')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="id_card"
                                                                name="id_card" value="{{ $kp_user->id_card }}">
                                                        </div>

                                                        <div class="col-12 col-sm-6">
                                                            <label for="fePhone">เบอร์โทรศัพท์
                                                                @error('phone')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="phone"
                                                                name="phone" value="{{ $kp_user->phone }}">
                                                        </div>
                                                        <div class="col-12 col-sm-3">
                                                            <label for="feInputAddress">ที่อยู่
                                                                @error('address')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="address"
                                                                name="address" value="{{ $kp_user->address }}">
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
                                                                    <option value="{{ $zone->id }}"
                                                                        {{ $kp_user->zone_id == $zone->id ? 'selected' : '' }}>
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
                                                <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next"
                                                    data-id="2" type="button" title="Next">Next</button>
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
                                                        name="new_meter_id" value="{{ $kp_user->id }}">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label>สถานะสมาชิก</label>
                                                    <select class="form-control" name="trashbank_status">
                                                        <option value="0" selected>ผู้ขอใช้บริการจัดเก็บขยะ</option>
                                                        <option value="1">สมาชิกธนาคารขยะ</option>
                                                        <option value="2">ยกเลิกการใช้งาน</option>
                                                    </select>
                                                </div>

                                                <div class="col-12 col-sm-2">
                                                    <label>ประเภทผู้ใช้งาน
                                                        @error('usergroup')
                                                            <span class="text-danger h-8">({{ $message }})</span>
                                                        @enderror
                                                    </label>
                                                    <select class="form-control" name="usergroup" id="usergroup">
                                                        <option value="">เลือก...</option>
                                                        @foreach ($usergroups as $usergroup)
                                                            <option value="{{ $usergroup->id }}"
                                                                {{ $kp_user->user_kaya_infos->kp_usergroup->id == $usergroup->id ? 'selected' : '' }}>
                                                                {{ $usergroup->usergroup_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-2">
                                                    {{-- {{ dd($kp_user->user_kaya_infos->kp_bins) }} --}}
                                                    <label>ราคา (บาท/ถัง)</label>
                                                    <input type="text" class="form-control" readonly
                                                        name="rate_payment_per_year" id="rate_payment_per_year"
                                                        value="{{ $kp_user->user_kaya_infos->kp_usergroup->kp_usergroup_payrate_permonth[0]->payrate_permonth }}">
                                                </div>
                                                <div class="col-12 col-sm-2">
                                                    <label>จำนวนถังที่ใช้(ถัง) </label>
                                                    @error('bin_quantity')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                    @enderror
                                                    <input type="text" class="form-control" name="bin_quantity"
                                                        id="bin_quantity"
                                                        value="{{ collect($kp_user->user_kaya_infos->kp_bins)->count() }}">
                                                    <input type="hidden" name="bin_quantity_ref"
                                                        value="{{ collect($kp_user->user_kaya_infos->kp_bins)->count()}}">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    {{-- {{dd($kp_user->user_kaya_infos->kp_bins)}} --}}
                                                    @foreach ($kp_user->user_kaya_infos->kp_bins as $bin)
                                                    <div class="d-flex">
                                                        <div>
                                                            <label>code</label>
                                                            <input type="text" class="form-control" value="{{$bin->bincode}}" readonly>
                                                        </div>
                                                        <div> 
                                                            <label>เป็นเงิน(บาท/ปี) </label>
                                                            <input type="text" class="form-control" value="{{$kp_user->user_kaya_infos->kp_usergroup->kp_usergroup_payrate_permonth[0]->payrate_permonth *12}}">
                                                        </div>
                                                    </div>
                                                   
                                                        {{-- <label>เป็นเงิน(บาท/ปี) </label>
                                                    <input type="text" class="form-control"
                                                        name="total_payment_per_year" id="total_payment_per_year"
                                                        value="
                                                        {{-- {{ $kp_user->payment_per_year_infos($kp_user->id)->total_payment_per_year }}" --}
                                                        readonly> --}}
                                                    @endforeach
                                                    
                                                </div>


                                            </div>
                                            <div class="button-row d-flex mt-5 mb-5">
                                                <button class="btn bg-gradient-light mb-0 js-btn-prev" data-id="1"
                                                    type="button" title="Prev">Prev</button>
                                                <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next"
                                                    data-id="3" type="button" title="Next">Next</button>
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
                                                        name="username" value="{{ $kp_user->username }}">
                                                </div>
                                                <div class="col-6">
                                                    <label>Password</label>
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="password" value="">
                                                </div>
                                                <div class="col-6">
                                                    <label>Email</label>
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="email" value="{{ $kp_user->email }}">
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
        let bin_quantity_ref = 0;

        $(document).ready(function() {
            bin_quantity_ref = $('#bin_quantity').val()
        })

        $(document).on('keyup', '#bin_quantity', function(e) {
            e.preventDefault();
            let bin_quantity = $(this).val();
            //ถ้าจำนวนถังที่ต้องการเปลี่ยนน้อยกว่า ref_bin_quantity ให้แจ้งเตือนว่าไม่สามารถทำได้
            if ((bin_quantity !== "") && (bin_quantity < bin_quantity_ref)) {
                console.log('bin_quantity ', bin_quantity)
                $(`#bin_quantity`).val(bin_quantity_ref)
                alert(
                    `ไม่สามารถลดจำนวนถังขยะที่ลงทะเบียนใช้แล้วได้\nต้องใช้ถังขยะจำนวน ${bin_quantity_ref} ถัง จนถึงสิ้นปีงบประมาณนี้`)
                let total = $('#rate_payment_per_year').val() * bin_quantity_ref;
                $('#total_payment_per_year').val(total)
            } else {
                let total = $('#rate_payment_per_year').val() * bin_quantity;
                $('#total_payment_per_year').val(total)
            }
        })

        $('button.js-btn-next').click(function(e) {
            var id = parseInt($(this).data('id'));
            console.log(id)
            changePageInfo(id)
        })
        $('button.js-btn-prev').click(function(e) {
            var id = parseInt($(this).data('id'));
            changePageInfo(id)
        })
        $('button.multisteps-form__progress-btn').click(function(e) {
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


        $('#prefix_select').change(function() {
            if ($(this).val() === "other") {
                $('#prefix_text').removeClass('hidden')
            } else {
                if (!$('#prefix_text').hasClass('hidden')) {
                    $('#prefix_text').addClass('hidden')
                }
            }
        })

        $('#usergroup').change(function() {
            let id = $(this).val()
            $.get(`/admin/usergroup/${id}/infos`).done(function(data) { //server
                $(`#rate_payment_per_year`).val(data.rate_payment_per_year);
                $(`#bin_quantity`).val(1);
                $('#total_payment_per_year').val(data.rate_payment_per_year)

            })
        });

        // $(`#bin_quantity`).keyup(function(){
        //     let bin_quantity = $(this).val();
        //     let total = $('#rate_payment_per_year').val() * bin_quantity;
        //     $('#total_payment_per_year').val(total)
        // })

        function getDistrict() {
            var id = $("#province_code").val();
            $.get("/district/getDistrict/" + id).done(function(data) {
                var text = "<option>--Select--</option>";
                data.forEach(function(val) {
                    text += "<option seleted value='" + val.district_code + "'>" + val.district_name +
                        "</option>";
                });
                $("#district_code").html(text);
            });
        }

        function getTambon() {
            var id = $("#district_code").val();
            $.get("/tambon/getTambon/" + id).done(function(data) {
                console.log(data)
                var text = "<option>--Select--</option>";
                data.forEach(function(val) {
                    text += "<option seleted value='" + val.tambon_code + "'>" + val.tambon_name +
                        "</option>";
                });
                $("#tambon_code").html(text);
            });
        }

        function getZone() {
            var id = $("#tambon_code").val();
            $.get("../../../zone/getZone/" + id).done(function(data) {
                var text = "<option>--Select--</option>";
                data.forEach(function(val) {
                    text += "<option seleted value='" + val.id + "'>" + val.zone_name + "</option>";
                });
                $("#zone_id").html(text);
            });
        }

        function getSubzone() {
            var zone_id = $("#undertake_zone_id").val();
            $.get(`/admin/subzone/${zone_id}/getSubzone`).done(function(data) {
                var text = "<option>เลือก...</option>";
                console.log('data', data)
                if (data.length == 0) {
                    text += "<option value='0'>-</option>";
                } else {
                    data.forEach(function(val) {
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
    </script>
@endsection
