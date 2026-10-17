<?php

namespace App\Models\Tabwater;

use App\Models\Admin\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabwaterDeviceLog extends Model
{
    use HasFactory;

    protected $table = 'tabwater_device_logs';

    protected $fillable = [
        'device_id',
        'staff_id',
        'action',
        'mode',
        'trigger_reason',
    ];

    // Relationships
    public function device()
    {
        return $this->belongsTo(TabwaterDevice::class, 'device_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}