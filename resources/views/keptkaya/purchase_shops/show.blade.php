@extends('layouts.keptkaya')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">รายละเอียดร้านรับซื้อ</h1>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">ชื่อร้าน:</dt>
                <dd class="col-sm-8">{{ $shop->shop_name }}</dd>

                <dt class="col-sm-4">ผู้ติดต่อ:</dt>
                <dd class="col-sm-8">{{ $shop->contact_person ?? 'N/A' }}</dd>

                <dt class="col-sm-4">เบอร์โทรศัพท์:</dt>
                <dd class="col-sm-8">{{ $shop->phone ?? 'N/A' }}</dd>

                <dt class="col-sm-4">ที่อยู่:</dt>
                <dd class="col-sm-8">{{ $shop->address ?? 'N/A' }}</dd>

                <dt class="col-sm-4">สถานะ:</dt>
                <dd class="col-sm-8">
                    <span class="badge {{ $shop->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($shop->status) }}
                    </span>
                </dd>

                <dt class="col-sm-4">หมายเหตุ:</dt>
                <dd class="col-sm-8">{{ $shop->comment ?? 'N/A' }}</dd>

                <dt class="col-sm-4">สร้างเมื่อ:</dt>
                <dd class="col-sm-8">{{ $shop->created_at->format('Y-m-d H:i:s') }}</dd>

                <dt class="col-sm-4">อัปเดตเมื่อ:</dt>
                <dd class="col-sm-8">{{ $shop->updated_at->format('Y-m-d H:i:s') }}</dd>
            </dl>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('keptkaya.purchase-shops.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
            <a href="{{ route('keptkaya.purchase-shops.edit', $shop->id) }}" class="btn btn-warning">แก้ไข</a>
        </div>
    </div>
</div>
@endsection
