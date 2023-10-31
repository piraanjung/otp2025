<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subzone extends Model
{
    use HasFactory;

    protected $fillable = [ "zone_id","subzone_name","status"];
    protected $table = "subzones";
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }
}
