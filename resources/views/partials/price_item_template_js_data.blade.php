{{-- partials/price_item_template_js_data.blade.php --}}

@php
    // สร้าง HTML สำหรับรายการขยะใหม่ (ใช้ Index 999 เป็น placeholder)
    $tempIndex = 999;
    $tempItemData = [
        'kp_items_idfk' => '', 'effective_date' => date('Y-m-d'), 'end_date' => $oneYearFromNow,
        'is_forever_active' => true, // ค่าเริ่มต้นเป็นตลอดไป
        'units_data' => [
            [ 'kp_units_idfk' => '', 'price_from_dealer' => 0, 'price_for_member' => 0, 'point' => 0 ]
        ]
    ];
@endphp

{{-- Hidden Template สำหรับ Item Block ใหม่ --}}
<template id="item-block-template">
    @include('partials.price_item_template', [
        'itemIndex' => $tempIndex,
        'itemData' => $tempItemData,
        'items' => $items,
        'units' => $units,
        'oneYearFromNow' => $oneYearFromNow
    ])
</template>

<script>
    // สร้าง HTML options สำหรับหน่วยนับใหม่ (ไม่ต้องใช้ AJAX)
    const unitOptionsHtml = `
        @foreach ($units as $unit)
            <option value="{{ $unit->id }}">{{ $unit->unitname }}</option>
        @endforeach
    `;
</script>