function BluethoothConnectedModal(from) {

    
    if (!printCharacteristic) {
        if(from === 'recycle'){
            document.getElementById('customCartModal').remove();
        }
        

        Swal.fire({
            title: 'ยังไม่ได้เชื่อมต่อเครื่องพิมพ์!',
            text: 'กรุณากดเชื่อมต่อเครื่องพิมพ์บลูทูธก่อนทำการบันทึกและพิมพ์บิล',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '🔄 เชื่อมต่อเครื่องพิมพ์ตอนนี้',
            cancelButtonText: 'ยกเลิก'
        }).then(async (result) => {
            if (result.isConfirmed) {
                // เรียกใช้ฟังก์ชันเชื่อมต่อ Bluetooth โดยตรงโดยไม่ต้องเปลี่ยนหน้า
                try {
                    await connectToPrinter();
                } catch (err) {
                    console.error("Connection error:", err);
                }
            }
        });
        return
    }
    Swal.fire({
        title: 'กำลังบันทึกข้อมูล...',
        text: 'กรุณารอสักครู่ ห้ามปิดหน้าต่างนี้',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}