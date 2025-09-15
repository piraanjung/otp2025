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
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">สรุปการซื้อขยะรีไซเคิล</h1>
            <a href="{{ route('keptkayas.purchase.select_user') }}" class="btn btn-secondary">
                <i class="bi bi-person-fill me-1"></i> เลือกผู้ใช้งานใหม่
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="xx" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (empty($cart))
            <div class="alert alert-info text-center">รถเข็นว่างเปล่า</div>
            <a href="{{ route('keptkayas.purchase.form', $user->id) }}" class="btn btn-primary mt-3">
                <i class="bi bi-arrow-left me-1"></i> กลับไปเพิ่มรายการ
            </a>
        @else
            {{-- Member Details --}}
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

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">รายการในรถเข็น</h5>
                </div>
                <div class="card-body">
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
                                                <button type="submit" class="btn btn-danger btn-sm"><i
                                                        class="fa fa-trash"></i></button>
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
                </div>
            </div>

            {{-- Save Transaction Buttons --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('keptkayas.purchase.form', $user->id) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> กลับไปเพิ่มรายการ
                </a>
                <form action="{{ route('keptkayas.purchase.save_transaction') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-save me-1"></i> บันทึกธุรกรรม
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection