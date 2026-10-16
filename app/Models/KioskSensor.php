<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KioskSensor extends Model
{
    protected $table = 'kiosk_sensors';
    protected $fillable = [
        'kiosk_id',
        'sensor_code',
        'sensor_name',
        'status',
        'last_val',
        'last_ping_at',
    ];

    public function kiosk()
    {
        return $this->belongsTo(Kiosk::class, 'kiosk_id', 'id');
    }
}
