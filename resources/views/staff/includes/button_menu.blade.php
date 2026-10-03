<div id="button_menu">
<div class="custom-nav-container">
        <ul class="nav-list" id="bottomNav">
            <!-- วงกลมโค้งเว้าที่จะเลื่อนตำแหน่งอัตโนมัติด้วย JS -->
            <div class="nav-indicator" id="navIndicator"></div>

            <!-- เมนูที่ 1 -->
            <li class="nav-item active" onclick="switchTab(this, 0, 'home')">
                <div class="nav-icon"><i class="fa-solid fa-house"></i></div>
                <div class="nav-text">home</div>
            </li>

            <!-- เมนูที่ 2 -->
            <li class="nav-item" onclick="switchTab(this, 1, 'cards')">
                <div class="nav-icon"><i class="fa-regular fa-credit-card"></i></div>
                <div class="nav-text">ธนาคารขยะ</div>
            </li>

            <!-- เมนูที่ 3 -->
            <li class="nav-item" onclick="switchTab(this, 2, 'add-funds')">
                <div class="nav-icon"><i class="fa-solid fa-wallet"></i></div>
                <div class="nav-text">ถังขยะรายปี</div>
            </li>

            <!-- เมนูที่ 4 -->
            <li class="nav-item" onclick="switchTab(this, 3, 'transaction')">
                <div class="nav-icon"><i class="fa-solid fa-rotate"></i></div>
                <div class="nav-text">คลังพัสดุ</div>
            </li>

            <!-- เมนูที่ 5 -->
            <li class="nav-item" onclick="switchTab(this, 4, 'profile')">
                <div class="nav-icon"><i class="fa-regular fa-user"></i></div>
                <div class="nav-text">งานประปา</div>
            </li>
        </ul>

        <!-- ขีดล่างมือถือ -->
        <div class="iphone-home-indicator"></div>
    </div>

</div>

<script>
        function switchTab(element, index, targetScreen) {
            // ลบสถานะ active จากทุกเมนู
            const items = document.querySelectorAll('.nav-item');
            items.forEach(item => item.classList.remove('active'));

            // เพิ่มสถานะ active ให้เมนูที่ถูกคลิก
            element.classList.add('active');

            // คำนวณระยะการเลื่อนของรอยหยัก (Indicator) ตามสัดส่วนของเมนู
            const indicator = document.getElementById('navIndicator');
            const itemWidth = element.offsetWidth;
            const computedLeft = element.offsetLeft + (itemWidth / 2) - 35; // 35 คือครึ่งหนึ่งของความกว้าง 70px ของ indicator
            
            indicator.style.transform = `translateX(${computedLeft}px)`;

            // สามารถใส่ฟังก์ชันเปลี่ยนหน้าจอระบบของคุณตรงนี้ได้ เช่น navigateTo(targetScreen)
            console.log("Switched to tab: " + targetScreen);
        }

        // จัดตำแหน่ง Indicator ให้ตรงกับปุ่มแรกตอนโหลดหน้าเว็บครั้งแรก
        window.addEventListener('load', () => {
            const firstActive = document.querySelector('.nav-item.active');
            if(firstActive) {
                const indicator = document.getElementById('navIndicator');
                const itemWidth = firstActive.offsetWidth;
                const computedLeft = firstActive.offsetLeft + (itemWidth / 2) - 35;
                indicator.style.transform = `translateX(${computedLeft}px)`;
            }
        });

        // รองรับการปรับขนาดหน้าจอ (Responsive Resize)
        window.addEventListener('resize', () => {
            const activeItem = document.querySelector('.nav-item.active');
            if(activeItem) {
                const indicator = document.getElementById('navIndicator');
                const itemWidth = activeItem.offsetWidth;
                const computedLeft = activeItem.offsetLeft + (itemWidth / 2) - 35;
                indicator.style.transform = `translateX(${computedLeft}px)`;
            }
        });
    </script>