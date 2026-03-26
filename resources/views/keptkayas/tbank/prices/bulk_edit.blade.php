@extends('layouts.keptkaya')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>จัดการราคาและแต้มขยะ (Bulk Edit)</h5>
            <div>
                <button type="submit" form="bulkUpdateForm" class="btn btn-light fw-bold text-primary">
                    <i class="fas fa-save me-1"></i> บันทึกการเปลี่ยนแปลงทั้งหมด
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="พิมพ์ชื่อขยะเพื่อค้นหา...">
            </div>

            <form action="{{ route('keptkayas.tbank.prices.bulk_update') }}" method="POST" id="bulkUpdateForm">
                @csrf
                <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                    <table class="table table-bordered table-hover">
    <thead class="table-dark sticky-top">
        <tr>
            <th class="text-center">ชื่อขยะ</th>
            <th width="120" class="text-center">ช่องทาง</th>
            <th width="150" class="text-center">ราคาร้านซื้อ (Dealer)</th>
            <th width="150" class="text-center">ราคาสมาชิก (Member)</th>
            <th width="120" class="text-center">แต้ม (P)</th>
            <th width="110" class="text-center">สถานะ</th>
        </tr>
    </thead>
    <tbody id="itemTableBody">
        @foreach($items as $item)
            @php
                $bankPrice = $item->prices->where('kp_units_idfk', $item->unit_bank_idfk)->first();
                $kioskPrice = $item->unit_kiosk_idfk
                    ? $item->prices->where('kp_units_idfk', $item->unit_kiosk_idfk)->first()
                    : null;
            @endphp

            <tr class="item-row-group">
                <td rowspan="2" class="align-middle item-name bg-light">
                    <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                    <strong>{{ $item->kp_itemsname }}</strong>
                </td>
                <td class="text-center bg-primary-subtle border-primary">
                    <span class="badge bg-primary">Bank</span><br>
                    <small>{{ $item->unit_bank->unit_short_name ?? 'กก.' }}</small>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[{{ $item->id }}][dealer_bank]"
                           class="form-control text-end border-secondary"
                           value="{{ $bankPrice->price_from_dealer ?? 0 }}">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[{{ $item->id }}][member_bank]"
                           class="form-control text-end border-primary fw-bold"
                           value="{{ $bankPrice->price_for_member ?? 0 }}">
                </td>
                <td>
                    <input type="number" step="1" name="items[{{ $item->id }}][point_bank]"
                           class="form-control text-end border-primary"
                           value="{{ $bankPrice->point ?? 0 }}">
                </td>
                <td rowspan="2" class="align-middle text-center">
                    <select name="items[{{ $item->id }}][status]" class="form-select form-select-sm">
                        <option value="active" {{ $item->status == 'active' ? 'selected' : '' }}>เปิด</option>
                        <option value="inactive" {{ $item->status == 'inactive' ? 'selected' : '' }}>ปิด</option>
                    </select>
                </td>
            </tr>

            <tr class="item-row-group">
                <td class="text-center bg-warning-subtle border-warning">
                    <span class="badge bg-warning text-dark">Kiosk</span><br>
                    <small>{{ $item->unit_kiosk->unit_short_name ?? '-' }}</small>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[{{ $item->id }}][dealer_kiosk]"
                           class="form-control text-end border-secondary"
                           {{ !$item->unit_kiosk_idfk ? 'disabled' : '' }}
                           value="{{ $kioskPrice->price_from_dealer ?? 0 }}">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[{{ $item->id }}][member_kiosk]"
                           class="form-control text-end border-warning fw-bold"
                           {{ !$item->unit_kiosk_idfk ? 'disabled' : '' }}
                           value="{{ $kioskPrice->price_for_member ?? 0 }}">
                </td>
                <td>
                    <input type="number" step="1" name="items[{{ $item->id }}][point_kiosk]"
                           class="form-control text-end border-warning"
                           {{ !$item->unit_kiosk_idfk ? 'disabled' : '' }}
                           value="{{ $kioskPrice->point ?? 0 }}">
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ฟังก์ชัน Search แบบ Real-time
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelectorAll("#itemTableBody tr.item-row-group");

    // เนื่องจาก 1 ไอเทมมี 2 แถว เราจะวนลูปทีละ 2
    for (let i = 0; i < rows.length; i += 2) {
        let nameField = rows[i].querySelector(".item-name");
        if (nameField) {
            let textValue = nameField.textContent || nameField.innerText;
            if (textValue.toUpperCase().indexOf(filter) > -1) {
                rows[i].style.display = "";
                rows[i+1].style.display = "";
            } else {
                rows[i].style.display = "none";
                rows[i+1].style.display = "none";
            }
        }
    }
});
</script>

<style>
    .table-bordered td, .table-bordered th { border: 1px solid #dee2e6 !important; }
    .sticky-top { position: sticky; top: 0; background-color: #212529; }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { opacity: 1; }
    .bg-primary-subtle { background-color: #e7f1ff; }
    .bg-warning-subtle { background-color: #fff3cd; }
</style>
@endsection
