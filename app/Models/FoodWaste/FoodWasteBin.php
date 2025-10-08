<?php

namespace App\Models\FoodWaste;

use App\Models\User;
use App\Models\KeptKaya\WasteBinSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodWasteBin extends Model
{
    use HasFactory;

    protected $table = 'foodwaste_bins';
    protected $fillable = [
        'user_id',
        'bin_code',
        'bin_type',
        'location_description',
        'latitude',
        'longitude',
        'status', // สถานะโดยรวมของถัง (active, inactive, damaged, removed)
    ];

   
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
      public function subscriptions()
    {
        return $this->hasMany(WasteBinSubscription::class);
    }
}