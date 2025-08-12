<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpPurchaseTransaction extends Model
{
    use HasFactory;

    protected $table = 'kp_purchase_transactions';

    protected $fillable = [
        'kp_u_trans_no',
        'kp_user_id_fk',
        'transaction_date',
        'total_weight',
        'total_amount',
        'total_points',
        'status',
        'recorder_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_weight' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_points' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        // assuming kp_user_keptkaya_infos is a model for your members
        return $this->belongsTo(UserWastePreference::class, 'kp_user_id_fk', 'id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorder_id', 'id');
    }
    
    public function details(): HasMany
    {
        return $this->hasMany(KpPurchaseDetail::class, 'kp_purchase_trans_id');
    }
}
