<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="owe_count">จำนวนรอบบิลที่ค้างชำระแล้วต้องทำการตัดมิเตอร์</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="owe_count"
                    value="{{ isset($owe_count['values']) ? $owe_count['values'] : 0 }}" id="owe_count">
                <div class="input-group-append">
                    <span class="input-group-text">รอบบิล</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="invoice_expired">กำหนดวันชำระเงิน นับตั้งแต่ได้รับใบแจ้งหนี้ (จำนวนวัน)</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="invoice_expired"
                    value="{{ isset($invoice_expired['values']) ? $invoice_expired['values'] : 0 }}"
                    id="payment_expired_date">
                <div class="input-group-append">
                    <span class="input-group-text">วัน</span>
                </div>
            </div>
        </div>
    </div>
</div>
