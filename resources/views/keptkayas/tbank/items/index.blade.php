@extends('layouts.keptkaya')
@section('nav-header', 'ขยะรีไซเคิล')
@section('nav-current', ' ข้อมูลขยะรีไซเคิล')
@section('page-topic', ' ข้อมูลขยะรีไซเคิล')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">รายการขยะรีไซเคิลในระบบ</h3>
            <div class="card-tools">
                <a class="btn btn-primary btn-sm" href="{{ route('keptkayas.tbank.items.create') }}">
                    <i class="fas fa-plus"></i> สร้างข้อมูล
                </a>
                <a class="btn btn-outline-danger btn-sm" href="{{ route('keptkayas.tbank.items.trash') }}">
                    <i class="fas fa-trash"></i> ดูถังขยะ
                </a>
                <a class="btn btn-outline-danger btn-sm" href="{{ route('keptkayas.tbank.items.pendingEf') }}">
                    <i class="fas fa-trash"></i> รายขยะที่ยังไม่มี EFactor
                </a>

                <!-- เพิ่มปุ่มสำหรับหน้า Import/Export ที่เราคุยกันก่อนหน้าได้ที่นี่ -->
            </div>
        </div>
        <div class="card-body p-0"> {{-- p-0 ช่วยให้ตารางชิดขอบการ์ด สวยงามแบบ AdminLTE --}}
            <table class="table table-striped table-hover m-0">
                <thead>
                    <tr>
                        <th style="width: 80px">รูปภาพ</th>
                        <th>รหัส</th>
                        <th>ชื่อรายการ</th>
                        <th class="text-center">หน่วย (Bank/Kiosk)</th>
                        <th class="text-center">ค่าคาร์บอน (EF)</th>
                        <th class="text-center">สถานะ</th>
                        <th style="width: 150px">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kp_tbank_items as $item)
                        <tr>
                            <td>
                                @if($item->image)
                                    <img src="{{ asset('keptkaya/items/' . $item->image) }}" class="img-thumbnail"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light text-center" style="width: 50px; height: 50px; line-height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle text-bold text-primary">{{ $item->kp_itemscode }}</td>
                            <td class="align-middle">{{ $item->kp_itemsname }}</td>

                            {{-- ส่วนแสดงหน่วยนับ --}}
                            <td class="align-middle text-center">
                                <span class="badge badge-info" title="หน่วยธนาคาร">
                                    {{ $item->unitBank->unit_short_name ?? 'N/A' }}
                                </span>
                                <i class="fas fa-arrow-right mx-1 text-muted small"></i>
                                <span class="badge badge-warning" title="หน่วยตู้ Kiosk">
                                    {{ $item->unitKiosk->unit_short_name ?? '-' }}
                                </span>
                            </td>

                            {{-- ส่วนแสดงค่า EF --}}
                            <td class="align-middle text-center">
                                @if($item->emissionFactor)
                                    <span
                                        class="text-success text-bold">{{ number_format($item->emissionFactor->ef_value, 4) }}</span>
                                    <br><small class="text-muted">kgCO2e/kg</small>
                                @else
                                    <span class="badge badge-secondary py-1" style="opacity: 0.7">ยังไม่ได้ตั้งค่า</span>
                                @endif
                            </td>

                            <td class="align-middle text-center">
                                @if($item->status == 'active')
                                    <span class="badge badge-success">เปิดใช้งาน</span>
                                @else
                                    <span class="badge badge-danger">ปิดใช้งาน</span>
                                @endif
                            </td>

                            <td class="align-middle text-right">
                                <div class="btn-group">
                                    <a class="btn btn-info btn-sm" href="{{ route('keptkayas.tbank.items.edit', $item->id) }}"
                                        title="แก้ไข">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    {{-- แบบฟอร์มสำหรับการลบ --}}
                                    <form action="{{ route('keptkayas.tbank.items.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('คุณมั่นใจหรือไม่ที่จะลบรายการนี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> ลบ
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{-- ใส่ Pagination ถ้ามี --}}
            {{-- $kp_tbank_items->links() --}}
        </div>
    </div>
@endsection
