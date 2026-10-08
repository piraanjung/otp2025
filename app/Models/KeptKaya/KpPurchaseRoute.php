<?php

namespace App\Models\KeptKaya;

use App\Models\Admin\Organization;
use App\Models\Admin\Zone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpPurchaseRoute extends Model
{
    use HasFactory;

    protected $table = 'kp_purchase_routes';

    protected $fillable = [
        'org_id_fk',
        'route_name',
        'status',
    ];

    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'kp_purchase_route_zones', 'kp_purchase_route_id', 'zone_id');
    }

    public function routeZones()
    {
        return $this->hasMany(KpPurchaseRouteZone::class, 'kp_purchase_route_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id_fk', 'id');
    }
}