<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpTbankItemsGroups extends Model
{
    use HasFactory;
    protected $table = 'kp_tbank_items_groups';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kp_items_groupname',
        'item_group_code',
        'sequence_num',
        'status',
        'deleted'	
    ];
}
