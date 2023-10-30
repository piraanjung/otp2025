@extends('layouts.admin1')

@section('title_page')
    ข้อมูลผู้ใช้งานระบบ
@endsection

@section('url')
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">แอดมิน</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">ผู้ใช้งานระบบ</li>
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
                                        <span>User Info</span>
                                    </button>
                                    <button class="multisteps-form__progress-btn" id="b2" data-id="2"
                                        type="button" title="Address">Meter Info</button>
                                    <button class="multisteps-form__progress-btn" id="b3" data-id="3"
                                        type="button" title="Socials">บันทึก</button>

                                </div>
                            </div>
                        </div>

                        <div class="row ">
                            <div class="col-12 col-lg-10 m-auto">
                                <form class="multisteps-form__form mb-8" action="{{ route('admin.users.store') }}" method="post"
                                    style="height: 492px;">
                                    @csrf
                                    <div class="card multisteps-form__panel p-3 border-radius-xl bg-white  js-active"
                                        data-animation="FadeIn" id="pd1">
                                        <h5 class="font-weight-bolder mb-0">User Info</h5>
                                        <p class="mb-0 text-sm">สมาชิกผู้ใช้น้ำ</p>
                                        <div class="multisteps-form__content">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="card card-primary card-outline">
                                                        <div class="card-body box-profile">
                                                            <div class="text-center">
                                                                <img class="avatar avatar-xxl position-relative"
                                                                    src="{{ asset('soft-ui/assets/img/bruce-mars.jpg') }}"
                                                                    alt="User profile picture">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="row">
                                                        <div class="col-12 col-sm-6">
                                                            <label for="feFirstName">ชื่อ - สกุล
                                                                @error('name')
                                                                <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="name"
                                                                name="name">
                                                        </div>

                                                        <div class="col-12 col-sm-6">
                                                            <label for="feGender">เพศ
                                                                @error('gender')
                                                                <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <select name="gender" id="gender" class="form-control">
                                                                <option value="0">เลือก..</option>
                                                                <option value="m">ชาย</option>
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
                                                                name="id_card">
                                                        </div>

                                                        <div class="col-12 col-sm-6">
                                                            <label for="fePhone">เบอร์โทรศัพท์
                                                                @error('phone')
                                                                <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="phone"
                                                                name="phone">
                                                        </div>
                                                        <div class="col-12 col-sm-3">
                                                            <label for="feInputAddress">ที่อยู่
                                                                @error('address')
                                                                <span class="text-danger h-8">({{ $message }})</span>
                                                                @enderror
                                                            </label>
                                                            <input type="text" class="form-control" id="address"
                                                                name="address">
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
                                                                <option value="35">ยโสธร</option>
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
                                            <div class="button-row d-flex mt-4">
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
                                                        name="new_meter_id" value="{{ $meternumber }}">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label>เลขมิเตอร์</label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        name="meternumber" id="meternumber" value="{{ $meternumber }}">
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <label>ประเภทมิเตอร์
                                                        @error('metertype')
                                                        <span class="text-danger h-8">({{ $message }})</span>
                                                        @enderror
                                                    </label>
                                                    <select class="form-control" name="metertype" id="metertype">
                                                        <option value="">เลือก...</option>
                                                        @foreach ($meter_types as $meter_type)
                                                            <option value="{{ $meter_type->id }}">
                                                                {{ $meter_type->meter_type_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label>ราคาต่อหน่วย (บาท)</label>
                                                    <input type="text" class="form-control bg-gray-200" readonly
                                                        name="counter_unit" id="counter_unit" value="">
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label>ขนาดมิเตอร์ </label>
                                                    <input type="text" class="form-control bg-gray-200" readonly name="metersize"
                                                        id="metersize">
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
                                                            <option value="{{ $zone->id }}">
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
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <label>วันที่ขอใช้น้ำ</label>
                                                    <?php $year = date('Y') + 543;
                                                    $now = date('d/m/' . $year); ?>
                                                    <input type="text" class="form-control datepicker bg-gray-200"
                                                        name="acceptance_date" id="acceptance_date"
                                                        value="{{ $now }}">
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
                                                        <input type="text" class="form-control bg-gray-200" name="discounttype"
                                                            value="1" readonly>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <label>สถานะ</label>
                                                    <?php $year = date('Y') + 543;
                                                    $now = date('d/m/' . $year); ?>
                                                    <select class="form-control bg-green-200" name="status" id="status">
                                                        <option selected value="active"> เปิดใช้งาน</option>
                                                    </select>
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
                                        <h5 class="font-weight-bolder">บันทึกข้อมูล</h5>
                                        <div class="multisteps-form__content text-center">
                                            <button class="btn btn-success ms-auto mb-0 bg-red-500 hover:bg-green-300"
                                                type="submit" title="บันทึกข้อมูล">บันทึกข้อมูล</button>

                                            <div class="button-row d-flex mt-4">
                                                <button class="btn bg-gradient-light mb-0 js-btn-prev" data-id="2"
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
    <script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
        crossorigin="anonymous"></script>
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
            if (id === 2) {
                if ($(`#b3`).hasClass('js-active')) {
                    $('#b3').removeClass('js-active')
                    $('#pd3').removeClass('js-active')
                    $('#pd1').removeClass('js-active')
                }
            } else if (id === 1) {
                if ($(`#b3`).hasClass('js-active')) {
                    $('#b3').removeClass('js-active')
                }
                if ($(`#b2`).hasClass('js-active')) {
                    $('#b2').removeClass('js-active')
                }
                $('#pd2').removeClass('js-active')
                $('#pd3').removeClass('js-active')
            } else { //pd3
                $('#pd2').removeClass('js-active')
                $('#pd1').removeClass('js-active')
                $('#b1').addClass('js-active')
                $('#b2').addClass('js-active')

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

        $('#metertype').change(function() {
            let id = $(this).val()
            $.get(`../../../tabwatermeter/infos/${id}`).done(function(data) { //server
                $('#counter_unit').val(data.price_per_unit)
                $('#metersize').val(data.metersize)
            })
        });

        function getDistrict() {
            var id = $("#province_code").val();
            $.get("/district/getDistrict/" + id).done(function(data) {
                var text = "<option>--Select--</option>";
                data.forEach(function(val) {
                    text += "<option value='" + val.district_code + "'>" + val.district_name + "</option>";
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
                    text += "<option value='" + val.tambon_code + "'>" + val.tambon_name + "</option>";
                });
                $("#tambon_code").html(text);
            });
        }

        function getZone() {
            var id = $("#tambon_code").val();
            $.get("../../../zone/getZone/" + id).done(function(data) {
                var text = "<option>--Select--</option>";
                data.forEach(function(val) {
                    text += "<option value='" + val.id + "'>" + val.zone_name + "</option>";
                });
                $("#zone_id").html(text);
            });
        }

        function getSubzone() {
            var id = $("#undertake_zone_id").val();
            $.get("../../../subzone/getSubzone/" + id).done(function(data) {
                // $.get("/subzone/getSubzone/" + id).done(function (data) {

                var text = "<option>เลือก...</option>";
                console.log('data', data)
                if (data.length == 0) {
                    text += "<option value='0'>-</option>";
                } else {
                    data.forEach(function(val) {
                        text += "<option value='" + val.id + "'>" + val.subzone_name + "</option>";
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
