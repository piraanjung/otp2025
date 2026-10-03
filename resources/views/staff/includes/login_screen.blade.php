

    <div class="card neu-card" id="loginScreen">
        <div class="card-body">
            
            <!-- รูปโปรไฟล์ /โลโก้ -->
            <div class="text-center mb-4">
                <div class="neu-profile">
                    <!-- แทนที่ด้วยรูปภาพของคุณ -->
                    <img src="{{ asset('logo/ko_envsogo.png') }}" alt="Profile">
                </div>
            </div>

            <form id="loginForm">
                <!-- ช่อง Username -->
                <div class="mb-3">
                    <div class="neu-input-group">
                        <span class="input-group-text bg-transparent border-0"><i class="fa-regular fa-user"></i></span>
                    <input type="text" id="username" placeholder=" " value="twman6" required>
                    </div>
                </div>

                <!-- ช่อง Password -->
                <div class="mb-4">
                    <div class="neu-input-group">
                        <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" placeholder=" " value="123456" required>
                    </div>
                </div>

                <!-- ปุ่ม Login -->
                <div class="d-grid mb-4">
                    <button type="submit" id="btnLogin" class="btn neu-button py-2">Login</button>
                </div>

                
            </form>

        </div>
    </div>
