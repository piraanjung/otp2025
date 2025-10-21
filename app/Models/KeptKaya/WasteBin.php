<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use App\Models\KeptKaya\WasteBinSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteBin extends Model
{
    use HasFactory;

    protected $table = 'kp_waste_bins';
    protected $fillable = [
        'user_id',
        'bin_code',
        'bin_type',
        'location_description',
        'latitude',
        'longitude',
        'status', // สถานะโดยรวมของถัง (active, inactive, damaged, removed)
        'is_active_for_annual_collection', // สถานะเฉพาะสำหรับการเก็บรายปี
    ];

    protected $casts = [
        'is_active_for_annual_collection' => 'boolean',
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