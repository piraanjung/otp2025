<div class="row">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    {{-- logo/bill.png ไม่มีใน repo (เดิมแสดงเป็นรูปเสีย) แสดงเมื่อมีไฟล์เท่านั้น --}}
                    @if (file_exists(public_path('logo/bill.png')))
                        <div class="col-12">
                            <img class="w-100 border-radius-lg shadow-lg " src="{{ asset('logo/bill.png') }}"
                                alt="product_image">
                        </div>
                    @endif
                    <div class="col-12 mt-4">
                        <div class="d-flex">
                            <input type="text" class="form-control text-lg text-bolder" name="meternumber_code"
                                id="meternumber_code"
                                value="{{ isset($meternumber_code['values']) ? $meternumber_code['values'] : '' }}"
                                placeholder="IV">
                            <span
                                class="badge bg-gradient-secondary ms-auto text-lg">00001&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
