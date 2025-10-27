@extends('layouts.keptkaya')
@section('nav-header', 'กำหนดราคาและคะแนน')
@section('nav-current', 'สร้างข้อมูลราคาขยะ')
@section('page-topic', 'สร้างข้อมูลกำหนดราคาขยะหลายรายการ')

@section('content')

    <div class="card card-info">
        <div class="card-header"></div>
        <div class="card-body">
            <form action="{{ route('keptkayas.tbank.prices.store') }}" class="form-horizontal" method="post">
                @csrf

                @php
                    use Carbon\Carbon;
                    use Illuminate\Support\Facades\Auth;

                    // กำหนดค่าเริ่มต้นสำหรับ Item Block แรก (เพื่อจัดการ old input/edit mode)
                    $oneYearFromNow = Carbon::now()->addYear()->format('Y-m-d');
                    $isForeverActive = ($price->end_date ?? null) === null;

                    // เตรียม Array หลักสำหรับ Item Block (รายการขยะ)
                    $formItems = old('items_data', [
                        [
                            'kp_items_idfk' => $price->kp_items_idfk ?? '',
                            'effective_date' => $price->effective_date ? $price->effective_date->format('Y-m-d') : date('Y-m-d'),
                            'end_date' => $price->end_date ? $price->end_date->format('Y-m-d') : $oneYearFromNow,
                            'is_forever_active' => $isForeverActive,
                            // unitsData (Price Tiers) ต้องอยู่ใน Item Block
                            'units_data' => $price->unitsData ?? [
                                [ 'kp_units_idfk' => '', 'price_from_dealer' => 0, 'price_for_member' => 0, 'point' => 0 ]
                            ]
                        ]
                    ]);
                @endphp

                <h5 class="mb-3">รายการขยะและราคา</h5>
                
                <div id="items-container">
                    @foreach ($formItems as $itemIndex => $itemData)
                        {{-- เรียกใช้ Template สำหรับแต่ละรายการ --}}
                        @include('partials.price_item_template', [
                            'itemIndex' => $itemIndex,
                            'itemData' => $itemData,
                            'items' => $items, // รายการขยะทั้งหมด
                            'units' => $units, // หน่วยนับทั้งหมด
                            'oneYearFromNow' => $oneYearFromNow
                        ])
                    @endforeach
                </div>

                <button type="button" class="btn btn-info mt-3" id="add-item-btn"><i class="fa fa-plus-circle me-1"></i> เพิ่มรายการขยะใหม่</button>

                <hr>
                
                {{-- Global Fields --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="recorder_id" class="form-label">ผู้บันทึก:</label>
                        <select id="recorder_id" name="recorder_id" class="form-select @error('recorder_id') is-invalid @enderror">
                            <option value="">เลือกผู้บันทึก</option>
                            @foreach ($recorders as $recorder)
                                <option value="{{ $recorder->user_id }}" {{ old('recorder_id', $price->recorder_id ?? Auth::id()) == $recorder->user_id ? 'selected' : '' }}>
                                    {{ $recorder->user->firstname }} {{ $recorder->user->lastname }}
                                </option>
                            @endforeach
                        </select>
                        @error('recorder_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-3 mt-4">
                    <label for="comment" class="form-label">หมายเหตุ:</label>
                    <textarea id="comment" name="comment" class="form-control @error('comment') is-invalid @enderror">{{ old('comment', $price->comment ?? '') }}</textarea>
                    @error('comment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1" {{ old('is_active', $price->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" class="form-check-label">ใช้งานอยู่</label>
                </div>

                <button type="submit" class="btn btn-success">บันทึกข้อมูลทั้งหมด</button>
            </form>
        </div>
    </div>

    @include('partials.price_item_template_js_data') {{-- สำหรับ Template ใน JS --}}
    @include('partials.price_item_javascript') {{-- ไฟล์ JS หลัก --}}

@endsection