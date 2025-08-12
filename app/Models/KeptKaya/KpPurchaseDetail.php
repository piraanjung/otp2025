<?php

namespace App\Models\KeptKaya;

use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\Admin\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpPurchaseDetail extends Model
{
    use HasFactory;

    protected $table = 'kp_purchase_details';

    protected $fillable = [
        'kp_purchase_trans_id',
        'kp_recycle_item_id',
        'kp_tbank_items_pricepoint_id',
        'recorder_id',
        'amount_in_units',
        'price_per_unit',
        'amount',
        'points',
        'comment',
    ];

    protected $casts = [
        'amount_in_units' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'amount' => 'decimal:2',
        'points' => 'integer',
    ];

    // Relationships
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(KpPurchaseTransaction::class, 'kp_purchase_trans_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(KpTbankItems::class, 'kp_recycle_item_id');
    }
    
    public function pricePoint(): BelongsTo
    {
        return $this->belongsTo(KpTbankItemsPriceAndPoint::class, 'kp_tbank_items_pricepoint_id');
    }



    public function user(): BelongsTo
    {
        return $this->belongsTo(UserWastePreference::class, 'kp_user_id_fk', 'id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'recorder_id', 'id');
    }
    
    public function details()
    {
        return $this->hasMany(KpPurchaseDetail::class, 'kp_purchase_trans_id');
    }
}
