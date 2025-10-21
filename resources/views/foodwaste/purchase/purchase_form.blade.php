@can('access waste bank mobile')
    @php
        $layout = 'layouts.keptkaya_mobile';
    @endphp

@elsecan('access tabwater2')
    @php
        $layout = 'layouts.keptkaya_mobile';
     @endphp
@endcan
@extends($layout)


@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">รับซื้อขยะรีไซเคิล</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <h6>โปรดแก้ไขข้อผิดพลาดดังต่อไปนี้:</h6>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Member Details Card --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">ผู้ใช้งาน: {{ $user->firstname }} {{ $user->lastname }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Username:</strong> {{ $user->username }}
                </div>
                <div class="col-md-6">
                    <strong>ที่อยู่:</strong> {{ $user->address }}
                </div>
            </div>
        </div>
    </div>

    {{-- Add Item Form --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">เพิ่มรายการขยะ</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('keptkayas.purchase.add_to_cart') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div class="row g-3">
                    <div class="col-md-3 col-9">
                        <label for="kp_units_idfk" class="form-label">ชื่อขยะ:</label>
                        <select id="kp_itemscode" name="kp_itemscode" class="form-select recyclename" required >
                            <option value="">ชื่อขยะ</option>
                            @foreach ($recycleItems as $item)
                                <option value="{{ $item->kp_itemscode }}" data-id="{{ $item->id }}">{{ $item->kp_itemsname }} | {{ $item->kp_itemscode }}</option>
                            @endforeach
                        </select>

                        <input type="hidden" name="kp_tbank_item_id" id="kp_tbank_item_id">
                    </div>
                    <div class="col-md-1 col-3">
                        <label for="kp_itemscode" class="form-label">QRCode</label>

                          <button type="button" class="btn btn-outline-secondary form-control pl-2" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                                <i class="fa fa-qrcode"></i>
                            </button>
                    </div>
                   
                    <div class="col-lg-3 col-6">
                        <label for="amount_in_units" class="form-label">จำนวน:</label>
                        <input type="number" step="0.01" name="amount_in_units" id="amount_in_units" class="form-control" required min="0.01">
                    </div>
                    <div class="col-md-3 col-6">
                        <label for="kp_units_idfk" class="form-label">หน่วยนับ:</label>
                        <select id="kp_units_idfk" name="kp_units_idfk" style="width: 30%" class="form-select recyclename" required>
                            <option value="">เลือกหน่วยนับ</option>
                            @foreach ($allUnits as $unit)
                                <option value="{{ $unit->id }}" {{ $unit->id == 1 ? 'selected' : '' }}>{{ $unit->unitname }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle-fill me-1"></i> เพิ่มลงรถเข็น
                        </button>
                    </div>
                </div>
            </form>

          
        </div>
    </div>

    {{-- Cart List (Responsive) --}}
    @if(Session::has('purchase_cart') && count(Session::get('purchase_cart')) > 0)
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">รายการในรถเข็น</h5>
            <a href="{{ route('keptkayas.purchase.cart') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-cart-fill me-1"></i> ไปหน้าสรุปการซื้อ
            </a>
        </div>
        <div class="card-body">
            {{-- Desktop Table View --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>รายการขยะ</th>
                            <th>ปริมาณ</th>
                            <th>ราคา/หน่วย</th>
                            <th>เป็นเงิน</th>
                            <th>คะแนน</th>
                            <th style="width: 100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $cart = Session::get('purchase_cart');
                            $grandTotal = 0;
                            $grandPoints = 0;
                        @endphp
                        @foreach ($cart as $index => $item)
                            @php
                                $grandTotal += $item['amount'];
                                $grandPoints += $item['points'];
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['item_name'] }}</td>
                                <td>{{ number_format($item['amount_in_units'], 2) }} {{ $item['unit_name'] }}</td>
                                <td>{{ number_format($item['price_per_unit'], 2) }}</td>
                                <td>{{ number_format($item['amount'], 2) }}</td>
                                <td>{{ number_format($item['points']) }}</td>
                                <td>
                                    <form action="{{ route('keptkayas.purchase.remove_from_cart', $index) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">ยอดรวม:</th>
                            <th>{{ number_format($grandTotal, 2) }} บาท</th>
                            <th>{{ number_format($grandPoints) }} คะแนน</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="d-md-none">
                @php
                    $cart = Session::get('purchase_cart');
                    $grandTotal = 0;
                    $grandPoints = 0;
                @endphp
                @foreach ($cart as $index => $item)
                    @php
                        $grandTotal += $item['amount'];
                        $grandPoints += $item['points'];
                    @endphp
                    <div class="card mb-3">
                        <div class="card-body p-3">
                             <div style="position: absolute; right: 10px; top: 10px; z-index: 50;">
                                <form action="{{ route('keptkayas.purchase.remove_from_cart', $index) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                            <h6 class="text-lg mb-0 text-capitalize font-weight-bolder text-success">{{ $item['item_name'] }}</h6>
                            <p class="text-sm text-secondary mb-1">
                                {{ number_format($item['amount_in_units'], 2) }} {{ $item['unit_name']  }} x {{ number_format($item['price_per_unit'], 2) }} บาท
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="font-weight-bold mb-0">{{ number_format($item['amount'], 2) }} บาท</h6>
                                    <span class="text-sm">{{ number_format($item['points']) }} คะแนน</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="card p-3 mt-3">
                    <div class="d-flex justify-content-between font-weight-bold">
                        <span>ยอดรวม:</span>
                        <span>{{ number_format($grandTotal, 2) }} บาท</span>
                    </div>
                    <div class="d-flex justify-content-between font-weight-bold">
                        <span>คะแนนรวม:</span>
                        <span>{{ number_format($grandPoints) }} คะแนน</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-info text-center">ยังไม่มีรายการขยะในรถเข็น</div>
    @endif


 {{-- QR Scanner Modal --}}
    <div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrScannerModalLabel">สแกน QR Code เพื่อค้นหาสมาชิก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qr-reader" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

  @section('script')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            $(document).ready(function(){
                $('.recyclename').select2();

                $('#kp_itemscode').change(function(){
                    let selectedOption = $(this).find(':selected');
                    $('#kp_tbank_item_id').val(selectedOption.data('id'));
                });
            })
            document.addEventListener('DOMContentLoaded', function () {
                const qrScannerModal = document.getElementById('qrScannerModal');
                const kp_itemscode = document.getElementById('kp_itemscode');
                const kp_tbank_item_id = document.getElementById('kp_tbank_item_id');
                const html5QrCode = new Html5Qrcode("qr-reader");

                qrScannerModal.addEventListener('shown.bs.modal', () => {
                    html5QrCode.start({
                        facingMode: "environment"
                    }, {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    },
                        (decodedText, decodedResult) => {
                            // on success
                            console.log(`QR Code scanned: ${decodedText.toString().toUpperCase()}`);
                            // Stop the scanner and close the modal
                            html5QrCode.stop().then(() => {
                                const modal = bootstrap.Modal.getInstance(qrScannerModal);
                                modal.hide();
                            }).catch(err => {
                                console.error("Failed to stop the scanner.", err);
                            });

                            // Set the value of the Select2 dropdown
                            $('#kp_itemscode').val(decodedText.toString().toUpperCase()).trigger('change');
                            
                            // Check if the item code exists and update the hidden ID
                            const selectedOption = $('#kp_itemscode').find(':selected');
                            if (selectedOption.length) {
                                kp_tbank_item_id.value = selectedOption.data('id');
                            } else {
                                kp_tbank_item_id.value = '';
                                alert('ไม่พบรหัสขยะที่สแกน');
                            }
                            
                        },
                        (errorMessage) => {
                            // on failure (or no QR code found)
                            // This function is called continuously, so we don't need to do anything here
                        }
                    ).catch(err => {
                        console.error("Failed to start the scanner.", err);
                        alert("ไม่สามารถเปิดใช้งานกล้องได้ โปรดตรวจสอบการอนุญาตใช้งานกล้อง");
                    });
                });

                qrScannerModal.addEventListener('hidden.bs.modal', () => {
                    // Ensure the scanner is stopped when the modal is closed manually
                    if (html5QrCode.isScanning) {
                        html5QrCode.stop().catch(err => {
                            console.error("Failed to stop the scanner.", err);
                        });
                    }
                });
            });
        </script>
    @endsection