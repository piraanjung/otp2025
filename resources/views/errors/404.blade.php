<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>ไม่พบหน้าที่ต้องการ | Envsogo</title>
  <link rel="icon" type="image/png" href="{{ asset('logo/ko_envsogo.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link href="{{ asset('soft-ui/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('soft-ui/assets/css/soft-ui-dashboard.css?v=1.0.7') }}" rel="stylesheet" />
  <style>
    body { font-family: "Open Sans", "Sarabun", sans-serif; }
  </style>
</head>

{{-- หน้านี้ไม่ใช้ layout หลักโดยตั้งใจ: หน้า error ถูก render นอก middleware ของ session
     จึงไม่มี Auth::user() และ layout ที่เรียก auth()->user()->can() จะพังเป็น 500 ซ้อน --}}
<body class="bg-gray-100">
  <main class="main-content mt-0">
    <div class="container py-9">
      <div class="row">
        <div class="col-lg-6 col-md-8 mx-auto text-center">
          <h1 class="display-1 text-gradient text-primary mb-0">404</h1>
          <h3 class="font-weight-bolder">ไม่พบหน้าที่คุณต้องการ</h3>
          <p class="text-secondary">หน้าที่เรียกอาจไม่มีอยู่จริง หรือถูกย้ายไปแล้ว</p>
          <a href="{{ url('/') }}" class="btn bg-gradient-primary mt-3">กลับสู่หน้าหลัก</a>
          <a href="javascript:history.back()" class="btn btn-outline-secondary mt-3">ย้อนกลับ</a>
        </div>
      </div>
    </div>
  </main>
</body>

</html>
