<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tambon extends Model
{
    use HasFactory;

    public $table = 'tambons';

    public $fillable = [
        'id',
        'tambon_name',
        'district_id', 
        'zipcode',
    ];

    public function zones(){
        return $this->hasMany(Zone::class, 'tambon_id', 'id');
    }
}
