<?php

namespace App\Models\Tabwater;

use Illuminate\Database\Eloquent\Model;

class TwNotifyStaffs extends Model
{
    protected $table = 'tw_notify_staff';

    protected $fillable = [
        'notify_id',	'user_id',	'staff_status'	
    ];
}
