<?php

namespace App\Models\Tabwater;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabwaterDevice extends Model
{
    use HasFactory;

    protected $table = 'tabwater_devices';

    protected $fillable = [
        'station_id',
        'device_code',
        'name',
        'type',
        'status',
        'control_mode',
        'auto_control_enabled',
        'mqtt_topic',
        'relay_channel',
    ];

    protected $casts = [
        'status' => 'boolean',
        'auto_control_enabled' => 'boolean',
        'relay_channel' => 'integer',
    ];

    // Relationships
    public function station()
    {
        return $this->belongsTo(TabwaterStation::class, 'station_id');
    }

    public function logs()
    {
        return $this->hasMany(TabwaterDeviceLog::class, 'device_id');
    }

    public function latestLog()
    {
        return $this->hasOne(TabwaterDeviceLog::class, 'device_id')->latestOfMany();
    }
}