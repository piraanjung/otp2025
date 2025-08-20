@extends('layouts.tabwater_staff_mobile')

@section('content')
    <div class="container-fluid py-4">
        <div class="card p-4">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">จดมิเตอร์</h1>
                <p class="text-sm text-gray-500 mt-1">
                    รหัสมิเตอร์: {{ $meter[0]->meter_id }}
                </p>
            </div>
            <div class="mt-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">ข้อมูลมิเตอร์ล่าสุด</h2>
                <div class="bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-gray-600">ค่าที่จดล่าสุด: {{ number_format($meter[0]->last_meter_recording, 2) }} หน่วย</p>
                </div>
            </div>

            <!-- ฟอร์มสำหรับจดมิเตอร์ใหม่ -->
            <form id="meter-reading-form" action="{{ route('tabwater.staff.mobile.process_meter_image') }}" method="POST" enctype="multipart/form-data" class="mt-8 flex flex-col space-y-4">
                @csrf
                <!-- ส่วนสำหรับอัปโหลดรูปภาพและแสดงตัวอย่าง -->
                <div class="form-group">
                    <label for="meter_image" class="form-label">รูปภาพมิเตอร์</label>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary flex-grow-1 me-2" id="takePhotoBtn">
                            ถ่ายภาพ
                        </button>
                        <button type="button" class="btn btn-secondary flex-grow-1" id="uploadFileBtn">
                            อัปโหลดไฟล์
                        </button>
                    </div>
                    <!-- input file สำหรับรับไฟล์รูปภาพ -->
                    <input type="file" id="image_upload" name="meter_image" accept="image/*" class="d-none">
                    <img id="image_preview" src="#" alt="ตัวอย่างรูปภาพ" class="img-fluid mt-2" style="display: none; max-width: 100%; height: auto;">
                </div>
                
                <div>
                    <label for="new_reading" class="form-label">ค่าที่จดได้ (หน่วย)</label>
                    <input type="number" id="new_reading" name="new_reading" required class="form-control" placeholder="กรอกค่ามิเตอร์ใหม่">
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                    บันทึกการจดมิเตอร์
                </button>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script>
    const takePhotoBtn = document.getElementById('takePhotoBtn');
    const uploadFileBtn = document.getElementById('uploadFileBtn');
    const imageUploadInput = document.getElementById('image_upload');
    const imagePreview = document.getElementById('image_preview');
    const newReadingInput = document.getElementById('new_reading');
    const meterReadingForm = document.getElementById('meter-reading-form');

    // ฟังก์ชันสำหรับบีบอัดรูปภาพให้ขนาดไม่เกิน 300KB
    function compressImage(file, targetSizeKB = 300) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    // ปรับขนาดรูปภาพ (Resizing)
                    const MAX_WIDTH = 1024;
                    const MAX_HEIGHT = 1024;
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > MAX_WIDTH) {
                            height *= MAX_WIDTH / width;
                            width = MAX_WIDTH;
                        }
                    } else {
                        if (height > MAX_HEIGHT) {
                            width *= MAX_HEIGHT / height;
                            height = MAX_HEIGHT;
                        }
                    }
                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    // บีบอัดคุณภาพ (Compression)
                    let quality = 0.9;
                    let blob;
                    
                    do {
                        canvas.toBlob((b) => {
                            blob = b;
                        }, 'image/jpeg', quality);
                        quality -= 0.1;
                    } while (blob && blob.size > targetSizeKB * 1024 && quality > 0);
                    
                    if (blob && blob.size > targetSizeKB * 1024) {
                        console.warn('Cannot compress image to target size. Final size:', (blob.size / 1024).toFixed(2), 'KB');
                    }
                    
                    resolve(blob || file); // resolve with the original file if blob is null
                };
                img.onerror = reject;
                img.src = e.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    // ฟังก์ชันสำหรับการส่งไฟล์ไปประมวลผลที่เซิร์ฟเวอร์
    async function processImageOnServer(file) {
        if (!file) return;

        newReadingInput.placeholder = 'กำลังอัปโหลดและประมวลผล...';
        newReadingInput.disabled = true;

        // บีบอัดรูปภาพก่อนอัปโหลด
        const compressedFile = await compressImage(file);

        const formData = new FormData();
        formData.append('meter_image', compressedFile);
        formData.append('_token', '{{ csrf_token() }}');

        try {
            const response = await fetch(meterReadingForm.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                newReadingInput.value = result.reading;
            } else {
                newReadingInput.value = '';
                console.log(result.message);
            }
        } catch (error) {
            console.log('Error uploading or processing image:', error);
            console.log('Error uploading or processing image:', error);
            // alert('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์ กรุณาลองใหม่อีกครั้ง');
        } finally {
            newReadingInput.disabled = false;
            newReadingInput.placeholder = 'กรอกค่ามิเตอร์ใหม่';
        }
    }
    
    // Event listener สำหรับปุ่ม "ถ่ายภาพ"
    takePhotoBtn.addEventListener('click', () => {
        imageUploadInput.setAttribute('capture', 'environment');
        imageUploadInput.click();
    });

    // Event listener สำหรับปุ่ม "อัปโหลดไฟล์"
    uploadFileBtn.addEventListener('click', () => {
        imageUploadInput.removeAttribute('capture');
        imageUploadInput.click();
    });

    // Event listener เมื่อเลือกรูปภาพ
    imageUploadInput.addEventListener('change', async (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
            
            await processImageOnServer(file);
        }
    });

</script>
@endsection