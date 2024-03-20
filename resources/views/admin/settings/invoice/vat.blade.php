<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="vat">ภาษีมูลค่าเพิ่ม</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="vat"
                    value="{{ isset($vat['values']) ? $vat['values'] : 0 }}" id="vat">
                <div class="input-group-append">
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </div>

    </div>
</div>
