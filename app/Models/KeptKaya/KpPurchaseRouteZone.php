<?php

namespace App\Models\KeptKaya;

use App\Models\Admin\Zone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpPurchaseRouteZone extends Model
{
    use HasFactory;

    protected $table = 'kp_purchase_route_zones';

    protected $fillable = [
        'kp_purchase_route_id',
        'zone_id',
    ];

    public function route()
    {
        return $this->belongsTo(KpPurchaseRoute::class, 'kp_purchase_route_id', 'id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }
}