<!DOCTYPE html>
<html lang="en">
<?php
use App\Http\Controllers\Api\FunctionsController;
$fnc = new FunctionsController();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://static.line-scdn.net/liff/edge/2.1/liff.js"></script>
    <title>U-Tabwater</title>
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <style>
        .hidden {
            display: none;
        }

        .hder {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            text-align: center
        }

        .hder3 {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            text-align: center
        }

        .hder2 {
            font-size: 15px;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            text-align: center
        }

        .table td,
        .table th {
            padding: 0.1rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table th {
            background-color: lightgray
        }

        #plus8 {
            font-size: 10px;
            color: red
        }

        .total {
            background-color: lightblue;
            font-weight: bold
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <App />
        {{-- <div class="content-wrapper">
            <section class="content">

                <div class="card card-widget widget-user">
                    <div class="widget-user-header bg-info">
                        <h3 class="widget-user-username" id="displayName"></h3>
                        <h5 class="widget-user-desc">ผู้ใช้น้ำ</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle elevation-2" id="pictureUrl">
                        <input type="hidden" class="form-control col-8" id="line_id">
                    </div>
                    <br>
                    <section id="regis-form" class="hidden">
                        <h5 class="mt-4 mb-2">ใส่หมายเลขผู้ใช้น้ำ <code>เพื่อทำการลงทะเบียน</code></h5>
                        <div class="row ">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-gradient-danger">
                                    <span class="info-box-icon"><i class="fas fa-user-plus"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-number">
                                            <input type="text" class="form-control col-8" id="user_id">
                                        </span>

                                        <div class="progress">
                                            <div class="progress-bar" style="width: 70%"></div>
                                        </div>
                                        <span class="progress-description">
                                            <button type="button"
                                                class="btn btn-warning btn-register  float-right">ลงทะเบียน</button>
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                        </div>
                    </section>
                    <section id="have-invoice-or-owe" class="hidden mt-5">
                        <section id="invoice-form" class="content hidden">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="callout callout-info">
                                            <h5><i class="fas fa-info-circle"></i> ใบแจ้งหนี้ค่าน้ำประปา</h5>
                                        </div>


                                        <div class="invoice p-3 mb-3">
                                            <!-- title row -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <h5>
                                                        <i class="fas fa-globe"></i> กิจการประปา เทศบาลตำบลขามป้อม
                                                    </h5>
                                                    โทร: 08-81005436
                                                </div>
                                                <!-- /.col -->
                                            </div>

                                            <div class="mt-3">
                                                <div class="row">
                                                    <div class="col-4">ชื่อผู้ใช้น้ำ</div>
                                                    <div class="col-8">
                                                        <strong class="name"></strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-1">
                                                <div class="row">
                                                    <div class="col-4">ที่อยู่</div>
                                                    <div class="col-8">
                                                        <strong class="address"></strong>
                                                        <strong class="zone_name"></strong>
                                                        <strong>ต.ขามป้อม <br> อ.พระยืน จ.ขอนแก่น 40003</strong>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row mt-2">
                                                <div class="col-3 hder">ประจำเดือน </div>
                                                <div class="col-3 hder">เส้นทาง</div>
                                                <div class="col-3 hder">เลขผู้ใช้น้ำ</div>
                                                <div class="col-3 hder">เลขมิเตอร์</div>

                                                <div class="col-3 text-center"><span class="inv_period"></span> </div>
                                                <div class="col-3 text-center"><span class="subzone_name"></span> </div>
                                                <div class="col-3 text-center"><span class="user_id"></span> </div>
                                                <div class="col-3 text-center"><span class="meternumber"></span> </div>
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                        <div class="invoice mb-3">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="hder3">วันที่จดมาตร </th>
                                                        <th class="hder3">มิเตอร์<br>ปัจจุบัน</th>
                                                        <th class="hder3">มิเตอร์<br>ครั้งก่อน</th>
                                                        <th class="hder3">จำนวน<br>หน่วยที่ใช้</th>
                                                        <th class="hder3">จำนวนเงิน<br>(บาท)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="hder2 rec_date"> </td>
                                                        <td class="hder2 currentmeter"></td>
                                                        <td class="hder2 lastmeter"></td>
                                                        <td class="hder2" id="unit_used">
                                                            <span id="plus8"> (x 8 บาท)
                                                        </td>
                                                        <td class="hder2 diffPlus8"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="hder2" rowspan="3" colspan="2"
                                                            style="font-size: 13px;">
                                                            <div id="test" style="margin-left: 10px" class=""></div>
                                                            เลขใบแจ้งหนี้: <span class="inv_id"></span>
                                                        </td>
                                                        <td class="hder2" colspan="2">ค่ารักษามาตร (บาท)</td>

                                                        <td class="hder2 reserveMeter"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="hder2" colspan="2">ภาษีมูลค่าเพิ่ม 7% </td>

                                                        <td class="hder2">0</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="hder2" colspan="2">รวมเป็นเงินที่ต้องชำระ </td>

                                                        <td class="hder2 total"></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>

                                    </div><!-- /.col -->
                                </div><!-- /.row -->
                            </div><!-- /.container-fluid -->
                        </section>
                        <section id="owe-infos" class="p-2"> </section>
                        <section id="due-paid" class="hidden">
                            <div style="border-bottom: 1px solid black; text-align:center">โปรดชำระเงินภายในวันที่
                                <span class="text-red due-paid"></span>
                            </div>
                            <div style="font-size: 14px" class="mt-2">
                                *หากเกินกำหนดจะถูกระงับการใช้น้ำ และจะจ่ายน้ำใหม่หลังจากได้
                                รับการชำระหนี้ค้างทั้งหมดพร้อมค่าธรรมเนียมการใช้น้ำแล้ว
                            </div>
                        </section>
                    </section><!--haveInvoice-or-owe -->
                    <div id="no-have-invoice-or-owe" class="p-2 mt-3"></div>

            </section>
        </div> --}}
        <button type="button" id="open" onclick="redirect()" style="display: none">xxx</button>
        <button type="button" id="open2" onclick="redirect()" style="display: none">กำลังเปิดใน liff</button>
    </div>
</body>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="{{ asset('/davidshimjs-qrcodejs-04f46c6/qrcode.js') }}"></script>
<script>
    let init = 0;

    async function main() {
        await liff.init({
            liffId: '1656872156-bzVww601'
        })

        liff.openWindow({
            url: "https://webbluetoothcg.github.io/demos/bluetooth-printer/",
            external: true,
        })

        liff
            .initPlugins(["bluetooth"])
            .then(() => {
                liffCheckAvailablityAndDo(() => liffRequestDevice());
            })
            .catch((error) => {
                alert(error);
            });
        liff.bluetooth.getAvailability().then((available) => {
            alert("available?" + available);
        });





    }
    main();

    async function redirect() {
        const liffUrl =
            "https://webbluetoothcg.github.io/demos/bluetooth-printer/" //await liff.permanentLink.createUrlBy(window.location.href)
        window.location = liffUrl
    }
    //  function runApp() {

    //     liff.getProfile().then(profile => {
    //         const redirect =
    //         // check_user(profile);

    //     }).catch(err => console.log(err));
    // }

    // liff.init({ liffId: '1656872156-bzVww601' }, () => {
    //     if(liff.isLoggedIn()){
    //         runApp();
    //     }else{
    //         liff.login();
    //     }
    // }, err => {
    //     //liff.closeWindow();
    //     console.log(err.code, error.message);
    // });

    function check_user(profile) {
        //chek ว่า user มี การบันทึก line_id ในระบบหรือยัง
        $.get(`../api/users/check_line_id/${profile.userId}`).done(function (data) {
            if (data === "0") {
                //ถ้ายังไม่ได้ทำการบันทึก
                document.getElementById('pictureUrl').src = profile.pictureUrl;
                document.getElementById('displayName').innerHTML = profile.displayName;
                $('#line_id').val(profile.userId);
                setTimeout(() => {
                    $('#regis-form').removeClass('hidden')
                }, 500);
            } else {
                //ให้แสดงชื่อจริง จาก server
                document.getElementById('pictureUrl').src = profile.pictureUrl;
                $('#displayName').html(data[0].name)
                showInvoiceAndOwe(data[0].user_id)
            }
        }).catch(err => console.log(err));

    }

    $('.btn-register').click(function () {
        let user_id = $('#user_id').val()
        let line_id = $('#line_id').val()
        //ทำการลงทะเบียน line_id ของ user ใน database
        $.get(`../api/users/update_line_id/${user_id}/${line_id}`).done(function (data) {
            //จากนั้น ไปเอาข้อมูลใบแจ้งหนี้มาแสดง
            $.get(`../api/invoice/${data[0].user_id}`).done((res) => {

                if (Object.keys(res).length > 0) {
                    $('#regis-form').addClass('hidden')
                    res.forEach(element => {
                        if (element.status === 'invoice') {

                            showInvoiceAndOwe(element.user_id)
                        }
                    });
                } else {
                    //ไม่พบใบแจ้งหนี้
                    let res = `
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text"></span>
                                <span class="info-box-number">ไม่พบรายการใบแจ้งหนี้ หรือ รายการค้างชำระ</span>
                            </div>
                            <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                    `
                    $('#no-have-invoice-or-owe').html(res)

                }
            }) //inner $.get
        }); // outer $,get
    }) //.btn-register

    function showInvoiceAndOwe(user_id) {

        $.get(`../api/invoice/${user_id}`).done((data) => {

            $('#regis-form').addClass('hidden')
            if (Object.keys(data).length > 0) {
                data.forEach(element => {
                    //หา รายการแจ้งหนี้ (invoice)
                    if (element.status === 'invoice') {
                        $('.name').text(element.usermeterinfos.user_profile.name)
                        $('.address').text(element.usermeterinfos.user_profile.address)
                        $('.zone_name').text(element.usermeterinfos.zone.zone_name)
                        $('.subzone_name').text(element.usermeterinfos.subzone.subzone_name)
                        $('.inv_period').text(element.invoice_period.inv_period_name)
                        $('.inv_id').text(element.id)
                        $('.user_id').text(element.usermeterinfos.user_profile.user_id)
                        $('.meternumber').text(element.usermeterinfos.meternumber)
                        let rec_date = element.updated_at.split("T")
                        var date = new Date(rec_date[0]);

                        var options = {
                            year: "2-digit",
                            month: "2-digit",
                            day: "numeric"
                        };

                        $('.rec_date').text(date.toLocaleDateString("th", options))
                        $('.currentmeter').text(element.currentmeter)
                        $('.lastmeter').text(element.lastmeter)

                        let diff = element.currentmeter - element.lastmeter;
                        let diffPlus8 = diff == 0 ? 0 : diff * 8;
                        let reserveMeter = diffPlus8 == 0 ? 10 : 0;
                        let total = diffPlus8 + reserveMeter;

                        $('#unit_used').prepend(diff)
                        $('.diffPlus8 ').text(diffPlus8)
                        $('.qr_iv_id').text(element.id)
                        $('.reserveMeter').text(reserveMeter)
                        $('.total').text(total)
                        $('.qr_iv_id').text(element.id)
                        var qrcode = new QRCode("test", {
                            text: element.id,
                            width: 60,
                            height: 60,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                        //สร้างวันที่แจ้งเตือนให้ชำระหนี้ถายในวันที่ ...
                        let month = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                            'กรกฎาคม', 'สิงหาคม', 'กันยายน',
                            'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                        ];
                        //หาวันสุดท้ายของเดือน
                        var _date = rec_date[0].split('-')
                        var lastdayOfMonth = new Date(new Date(new Date().setMonth(_date[1])).setDate(
                            0)).getDate();
                        var _index = _date[1] - 1;
                        var year = parseInt(_date[0]) + 543
                        $('.due-paid').text(`${lastdayOfMonth} ${month[_index]} ${year}`)
                        $('#have-invoice-or-owe').removeClass('hidden')
                        $('#invoice-form').removeClass('hidden')
                        $('#due-paid').removeClass('hidden')

                    }
                });

                //หา รายการค้างชำระย้อนหลัง (owe)
                let oweCount = 0;
                let oweTotal = 0;
                let oweText = `
                <div class="card card-widget widget-user-2">
                    <div class="bg-warning  p-1 ">
                        <h5 class="ml-2">รายการค้างชำระ</h5>
                    </div>
                    <div class="card-footer p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item bg-secondary">
                                <a href="#" class="nav-link">
                                ประจำเดือน
                                <span class="float-right">
                                    จำนวนเงิน (บาท)
                                </span>
                                </a>
                            </li>
                `;
                data.forEach(ele => {
                    if (ele.status === 'owe') {
                        let diff = ele.currentmeter - ele.lastmeter;
                        let diffPlus8 = diff == 0 ? 0 : diff * 8;
                        let reserveMeter = diffPlus8 == 0 ? 10 : 0;
                        let total = diffPlus8 + reserveMeter;
                        oweTotal += total
                        oweCount++;
                        oweText += `
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                ${ele.invoice_period.inv_period_name}
                                <span class="float-right badge bg-primary">
                                    ${total}
                                </span>
                                </a>
                            </li>

                        `;
                    }
                });
                oweText += `
                        <li class="nav-item bg-info">
                            <a href="#" class="nav-link">
                            รวม
                            <span class="float-right">
                                ${oweTotal} บาท
                            </span>
                            </a>
                        </li>
                    </ul></div></div>`

                if (oweCount > 0) {
                    $('#have-invoice-or-owe').removeClass('hidden')
                    $('#owe-infos').html(oweText)
                }
            } else {
                //ไม่พบใบแจ้งหนี้
                let text = `<div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text"></span>
                                <span class="info-box-number">ไม่พบการค้างชำระค่าน้ำประปา</span>
                            </div>
                            </div>
                        </div>`;
                $('#no-have-invoice-or-owe').html(text)
            }
        })
    }
</script>

</html>