<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // *สำคัญ: ใช้ Now เพื่อให้ส่งทันที
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// ต้อง implements ShouldBroadcastNow เสมอสำหรับ Real-time
class KioskImageCaptured implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $kioskId;
    public $image; // ข้อมูลรูปภาพ (Base64)

    public function __construct($kioskId, $image)
    {
        $this->kioskId = $kioskId;
        $this->image = $image;
    }

    // กำหนดช่องสัญญาณเป็น kiosk.{id}
    public function broadcastOn(): array
    {
        return [
            new Channel('kiosk.' . $this->kioskId),
        ];
    }

    // ตั้งชื่อ Event ที่ฝั่ง Frontend จะรอฟัง
    public function broadcastAs(): string
    {
        return 'image.sent';
    }
}
