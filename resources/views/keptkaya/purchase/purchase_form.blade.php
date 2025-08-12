    @extends('layouts.keptkaya')

    @section('content')
    <div class="container-fluid">
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
                <form action="{{ route('keptkaya.purchase.add_to_cart') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="kp_tbank_item_id" class="form-label">รายการขยะ:</label>
                            <select id="kp_tbank_item_id" name="kp_tbank_item_id" class="form-select" required>
                                <option value="">เลือกรายการขยะ</option>
                                @foreach ($recycleItems as $item)
                                    <option value="{{ $item->id }}">{{ $item->kp_itemsname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="kp_units_idfk" class="form-label">หน่วยนับ:</label>
                            <select id="kp_units_idfk" name="kp_units_idfk" class="form-select" required>
                                <option value="">เลือกหน่วยนับ</option>
                                @foreach ($allUnits as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unitname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="amount_in_units" class="form-label">น้ำหนัก/ชิ้น:</label>
                            <input type="number" step="0.01" name="amount_in_units" id="amount_in_units" class="form-control" required min="0.01">
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

        {{-- Cart List --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">รายการในรถเข็น</h5>
                @if(Session::has('purchase_cart') && count(Session::get('purchase_cart')) > 0)
                    <a href="{{ route('keptkaya.purchase.cart') }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-cart-fill me-1"></i> ไปหน้าสรุปการซื้อ
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if(!Session::has('purchase_cart') || empty(Session::get('purchase_cart')))
                    <div class="alert alert-info text-center">ยังไม่มีรายการขยะในรถเข็น</div>
                @else
                    <div class="table-responsive">
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
                                            <form action="{{ route('keptkaya.purchase.remove_from_cart', $index) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
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
                @endif
            </div>
        </div>
    </div>
    @endsection
    