<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpPointTransfer extends Model
{
    protected $table = 'kp_point_transfers';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'note',
    ];
}
