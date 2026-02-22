<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $table = 'machines';
    protected $fillable =[
	'id','machine_id', 'org_id_fk','current_user_active_id','has_new_object',
    'pending_command','status','machine_ready',
    'last_heartbeat_at'
    ];
}
