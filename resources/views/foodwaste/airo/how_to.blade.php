<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คู่มือการใช้งาน AiroBact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #11998e;
            --secondary-color: #38ef7d;
            --bg-color: #f8fafc;
        }
        body { background-color: var(--bg-color); font-family: 'Nunito', sans-serif; color: #334155; }

        /* Header Section */
        .how-to-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 40px 20px;
            border-radius: 0 0 30px 30px;
            color: white;
            text-align: center;
            margin-bottom: -30px;
        }

        /* Card Customization */
        .custom-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .section-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 10px;
            display: block;
        }

        /* Video Styling */
        .video-container { border-radius: 20px; overflow: hidden; }

        /* Step Styling */
        .step-item {
            display: flex;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .step-number {
            background: #e6fffa;
            color: var(--primary-color);
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }

        /* FAQ Accordion */
        .accordion-item { border: none; margin-bottom: 10px; border-radius: 15px !important; overflow: hidden; }
        .accordion-button { background-color: white !important; font-weight: 600; padding: 18px; }
        .accordion-button:not(.collapsed) { color: var(--primary-color); box-shadow: none; }
        .accordion-body { background-color: white; color: #64748b; font-size: 0.9rem; padding-top: 0; }
    </style>
</head>
<body>

<div class="how-to-header">
    <h2 class="fw-bold"><i class="bi bi-journal-richtext"></i> วิธีการใช้งาน</h2>
    <p class="opacity-75">เปลี่ยนขยะเป็นปุ๋ยง่ายๆ ในไม่กี่ขั้นตอน</p>
</div>

<div class="container" style="max-width: 600px; margin-top: 50px; padding-bottom: 50px;">

    <span class="section-label">01 — วิดีโอสาธิต</span>
    <div class="card custom-card">
       <div class="ratio ratio-16x9 custom-card shadow-sm" style="border-radius: 20px; overflow: hidden;">
    <iframe
        src="https://drive.google.com/file/d/16_vJeUVoPL4tfnsUys57qmXWlLlRwxHd/preview"
        allow="autoplay"
        allowfullscreen
        style="border: none;">
    </iframe>
</div>
        <div class="card-body p-3">
            <h6 class="fw-bold mb-1">สาธิตการใช้งาน AiroBact Bin</h6>
            <p class="small text-muted mb-0">ดูขั้นตอนการเริ่มใช้งานถังหมักตั้งแต่แกะกล่องจนถึงได้ปุ๋ย</p>
        </div>
    </div>

    <span class="section-label">02 — ขั้นตอนการดูแล</span>
    <div class="card custom-card">
        <div class="card-body p-4">
            <div class="step-item">
                <div class="step-number">1</div>
                <div>
                    <h6 class="fw-bold mb-1">บันทึกมื้ออาหาร</h6>
                    <p class="small text-muted mb-0">ถ่ายรูปมื้ออาหารของคุณเพื่อให้ AI ช่วยวิเคราะห์แคลอรี่และขยะที่อาจเกิดขึ้น</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-number">2</div>
                <div>
                    <h6 class="fw-bold mb-1">แยกขยะเปียก</h6>
                    <p class="small text-muted mb-0">เทเศษอาหารลงในถัง AiroBact และบันทึกน้ำหนักเพื่อเก็บแต้มรักษ์โลก</p>
                </div>
            </div>
            <div class="step-item border-0">
                <div class="step-number">3</div>
                <div>
                    <h6 class="fw-bold mb-1">เติมอากาศและจุลินทรีย์</h6>
                    <p class="small text-muted mb-0">ทวนถังหมักสัปดาห์ละครั้ง เพื่อให้จุลินทรีย์ย่อยสลายได้รวดเร็วขึ้น</p>
                </div>
            </div>
        </div>
    </div>

    <span class="section-label">03 — คำถามที่พบบ่อย (FAQ)</span>
    <div class="accordion" id="faqAccordion">
        <div class="accordion-item shadow-sm">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                    ถ้ามีกลิ่นเหม็นต้องแก้ไขอย่างไร?
                </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    แนะนำให้เติมใบไม้แห้ง กากกาแฟ หรือรำ เพื่อลดความชื้น และทำการคนกองปุ๋ยให้ทั่วเพื่อเพิ่มอากาศครับ
                </div>
            </div>
        </div>

        <div class="accordion-item shadow-sm">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                    ตัวหนอนสีขาวในถังอันตรายไหม?
                </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    ไม่ต้องตกใจ! นั่นคือหนอนแมลงวันลาย เป็นฮีโร่ช่วยย่อยสลายขยะเปียกได้เร็วขึ้นมาก และไม่มีเชื้อโรคครับ
                </div>
            </div>
        </div>

        <div class="accordion-item shadow-sm border-0">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                    ใช้เวลานานแค่ไหนถึงจะได้ปุ๋ย?
                </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    โดยปกติจะใช้เวลาประมาณ 30-45 วัน ขึ้นอยู่กับประเภทขยะและความถี่ในการเติมอากาศครับ
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 text-center">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
            <i class="bi bi-arrow-left"></i> ย้อนกลับ
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
