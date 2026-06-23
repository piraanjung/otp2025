@extends('layouts.admin1')

@section('content')
<div class="container text-center">
    <h2>📸 พร้อมรับขวดแล้ว!</h2>
    <p>กรุณาส่องขวดให้เห็นชัดๆ</p>

    <div id="ai-container">
        <canvas id="canvasIn" style="display:none;"></canvas>
        <canvas id="canvasOut" style="width: 100%; max-width: 300px; border: 2px solid #4caf50; border-radius: 10px;"></canvas>
        <div id="result-label" style="font-size: 24px; font-weight: bold; margin-top: 10px;">...</div>
    </div>

    <button onclick="finishSession()" class="btn btn-danger mt-4">❌ จบการทำงาน</button>
</div>

<script>
    const kioskId = "{{ $kioskId }}";

    // --- ตรงนี้ใส่โค้ด AI / Teachable Machine ที่เราเขียนกันไว้ ---
    // ... (Init AI, Load Model, Wait for WebSocket Image) ...

    function finishSession() {
        // แจ้ง Server ว่าจบงานแล้ว
        fetch('/api/kiosk/finish', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ kiosk_id: kioskId })
        }).then(() => {
            window.location.href = "/home"; // กลับหน้าหลัก
        });
    }
</script>
@endsection
