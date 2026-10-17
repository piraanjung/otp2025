@extends('layouts.keptkaya')
@section('page-topic', 'ถังขยะ (รายการที่ถูกลบ)')

@section('content')
<div class="card card-outline card-danger">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-trash-alt text-danger"></i> รายการขยะในถังขยะ</h3>
        <div class="card-tools">
            <a href="{{ route('keptkayas.tbank.items.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left"></i> กลับไปหน้ารายการปกติ
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>รูป</th>
                    <th>ชื่อรายการ</th>
                    <th>วันที่ลบ</th>
                    <th class="text-right">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td><img src="{{ asset('keptkaya/items/' . $item->image) }}" width="40"></td>
                    <td>{{ $item->kp_itemsname }} <br><small class="text-muted">{{ $item->kp_itemscode }}</small></td>
                    <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                    <td class="text-right">
                        <!-- ปุ่มกู้คืน -->
                        <a href="{{ route('keptkayas.tbank.items.restore', $item->id) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-undo"></i> กู้คืน
                        </a>

                        <!-- ปุ่มลบถาวร -->
                        <form action="{{ route('keptkayas.tbank.items.forceDelete', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('ยืนยันการลบถาวร? ข้อมูลจะหายไปจากฐานข้อมูลทันที!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-fire"></i> ลบถาวร
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center p-5 text-muted">ไม่มีรายการในถังขยะ</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
