<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class KioskCommandSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $kioskId;
    public $command;

    public function __construct($kioskId, $command)
    {
        $this->kioskId = $kioskId;
        $this->command = $command;
    }

    public function broadcastOn()
    {
        // ส่งไปที่ Channel เดียวกับที่ ESP32 และหน้าเว็บฟังอยู่
        return new Channel('kiosk.' . $this->kioskId);
    }

    public function broadcastAs()
    {
        return 'command.received';
    }
}
