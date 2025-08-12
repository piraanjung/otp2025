<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWastePreference extends Model
{
    use HasFactory;

    protected $table = 'user_waste_preferences';

    protected $fillable = [
        'user_id',
        'is_annual_collection',
        'is_waste_bank',
    ];

    protected $casts = [
        'user_id',
        'is_annual_collection',
        'is_waste_bank',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wasteBins()
    {
        return $this->hasMany(WasteBin::class,'user_id', 'user_id');
    }
}