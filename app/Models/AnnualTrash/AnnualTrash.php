<?php

namespace App\Models\AnnualTrash;

use App\Models\AnnualTrash\AnnualTrashSubscription;
use App\Models\KeptKaya\KpUserGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualTrash extends Model
{
    use HasFactory;

    protected $table = 'annual_trashs';
    protected $fillable = [
        'id',
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

    public function kpUserGroup()
    {
        // belongsTo(Model ปลายทาง, ชื่อ FK ในตารางนี้, ชื่อ PK ปลายทาง)
        return $this->belongsTo(KpUserGroup::class, 'bin_type', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function subscriptions()
{
    return $this->hasMany(AnnualTrashSubscription::class, 'waste_bin_id', 'id');
}
}
