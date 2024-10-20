@extends('layouts.admin1')

@section('title_page')
    แก้ไขข้อมูลผู้ใช้งานระบบ
@endsection

@section('url')
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">แอดมิน</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">ผู้ใช้งานระบบ</li>
@endsection
@section('style')
    <style>
        .hidden {
            display: none
        }

        .other_input {
            border: 1px solid red
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
                                        type="button" title="Address">Meter Info</button>
                                    <button class="multisteps-form__progress-btn" id="b3" data-id="3"
                                        type="button" title="Address">ตั้งค่าเข้าใช้งานระบบ</button>
                                    <button class="multisteps-form__progress-btn" id="b4" data-id="4"
                                        type="button" title="Socials">บันทึก</button>

                                </div>
                            </div>
                        </div>

                        <div class="row ">
                            <div class="col-12 col-lg-10 m-auto">
                                <form class="multisteps-form__form mb-8" action="{{ route('admin.users.update', $user[0]->id) }}"
                                    method="post" style="height: 492px;">
                                    @method("PUT")
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
                                                                <option {{ $user[0]->prefix == "คุณ" ? "selected" : "" }} value="คุณ">คุณ</option>
                                                                <option {{ $user[0]->prefix == "นาย" ? "selected" : "" }} value="นาย">นาย</option>
                                                                <option {{ $user[0]->prefix == "นาง" ? "selected" : "" }} value="นาง">นาง</option>
                                                                <option {{ $user[0]->prefix == "นส." ? "selected" : "" }} value="นส.">นส.</option>
                                                                <option {{ $user[0]->prefix == "other" ? "selected" : "" }} value="other">อื่นๆ</option>
                                                            </select>
                                                            <input type="text"
                                                                class="form-control hidden mt-1 other_input"
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
                                                                name="firstname" value="{{ $user[0]->firstname }}">
                                                        </div>
                                                        <div class="col-12 col-sm-4">
                                                            <label for="feFirstName">สกุล
                                                                @error('lastname')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="lastname"
                                                                name="lastname" value="{{ $user[0]->lastname }}">
                                                        </div>

                                                        <div class="col-12 col-sm-2">
                                                            <label for="feGender">เพศ
                                                                @error('gender')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <select name="gender" id="gender" class="form-control">
                                                                <option value="0">เลือก..</option>
                                                                <option {{ $user[0]->gender == "m" ? 'selected' : "" }} value="m">ชาย</option>
                                                                <option {{ $user[0]->gender == "w" ? 'selected' : "" }} value="w">หญิง</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <label for="feID_card">เลขบัตรประจำตัวประชาชน
                                                                @error('id_card')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="id_card"
                                                                name="id_card" value="{{ $user[0]->id_card }}">
                                                        </div>

                                                        <div class="col-12 col-sm-6">
                                                            <label for="fePhone">เบอร์โทรศัพท์
                                                                @error('phone')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="phone"
                                                                name="phone" value="{{ $user[0]->phone }}">
                                                        </div>
                                                        <div class="col-12 col-sm-3">
                                                            <label for="feInputAddress">ที่อยู่
                                                                @error('address')
                                                                    <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="address"
                                                                name="address" value="{{ $user[0]->address }}">
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
                                                                    <option value="{{ $zone->id }}" {{ $user[0]->zone_id == $zone->id ? "selected" : "" }}>
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
                                        <h5 class="font-weight-bolder">Meter Info</h5>
                                        <div class="multisteps-form__content">
                                            <div class="row mt-3">
                                                <div class="col-12 col-sm-6">
                                                    <label>เลขที่ผู้ใช้น้ำประปา</label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        name="new_meter_id" value="{{ $user[0]->id }}">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label>เลขมิเตอร์</label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        name="meternumber" id="meternumber" value="{{ $user[0]->usermeterinfos[0]->meternumber }}">
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <label>ประเภทมิเตอร์
                                                        @error('metertype_id')
                                                            <span class="text-danger h-8">({{ $message }})</span>
                                                        @enderror
                                                    </label>
                                                    <select class="form-control" name="metertype_id" id="metertype_id">
                                                        <option>เลือก...</option>
                                                        @foreach ($meter_types as $meter_type)
                                                            <option value="{{ $meter_type->id }}" {{ $user[0]->usermeterinfos[0]->metertype_id == $meter_type->id ? "selected" :"" }}>
                                                                {{ $meter_type->meter_type_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label>ราคาต่อหน่วย (บาท)</label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        name="counter_unit" id="counter_unit" value="{{ $user[0]->usermeterinfos[0]->meter_type->price_per_unit }}">
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label>ขนาดมิเตอร์ </label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        name="metersize" id="metersize" value="{{ $user[0]->usermeterinfos[0]->meter_type->metersize }}">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label>พื้นที่จัดเก็บ
                                                        @error('undertake_zone_id')
                                                            <span class="text-danger h-8">({{ $message }})</span>
                                                        @enderror
                                                    </label>
                                                    <select class="form-control"
                                                        name="undertake_zone_id"id="undertake_zone_id"
                                                        onchange="getSubzone()">
                                                        <option>เลือก...</option>
                                                        @foreach ($zones as $zone)
                                                            <option value="{{ $zone->id }}" {{ $user[0]->usermeterinfos[0]->undertake_zone_id ==  $zone->id ? "selected" : "" }}>
                                                                {{ $zone->zone_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label>เส้นทางจัดเก็บ
                                                        @error('undertake_subzone_id')
                                                            <span class="text-danger h-8">({{ $message }})</span>
                                                        @enderror
                                                    </label>
                                                    <select class="form-control" name="undertake_subzone_id"
                                                        id="undertake_subzone_id">
                                                        <option value="{{ $user[0]->usermeterinfos[0]->undertake_subzone_id }}" selected>
                                                            {{ $user[0]->usermeterinfos[0]->undertake_subzone->subzone_name }}</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label>วันที่ขอใช้น้ำ</label>
                                                    <?php $year = date('Y') + 543;
                                                    $now = date('d/m/' . $year); ?>
                                                    <input type="text" class="form-control datepicker bg-gray-200"
                                                        name="acceptance_date" id="acceptance_date" readonly
                                                        value="{{ $user[0]->usermeterinfos[0]->acceptance_date }}">
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label>วิธีชำระเงิน</label>
                                                    <span class="ml-auto text-right text-semibold text-reagent-gray">
                                                        <input type="text" class="form-control bg-gray-200" readonly
                                                            name="payment_id" value="1">
                                                    </span>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label>ประเภทผู้ได้ส่วนลด</label>
                                                    <span class="ml-auto text-right text-semibold text-reagent-gray">
                                                        <input type="text" class="form-control bg-gray-200"
                                                            name="discounttype" value="1" readonly>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="button-row d-flex mt-4">
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
                                                    @error('username')
                                                        <div class="text-danger h-8">({{ $message }})</div>
                                                    @enderror
                                                    <label>User name</label>
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="username" value="{{ $user[0]->username }}">
                                                </div>
                                                <div class="col-6">
                                                    @error('password')
                                                        <div class="text-danger h-8">({{ $message }})</div>
                                                    @enderror
                                                    <label>Password</label>
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="password" value="">
                                                </div>
                                                <div class="col-6">
                                                    @error('email')
                                                        <div class="text-danger h-8">({{ $message }})</div>
                                                    @enderror
                                                    <label>Email</label>
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="email" value="{{ $user[0]->email }}">
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
                if ($(`#b1`).hasClass('js-active')) {
                    $('#b1').removeClass('js-active')
                }
                $('#pd3').removeClass('js-active')
                $('#pd1').removeClass('js-active')
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
                $('#payment_per_year').val(data.rate_payment_per_year)

            })
        });

        $('#metertype_id').change(function() {
            let id = $(this).val()
            $.get(`/admin/metertype/${id}/infos`).done(function(data) { //server
                $('#counter_unit').val(data.price_per_unit)
                $('#metersize').val(data.metersize)
            })
        });

        $(`#bin_quantity`).keyup(function() {
            let bin_quantity = $(this).val();
            let total = $('#rate_payment_per_year').val() * bin_quantity;
            $('#payment_per_year').val(total)
        })

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
                if (data.length == 1) {
                    text += `<option value='${data[0].id}' selected>${data[0].subzone_name}</option>`;
                } else {
                    if (data.length == 0) {
                        text += "<option value='0'>-</option>";
                    } else {
                        data.forEach(function(val) {
                            text += "<option seleted value='" + val.id + "'>" + val.subzone_name +
                                "</option>";
                        });
                    }
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
