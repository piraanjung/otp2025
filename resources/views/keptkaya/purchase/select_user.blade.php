@extends('layouts.keptkaya') {{-- Assuming your keptkaya layout is keptkaya --}}


@section('content')
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between">
            <h5 class="mb-0">ค้นหาสมาชิก</h5>
            <button class="btn btn-primary btn-sm " type="button" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                <i class="fa fa-qrcode me-2"></i>
            </button>
        </div>

        <div class="card-body">
            <form action="{{ route('keptkaya.purchase.select_user') }}" method="GET" id="user-search-form">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="name_search" class="form-label">ชื่อ-สกุล:</label>
                        <input type="text" name="name_search" id="name_search" class="form-control"
                            value="{{ request('name_search') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="username_search" class="form-label">Username:</label>
                        <input type="text" name="username_search" id="username_search" class="form-control"
                            value="{{ request('username_search') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">ค้นหา</button>
                        <a href="{{ route('keptkaya.purchase.select_user') }}" class="btn btn-outline-secondary">ล้างค่า</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-success text-white d-grid gap-2">
            <h5 class="card-title mb-0">เลือกผู้ใช้งานสำหรับทำธุรกรรม</h5>
        </div>
        <div class="card-body">
            {{-- Filter/Search Form --}}


            <div class="table-responsive d-none d-md-block"> {{-- Hide table on small screens --}}
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>ชื่อ-สกุล</th>
                            <th>Username</th>
                            <th>ที่อยู่</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($keptKayaMembers as $member)
                            <tr>
                                <td>{{ $member->id }}</td>
                                <td>{{ $member->firstname }} {{ $member->lastname }}</td>
                                <td>{{ $member->username }}</td>
                                <td>{{ Str::limit($member->address ?? 'N/A', 50) }}</td>
                                <td>
                                    @if ($member->purchaseTransactions->count() > 0)
                                        <div class="p-2 mb-2 rounded bg-light">
                                            <h6 class="mb-1">ธุรกรรมวันนี้:</h6>
                                            <ul class="list-unstyled mb-0 small">
                                                @php
                                                    $todayTransactions = $member->purchaseTransactions;
                                                @endphp
                                                <li>
                                                    <strong>จำนวนรายการ:</strong> {{ $todayTransactions->count() }}
                                                </li>
                                                <li>
                                                    <strong>ยอดรวม:</strong>
                                                    {{ number_format($todayTransactions->sum('total_amount'), 2) }} บาท
                                                </li>
                                                <li>
                                                <li>
                                                    <a href="{{ route('keptkaya.purchase.receipt', $todayTransactions[0]->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="bi bi-receipt me-1"></i> ดูใบเสร็จ
                                                    </a>
                                                </li>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif

                                    <a href="{{ route('keptkaya.purchase.history', $member->id) }}"
                                        class="btn btn-info btn-sm me-2">
                                        <i class="bi bi-clock-history me-1"></i> ดูประวัติ
                                    </a>
                                    <a href="{{ route('keptkaya.purchase.start_purchase', $member->id) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="bi bi-cart-plus me-1"></i> เริ่มรับซื้อ
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">ไม่พบผู้ใช้งานที่เป็นสมาชิก Keptkaya</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card Layout --}}
            <div class="d-md-none  mt-4">
                <div class="row g-3">
                    @forelse ($keptKayaMembers as $member)
                        <div class="col-12">
                            <div class="card h-100 shadow-sm border-secondary">
                                <div class="card-body">
                                    <h5 class="card-title"><strong>{{ $member->firstname }} {{ $member->lastname }}</strong>
                                    </h5>
                                    <h6 class="card-subtitle mb-2 text-muted">@ {{ $member->username }}</h6>
                                    <p class="card-text mb-2">
                                        <i class="bi bi-geo-alt me-1"></i> {{ Str::limit($member->address ?? 'N/A', 50) }}
                                    </p>

                                    @if ($member->purchaseTransactions->count() > 0)
                                        <div class="p-2 my-2 rounded bg-light">
                                            <h6 class="mb-1">ธุรกรรมวันนี้:</h6>
                                            <ul class="list-unstyled mb-0 small">
                                                @php
                                                    $todayTransactions = $member->purchaseTransactions;
                                                @endphp
                                                <li>
                                                    <strong>จำนวนรายการ:</strong> {{ $todayTransactions->count() }}
                                                </li>
                                                <li>
                                                    <strong>ยอดรวม:</strong>
                                                    {{ number_format($todayTransactions->sum('total_amount'), 2) }} บาท
                                                </li>
                                                <li>
                                                    <a href="{{ route('keptkaya.purchase.receipt', $todayTransactions[0]->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="bi bi-receipt me-1"></i> ดูใบเสร็จ
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between mt-3">
                                        <a href="{{ route('keptkaya.purchase.history', $member->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="bi bi-clock-history me-1"></i> ดูประวัติ
                                        </a>
                                        <a href="{{ route('keptkaya.purchase.start_purchase', $member->id) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="bi bi-cart-plus me-1"></i> เริ่มรับซื้อ
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">ไม่พบผู้ใช้งานที่เป็นสมาชิกธนาคารขยะ</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
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
            document.addEventListener('DOMContentLoaded', function () {
                const qrScannerModal = document.getElementById('qrScannerModal');
                const usernameSearchInput = document.getElementById('username_search');
                const userSearchForm = document.getElementById('user-search-form');

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
                            console.log(`QR Code scanned: ${decodedText}`);

                            // Stop the scanner and close the modal
                            html5QrCode.stop().then(() => {
                                const modal = bootstrap.Modal.getInstance(qrScannerModal);
                                modal.hide();
                            }).catch(err => {
                                console.error("Failed to stop the scanner.", err);
                            });

                            // Set the username and submit the form
                            usernameSearchInput.value = decodedText;
                            userSearchForm.submit();
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