@extends('layouts.admin1')

@section('nav-invoice')
    active
@endsection
@section('nav-header')
    จัดการใบแจ้งหนี้
@endsection
@section('nav-main')
    <a href="{{ route('invoice.index') }}"> ออกใบแจ้งหนี้</a>
@endsection
{{-- @section('nav-current')
    ข้อมูลใบแจ้งหนี้แยกตามเส้นทางจัดเก็บ
@endsection --}}
@section('page-topic')
    ข้อมูลใบแจ้งหนี้แยกตามเส้นทางจัดเก็บ
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
    <div class="card">
        <div class="card-body">
            <div class="multisteps-form mb-6">
                <div class="multisteps-form__progress">
                    <button class="multisteps-form__progress-btn js-active" id="b1" data-id="1" type="button"
                        title="User Info">
                        <span>ติดตั้งมิเตอร์</span>
                    </button>


                </div>
            </div>
            <form action="{{ route('cutmeter.update', $cutmeter) }}" method="POST">
                @method('PUT')
                @csrf
                {{-- ตัดมิเตอร์ --}}
                <div class="card multisteps-form__panel p-3 mt-6 border-radius-xl js-active" data-animation="FadeIn"
                    id="pd1">
                    <h5 class="font-weight-bolder">ติดตั้งมิเตอร์</h5>
                    <div class="card"
                        style="background-image: url('../../../assets/img/curved-images/white-curved.jpeg')">
                        <span class="mask bg-gradient-dark opacity-9 border-radius-xl"></span>
                        <div class="card-body p-3 position-relative">
                            <div class="row">
                                <div class="col-8 text-start">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                        <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"
                                            aria-hidden="true"></i>
                                    </div>
                                    <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                        {{ $cutmeter->usermeterinfo->user->firstname . ' ' . $cutmeter->usermeterinfo->user->lastname }}
                                    </h5>
                                    <span
                                        class="text-white text-sm">{{ $cutmeter->usermeterinfo->user->address . ' ' . $cutmeter->usermeterinfo->user->user_zone->zone_name }}</span>
                                </div>
                                <div class="col-4">
                                    <p class="text-white text-sm text-end font-weight-bolder mt-auto mb-0">
                                        ค้าง {{ $cutmeter->owe_count }} รอบบิล</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ก่อนจด</label>
                        <input class="form-control" type="text" name="lastmeter" value="{{ $lastmeter->lastmeter }}">
                        <input type="hidden" class="form-control" name="cutmeter_id" value="{{ $cutmeter->id }}">
                        <input type="hidden" class="" name="inv_id" value="{{ $lastmeter->inv_id }}">
                    </div>
                    <label class="form-label">เลขมิเตอร์ก่อนตัดมิเตอร์</label>
                    <div class="form-group">
                        <input class="form-control border-danger" type="text" name="currentmeter">
                    </div>
                    <label class="form-label">เจ้าหน้าที่ผู้ติดตั้งมิเตอร์</label>
                    <div class="form-group">

                        <?php $i = 1; ?>
                        @foreach ($cutmeter->twmanArray as $item)
                            <select name="twman{{ $i++ }}" class="form-control mt-2">
                                <option value="0">เลือก</option>
                                @foreach ($twmans as $twman)
                                    <option value="{{ $twman->id }}" {{ $twman->id == $item ? 'selected' : '' }}>
                                        {{ $twman->prefix . '' . $twman->firstname . ' ' . $twman->lastname . '' . $twman->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endforeach

                        @if (collect($cutmeter->twmanArray)->count() == 1)
                            <select name="twman1" class="form-control mt-2">
                                <option value="0">เลือก</option>
                                @foreach ($twmans as $twman)
                                    <option value="{{ $twman->id }}">
                                        {{ $twman->prefix . '' . $twman->firstname . ' ' . $twman->lastname . '' . $twman->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif


                    </div>
                    <label class="form-label">สถานะ</label>
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="complete" selected>ติดตั้งมิเตอร์เรียบร้อย</option>
                        </select>
                    </div>


                    <input type="submit" class="btn  btn-sm float-end mt-3 mb-0" value="บันทึกข้อมูลการติดตั้งมิเตอร์">
                </div>
            </form>

        </div><!--card-body -->
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
