<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpTbankItems extends Model
{
    use HasFactory;
    protected $fillable =[
        'id',
        'kp_itemscode',
        'kp_itemsname',
        'kp_items_group_idfk',
        'status',
        'image',
        'deleted',
        'org_id_fk'
    ];

    protected $table = 'kp_tbank_items';


    public function items_price_and_point_infos(){
        return $this->hasMany(KpTbankItemsPriceAndPoint::class, 'kp_items_idfk', 'id');
    }

     public function prices()
    {
        return $this->hasMany(KpTbankItemsPriceAndPoint::class, 'kp_items_idfk', 'id');
    }

    // A specific relationship to only get the ACTIVE price points
    public function activePrices()
    {
        return $this->hasMany(KpTbankItemsPriceAndPoint::class, 'kp_items_idfk', 'id')
                    ->where('status', 'active');
    }
    
    public function kp_items_groups(){
        return $this->belongsTo(KpTbankItemsGroups::class, 'kp_items_group_idfk', 'id');
    }
}
