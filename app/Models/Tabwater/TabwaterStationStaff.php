<?php

namespace App\Models\Tabwater;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TabwaterStationStaff extends Pivot
{
    protected $table = 'tabwater_station_staffs';

    protected $fillable = [
        'station_id',
        'staff_id',
        'role',
    ];
}