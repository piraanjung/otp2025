<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovalNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transactions;
    public $stepData;
    public $approverData;
    // รับข้อมูลใบเบิกและข้อมูลสเต็ปเข้ามา
    public function __construct($transactions, $stepData, $approver)
    {
        $this->transactions = $transactions;
        $this->stepData = $stepData;
        $this->approverData = $approver;
    }

    public function build()
    {
        return $this->subject('แจ้งเตือน: มีรายการรอการอนุมัติ (Ref: ' . $this->transactions[0]->ref_no . ')')
                    ->view('emails.approval_notification'); // ชื่อไฟล์หน้า View สำหรับทำเนื้อหาอีเมล
    }
}