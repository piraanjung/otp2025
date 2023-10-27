@extends('layouts.admin1')

@section('content')
    <div class="row">
        <div class="col-12">
            <form action="{{ url('/users/store') }}" method="post" onSubmit="return checkValues();">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle"
                                        src="{{ asset('adminlte/dist/img/user4-128x128.jpg') }}" alt="User profile picture">
                                </div>
                                <p class="text-muted text-center">สมาชิกผู้ใช้น้ำ</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#activity"
                                            data-toggle="tab">ข้อมูลทั่วไป และ เกี่ยวมิเตอร์มาตรวัด</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="activity">
                                        <div class="card22 card-small mb-4">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item p-3">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label for="feFirstName">ชื่อ - สกุล</label>
                                                                    <input type="text" class="form-control"
                                                                        id="name" name="name">
                                                                </div>

                                                                <div class="form-group col-md-6">
                                                                    <label for="feGender">เพศ</label>
                                                                    <select name="gender" id="gender"
                                                                        class="form-control">
                                                                        <option>เลือก..</option>

                                                                        <option value="m" selected>ชาย</option>
                                                                        <option value="w">หญิง</option>


                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label for="feID_card">เลขบัตรประจำตัวประชาชน</label>
                                                                    <input type="text" class="form-control"
                                                                        id="id_card" name="id_card">
                                                                </div>

                                                                <div class="form-group col-md-6">
                                                                    <label for="fePhone">เบอร์โทรศัพท์</label>
                                                                    <input type="text" class="form-control"
                                                                        id="phone" name="phone">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-3">
                                                                    <label for="feInputAddress">ที่อยู่</label>
                                                                    <input type="text" class="form-control"
                                                                        id="address" name="address">
                                                                </div>
                                                                <div class="form-group col-2">
                                                                    <label>หมู่ที่</label>
                                                                    <select class="form-control" name="zone_id"
                                                                        id="zone_id">
                                                                        <option>เลือก...</option>
                                                                        @foreach ($zones as $zone)
                                                                            <option value="{{ $zone->id }}" selected>
                                                                                {{ $zone->zone_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="form-group col-3">
                                                                    <label>จังหวัด</label>
                                                                    <select class="form-control" name="province_code"
                                                                        id="province_code" onchange="getDistrict()">
                                                                        <option value="35">ยโสธร</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-2">
                                                                    <label>อำเภอ</label>
                                                                    <select class="form-control" name="district_code"
                                                                        id="district_code" onchange="getTambon()">
                                                                        <option value="3508">เลิงนกทา</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-2">
                                                                    <label>ตำบล</label>
                                                                    <select class="form-control" name="tambon_code"
                                                                        id="tambon_code" onchange="getZone()">
                                                                        <option value="350805">ห้องแซง</option>

                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>

                                            <div class="card22 card-small col-12">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="form-group col-6">
                                                            <label>เลขที่ผู้ใช้น้ำประปา</label>
                                                            <input type="text" class="form-control" readonly
                                                                name="new_meter_id" value="{{ $meternumber }}">
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label>เลขมิเตอร์</label>
                                                            <input type="text" class="form-control" readonly
                                                                name="meternumber" id="meternumber"
                                                                value="HS{{ $meternumber }}">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-4">
                                                            <label>ประเภทมิเตอร์</label>
                                                            <select class="form-control" name="metertype" id="metertype">
                                                                <option>เลือก...</option>
                                                                @foreach ($tabwatermeters as $tabwatermeter)
                                                                    <option value="{{ $tabwatermeter->id }}">
                                                                        {{ $tabwatermeter->typemetername }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-4">
                                                            <label>ราคาต่อหน่วย (บาท)</label>
                                                            <input type="text" class="form-control" readonly
                                                                name="counter_unit" id="counter_unit" value="">
                                                        </div>
                                                        <div class="form-group col-4">
                                                            <label>ขนาดมิเตอร์</label>
                                                            <input type="text" class="form-control" readonly
                                                                name="metersize" id="metersize">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-6">
                                                            <label>พื้นที่จัดเก็บ</label>
                                                            <div class="err" id="undertake_zone_idErr"></div>
                                                            <select class="form-control" name="undertake_zone_id"
                                                                id="undertake_zone_id" onchange="getSubzone()">
                                                                <option>เลือก...</option>
                                                                @foreach ($zones as $zone)
                                                                    <option value="{{ $zone->id }}">
                                                                        {{ $zone->zone_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label>เส้นทางจัดเก็บ</label>
                                                            <div class="err" id="undertake_subzone_idErr"></div>
                                                            <select class="form-control" name="undertake_subzone_id"
                                                                id="undertake_subzone_id">

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-4">
                                                            <label>วันที่ขอใช้น้ำ</label>
                                                            <?php $year = date('Y') + 543;
                                                            $now = date('d/m/' . $year); ?>
                                                            <input type="text" class="form-control datepicker"
                                                                name="acceptance_date" id="acceptance_date"
                                                                value="{{ $now }}">
                                                        </div>
                                                        <div class="form-group col-4">
                                                            <label>วิธีชำระเงิน</label>
                                                            <span
                                                                class="ml-auto text-right text-semibold text-reagent-gray">
                                                                <input type="text" class="form-control" readonly
                                                                    name="payment_id" value="1">
                                                            </span>
                                                        </div>
                                                        <div class="form-group col-4">
                                                            <label>ประเภทผู้ได้ส่วนลด</label>
                                                            <span
                                                                class="ml-auto text-right text-semibold text-reagent-gray">
                                                                <input type="text" class="form-control"
                                                                    name="discounttype" value="1" readonly>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-6">
                                                            <label>สถานะ</label>
                                                            <?php $year = date('Y') + 543;
                                                            $now = date('d/m/' . $year); ?>
                                                            <select class="form-control" name="status" id="status">
                                                                <option selected value="active">เปิดใช้งาน</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                                <button type="submit" class="btn btn-success col-4">บันทึก</button>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                todayBtn: true,
                language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
                // thaiyear: true              //Set เป็นปี พ.ศ.
            }).datepicker(); //กำหนดเป็นวันปัจุบัน
        });

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
