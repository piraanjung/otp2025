
<div style="font-family: 'Sarabun', sans-serif; padding: 20px;">
    <h2 style="color: #2d3748;">เรียน {{ $approverData->prefix."".$approverData->lastname." ".$approverData->lastname }}
    <p>มีคำขอเบิกพัสดุใหม่ที่รอการตรวจสอบและอนุมัติจากท่านในขั้นตอน: <strong>{{ $stepData->step_name }}</strong></p>
    
    <div style="background: #f7fafc; padding: 15px; border-radius: 5px; margin: 15px 0;">
        <p>เลขที่ใบเบิก (Ref No): {{ $transactions[0]->ref_no }}</p>

        <p><strong>ผู้ขอเบิก:</strong> {{ $transactions[0]->requester->prefix."".$transactions[0]->requester->firstname." ".$transactions[0]->requester->lastname }}</p>
        <p><วัตถุประสงค์: {{ $transactions[0]->purpose }}</p>
        <p>วันที่ขอ: {{ $transactions[0]->transaction_date }}</p>
        <p><strong>รายการ:</strong></p>

        @foreach ($transactions as $transaction )
            <p> <strong>{{ $transaction->item->name }}</strong>   {{ $transaction->quantity."  ". $transaction->item->unit }}</p>
        @endforeach
    <p>กรุณาคลิกที่ลิงก์ด้านล่างเพื่อเข้าสู่ระบบพัสดุและทำการอนุมัติ:</p>
    <a href="{{ route('inventory.withdraw.show_ref', $transaction->ref_no) }}" 
       style="background: #3182ce; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
        ตรวจสอบและอนุมัติ
    </a>

    <p style="margin-top: 30px; color: #718096; font-size: 12px;">ระบบแจ้งเตือนอัตโนมัติ คลังพัสดุออนไลน์</p>
</div>