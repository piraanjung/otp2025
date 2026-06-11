<?php

namespace App\Models\KeptKaya;

use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\Admin\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpPurchaseTransactionDetail extends Model
{
    use HasFactory;

    protected $table = 'kp_purchase_transactions_details';

    protected $fillable = [
        'org_id_fk',
        'kp_purchase_trans_id',
        'kp_recycle_item_id',
        'kp_tbank_items_pricepoint_id',
        'recorder_id',
        'amount_in_units',
        'kp_units_idfk',
        'price_per_unit',
        'amount',
        'points',
        'carbon_saved',
        'image_path'
    ];


    protected $casts = [
        'amount_in_units' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'amount' => 'decimal:2',
        'points' => 'integer',
        'carbon_saved'    => 'decimal:4', // ✅ [เพิ่มใหม่] ทศนิยม 4 ตำแหน่งเพื่อความแม่นยำ
    ];

    // Relationships
    public function unit(): BelongsTo
    {
        return $this->belongsTo(KpTbankUnits::class, 'kp_units_idfk', 'id');
    }
    public function items_units()
    {
        return $this->belongsTo(KpTbankUnits::class, 'kp_tbank_items_units');
    }

    public function transaction()
    {
        return $this->belongsTo(KpPurchaseTransaction::class, 'kp_purchase_trans_id');
    }
    public function item()
    {
        return $this->belongsTo(KpTbankItems::class, 'kp_recycle_item_id');
    }

    public function pricePoint()
    {
        return $this->belongsTo(KpTbankItemsPriceAndPoint::class, 'kp_tbank_items_pricepoint_id');
    }



    public function user(): BelongsTo
    {
        return $this->belongsTo(KpUserWastePreference::class, 'kp_user_id_fk', 'id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorder_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(KpPurchaseTransactionDetail::class, 'kp_purchase_trans_id');
    }
}
