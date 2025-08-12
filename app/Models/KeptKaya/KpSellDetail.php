<?php

namespace App\Models\KeptKaya;

use App\Models\Admin\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpSellDetail extends Model
{
    use HasFactory;

    protected $table = 'kp_sell_details';

    protected $fillable = [
        'kp_sell_trans_id',
        'kp_recycle_item_id',
        'recorder_id',
        'weight',
        'price_per_unit',
        'amount',
        'comment',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(KpSellTransaction::class, 'kp_sell_trans_id', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(KpTbankItems::class, 'kp_recycle_item_id');
    }
    
    public function recorder(): BelongsTo
    {
        // Assuming recorder_id is a foreign key to the 'staffs' table's 'id'
        return $this->belongsTo(Staff::class, 'recorder_id', 'id');
    }
}
