<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="lat">Latitude (ละติจูด)</label>
            <input type="text" class="form-control" id="lat" name="lat"
                   value="{{ old('lat', $kiosk->lat ?? '') }}" placeholder="เลือกจุดบนแผนที่" readonly required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="lng">Longitude (ลองจิจูด)</label>
            <input type="text" class="form-control" id="lng" name="lng"
                   value="{{ old('lng', $kiosk->lng ?? '') }}" placeholder="เลือกจุดบนแผนที่" readonly required>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0">
                <h6>ระบุตำแหน่งบนแผนที่ (คลิก หรือ ลากหมุดเพื่อแก้ไข)</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div id="map" style="height: 500px; width: 100%; border-radius: 8px;"></div>
            </div>
        </div>
    </div>
</div>


<script>
    let map;
    let marker;
    // พิกัดเริ่มต้น (บ้านนาสนาม เขมราฐ)
    const defaultLocation = { lat: 16.0591353, lng: 105.1723741 };

    function initMap() {
        // 1. ตรวจสอบค่าเริ่มต้นจาก Input (กรณี Edit หรือ Validation Fail)
        const latInput = document.getElementById("lat").value;
        const lngInput = document.getElementById("lng").value;

        let initialPosition = defaultLocation;
        let hasInitialData = false;

        // ถ้ามีข้อมูลเดิม ให้ใช้พิกัดเดิม
        if (latInput && lngInput) {
            initialPosition = {
                lat: parseFloat(latInput),
                lng: parseFloat(lngInput)
            };
            hasInitialData = true;
        }

        // 2. สร้างแผนที่
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 16,
            center: initialPosition,
        });

        // 3. ถ้าเป็นหน้า Edit ให้ปักหมุดเดิมรอไว้เลย
        if (hasInitialData) {
            placeMarker(initialPosition);
        }

        // 4. Event Listener: คลิกบนแผนที่เพื่อย้ายหมุด
        map.addListener("click", (event) => {
            placeMarker(event.latLng);
        });
    }

    // ฟังก์ชันสร้าง/ย้ายหมุด และอัปเดต Input
    function placeMarker(location) {
        // ถ้ามีหมุดอยู่แล้ว ให้ย้ายตำแหน่ง
        if (marker) {
            marker.setPosition(location);
        } else {
            // ถ้ายังไม่มี ให้สร้างใหม่
            marker = new google.maps.Marker({
                position: location,
                map: map,
                draggable: true, // อนุญาตให้ลากหมุดได้
                animation: google.maps.Animation.DROP
            });

            // Event Listener: เมื่อลากหมุดเสร็จ ให้ดึงค่าพิกัดใหม่
            marker.addListener('dragend', function(event) {
                updateInputs(event.latLng);
            });
        }

        // อัปเดตค่าลง Input
        updateInputs(location);
    }

    // ฟังก์ชันอัปเดตค่าลง HTML Input
    function updateInputs(latLng) {
        // Google Maps บางทีส่งมาเป็น Object บางทีเป็น Function
        const lat = typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat;
        const lng = typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng;

        document.getElementById("lat").value = lat.toFixed(7);
        document.getElementById("lng").value = lng.toFixed(7);
    }
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initMap">
</script>

