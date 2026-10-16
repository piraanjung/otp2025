@extends('layouts.welfare')
@section('title_page', 'รายการรอจัดเก็บเงินสมทบ')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm rounded-4">

                    <!-- Card Header -->
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h5 class="fw-bold mb-1"><i
                                        class="fas fa-hand-holding-usd text-warning me-2"></i>รายการรอจัดเก็บเงินสมทบจากสมาชิก
                                </h5>
                                <p class="text-sm text-muted mb-0">รายชื่อสมาชิกที่ยอดเงินในบัญชีไม่พอหักอัตโนมัติ
                                    เจ้าหน้าที่สามารถตรวจสอบและบันทึกรับเงินสด/โอนเงินได้ที่นี่</p>
                            </div>
                        </div>

                        <!-- ================= ส่วน Filter & ค้นหา ================= -->
                        <form action="{{ route('admin.welfare.collections.index') }}" method="GET"
                            class="mt-4 bg-light p-3 rounded-4">
                            <div class="row g-2 align-items-center">

                                <!-- เลือกโซน / โซนการจัดเก็บ -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-xs text-secondary mb-1"><i
                                            class="fas fa-map-marker-alt me-1"></i>เลือกโซน / เขตจัดเก็บ</label>
                                    <select name="zone_id" class="form-select bg-white border-0 shadow-sm rounded-3"
                                        onchange="this.form.submit()">
                                        <option value="">-- แสดงทุกโซน --</option>
                                        @foreach($zones as $z)
                                            <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>
                                                {{ $z->zone_name ?? 'โซนที่ ' . $z->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- ช่องค้นหาชื่อ/เบอร์โทร -->
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-xs text-secondary mb-1"><i
                                            class="fas fa-search me-1"></i>ค้นหาชื่อ, นามสกุล หรือเบอร์โทร</label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <input type="text" name="search" class="form-control border-0 bg-white"
                                            placeholder="พิมพ์ชื่อ, นามสกุล หรือเบอร์โทร..."
                                            value="{{ request('search') }}">
                                        <button class="btn btn-primary mb-0 px-3" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- ปุ่มล้าง Filter -->
                                <div class="col-md-3 d-flex align-items-end pt-3 pt-md-0">
                                    @if(request('search') || request('zone_id'))
                                        <a href="{{ route('admin.welfare.collections.index') }}"
                                            class="btn btn-outline-secondary w-100 mb-0 rounded-3 shadow-sm">
                                            <i class="fas fa-undo me-1"></i> ล้างการค้นหา
                                        </a>
                                    @endif
                                </div>

                            </div>
                        </form>
                        <!-- ======================================================= -->

                    </div>

                    <div class="card-body px-0 pt-0">
                        @if(session('success'))
                            <div class="alert alert-success mx-4 rounded-3">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger mx-4 rounded-3">{{ session('error') }}</div>
                        @endif

                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr
                                        class="bg-light text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        <th class="ps-4">สมาชิก / เบอร์โทร / โซนที่อยู่</th>
                                        <th>เคสเสียชีวิตที่เกี่ยวข้อง</th>
                                        <th>ยอดเงินที่ต้องเก็บ (บาท)</th>
                                        <th>สถานะบัญชีปัจจุบัน</th>
                                        <th>หมายเหตุระบบ</th>
                                        <th class="text-end pe-4">จัดการ / บันทึกรับเงิน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingContributions as $item)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-0 text-sm fw-bold">
                                                        {{ ($item->user->firstname ?? '') . ' ' . ($item->user->lastname ?? '') ?: 'ไม่ระบุชื่อ' }}
                                                    </h6>
                                                    <span class="text-xs text-muted"><i
                                                            class="fas fa-phone-alt me-1"></i>{{ $item->user->phone ?? '-' }}</span>
                                                    <span class="text-xs text-muted">
                                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                                        {{ $item->user->address ?? '-' }}
                                                        <strong
                                                            class="text-primary">({{ $item->user->user_zone->zone_name ?? 'ไม่ระบุโซน' }})</strong>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-sm font-weight-bold text-dark">
                                                    {{ $item->payout->deceased_name ?? 'ไม่ระบุเคส' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient-danger fs-6 px-3 py-2 shadow-sm">
                                                    ฿ {{ number_format($item->amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-sm fw-bold text-warning">
                                                    ฿ {{ number_format($item->user->wallet_balance ?? 0, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-xs text-muted text-wrap"
                                                    style="max-width: 250px; display: inline-block;">
                                                    {{ $item->note }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <!-- ปุ่มเปิด Modal บันทึกรับเงิน -->
                                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm"
                                                    data-bs-toggle="modal" data-bs-target="#collectModal{{ $item->id }}">
                                                    <i class="fas fa-check-circle me-1"></i> บันทึกรับเงิน
                                                </button>

                                                <!-- Modal บันทึกรับเงิน -->
                                                <!-- ปรับปรุงส่วนฟอร์มใน Modal (วางทับ Modal เดิม) -->
                                                <div class="modal fade text-start" id="collectModal{{ $item->id }}"
                                                    tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content rounded-4 border-0 shadow">
                                                            <form
                                                                action="{{ route('admin.welfare.collections.store', $item->id) }}"
                                                                method="POST" enctype="multipart/form-data" class="collect-form"
                                                                data-id="{{ $item->id }}">
                                                                @csrf
                                                                <div class="modal-header border-0 pb-0">
                                                                    <h5 class="fw-bold">บันทึกรับเงินสมทบ:
                                                                        {{ ($item->user->firstname ?? '') . ' ' . ($item->user->lastname ?? '') }}
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3 bg-light p-3 rounded-3">
                                                                        <p class="mb-1 text-sm text-muted">ยอดที่ต้องจัดเก็บ:
                                                                            <strong class="text-danger fs-5">฿
                                                                                {{ number_format($item->amount, 2) }}</strong>
                                                                        </p>
                                                                        <p class="mb-0 text-xs text-muted">เคส:
                                                                            {{ $item->payout->deceased_name ?? '-' }}</p>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-bold small">ช่องทางการชำระเงิน</label>
                                                                        <select name="payment_method"
                                                                            class="form-select bg-light border-0 rounded-3"
                                                                            required>
                                                                            <option value="cash">เงินสด (รับหน้างาน/ที่สำนักงาน)
                                                                            </option>
                                                                            <option value="transfer">โอนเงินเข้าบัญชี</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-bold small">แนบสลิปหลักฐานการโอน
                                                                            (ถ้ามี)</label>
                                                                        <input type="file" name="slip_img"
                                                                            class="form-control bg-light border-0 rounded-3"
                                                                            accept="image/*">
                                                                    </div>

                                                                    <!-- ================= ตัวเลือกการพิมพ์ใบเสร็จ ================= -->
                                                                    <div class="mb-3 bg-white p-3 border rounded-3">
                                                                        <label
                                                                            class="form-label fw-bold small d-block mb-2 text-primary">
                                                                            <i class="fas fa-print me-1"></i>
                                                                            ตัวเลือกการพิมพ์ใบเสร็จ
                                                                        </label>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="print_option"
                                                                                id="print_none_{{ $item->id }}" value="none"
                                                                                checked>
                                                                            <label class="form-check-label text-sm fw-bold"
                                                                                for="print_none_{{ $item->id }}">
                                                                                บันทึกข้อมูลอย่างเดียว
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="print_option"
                                                                                id="print_bt_{{ $item->id }}" value="bluetooth">
                                                                            <label
                                                                                class="form-check-label text-sm fw-bold text-success"
                                                                                for="print_bt_{{ $item->id }}">
                                                                                <i class="fab fa-bluetooth-b me-1"></i>
                                                                                พิมพ์ผ่าน Bluetooth
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <!-- ========================================================== -->

                                                                </div>
                                                                <div class="modal-footer border-0 pt-0">
                                                                    <button type="button"
                                                                        class="btn btn-light rounded-pill px-4"
                                                                        data-bs-dismiss="modal">ยกเลิก</button>
                                                                    <button type="submit"
                                                                        class="btn btn-success rounded-pill px-4 shadow-sm btn-submit-collect">
                                                                        <i class="fas fa-save me-1"></i> ยืนยันการรับเงิน
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-check-circle fa-3x text-success mb-3 opacity-50"></i>
                                                <p class="mb-0">ไม่พบรายการค้างชำระที่ตรงกับเงื่อนไขการค้นหา</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-4 pt-3">
                            {{ $pendingContributions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let bluetoothDevice = null;
    let bluetoothGattServer = null;
    let printCharacteristic = null;

    // Service & Characteristic UUIDs ยอดนิยมของเครื่องพิมพ์ความร้อน ESC/POS
    const PRINTER_SERVICE_UUIDS = [
        '000018f0-0000-1000-8000-00805f9b34fb', 
        'e7810a71-73ae-4d2d-a13f-782d9b203d21',
        '0000ff00-0000-1000-8000-00805f9b34fb'
    ];

    /**
     * ฟังก์ชันเชื่อมต่อเครื่องพิมพ์ บลูทูธ แบบจดจำการเชื่อมต่อเดิม (Auto-Reconnect)
     */
    async function getOrConnectBluetoothPrinter() {
        try {
            // 1. ถ้าเชื่อมต่อค้างไว้อยู่แล้ว ให้ใช้งานได้ทันที
            if (bluetoothGattServer && bluetoothGattServer.connected && printCharacteristic) {
                return true;
            }

            // 2. ลองค้นหาอุปกรณ์ที่เคยได้รับอนุญาตไว้แล้ว (getDevices API) เพื่อไม่ต้องกดเลือกใหม่
            if (navigator.bluetooth && navigator.bluetooth.getDevices) {
                const previousDevices = await navigator.bluetooth.getDevices();
                const lastDeviceId = localStorage.getItem('kp_bt_printer_id');

                if (previousDevices.length > 0) {
                    // ค้นหาเครื่องพิมพ์เดิมที่เคยจำไว้ หรือเลือกตัวแรกที่เคย paired ไว้
                    bluetoothDevice = previousDevices.find(d => d.id === lastDeviceId) || previousDevices[0];
                    console.log('พบเครื่องพิมพ์ที่เคยเชื่อมต่อไว้:', bluetoothDevice.name);
                }
            }

            // 3. ถ้ายังไม่เคยเชื่อมต่อเลย หรือหาไม่เจอ ให้เปิดป็อปอัปเลือกอุปกรณ์ (ครั้งแรกครั้งเดียว)
            if (!bluetoothDevice) {
                console.log('สแกนหาเครื่องพิมพ์บลูทูธใหม่...');
                bluetoothDevice = await navigator.bluetooth.requestDevice({
                    acceptAllDevices: true,
                    optionalServices: PRINTER_SERVICE_UUIDS
                });
                
                // บันทึก ID ของเครื่องพิมพ์ลง localStorage เพื่อใช้ซ้ำครั้งถัดไป
                if (bluetoothDevice && bluetoothDevice.id) {
                    localStorage.setItem('kp_bt_printer_id', bluetoothDevice.id);
                }
            }

            // 4. สั่ง Connect GATT Server
            bluetoothGattServer = await bluetoothDevice.gatt.connect();

            // 5. ค้นหา Characteristic สำหรับสั่งพิมพ์
            const services = await bluetoothGattServer.getPrimaryServices();
            for (const service of services) {
                const characteristics = await service.getCharacteristics();
                if (characteristics.length > 0) {
                    printCharacteristic = characteristics[0];
                    break;
                }
            }

            if (!printCharacteristic) {
                alert('เชื่อมต่ออุปกรณ์ได้ แต่ไม่พบช่องทางส่งข้อมูลพิมพ์ (Characteristic)');
                return false;
            }

            return true;

        } catch (error) {
            console.error('Bluetooth Connection Error:', error);
            // ถ้าระบบจำตัวเก่าแล้วเชื่อมต่อไม่สำเร็จ ให้ล้างค่าจำ เพื่อให้ครั้งถัดไปกดเลือกรอบใหม่ได้
            localStorage.removeItem('kp_bt_printer_id');
            bluetoothDevice = null;
            
            alert('การเชื่อมต่อเครื่องพิมพ์บลูทูธล้มเหลว: ' + error.message);
            return false;
        }
    }

    /**
     * ฟังก์ชันสร้างคำสั่ง ESC/POS พิมพ์ใบเสร็จ
     */
    function generateReceiptData(memberName, amount, deceasedName, receiptNo) {
        let encoder = new TextEncoder();
        let commands = [];

        // Reset
        commands.push(new Uint8Array([0x1B, 0x40])); 
        // Align Center
        commands.push(new Uint8Array([0x1B, 0x61, 0x01])); 
        
        let headerText = "ใบเสร็จรับเงินสวัสดิการ\nกองทุนฌาปนกิจชุมชน\n--------------------------------\n";
        commands.push(encoder.encode(headerText));

        // Align Left
        commands.push(new Uint8Array([0x1B, 0x61, 0x00])); 
        let bodyText = `เลขที่: ${receiptNo}\n` +
                       `วันที่: ${new Date().toLocaleDateString('th-TH')}\n` +
                       `สมาชิก: ${memberName}\n` +
                       `เคสผู้เสียชีวิต: ${deceasedName}\n` +
                       `--------------------------------\n` +
                       `จำนวนเงินที่ชำระ: ${amount} บาท\n` +
                       `--------------------------------\n`;
        commands.push(encoder.encode(bodyText));

        // Align Center
        commands.push(new Uint8Array([0x1B, 0x61, 0x01])); 
        let footerText = "ขอบคุณที่ร่วมสร้างสวัสดิการชุมชน\n\n\n\n";
        commands.push(encoder.encode(footerText));

        // Cut Paper
        commands.push(new Uint8Array([0x1D, 0x56, 0x41, 0x10])); 

        let totalLength = commands.reduce((acc, curr) => acc + curr.length, 0);
        let result = new Uint8Array(totalLength);
        let offset = 0;
        for (let cmd of commands) {
            result.set(cmd, offset);
            offset += cmd.length;
        }
        return result;
    }

    // อีเวนต์ตอนกดบันทึกการรับเงิน
    document.querySelectorAll('.collect-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            const printOption = this.querySelector('input[name="print_option"]:checked').value;

            if (printOption === 'bluetooth') {
                e.preventDefault(); // ชะลอการยื่นฟอร์มเพื่อสั่งพิมพ์ก่อน

                const submitBtn = this.querySelector('.btn-submit-collect');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> กำลังเชื่อมต่อ & พิมพ์...';

                // ดึงการเชื่อมต่อเดิมหรือเชื่อมต่ออัตโนมัติ
                const isReady = await getOrConnectBluetoothPrinter();

                if (isReady && printCharacteristic) {
                    try {
                        const memberName = this.closest('.modal-content').querySelector('h5').innerText.replace('บันทึกรับเงินสมทบ: ', '');
                        const amount = this.closest('.modal-content').querySelector('.text-danger').innerText.replace('฿ ', '');
                        const deceasedName = this.closest('.modal-content').querySelector('.text-xs.text-muted').innerText.replace('เคส: ', '');
                        const receiptNo = 'REC-' + Date.now().toString().slice(-6);

                        const printData = generateReceiptData(memberName, amount, deceasedName, receiptNo);
                        await printCharacteristic.writeValue(printData);

                    } catch (err) {
                        console.error('Print Transmission Error:', err);
                        alert('ส่งข้อมูลพิมพ์ไม่สำเร็จ: ' + err.message);
                    }
                }

                // บันทึกฟอร์มลงฐานข้อมูลต่อ
                this.submit();
            }
        });
    });
</script>
@endsection