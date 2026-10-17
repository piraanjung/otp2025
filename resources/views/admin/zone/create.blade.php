@extends('layouts.super-admin')

@section('style')
    <style>
        /* --- Overall Flat UI --- */
        .card-flat {
            border: 1px solid #e9ecef !important;
            border-radius: 15px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
            background-color: #ffffff;
        }

        .form-control-flat {
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            background-color: #f8fafc !important;
            padding: 0.6rem 0.75rem;
        }

        .form-control-flat:focus {
            background-color: #fff !important;
            border-color: #6366f1 !important;
            box-shadow: none !important;
        }

        /* --- Google Maps Area --- */
        #map-container {
            width: 100%;
            height: 400px;
            border-radius: 12px;
            border: 1px solid #edf2f7;
        }

        /* --- Custom Elements จากรูปภาพ --- */
        .btn-save-all {
            background-color: #8ce11b !important;
            /* สีเขียวตามรูป */
            color: #fff !important;
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(140, 225, 27, 0.3);
        }

        .btn-pick-location {
            background-color: #5e72e4 !important;
            /* สีน้ำเงินตามรูป */
            border: none;
            color: white;
        }

        .btn-add-pink {
            border: 2px solid #d63384 !important;
            /* ขอบชมพูตามรูป */
            color: #d63384 !important;
            background: transparent;
            border-radius: 10px;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-selected {
            background-color: #11cdef !important;
            /* สีฟ้า Cyan ตามรูป */
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
        }

        /* Row Selection Effect */
        .zone-row {
            transition: all 0.3s ease;
            border: 1px solid transparent;
            border-radius: 12px;
        }

        .zone-row.active-row {
            background-color: #f0f7ff;
            border: 1px solid #cbd5e1;
        }
    </style>
@endsection

@section('content')
ตำบล {{ $org[0]->tambons->tambon_name }}
    <div class="container-fluid py-4">
        <form action="{{ url('admin/zone') }}" method="POST" id="mainZoneForm">
            @csrf
            <input type="hidden" name="org_id_fk" value="{{ $org_id_fk }}">
            <input type="hidden" value="{{ $org[0]->org_tambon_id_fk }}" name="tambon_id">

            <div class="row">
                <!-- ฝั่งซ้าย: ฟอร์มกรอกข้อมูล -->
                <div class="col-lg-7">
                    <div class="card card-flat p-4">
                        <h5 class="font-weight-bolder mb-4">ตั้งค่าหมู่บ้าน/หน่วยงาน</h5>

                        <div id="zonelist">
                            <!-- แถวที่ 1 (Index 0) -->
                            <div class="row mb-4 zone-row align-items-end p-2 active-row" data-index="0">
                                <div class="col-md-11">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-xs font-weight-bold text-secondary">ชื่อหมู่ / โซน</label>
                                            <input type="text" name="zone[0][zonename]"
                                                class="form-control form-control-flat" placeholder="ระบุชื่อหมู่" required>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="text-xs font-weight-bold text-secondary">พิกัด GPS</label>
                                            <div class="input-group">
                                                <input type="text" name="zone[0][lat]"
                                                    class="form-control form-control-flat lat-input" placeholder="Lat"
                                                    >
                                                <input type="text" name="zone[0][long]"
                                                    class="form-control form-control-flat lng-input" placeholder="Long"
                                                    >

                                                <button type="button" class="btn btn-pick-location mb-0 pick-map-btn">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </button>

                                            </div>
                                             <input type="text" name="zone[0][location]"
                                                    class="form-control form-control-flat lng-input mt-1" placeholder="location"
                                                    >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 p-0 text-center">
                                    <button type="button" class="btn btn-add-pink addZoneBtn mb-0">
                                        <i class="fas fa-plus fa-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 border-top pt-4">
                            <a href="{{ url('admin/zone') }}" class="text-secondary font-weight-bold">ยกเลิก</a>
                            <button type="submit" class="btn btn-save-all">
                                บันทึกข้อมูลทั้งหมด
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ฝั่งขวา: แผนที่ Google Maps -->
                <div class="col-lg-5">
                    <div class="card card-flat sticky-top" style="top: 20px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="font-weight-bolder mb-0">ปักหมุด GPS</h5>
                                <span class="badge badge-selected text-white" id="active-label">แถวที่ 1
                                    กำลังถูกเลือก</span>
                            </div>

                            <!-- ใส่พื้นหลังสีเทาไว้ด้วย ถ้าแผนที่ยังไม่มาแต่เห็นกล่องสีเทา แปลว่าเป็นที่ API หรือ JS -->
                            <div class="mb-2">
                                <input id="pac-input" type="text" class="form-control form-control-flat"
                                    placeholder="🔍 ค้นหาสถานที่, ตำบล, อำเภอ หรือ จังหวัด..."
                                    style="border-color: #11cdef; box-shadow: 0 2px 6px rgba(17, 205, 239, 0.2);">
                            </div>
                            <div id="map-container" class="mb-4"
                                style="width: 100%; height: 400px !important; min-height: 400px; background-color: #e9ecef; border-radius: 12px; border: 1px solid #edf2f7;">
                            </div>
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <small class="text-secondary font-weight-bold text-uppercase">Latitude</small>
                                    <h4 class="font-weight-bolder" id="display-lat">-</h4>
                                </div>
                                <div class="col-6">
                                    <small class="text-secondary font-weight-bold text-uppercase">Longitude</small>
                                    <h4 class="font-weight-bolder" id="display-lng">-</h4>
                                </div>
                            </div>
                            <p class="text-center text-sm text-muted">
                                * คลิกเลือกแถวที่ต้องการ แล้วคลิกบนแผนที่เพื่อระบุพิกัด
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
    // 1. ประกาศตัวแปรเป็น Global เพื่อให้เข้าถึงได้จากทุกที่
    var map;
    var marker;
    var currentRowIndex = 0;
    var rowCount = 1;

    // 2. บังคับผูกฟังก์ชัน initMap เข้ากับ window object เพื่อไม่ให้ Google Maps หาไม่เจอ
    window.initMap = function () {
        console.log("Google Maps is loading..."); // เช็คใน Console (F12) ว่าข้อความนี้ขึ้นไหม

        var mapElement = document.getElementById("map-container");

        // ถ้าหา element ไม่เจอ ให้หยุดทำงาน
        if (!mapElement) {
            console.error("Error: ไม่พบ Element id='map-container'");
            return;
        }

        var defaultLoc = { lat: 13.7563, lng: 100.5018 };

        map = new google.maps.Map(mapElement, {
            zoom: 13,
            center: defaultLoc,
            mapTypeId: "roadmap" // กำหนดประเภทแผนที่ให้ชัดเจน
        });

        marker = new google.maps.Marker({
            position: defaultLoc, // ต้องใส่ position ให้ marker ตอนเริ่มต้นด้วย
            map: map,
            draggable: true
        });

        // เมื่อคลิกบนแผนที่
        map.addListener("click", function (event) {
            updateCoords(event.latLng);
        });

        // เมื่อลากหมุด
        marker.addListener("dragend", function () {
            updateCoords(marker.getPosition());
        });

        // ==========================================
        // ย้ายส่วน Search Box เข้ามาไว้ในนี้
        // ==========================================
        var input = document.getElementById("pac-input");
        if (input) {
            var searchBox = new google.maps.places.SearchBox(input);

            // วางกล่องค้นหาไว้บนแผนที่
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

            // กำหนดขอบเขตการค้นหาให้อยู่ในมุมมองปัจจุบัน
            map.addListener("bounds_changed", function () {
                searchBox.setBounds(map.getBounds());
            });

            // เมื่อผู้ใช้เลือกสถานที่จากการค้นหา
            searchBox.addListener("places_changed", function () {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // สร้างกรอบเขตพื้นที่เพื่อซูมไปหา
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function (place) {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }

                    // 1. เลื่อนหมุดไปที่ใหม่
                    marker.setPosition(place.geometry.location);

                    // 2. อัปเดตพิกัดลงฟอร์ม
                    updateCoords(place.geometry.location);

                    // 3. ปรับแผนที่ให้พอดีกับสถานที่ใหม่
                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);

                // ซูมเข้าไปใกล้ๆ ให้เห็นถนน (กรณีผลการค้นหามันกว้างไป)
                setTimeout(function () {
                    if (map.getZoom() > 16) map.setZoom(16);
                }, 100);
            });
        }
    };

    // 3. ฟังก์ชันอัปเดตพิกัด (ใช้ Vanilla JS ผสม jQuery เพื่อความชัวร์)
    function updateCoords(latLng) {
        marker.setPosition(latLng);
        var lat = latLng.lat().toFixed(6);
        var lng = latLng.lng().toFixed(6);

        document.getElementById('display-lat').innerText = lat;
        document.getElementById('display-lng').innerText = lng;

        // หาแถวปัจจุบันที่ Active อยู่
        var row = document.querySelector('.zone-row[data-index="' + currentRowIndex + '"]');
        if (row) {
            row.querySelector('.lat-input').value = lat;
            row.querySelector('.lng-input').value = lng;
        }
    }

    // 4. การจัดการปุ่มต่างๆ (ใช้ jQuery document.ready)
    $(document).ready(function () {
        // เมื่อกดปุ่มปักหมุดสีน้ำเงิน
        $(document).on('click', '.pick-map-btn', function () {
            var row = $(this).closest('.zone-row');
            currentRowIndex = row.data('index');

            $('.zone-row').removeClass('active-row');
            row.addClass('active-row');
            $('#active-label').text('แถวที่ ' + (parseInt(currentRowIndex) + 1) + ' กำลังถูกเลือก');

            var existingLat = row.find('.lat-input').val();
            var existingLng = row.find('.lng-input').val();

            // ถ้าแถวนี้มีพิกัดอยู่แล้ว ให้เลื่อนแผนที่ไปหา
            if (existingLat && existingLng && map && marker) {
                var pos = { lat: parseFloat(existingLat), lng: parseFloat(existingLng) };
                marker.setPosition(pos);
                map.panTo(pos);
                $('#display-lat').text(existingLat);
                $('#display-lng').text(existingLng);
            }
        });

        // เมื่อกดปุ่มเพิ่มแถว
        $(document).on('click', '.addZoneBtn', function () {
            var html = `
                <div class="row mb-4 zone-row align-items-end p-2" data-index="${rowCount}">
                    <div class="col-md-11">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="text-xs font-weight-bold text-secondary">ชื่อหมู่ / โซน</label>
                                <input type="text" name="zone[${rowCount}][zonename]" class="form-control form-control-flat" required>
                            </div>
                            <div class="col-md-8">
                                <label class="text-xs font-weight-bold text-secondary">พิกัด GPS</label>
                                <div class="input-group">
                                    <input type="text" name="zone[${rowCount}][lat]" class="form-control form-control-flat lat-input" placeholder="Lat" >
                                    <input type="text" name="zone[${rowCount}][long]" class="form-control form-control-flat lng-input" placeholder="Long" >
                                    <button type="button" class="btn btn-pick-location mb-0 pick-map-btn">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </button>
                                </div>

                                                                         <input type="text" name="zone[${rowCount}][location]"
                                                    class="form-control form-control-flat lng-input mt-1" placeholder="location"
                                                    >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 p-0 text-center">
                        <button type="button" class="btn btn-link text-danger delZoneBtn mb-0">
                            <i class="fas fa-minus-circle fa-lg"></i>
                        </button>
                    </div>
                </div>`;
            $('#zonelist').append(html);
            rowCount++;
        });

        // เมื่อกดลบแถว
        $(document).on('click', '.delZoneBtn', function () {
            $(this).closest('.zone-row').remove();
        });
    });
</script>

    <!-- โหลด Google API ใส่ async defer ตามมาตรฐานที่ทำงานได้เป๊ะๆ -->
    <!-- สังเกตว่ามี &libraries=places เพิ่มเข้ามา -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiUMndsOMLfXe0CW7ANc6u_T2vcxzPMnY&libraries=places&callback=initMap"></script>
@endsection
