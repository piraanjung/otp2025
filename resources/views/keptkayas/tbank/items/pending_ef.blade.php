@extends('layouts.keptkaya')
@section('page-topic', 'จับคู่ค่าคาร์บอน (Bulk Mapping)')

@section('content')
<div class="card card-default">
    <div class="card-body">
        <form action="{{ route('keptkayas.tbank.items.pendingEf') }}" method="GET" class="row">
            <div class="col-md-4">
                <label>กรองตามประเภทขยะ</label>
                <select name="group_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- แสดงทั้งหมด --</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->kp_items_groupname }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8 text-right align-self-end">
                <span class="text-muted">พบรายการที่ยังไม่ระบุค่า EF: <strong>{{ $items->count() }}</strong> รายการ</span>
            </div>
        </form>
    </div>
</div>

<div class="card card-outline card-warning shadow">
    <div class="card-header">
        <h3 class="card-title text-bold text-warning">
            <i class="fas fa-link"></i> จับคู่รายการขยะกับค่าการลดคาร์บอน
        </h3>
    </div>

    <form action="{{ route('keptkayas.tbank.items.updateEfBulk') }}" method="POST">
        @csrf
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-valign-middle">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 15%">รหัสสินค้า</th>
                        <th style="width: 30%">ชื่อรายการขยะ</th>
                        <th style="width: 55%">เลือกค่า Emission Factor (EF) ที่สอดคล้อง</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td><code>{{ $item->kp_itemscode }}</code></td>
                        <td>
                            <span class="text-bold">{{ $item->kp_itemsname }}</span><br>
                            <small class="badge badge-secondary">{{ $item->itemGroup->kp_items_groupname ?? 'ทั่วไป' }}</small>
                        </td>
                        <td>
                            <select name="ef_mapping[{{ $item->id }}]" class="form-control select2">
                                <option value="">-- ค้นหาวัสดุหรือค่า EF --</option>
                                @foreach($emissionFactors as $ef)
                                    <option value="{{ $ef->id }}">
                                        {{ $ef->material_name }} [{{ number_format($ef->ef_value, 4) }} {{ $ef->unit }}]
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <img src="{{ asset('images/check-success.png') }}" style="width: 100px; opacity: 0.5;">
                            <h5 class="mt-3 text-success">เรียบร้อย! ทุกรายการถูกจับคู่ค่า EF ครบแล้ว</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->count() > 0)
        <div class="card-footer text-right bg-white border-top">
            <button type="submit" class="btn btn-warning btn-lg px-5 text-bold shadow-sm">
                <i class="fas fa-save"></i> บันทึกการจับคู่ทั้งหมด
            </button>
        </div>
        @endif
    </form>
</div>
@endsection
