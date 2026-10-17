@extends('layouts.tabwater_staff_mobile')
@section('content') 
    <div class="py-4">
        <!-- Search and QR Code Scan section -->
        <div class="card p-3 mb-4">
            <div class="input-group">
                <input type="text" id="memberSearch" class="form-control" placeholder="ค้นหาสมาชิกด้วยชื่อ หรือ รหัสมิเตอร์">
                <button class="btn btn-outline-secondary" type="button" id="scanBtn">
                    <i class="fas fa-qrcode"></i>
                </button>
            </div>
        </div>
        
        @forelse ($members as $member)
            <div class="card mb-2 member-card" data-meter-id="{{ $member->meter_id }}">
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-8">
                      <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold meter-id">{{ $member->meter_id }}</p>
                        <h5 class="font-weight-bolder mb-0 member-name">
                          {{$member->user->prefix.$member->user->firstname." ".$member->user->lastname}}
                        </h5>
                          <div class="text-success text-sm font-weight-bolder">
                            {{ $member->meter_address}}
                        </div>
                      </div>
                    </div>
                    <div class="col-4 text-end">
                        <a href="{{route('tabwater.staff.mobile.meter_reading', $member->meter_id)}}">
                      <div class="icon icon-shape bg-primary shadow text-center border-radius-md">
                        <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
        @empty
            <p class="text-center text-gray-500">ไม่พบข้อมูลสมาชิก</p>
        @endforelse
        <p class="text-center text-gray-500 hidden" id="no-results-message">ไม่พบข้อมูลสมาชิกที่ค้นหา</p>
    </div>

    <!-- QR Code Scanner Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">สแกน QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reader" width="100%"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<!-- นำเข้าไลบรารี html5-qrcode -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    const searchInput = document.getElementById('memberSearch');
    const memberCards = document.querySelectorAll('.member-card');
    const noResultsMessage = document.getElementById('no-results-message');
    const scanBtn = document.getElementById('scanBtn');
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));

    // ฟังก์ชันสำหรับกรองข้อมูล
    function filterMembers(searchText, isQrCodeScan = false) {
        let foundResults = false;
        
        // ถ้า searchText เป็นค่าว่าง ให้แสดงบัตรสมาชิกทั้งหมด
        if (searchText.trim() === '') {
            memberCards.forEach(card => {
                card.style.display = 'block';
            });
            noResultsMessage.classList.add('hidden');
            return;
        }

        memberCards.forEach(card => {
            const memberName = card.querySelector('.member-name').textContent.toLowerCase();
            const meterId = card.querySelector('.meter-id').textContent.toLowerCase();
            
            // เพิ่มเงื่อนไขการกรอง
            if (isQrCodeScan) {
                // ถ้าเป็นการสแกน QR Code ให้กรองเฉพาะ meterId ที่ตรงกัน
                if (meterId === searchText) {
                    card.style.display = 'block';
                    foundResults = true;
                } else {
                    card.style.display = 'none';
                }
            } else {

                // ถ้าเป็นการพิมพ์ ให้กรองจากชื่อหรือ meterId ที่มีคำค้นหา
                if (memberName.includes(searchText) || meterId.includes(searchText)) {
                    card.style.display = 'block';
                    foundResults = true;
                } else {
                    card.style.display = 'none';
                }
            }
        });

        // แสดงข้อความเมื่อไม่พบผลลัพธ์
        if (foundResults) {
            noResultsMessage.classList.add('hidden');
        } else {
            noResultsMessage.classList.remove('hidden');
        }
    }

    // Event Listener สำหรับการค้นหาแบบ Keyup
    searchInput.addEventListener('keyup', (event) => {
        const searchText = event.target.value.toLowerCase();
        // ไม่ต้องส่ง isQrCodeScan เพราะเป็นการพิมพ์
        filterMembers(searchText);
    });

    // Event Listener สำหรับปุ่มสแกน
    scanBtn.addEventListener('click', () => {
        qrModal.show();
    });

    // เมื่อ Modal เปิด
    document.getElementById('qrModal').addEventListener('shown.bs.modal', event => {
        const html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        // เริ่มสแกน
        html5QrCode.start({ facingMode: "environment" }, config, 
            (decodedText, decodedResult) => {
                // เมื่อสแกนสำเร็จ
                if (decodedText) {
                    searchInput.value = decodedText; // ใส่ค่าที่สแกนได้ในช่องค้นหา
                    // เรียกใช้ฟังก์ชันกรองและส่งค่า isQrCodeScan = true
                    filterMembers(decodedText.toLowerCase(), true); 
                    qrModal.hide(); // ปิด Modal
                    html5QrCode.stop(); // หยุดการสแกน
                }
            },
            (errorMessage) => {
                // แสดงข้อผิดพลาดหากมี
                console.warn(`QR Code scanning failed: ${errorMessage}`);
            }
        ).catch((err) => {
            console.error(`Error starting QR code reader: ${err}`);
            alert('ไม่สามารถเปิดกล้องได้. โปรดตรวจสอบการอนุญาตใช้งานกล้อง.');
            qrModal.hide();
        });
    });

    // เมื่อ Modal ปิด ให้หยุดการสแกน
    document.getElementById('qrModal').addEventListener('hidden.bs.modal', event => {
        // Find if a scanner is running and stop it.
        const element = document.getElementById('reader');
        if (element.hasChildNodes()) {
            const html5QrCode = new Html5Qrcode("reader");
            html5QrCode.stop().then((ignore) => {
                // QR Code scanning stopped.
            }).catch((err) => {
                // Stop failed, handle it.
                console.error(`Failed to stop QR code reader: ${err}`);
            });
        }
    });

</script>
@endsection
