<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;
    protected $fillable = ["zone_name", "location","status"];

    public function subzone(){
        return $this->hasMany(Subzone::class, 'zone_id');
    }


}
