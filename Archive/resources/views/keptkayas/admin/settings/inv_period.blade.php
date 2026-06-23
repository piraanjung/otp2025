<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="owe_count">จำนวนรอบบิลที่ค้างชำระแล้วต้องทำการตัดมิเตอร์</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="owe_count"
                    value="{{ isset($owe_count['values']) ? $owe_count['values'] : '' }}" id="owe_count">
                <div class="input-group-append">
                    <span class="input-group-text">เดือน</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="payment_expired_date">กำหนดวันชำระเงิน นับตั้งแต่ได้รับใบแจ้งหนี้ (จำนวนวัน)</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="payment_expired_date"
                    value="{{ isset($payment_expired_date['values']) ? $payment_expired_date['values'] : '' }}"
                    id="payment_expired_date">
                <div class="input-group-append">
                    <span class="input-group-text">วัน</span>
                </div>
            </div>
        </div>
    </div>
</div>
