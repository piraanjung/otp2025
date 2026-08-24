<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>แจ้งเหตุท่อน้ำแตก</title>
  
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <style>
    body { font-family: sans-serif; padding: 20px; max-width: 500px; margin: 0 auto; }
    .status-box { padding: 12px; margin-bottom: 15px; border-radius: 8px; font-size: 14px; }
    .loading { background-color: #fff3cd; color: #856404; }
    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }
    
    /* กำหนดความสูงของแผนที่ */
    #map {
      height: 250px;
      width: 100%;
      border-radius: 8px;
      margin-bottom: 15px;
      display: none; /* ซ่อนไว้ก่อนจนกว่าจะได้ GPS */
    }

    button {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      background-color: #0d6efd;
      color: white;
      cursor: pointer;
    }
    button:disabled {
      background-color: #cccccc;
      cursor: not-allowed;
    }
    #preview { max-width: 100%; margin-top: 15px; display: none; border-radius: 8px; }
  </style>
</head>
<body>

  <h2>แจ้งเหตุท่อน้ำแตก</h2>

  <!-- กล่องแสดงสถานะ GPS -->
  <div id="statusBox" class="status-box loading">
    ⏳ กำลังค้นหาตำแหน่ง GPS ของคุณ... กรุณากด "อนุญาต"
  </div>

  <!-- พื้นที่แสดงแผนที่ Leaflet -->
  <div id="map"></div>

  <!-- ฟอร์มส่งข้อมูล -->
  <form id="reportForm">
    <!-- ซ่อนค่า lat, lng ไว้ส่งไปยัง Backend -->
    <input type="hidden" id="lat" name="lat">
    <input type="hidden" id="lng" name="lng">

    <label for="cameraInput" style="display: block; margin-bottom: 10px;">
      <b>ถ่ายภาพสถานที่เกิดเหตุ:</b>
    </label>
    <input 
      type="file" 
      id="cameraInput" 
      name="photo" 
      accept="image/*" 
      capture="environment" 
      disabled 
      required
    >

    <br><br>
    <img id="preview" alt="ตัวอย่างรูปถ่าย">
    <br><br>

    <button type="submit" id="submitBtn" disabled>ส่งข้อมูลแจ้งเหตุ</button>
  </form>

  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <script>
    const statusBox = document.getElementById('statusBox');
    const cameraInput = document.getElementById('cameraInput');
    const submitBtn = document.getElementById('submitBtn');
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const preview = document.getElementById('preview');

    let map;
    let marker;

    // 1. ดึงตำแหน่ง GPS เมื่อโหลดหน้าเว็บ
    window.addEventListener('DOMContentLoaded', () => {
      getLocation();
    });

    function getLocation() {
      if (!navigator.geolocation) {
        showError("เบราว์เซอร์ของคุณไม่รองรับการระบุตำแหน่ง GPS");
        return;
      }

      navigator.geolocation.getCurrentPosition(
        (position) => {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          const accuracy = position.coords.accuracy;

          // อัปเดตพิกัดลงใน Form
          updateCoordinates(lat, lng);

          // แสดงแผนที่ Leaflet
          initMap(lat, lng);

          // อัปเดตสถานะ UI
          statusBox.className = "status-box success";
          statusBox.innerHTML = `✅ ได้รับตำแหน่งแล้ว <br><small>ลากหมุดบนแผนที่เพื่อปรับจุดเกิดเหตุให้แม่นยำขึ้นได้</small>`;
          
          // ปลดล็อกการถ่ายรูป
          cameraInput.disabled = false;
        },
        (error) => {
          let message = "ไม่สามารถดึงตำแหน่งได้";
          switch(error.code) {
            case error.PERMISSION_DENIED:
              message = "กรุณายินยอมให้เข้าถึงตำแหน่ง (GPS) เพื่อใช้งานระบบร้องเรียน";
              break;
            case error.POSITION_UNAVAILABLE:
              message = "ไม่สามารถระบุตำแหน่งปัจจุบันได้ กรุณาตรวจสอบสัญญาณ GPS/Internet";
              break;
            case error.TIMEOUT:
              message = "การค้นหาตำแหน่งใช้เวลานานเกินไป กรุณารีเฟรชหน้าเว็บ";
              break;
          }
          showError(message);
        },
        {
          enableHighAccuracy: true,
          timeout: 15000,
          maximumAge: 0
        }
      );
    }

    // 2. สร้างแผนที่ Leaflet และกำหนดให้ หมุดสามารถลากได้ (draggable: true)
    function initMap(lat, lng) {
      const mapContainer = document.getElementById('map');
      mapContainer.style.display = 'block'; // แสดงกล่องแผนที่

      // สร้างแผนที่กำหนดพิกัดเริ่มต้นและ Zoom level
      map = L.map('map').setView([lat, lng], 17);

      // ใช้ OpenStreetMap Tile Layer
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      // สร้าง Marker แบบลากย้ายตำแหน่งได้ (draggable: true)
      marker = L.marker([lat, lng], { draggable: true }).addTo(map);
      marker.bindPopup("<b>จุดเกิดเหตุ</b><br>ลากหมุดนี้ไปยังจุดที่ท่อน้ำแตก").openPopup();

      // เหตุการณ์เมื่อผู้ใช้ "ลากหมุดไปวาง"
      marker.on('dragend', function (e) {
        const position = marker.getLatLng();
        updateCoordinates(position.lat, position.lng);
      });

      // เหตุการณ์เมื่อผู้ใช้ "คลิกบนแผนที่" เพื่อย้ายหมุดมาจุดที่คลิก
      map.on('click', function (e) {
        const newLat = e.latlng.lat;
        const newLng = e.latlng.lng;
        marker.setLatLng([newLat, newLng]);
        updateCoordinates(newLat, newLng);
      });

      // ปรับขนาด Map เพื่อป้องกันปัญหา Map แสดงผลไม่สมบูรณ์
      setTimeout(() => { map.invalidateSize(); }, 200);
    }

    // ฟังก์ชันอัปเดตค่าพิกัดไปยัง Hidden Inputs
    function updateCoordinates(lat, lng) {
      latInput.value = lat;
      lngInput.value = lng;
      console.log(`Updated Location: ${lat}, ${lng}`);
    }

    function showError(msg) {
      statusBox.className = "status-box error";
      statusBox.innerText = "❌ " + msg;
      cameraInput.disabled = true;
      submitBtn.disabled = true;
    }

    // 3. จัดการพรีวิวรูปภาพ
    cameraInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        submitBtn.disabled = false;
      }
    });

    // 4. Submit ฟอร์ม
    document.getElementById('reportForm').addEventListener('submit', (e) => {
      e.preventDefault();
      alert(`ส่งข้อมูลสำเร็จ!\nLatitude: ${latInput.value}\nLongitude: ${lngInput.value}\nไฟล์ภาพ: ${cameraInput.files[0].name}`);
    });
  </script>
</body>
</html>