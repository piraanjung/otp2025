<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvLocation extends Model
{
    protected $table = 'inv_locations';

    protected $fillable = [
        'org_id_fk',
        'department',
        'location',
        'description',
    ];

    // ความสัมพันธ์: 1 Location มีได้หลาย Lot (InvItemDetail)
    public function itemDetails()
    {
        return $this->hasMany(InvItemDetail::class, 'location_id_fk');
    }
}