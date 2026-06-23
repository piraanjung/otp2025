<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmissionFactor extends Model
{
    protected $table = 'emission_factors';
    protected $fillable = [
        'material_name',
        'unit',
        'ef_value',
        'source',
        'example'
    ];
}
