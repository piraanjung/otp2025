<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpTbankUnits extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'unitname',
        'unit_short_name',
        'status',
        'deleted',
    ];
    protected $table = 'kp_tbank_items_units';
}
