<?php

namespace App\Models\FoodWaste;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FoodWasteLog extends Model
{
    protected $table = 'foodwaste_waste_logs';
    protected $fillable = [
        'user_id',
        'weight_kg',
        'batch_id',
        'photo_path',
        'is_mixed',
        'moisture',
        'temperature_feel',
        'carbon_saved_kg',
        'actual_moisture_avg',
        'verified_dry_weight',
        'is_verified',
        'estimated_weight'
    ];

    public function user() { return $this->belongsTo(User::class); }

}
