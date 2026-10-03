<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestApprovedCompleteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transactions;

    /**
     * Create a new message instance.
     */
    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $refNo = $this->transactions->first()->ref_no ?? '';
        return new Envelope(
            subject: 'ใบเบิกพัสดุของคุณได้รับการอนุมัติเสร็จสิ้นสมบูรณ์แล้ว (Ref: ' . $refNo . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.request_approved_complete', // ชี้ไปที่ไฟล์ Blade View ที่เราจะสร้างด้านล่าง
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}