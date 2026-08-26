@extends('layouts.print')

@section('style')

    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/versions/2.22.3/sdk.js"></script>
    <script src="https://unpkg.com/axios@1.6.7/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap");

        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f7f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .liff-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            width: 100%;
            max-width: 450px;
        }

        .step-container {
            display: none;
            /* ซ่อนทุกขั้นตอนไว้ก่อน จะใช้ JS เปิดทีละสเต็ป */
        }

        .step-container.active {
            display: block;
            animation: fadeIn 0.4s ease-in-out;
        }

        .line-green-btn {
            background-color: #06C755;
            color: white;
            border: none;
        }

        .line-green-btn:hover {
            background-color: #05b34c;
            color: white;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <style>
        @import url("https://fonts.googleapis.com/css?family=Fredoka+One");

        .store-container {
            line-height: 0;
            margin: 50px auto;
            width: 90%;
        }

        .stroke {
            stroke: #0170bb;
            stroke-width: 5;
            stroke-linejoin: round;
            stroke-miterlimit: 10;
        }

        .round-end {
            stroke-linecap: round;
        }

        #store {
            animation: fadeIn 0.8s ease-in;
        }

        .border-animation {
            background-color: white;
            border-radius: 10px;
            position: relative;
        }

        .border-animation:after {
            content: "";
            background: linear-gradient(45deg, #ccc 48.9%, #0170bb 49%);
            background-size: 300% 300%;
            border-radius: 10px;
            position: absolute;
            top: -5px;
            left: -5px;
            height: calc(100% + 10px);
            width: calc(100% + 10px);
            z-index: -1;
            animation: borderGradient 8s linear both infinite;
        }

        @keyframes borderGradient {

            0%,
            100% {
                background-position: 0% 100%;
            }

            50% {
                background-position: 100% 0%;
            }
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        #browser {
            transform: translateY(-100%);
            -webkit-animation: moveDown 1.5s cubic-bezier(0.77, -0.5, 0.3, 1.5) forwards;
            animation: moveDown 1.5s cubic-bezier(0.77, -0.5, 0.3, 1.5) forwards;
        }

        @keyframes moveDown {
            from {
                transform: translate(0, -100%);
            }

            to {
                transform: translate(0, 0);
            }
        }

        #toldo {
            animation: fadeIn 1s 1.4s ease-in forwards;
        }

        .grass {
            animation: fadeIn 0.5s 1.6s ease-in forwards;
        }

        #window {
            animation: fadeIn 0.5s 1.8s ease-in forwards;
        }

        #door {
            animation: fadeIn 0.5s 2s ease-in forwards;
        }

        #sign {
            transform-origin: 837px 597px;
            animation: pendulum 1.5s 2s ease-in-out alternate;
        }

        .trees {
            animation: fadeIn 0.5s 2.2s ease-in forwards;
        }

        #toldo,
        .grass,
        #window,
        #door,
        .trees,
        .cat,
        .cat-shadow,
        .box,
        .parachute,
        .tshirt,
        .cap,
        .ball,
        #text,
        #button,
        .sky-circle,
        .sky-circle2,
        .sky-circle3 {
            opacity: 0;
        }

        @keyframes pendulum {
            20% {
                transform: rotate(60deg);
            }

            40% {
                transform: rotate(-40deg);
            }

            60% {
                transform: rotate(20deg);
            }

            80% {
                transform: rotate(-5deg);
            }
        }

        .cat {
            transform-origin: 1145px 620px;
        }

        .cat-shadow {
            transform-origin: 1115px 625px;
        }

        #store:hover .cat {
            animation: catHi 3s 3s cubic-bezier(0.7, -0.5, 0.3, 1.4);
        }

        #store:hover .cat-shadow {
            animation: catShadow 4s 2s cubic-bezier(0.7, -0.5, 0.3, 1.4) alternate;
        }

        @keyframes catHi {

            0%,
            100% {
                opacity: 0;
                transform: scale(0.8);
            }

            10%,
            60% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes catShadow {

            0%,
            100% {
                transform: translate(40px, -35px) scale(0.3);
            }

            10%,
            60% {
                opacity: 1;
                transform: translate(-5px, 10px) scale(0.5);
            }

            60% {
                opacity: 0;
            }
        }

        .box,
        .parachute {
            transform-origin: 430px 100px;
            animation: moveBox 14s 4s linear forwards infinite;
        }

        .parachute {
            animation: parachute 14s 4s linear forwards infinite;
        }

        @keyframes moveBox {
            0% {
                opacity: 0;
                transform: translate(0, -150px) rotate(20deg);
            }

            15% {
                opacity: 1;
                transform: translate(0, 100px) rotate(-15deg);
            }

            25% {
                transform: translate(0, 250px) rotate(10deg);
            }

            30% {
                transform: translate(0, 350px) rotate(-5deg);
            }

            35% {
                opacity: 1;
                transform: translate(0, 570px) rotate(0deg);
            }

            45%,
            100% {
                opacity: 0;
                transform: translate(0, 570px);
            }
        }

        @keyframes parachute {
            0% {
                transform: translate(0, -150px) rotate(20deg) scale(0.8);
                opacity: 0;
            }

            15% {
                transform: translate(0, 100px) rotate(-15deg) scale(1);
                opacity: 1;
            }

            25% {
                transform: translate(0, 250px) rotate(10deg);
            }

            30% {
                transform: translate(0, 350px) rotate(-5deg);
            }

            33% {
                transform: translate(0, 460px) rotate(0deg) scale(0.9);
                opacity: 1;
            }

            45%,
            100% {
                transform: translate(0, 480px);
                opacity: 0;
            }
        }

        .tshirt {
            animation: fadeInOut 42s 10s ease-in forwards infinite;
        }

        .cap {
            animation: fadeInOut 42s 24s ease-in forwards infinite;
        }

        .ball {
            animation: fadeInOut 42s 38s ease-in forwards infinite;
        }

        #text,
        #button {
            animation: fadeIn 1s 5s ease-in forwards;
        }

        @keyframes fadeInOut {

            5%,
            12% {
                opacity: 1;
            }

            20% {
                opacity: 0;
            }
        }

        .cloud {
            animation: clouds 50s linear backwards infinite;
        }

        .cloud2 {
            animation: clouds 40s 40s linear backwards infinite;
        }

        .plane {
            animation: clouds 30s linear backwards infinite;
            will-change: transform;
        }

        @keyframes clouds {
            from {
                transform: translate(-150%, 0);
            }

            to {
                transform: translate(150%, 0);
            }
        }

        .sky-circle {
            animation: fadeInOut 10s 5s ease-in infinite;
        }

        .sky-circle2 {
            animation: fadeInOut 12s 30s ease-in infinite;
        }

        .sky-circle3 {
            animation: fadeInOut 8s 40s ease-in infinite;
        }

        .hidden {
            display: none !important
        }

        .select2 {
            width: 100% !important
        }
    </style>
    <style>
        /* ปรับให้ Modal เต็มจอบนมือถือ */
        @media (max-width: 576px) {
            .modal-dialog.modal-fullscreen-sm-down {
                max-width: none;
                height: 100%;
                margin: 0;
            }

            .modal-content {
                height: 100%;
                border: 0;
                border-radius: 0;
            }
        }

        /* Input ที่กดแล้วเด้ง Modal */
        .clickable-input {
            background-color: #fff !important;
            /* ให้ดูเหมือน Input ปกติ */
            cursor: pointer;
            caret-color: transparent;
            /* ไม่ให้มี cursor กระพริบ */
        }

        /* รายการใน Modal */
        .list-group-item-action {
            cursor: pointer;
        }

        .list-group-item-action:active {
            background-color: #e9ecef;
        }

        /* Import ฟอนต์ไทยสวยๆ */
        @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap');

        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f0f2f5;
            /* สีพื้นหลังให้อ่อนสบายตา */
        }

        /* ปรับแต่ง Card หลัก */
        .material-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            background: #fff;
            overflow: hidden;
        }

        /* ส่วนหัว Profile */
        .profile-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 30px 20px 20px;
            text-align: center;
            border-radius: 0 0 50% 50% / 20px;
            /* ทำโค้งด้านล่างเล็กน้อย */
            margin-bottom: 25px;
        }

        .profile-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            object-fit: cover;
            background-color: #ddd;
        }

        /* Selection Cards (เลือกประเภทหน่วยงาน) */
        .org-selector-wrapper {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .org-radio-input {
            display: none;
        }

        .org-card {
            flex: 1;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 15px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
            color: #6c757d;
        }

        .org-card i {
            font-size: 1.5rem;
            margin-bottom: 5px;
            display: block;
        }

        /* เมื่อถูกเลือก (Checked State) */
        .org-radio-input:checked+.org-card {
            border-color: #007bff;
            background-color: #f0f7ff;
            color: #007bff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        }

        /* Floating Form Inputs Customization */
        .form-floating>.form-control {
            border-radius: 12px;
            border: 1px solid #dee2e6;
            height: 55px;
        }

        .form-floating>.form-control:focus {
            box-shadow: none;
            border-color: #007bff;
            border-width: 2px;
        }

        .select2-container .select2-selection--single {
            height: 55px !important;
            border-radius: 12px !important;
            border: 1px solid #dee2e6 !important;
            display: flex;
            align-items: center;
        }

        /* Clickable Readonly Inputs */
        .clickable-input {
            background-color: #fff !important;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        /* ปุ่ม Submit */
        .btn-submit-material {
            border-radius: 50px;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
            transition: transform 0.2s;
        }

        .btn-submit-material:active {
            transform: scale(0.98);
        }

        .topic_no {
            background: black;
            color: pink;
            border-radius: 50%;
            padding: 5px 12px;
            float: left;
            margin-left: -30px;
            margin-top: -30px;
        }


        .line {
            border: 1px solid #007bff;
            padding: 15px;
            margin-bottom: 30px
        }
    </style>
@endsection

@section('content')
    <div class="container d-flex justify-content-center p-3">
        <div class="liff-card">

            <div id="loading-state" class="text-center py-4">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-3 text-muted">กำลังตรวจสอบสถานะบัญชี LINE...</p>
            </div>

            <div id="step-phone" class="step-container">
                <h4 class="text-center mb-4 text-dark fw-bold"><i
                        class="bi bi-shield-lock-fill text-success me-2"></i>ยืนยันตัวตนเข้าสู่ระบบ</h4>
                <p class="text-muted text-center small mb-4">กรุณากรอกเบอร์โทรศัพท์ที่เคยลงทะเบียนไว้กับองค์กร</p>
                <form id="form-phone">
                    <div class="mb-3">
                        <label for="input-phone" class="form-label">เบอร์โทรศัพท์</label>
                        <input type="tel" class="form-control form-control-lg text-center" id="input-phone"
                            placeholder="เช่น 0812345678" maxlength="10" required>
                    </div>
                    <button type="submit" class="btn line-green-btn w-100 btn-lg mt-2">ตรวจสอบข้อมูล</button>
                    <div class="text-center mt-3 pt-2 border-top border-light">
                        <p class="text-muted small mb-1">ยังไม่เคยลงทะเบียนใช้งานระบบ?</p>
                        <button type="button" onclick="newUser()" class="btn btn-outline-primary btn-sm px-4 rounded-pill">
                            <i class="bi bi-person-plus-fill me-1"></i> ลงทะเบียนผู้ใช้ใหม่
                        </button>
                    </div>
                </form>
            </div>

            <div id="step-idcard" class="step-container">
                <div class="alert alert-warning text-center  mb-4">
                    <i class="bi bi-exclamation-triangle-fill"></i> ท่านยังไม่ได้ทำการลงทะเบียน
                    ใช้งาน Line OA
                </div>
                <h5 class="fw-bold mb-3">กรอกเลขบัตรประชาชน</h5>
                <form id="form-idcard">
                    <div class="mb-3">
                        <label for="input-idcard" class="form-label">เลขบัตรประจำตัวประชาชน</label>
                        <input type="text" class="form-control form-control-lg text-center"
                            style="font-size: large; font-weight: bold;" id="input-idcard" placeholder="เลข 13 หลัก"
                            maxlength="13" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg">ค้นหาด้วยเลขบัตรประชาชน</button>
                    {{-- <button type="button" onclick="goToStep('phone')"
                        class="btn btn-link w-100 text-muted mt-2 btn-sm text-decoration-none">ย้อนกลับไปกรอกเบอร์โทร</button>
                    --}}
                </form>
            </div>

            <div id="step-select-org" class="step-container">
                <h4 class="text-center mb-2 fw-bold text-success"><i class="bi bi-check-circle-fill"></i> ยืนยันตัวตนสำเร็จ
                </h4>
                <p class="text-muted text-center mb-4">พบบัญชีของท่านในระบบ
                    กรุณาเลือกองค์กรที่ต้องการเข้าใช้งาน</p>

                <div id="org-list-container" class="list-group mb-4">
                </div>
            </div>

            <div id="step-register-notice" class="step-container text-center py-3">
                <div id="new_user_form">
                    ลงทะเบียนผู้ใช้งานใหม่
                    <form id="registerForm">

                        {{-- 1. ชื่อ - นามสกุล (ย้ายมาไว้บนสุด เพื่อความชัดเจน) --}}
                        <div class="line">
                            <label class="d-block text-secondary mb-2 ps-1 text-start"><span
                                    class="topic_no">1</span></label>
                            <div class="row g-2">

                                <div class="col-6">
                                    <div class="form-floating">
                                        <input type="text" name="firstname" class="form-control" id="firstname"
                                            placeholder="ชื่อ" required>
                                        <label for="firstname">ชื่อ</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating">
                                        <input type="text" name="lastname" class="form-control" id="lastname"
                                            placeholder="นามสกุล" required>
                                        <label for="lastname">นามสกุล</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--
                        <hr class="text-muted opacity-25 mb-4"> --}}

                        {{-- 2. เลือกประเภทหน่วยงาน (Selection Cards) --}}
                        <div class="line">
                            <label class="d-block text-secondary mb-2 ps-1 text-start"><span
                                    class="topic_no">2</span></label>
                            <div class="form-group">
                                <label class="d-block text-secondary small mb-2 ps-1">สังกัดหน่วยงาน</label>

                                <div class="row g-2">

                                    <div class="col-6 col-md-6">
                                        <label class="w-100 m-0">
                                            <input type="radio" class="org-radio-input" name="org_type_selector"
                                                id="type_general" value="general">
                                            <div class="org-card text-center py-3">
                                                <i class="bi bi-building d-block mb-1 fs-4"></i>
                                                <span>เทศบาล/อบต.</span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="col-6 col-md-6">
                                        <label class="w-100 m-0">
                                            <input type="radio" class="org-radio-input" name="org_type_selector"
                                                id="type_uni" value="uni" checked>
                                            <div class="org-card text-center py-3">
                                                <i class="bi bi-mortarboard-fill d-block mb-1 fs-4"></i>
                                                <span>มหาวิทยาลัย</span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="col-6 col-md-6">
                                        <label class="w-100 m-0">
                                            <input type="radio" class="org-radio-input" name="org_type_selector"
                                                id="type_hospital" value="hospital">
                                            <div class="org-card text-center py-3">
                                                <i class="bi bi-hospital-fill d-block mb-1 fs-4"></i>
                                                <span>โรงพยาบาล</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <label class="w-100 m-0">
                                            <input type="radio" class="org-radio-input" name="org_type_selector"
                                                id="type_school" value="school">
                                            <div class="org-card text-center py-3">
                                                <i class="bi bi-hospital-fill d-block mb-1 fs-4"></i>
                                                <span>โรงเรียน</span>
                                            </div>
                                        </label>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- 3. Dropdown ชื่อหน่วยงาน --}}
                        <div class="line">
                            <label class="d-block text-secondary mb-2 ps-1 text-start"><span
                                    class="topic_no">3</span></label>
                            <div class="form-floating">
                                <input type="text" id="org_display" class="form-control clickable-input"
                                    placeholder="เลือกหน่วยงาน..." readonly>
                                <label id="org_label">เลือกสถานศึกษา ที่ท่านสังกัด</label>
                                <input type="hidden" name="org_id" id="org_id">
                                <input type="hidden" name="province_id" id="province_id">
                                <input type="hidden" name="district_id" id="district_id">
                                <input type="hidden" name="tambon_id" id="tambon_id">
                            </div>

                            <div id="location_info_display" class="d-none bg-light p-3 rounded-3 mb-3 border border-light">
                                <p class="small text-muted mb-2"><i class="bi bi-geo-alt-fill"></i> ที่ตั้งหน่วยงาน</p>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <input type="text" id="show_province"
                                            class="form-control form-control-sm bg-white border-0" disabled
                                            placeholder="จ.">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" id="show_district"
                                            class="form-control form-control-sm bg-white border-0" disabled
                                            placeholder="อ.">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" id="show_tambon"
                                            class="form-control form-control-sm bg-white border-0" disabled
                                            placeholder="ต.">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 5. ส่วนรายละเอียดฟอร์ม --}}
                        <div class="line">
                            <label class="d-block text-secondary mb-2 ps-1 text-start"><span
                                    class="topic_no">4</span></label>
                            <div id="form_details" class="d-none animate__animated animate__fadeIn">

                                {{-- Zone / Subzone (Clickable Inputs) --}}
                                <div class="form-floating mb-3">
                                    <input type="text" id="zone_display" class="form-control clickable-input"
                                        placeholder="เลือก" readonly>
                                    <label id="zone_label">หมู่ที่/โซน</label>
                                    <input type="hidden" name="zone_id" id="zone_id">
                                </div>

                                <div class="form-floating mb-4">
                                    <input type="text" id="subzone_display" class="form-control clickable-input"
                                        placeholder="เลือก" readonly disabled>
                                    <label id="subzone_label">ซอย/อาคาร</label>
                                    <input type="hidden" name="subzone_id" id="subzone_id">
                                </div>

                                {{-- สมาชิกใหม่ (กรอกเพิ่มเติม) --}}
                                <div id="new_member_div">
                                    <div class="form-floating mb-3" id="address_div">
                                        <input type="text" name="address" id="address" class="form-control"
                                            placeholder="บ้านเลขที่">
                                        <label>บ้านเลขที่ / ห้องเลขที่</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="tel" name="phone" id="phone" class="form-control"
                                            placeholder="เบอร์โทร">
                                        <label>หมายเลขโทรศัพท์ผู้สมัคร</label>
                                    </div>
                                </div>

                                <button type="button" id="phone_search_btn"
                                    class="btn btn-info text-white w-100 btn-submit-material mt-2">
                                    ลงทะเบียนเข้าใช้งาน
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>

        </div>
    </div>
    <div class="modal fade" id="selectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">เลือกรายการ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-2 bg-light sticky-top border-bottom">
                        <input type="text" id="modalSearch" class="form-control" placeholder="ค้นหา...">
                    </div>
                    <div class="list-group list-group-flush" id="modalListContainer">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let currentLineUserId = null;
        let currentLineUserImage = null;
        let userInputPhone = null;
        let mySelectionModal = null;
        const phone_search_btn = document.getElementById('phone_search_btn')
        const province_id_text = document.getElementById('province_id')
        const district_id_text = document.getElementById('district_id')
        const tambon_id_text = document.getElementById('tambon_id')
        const org_id_text = document.getElementById('org_id')

        let profile;

        // เปลี่ยนหน้ากาก Step ต่าง ๆ
        function goToStep(stepName) {
            $('.step-container').removeClass('active');
            $('#loading-state').addClass('d-none');
            $(`#step-${stepName}`).addClass('active');
        }

        // 1. เริ่มต้นการทำงานผูก LINE LIFF
        async function initializeLiff() {
            try {
                await liff.init({ liffId: "1656703539-5eopvjK9" }); // 👈 นำ LIFF ID จาก Line Developer Console มาใส่ตรงนี้
                if (!liff.isLoggedIn()) {
                    console.log('xx')

                    liff.login();
                    return;
                }

                profile = await liff.getProfile();
                console.log('profile', profile)

                currentLineUserId = profile.userId;
                console.log('currentLineUserId', currentLineUserId)
                // ป้องกัน Error กรณีผู้ใช้งานไม่ได้ตั้งรูปโปรไฟล์ใน LINE (pictureUrl จะเป็น undefined)
                if (profile.pictureUrl) {
                    currentLineUserImage = profile.pictureUrl.replace("https://profile.line-scdn.net/", "");
                } else {
                    currentLineUserImage = null;
                }

                // ส่งไปเช็คที่หลังบ้านก่อนเป็นอันดับแรกว่า Line ID นี้เคยผูกบัญชีไปหรือยัง
                // อ่าน Query Parameter จาก URL ว่ามาจากปุ่มไหน (?page=report หรือ ?page=login)
                const urlParams = new URLSearchParams(window.location.search);
                const page = urlParams.get('page');

                if (page === 'report') {
                    // หน้าแจ้งเหตุ: เช็กเพื่อ Auto-fill ข้อมูลสมาชิก (ถ้าไม่ใช่สมาชิกก็เปิดให้แจ้งเหตุแบบ Guest ได้)
                    checkUserForReportForm(currentLineUserId);
                } else {
                    // หน้าเช็กสิทธิ์/เข้าสู่ระบบเดิม
                    checkExistingLineUser(currentLineUserId);
                }

            } catch (error) {
                console.error("LIFF Initialization failed", error);
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อ LINE SDK ได้: ' + error.message, 'error');
            }
        }

        // 2. ฟังก์ชันตรวจสอบว่า LINE ID นี้ผูกกับ Account ไหนในฐานข้อมูลหรือยัง
        function checkExistingLineUser(lineId) {
            $.ajax({
                url: "{{ url('api/line/check-user') }}", // 👈 ตรวจสอบ URL ของ Route ให้ตรงกัน
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    line_user_id: lineId
                },
                success: function (response) {
                    console.log('res', response)
                    if (response.status === 'found') {
                        console.log('response.organization_list', response)
                        // หากเคยลงทะเบียนและผูก Line ID ไว้แล้ว นำไปสเต็ปเลือกองค์กรเลย
                        if (Object.keys(response.organization_list).length > 1) {

                            renderOrganizationList(response.organization_list);
                            goToStep('select-org');
                        } else {
                            pref_id = response.organization_list[0].pref_id;
                            orgId = response.organization_list[0].id;
                            if (Object.keys(response.organization_list).length === 1) {
                                window.location.href = "{{ url('/line/dashboard') }}/" + pref_id + "/" + orgId;

                            } else {
                                selectOrganization(userId, orgId, org_name)
                            }

                        }
                    } else {
                        // เป็นผู้ใช้ที่เข้ามาครั้งแรกหรือยังไม่ได้ผูก Line ID -> ไปหน้าสเต็ปกรอกเบอร์
                        console.log('phone')
                        goToStep('idcard');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์หลักได้', 'error');
                }
            });
        }

        // 3. จัดการตอนส่งฟอร์ม Step 1: ค้นหาด้วยเบอร์โทรศัพท์
        $('#form-phone').on('submit', function (e) {
            e.preventDefault();
            userInputPhone = $('#input-phone').val();

            Swal.showLoading();
            $.ajax({
                url: "{{ url('api/line/verify-user') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    phone: userInputPhone,
                    line_user_id: currentLineUserId,
                    line_user_image: currentLineUserImage
                },
                success: function (response) {
                    Swal.close();
                    if (response.status === 'found') {
                        Swal.fire('สำเร็จ', 'ผูกบัญชีผู้ใช้งานเรียบร้อย', 'success');
                        renderOrganizationList(response.organization_list);
                        goToStep('select-org');
                    } else if (response.status === 'ask_id_card') {
                        // ไม่เจอเบอร์โทร -> ไป Step 2 (ID Card Fallback)
                        goToStep('idcard');
                    }
                },
                error: function () {
                    Swal.close();
                    Swal.fire('ผิดพลาด', 'ระบบทำงานไม่สำเร็จ', 'error');
                }
            });
        });

        // 4. จัดการตอนส่งฟอร์ม Step 2: ค้นหาด้วยบัตรประชาชน (กรณีเปลี่ยนเบอร์โทร)
        $('#form-idcard').on('submit', function (e) {
            e.preventDefault();
            let idCard = $('#input-idcard').val();

            Swal.showLoading();
            $.ajax({
                url: "{{ url('api/line/verify-user') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    phone: userInputPhone, // ส่งเบอร์โทรใหม่ที่กรอกในขั้นแรกไปอัปเดตด้วย
                    id_card: idCard,
                    line_user_id: currentLineUserId,
                    line_user_image: currentLineUserImage

                },
                success: function (response) {
                    Swal.close();
                    if (response.status === 'found_and_updated') {
                        Swal.fire('สำเร็จ', 'อัปเดตข้อมูลและผูกบัญชีใหม่เรียบร้อย', 'success');
                        renderOrganizationList(response.organization_list);
                        goToStep('select-org');
                    } else if (response.status === 'go_to_register') {
                        // ไม่พบข้อมูลอะไรเลยจริงๆ -> ให้สมัครใหม่
                        goToStep('register-notice');
                    }
                },
                error: function () {
                    Swal.close();
                    Swal.fire('ผิดพลาด', 'ไม่สามารถค้นหาข้อมูลได้', 'error');
                }
            });
        });

        // 5. เรนเดอร์รายการองค์กร (Multi-organization UI)
        // 1. ปรับการ Render เพื่อให้ส่งค่า prefId เข้าไปในฟังก์ชันคลิกด้วย
        function renderOrganizationList(organizations) {
            let container = $('#org-list-container');
            container.empty();

            if (organizations.length === 0) {
                container.append('<div class="text-center text-danger p-3">ไม่พบองค์กรที่ท่านสังกัด กรุณาติดต่อเจ้าหน้าที่</div>');
                return;
            }

            organizations.forEach(function (org) {
                // org.pref_id จะได้มาจากการ Join ตารางใน Controller
                let buttonHtml = `
                        <button type="button" 
                            onclick="selectOrganization(${org.pref_id}, ${org.id}, '${org.org_name}')" 
                            class="btn btn-info btn-block mb-3">
                            <div>
                                <i class="fa fa-circle-user"></i>
                                <span class="fw-bold">${org.org_type}${org.org_name}</span>
                            </div>
                            <i class="fa-solid fa-circle-arrow-right"></i>
                        </button>
                    `;
                container.append(buttonHtml);
            });
        }

        // 2. ปรับฟังก์ชัน selectOrganization ให้รับค่า prefId และทำการ Redirect ไปยังตำแหน่งใหม่
        function selectOrganization(userId, orgId, orgName) {
            Swal.fire({
                title: 'ยืนยันการเข้าใช้งาน',
                text: `คุณต้องการเข้าใช้งานระบบของ "${orgName}" ใช่หรือไม่?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#06C755',
                confirmButtonText: 'ตกลง เข้าสู่ระบบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งข้อมูลไปเซ็ต Session หลังบ้าน
                    $.post("{{ url('api/line/set-session-org') }}", {
                        _token: "{{ csrf_token() }}",
                        org_id: orgId,
                        userId: userId
                    }).done(function (data) {
                        // 🚀 พารีไดเร็กต์ไปยังหน้า Dashboard พร้อมส่งค่า pref_id และ org_id ผ่าน URL ตามที่คุณต้องการ
                        window.location.href = "{{ url('/line/dashboard') }}/" + userId + "/" + orgId;
                    }).fail(function () {
                        Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกเซสชันได้', 'error');
                    });
                }
            });
        }

        function newUser() {
            // 1. นำเบอร์โทรศัพท์ที่ผู้ใช้กรอกตรวจไว้ในตอนแรก ไปเก็บพักไว้ในตัวแปรส่วนกลางก่อน (เนื่องจากช่อง #phone ยังไม่แสดง)
            let prefilledPhone = $('#verify_phone_input').val() || '';
            if (prefilledPhone.length === 10) {
                savedPhoneInput = prefilledPhone;
            }

            // 2. สั่งเปิดสเต็ปฟอร์มลงทะเบียนสมาชิกใหม่ (กล่อง #step-register-notice จะแสดงผล)
            goToStep('register-notice');

            // 3. เปิดตัว wrapper ของฟอร์มสมัครสมาชิก
            $('#new_user_form').removeClass('d-none');

            // 4. ฝังค่า Line User ID แฝงเข้าฟอร์มเดิมของคุณไว้รอเลย
            if ($('#registerForm input[name="line_id"]').length === 0) {
                $('#registerForm').append(`<input type="hidden" name="line_id" value="${currentLineUserId}">`);
            } else {
                $('input[name="line_id"]').val(currentLineUserId);
            }
        }


        // เรียกให้ทำงานทันทีเมื่อหน้าเว็บโหลดเสร็จ
        $(document).ready(function () {
            initializeLiff();
        });

        $('#phone_search_btn').on('click', async function () {
            let firstname = $('#firstname').val();
            let lastname = $('#lastname').val();
            let phone = $('#phone').val();
            let res = true;

            // Validation
            if (firstname === "") { $('#firstname').addClass('border border-danger'); res = false; }
            if (lastname === "") { $('#lastname').addClass('border border-danger'); res = false; }
            if (phone === "") { $('#phone').addClass('border border-danger'); res = false; }

            if (res === false) return false;

            // การจัดการรูปภาพแบบปลอดภัย (Defensive Coding)
            let line_user_image = "";
            if (typeof profile !== 'undefined' && profile.pictureUrl) {
                // ใช้การดึงเฉพาะรหัสไฟล์ท้าย URL
                line_user_image = profile.pictureUrl.split('/').pop();
            }

            // เตรียม Data ส่งไป API
            let payload = {
                phoneNum: phone,
                province_id: province_id_text.value,
                district_id: district_id_text.value,
                tambon_id: tambon_id_text.value,
                org_id: org_id_text.value,
                line_user_id: (typeof profile !== 'undefined') ? profile.userId : null,
                displayName: (typeof profile !== 'undefined') ? profile.displayName : '',
                line_user_image: line_user_image,
                firstname: firstname,
                lastname: lastname,
                address: $('#address').val(),
                zone_id: $('#zone_id').val(),
                subzone_id: $('#subzone_id').val(),
            };

            console.log("Sending data:", payload);

            try {
                const response = await $.post("{{ url('/line/user_line_register') }}", {
                    _token: "{{ csrf_token() }}",
                    payload: payload,
                });
                console.log('res', response)

                if (response.res == 1) {
                    window.location.href = `/line/dashboard/${response.user_id}/${org_id_text.value}`;
                } else {
                    // กรณีอื่นๆ
                }
            } catch (error) {
                console.error("API Error:", error);
                alert("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
            }
        });



    </script>

    <script>
        // 1. รับข้อมูล Org จาก Blade (PHP)
        let allOrganizations = @json($orgs);

        // Cache Data สำหรับ Modal
        let orgListCache = [];     // เก็บรายชื่อหน่วยงานที่กรองแล้ว
        let zoneListCache = [];
        let subzoneListCache = [];

        // ตัวบอกสถานะ Modal ว่ากำลังเลือกอะไร
        let currentModalType = ''; // 'org', 'zone', 'subzone'

        $(document).ready(function () {
            // $('#user_id').select2({ width: '100%' }); // อันนี้ค้นหาชื่อเก่า เก็บ select2 ไว้ หรือจะแก้เป็น modal ก็ได้

            // เริ่มต้น: กรองหน่วยงานแบบ มหาวิทยาลัย รอไว้
            filterOrgList('uni');
        });

        // ==========================================
        // 1. จัดการเลือกประเภทหน่วยงาน (Radio Change)
        // ==========================================
        $('input[name="org_type_selector"]').change(function () {
            let type = $(this).val();
            let org_type_id = 1;// default  เทศบาล
            let org_label = 'เลือกเทศบาล/อบต. ที่ท่านสังกัด'
            if (type === 'uni') {
                org_type_id = 6;
                org_label = 'เลือกสถานศึกษา ที่ท่านสังกัด'
            } else if (type === 'hospital') {
                org_type_id = 5;
                org_label = 'เลือกโรงพยาบาล ที่ท่านสังกัด'
            }
            else if (type === 'school') {
                org_type_id = 4;
                org_label = 'เลือกสถานศึกษา ที่ท่านสังกัด'
            }
            $('#org_label').html(org_label)
            resetForm(); // ล้างค่าเก่าออก
            $.get(`/api/line/get_org_lists/${org_type_id}`).done(function (data) {
                allOrganizations = data.orgs
                console.log('d', data.orgs)
                filterOrgList(type); // กรองข้อมูลใหม่ใส่ Cache
            }).fail(function () {
                Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกเซสชันได้', 'error');
            });

        });

        // ฟังก์ชันกรองข้อมูล (เก็บลง Array แทนการสร้าง Option)
        function filterOrgList(type) {

            orgListCache = []; // Reset Cache
            // Loop ข้อมูลดิบ แล้วเลือกเฉพาะที่ตรงประเภท
            for (const [key, org] of Object.entries(allOrganizations)) {
                let isUni = (org.org_type.code === 'ม.');
                let isHospital = (org.org_type.code === 'รพ.' || org.org_type.code === 'รพสต.');
                let isSchool = (org.org_type.code === 'รร.');

                if ((type === 'uni' && isUni) || (type === 'general' && !isUni) || (type === 'hospital' && (isHospital))
                    || (type === 'school' && (isSchool))
                ) {
                    // สร้าง Object สำหรับ Modal
                    orgListCache.push({
                        id: key, // key คือ ID ใน object json
                        name: `${org.org_type.name}${org.org_name}`, // ชื่อที่จะโชว์ตัวหนา
                        desc: `อ.${org.districts.district_name} จ.${org.provinces.province_name}`, // รายละเอียดตัวเล็ก
                        fullData: org // เก็บ object เต็มไว้ใช้ตอนเลือก
                    });
                }
            }

            // เคลียร์ค่าที่แสดงอยู่
            $('#org_display').val('').attr('placeholder', 'แตะเพื่อเลือกหน่วยงาน...');
        }

        // ==========================================
        // 2. จัดการ Modal (รวม Org, Zone, Subzone)
        // ==========================================

        // 2.1 เปิด Modal เลือก "หน่วยงาน"
        $('#org_display').click(function () {
            currentModalType = 'org';
            $('#modalTitle').text('เลือกหน่วยงาน');
            renderModalList(orgListCache); // ส่งข้อมูลที่กรองแล้วไปแสดง
            new bootstrap.Modal(document.getElementById('selectionModal')).show();
        });

        // 2.2 เปิด Modal เลือก "คณะ/โซน"
        $('#zone_display').click(function () {
            if (!$('#org_id').val()) { alert('กรุณาเลือกหน่วยงานก่อน'); return; }

            currentModalType = 'zone';
            $('#modalTitle').text($('#zone_label').text());
            renderModalList(zoneListCache);
            new bootstrap.Modal(document.getElementById('selectionModal')).show();
        });

        // 2.3 เปิด Modal เลือก "สาขา/อาคาร"
        $('#subzone_display').click(function () {
            if ($(this).is(':disabled')) return;

            currentModalType = 'subzone';
            $('#modalTitle').text($('#subzone_label').text());
            renderModalList(subzoneListCache);
            new bootstrap.Modal(document.getElementById('selectionModal')).show();
        });

        // ==========================================
        // 3. ฟังก์ชัน Render & Select
        // ==========================================

        // สร้าง List ใน Modal (ใช้ได้กับทุกประเภท)
        function renderModalList(items) {
            let html = '';
            $('#modalSearch').val('');

            if (!items || items.length === 0) {
                html = '<div class="p-4 text-center text-muted">ไม่พบข้อมูล</div>';
            } else {
                items.forEach(item => {
                    // เช็คว่า data มาท่าไหน (Org มี field name/desc, Zone อาจมีแค่ zone_name)
                    let name = item.name || item.zone_name || item.subzone_name;
                    let desc = item.desc || item.location || '';
                    let id = item.id;

                    html += `
                                    <a href="#" class="list-group-item list-group-item-action py-3 select-item-btn"
                                       data-id="${id}" data-type="${currentModalType}">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-bold text-dark">${name}</div>
                                                ${desc ? `<small class="text-muted">${desc}</small>` : ''}
                                            </div>
                                            <i class="bi bi-chevron-right text-muted opacity-50"></i>
                                        </div>
                                    </a>`;
                });
            }
            $('#modalListContainer').html(html);
        }

        // เมื่อกดเลือกรายการใน Modal
        $(document).on('click', '.select-item-btn', function (e) {
            e.preventDefault();
            let id = $(this).data('id');
            let type = $(this).data('type');
            console.log('idd', id)

            // หา Object เต็มจาก Cache (เพื่อเอาข้อมูลอื่นมาใช้)
            // สำหรับ Org เราเก็บ fullData ไว้, สำหรับ Zone/Subzone อาจจะต้อง find
            let selectedItem = null;

            if (type === 'org') {
                selectedItem = orgListCache.find(x => x.id == id);
                console.log('selectedItem', selectedItem)
                if (selectedItem) handleOrgSelection(selectedItem.fullData, id);

            } else if (type === 'zone') {
                let name = $(this).find('.fw-bold').text(); // ดึงชื่อจาก HTML หรือจะหาจาก cache ก็ได้
                $('#zone_id').val(id);
                $('#zone_display').val(name);

                // Logic โหลด Subzone (สาขา)
                fetchSubzones(id);

            } else if (type === 'subzone') {
                let name = $(this).find('.fw-bold').text();
                $('#subzone_id').val(id);
                $('#subzone_display').val(name);
            }

            // ปิด Modal
            bootstrap.Modal.getInstance(document.getElementById('selectionModal')).hide();
        });

        // ==========================================
        // 4. Logic เฉพาะเมื่อเลือกหน่วยงาน (แยกออกมาให้ชัด)
        // ==========================================
        function handleOrgSelection(orgData, id) {
            // 1. Set ค่าพื้นฐาน
            console.log('orgData.org_type_name', orgData)
            $('#org_id').val(orgData.id); // ใช้ ID จริงจาก DB
            $('#org_display').val(`${orgData.org_type.name}${orgData.org_name}`);

            // 2. Set Hidden Location
            $('#province_id').val(orgData.org_province_id_fk);
            $('#district_id').val(orgData.org_district_id_fk);
            $('#tambon_id').val(orgData.org_tambon_id_fk);

            // 3. Set Display Location (สำหรับ อบต.)
            $('#show_province').val('จ. ' + orgData.provinces.province_name);
            $('#show_district').val('อ. ' + orgData.districts.district_name);
            $('#show_tambon').val('ต. ' + orgData.tambons.tambon_name);

            // 4. Check Type (ม. หรือ อบต.)
            let isUni = (orgData.org_type.code === 'ม.');
            let isHospital = (orgData.org_type.code === 'รพ.' || orgData.org_type.code === 'รพสต.');

            if (isUni) {
                // === มหาวิทยาลัย ===
                $('#location_info_display').addClass('d-none');
                $('#address_div').addClass('d-none');
                $('#address').val('-');

                $('#zone_label').text('คณะ / หน่วยงาน');
                $('#subzone_label').text('สาขาวิชา / ภาควิชา');
                $('#zone_display').attr('placeholder', 'แตะเพื่อเลือกคณะ...');
                $('#subzone_display').attr('placeholder', 'แตะเพื่อเลือกสาขา...');
            } else if (isHospital) {
                $('#location_info_display').addClass('d-none');
                $('#address_div').addClass('d-none');
                $('#address').val('-');

                $('#zone_label').text('แผนก ');
                $('#subzone_label').text('หน่วย');
                $('#zone_display').attr('placeholder', 'แตะเพื่อเลือกแผนก...');
                $('#subzone_display').attr('placeholder', 'แตะเพื่อเลือกหน่วย...');
            }
            else {
                // === อบต. ===
                $('#location_info_display').removeClass('d-none');
                $('#address_div').removeClass('d-none');
                if ($('#address').val() === '-') $('#address').val('');

                $('#zone_label').text('หมู่ที่ / โซน');
                $('#subzone_label').text('ซอย / อาคาร');
                $('#zone_display').attr('placeholder', 'แตะเพื่อเลือกหมู่...');
                $('#subzone_display').attr('placeholder', 'แตะเพื่อเลือกซอย...');
            }

            // 5. Reset & Load Zones
            $('#form_details').removeClass('d-none');
            $('#zone_id').val('');
            $('#zone_display').val('');
            $('#subzone_id').val('');
            $('#subzone_display').val('').prop('disabled', true);
            $('#location_info_display').removeClass('d-none');


            // AJAX Get Zones
            $.get(`/api/line/getzones/${orgData.tambons.id}`).done(function (data) {
                console.log('z', data)
                zoneListCache = data.zones || [];
            });
        }

        // ฟังก์ชันย่อย: โหลด Subzone
        function fetchSubzones(zoneId) {
            $('#subzone_id').val('');
            $('#subzone_display').val('กำลังโหลด...').prop('disabled', true);

            // ** อย่าลืมแก้ URL ให้ตรงกับ Route ของคุณ **
            $.get(`/api/subzone/get_subzones_in_zone/${zoneId}`).done(function (data) {
                subzoneListCache = data || [];
                if (subzoneListCache.length > 0) {
                    $('#subzone_display').val('').prop('disabled', false).attr('placeholder', 'แตะเพื่อเลือก...');
                } else {
                    $('#subzone_display').val('-').prop('disabled', true);
                }
            }).fail(function () {
                $('#subzone_display').val('-').prop('disabled', true);
            });
        }

        // Reset Form Helper
        function resetForm() {
            $('#form_details').addClass('d-none');
            $('#org_id').val('');
            $('#org_display').val('');
            $('#zone_id').val('');
            $('#subzone_id').val('');
            zoneListCache = [];
            subzoneListCache = [];
        }

        // Search ใน Modal
        $('#modalSearch').on('keyup', function () {
            let value = $(this).val().toLowerCase();
            $("#modalListContainer a").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    </script>
@endsection