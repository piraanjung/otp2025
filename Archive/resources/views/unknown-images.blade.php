<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการรูปภาพ (Retrain AI Data)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8 font-sans">

    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><i class="fas fa-robot text-orange-500 mr-2"></i> ภาพที่ AI ไม่รู้จัก (Low Confidence)</h1>
                <p class="text-gray-500 text-sm mt-1">ดาวน์โหลดภาพเหล่านี้ไปอัปโหลดเข้า Teachable Machine เพื่อสอน AI ให้เก่งขึ้น</p>
            </div>
            <div class="space-x-3">
                <button onclick="selectAll()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-check-square"></i> เลือกทั้งหมด
                </button>
                <button onclick="deleteSelected()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-bold shadow-md transition">
                    <i class="fas fa-trash-alt"></i> ลบ (<span id="countDelete">0</span>)
                </button>
                <button onclick="downloadSelected()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-bold shadow-md transition">
                    <i class="fas fa-download"></i> โหลด (<span id="count">0</span>)
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @forelse($images as $img)
            <div onclick="toggleCardCheckbox(this)" class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition relative group cursor-pointer">

                    <div class="absolute top-2 left-2 z-10">
                        <input type="checkbox" class="img-checkbox w-5 h-5 accent-orange-500 pointer-events-none"
                               value="{{ $img['url'] }}"
                               data-filename="{{ $img['filename'] }}">
                    </div>

                    </div>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition relative group">

                    <div class="absolute top-2 left-2 z-10">
                        <input type="checkbox" class="img-checkbox w-5 h-5 accent-orange-500 cursor-pointer"
                               value="{{ $img['url'] }}"
                               data-filename="{{ $img['filename'] }}"
                               onchange="updateCount()">
                    </div>

                    <div class="h-40 overflow-hidden bg-gray-200">
                        <img src="{{ $img['url'] }}" alt="Unknown" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                    </div>

                    <div class="p-3">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold px-2 py-1 bg-gray-100 text-gray-600 rounded">{{ $img['class'] }}</span>
                            <span class="text-xs font-bold {{ $img['prob'] < 50 ? 'text-red-500' : 'text-yellow-500' }}">
                                {{ $img['prob'] }}%
                            </span>
                        </div>
                        <div class="text-[10px] text-gray-400 mt-2"><i class="far fa-clock"></i> {{ $img['date'] }}</div>

                        <a href="{{ $img['url'] }}" download="{{ $img['filename'] }}" class="mt-3 block text-center w-full bg-blue-50 hover:bg-blue-100 text-blue-600 text-sm py-1.5 rounded transition">
                            โหลดรูปนี้
                        </a>
                    </div>

                </div>
            @empty
                <div class="col-span-full bg-white p-12 text-center rounded-xl shadow-sm">
                    <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">ยังไม่มีรูปภาพที่ AI สแกนไม่ผ่านครับ เยี่ยมมาก!</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
       function updateCount() {
            const checkboxes = document.querySelectorAll('.img-checkbox:checked');
            document.getElementById('count').innerText = checkboxes.length;
            document.getElementById('countDelete').innerText = checkboxes.length;
        }

        // ฟังก์ชันใหม่: คลิกทั้งการ์ดเพื่อติ๊กถูก
        function toggleCardCheckbox(cardElement) {
            const checkbox = cardElement.querySelector('.img-checkbox');
            checkbox.checked = !checkbox.checked;

            // เปลี่ยนสีการ์ดนิดหน่อยให้รู้ว่าเลือกแล้ว
            if(checkbox.checked) {
                cardElement.classList.add('ring-4', 'ring-orange-400');
            } else {
                cardElement.classList.remove('ring-4', 'ring-orange-400');
            }
            updateCount();
        }

        function selectAll() {
            const checkboxes = document.querySelectorAll('.img-checkbox');
            let allChecked = true;
            checkboxes.forEach(cb => { if(!cb.checked) allChecked = false; });

            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
                // อัปเดตกรอบสีของการ์ดด้วย
                const card = cb.closest('.group');
                if(cb.checked) card.classList.add('ring-4', 'ring-orange-400');
                else card.classList.remove('ring-4', 'ring-orange-400');
            });
            updateCount();
        }

        // เตรียมฟังก์ชันลบ
        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.img-checkbox:checked');
            if (checkboxes.length === 0) return alert('กรุณาเลือกรูปภาพก่อนครับ');

            if(confirm(`ต้องการลบรูปภาพ ${checkboxes.length} รูป ใช่หรือไม่?`)) {
                // TODO: สร้าง Array ของ filename แล้วยิง Fetch API ไปที่ Laravel Controller เพื่อสั่ง File::delete()
                alert('ส่ง API ไปลบที่เซิร์ฟเวอร์...');
            }
        }
    </script>
</body>
</html>
