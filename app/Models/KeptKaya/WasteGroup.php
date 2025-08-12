<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function wasteTypes()
    {
        return $this->hasMany(WasteType::class);
    }
}
