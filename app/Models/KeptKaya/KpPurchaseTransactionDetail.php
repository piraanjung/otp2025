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
        'kp_purchase_trans_id',         // เชื่อมไปหา Header (ซึ่งมี org_id_fk อยู่แล้ว)
        'kp_recycle_item_id',
        'kp_units_idfk',                    // <--- เพิ่มตัวนี้ (สำคัญ! เพราะ Controller ส่งมา)
        'kp_tbank_items_pricepoint_id',
        // 'recorder_id',               // แนะนำให้เอาออก ถ้าคนบันทึกคือคนเดียวกับ Header
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
