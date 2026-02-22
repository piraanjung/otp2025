<?php

namespace App\Models\FoodWaste;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodWasteUserPreference extends Model
{
    use HasFactory;

    protected $table = 'foodwaste_user_preferences';

    protected $fillable = [
        'id',
        'user_id',
        'is_foodwaste_bank'
    ];

    protected $casts = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foodwaste_bins()
    {
        return $this->hasMany(FoodWasteBin::class,'u_pref_id_fk');
    }

    public function purchaseTransactions(): HasMany
    {
        // Assuming kp_user_id_fk is the foreign key in the kp_purchase_transactions table
        return $this->hasMany(KpPurchaseTransaction::class, 'kp_user_w_pref_id_fk', 'id');
    }

    public function kp_account(){
        return $this->hasOne(KPAccounts::class, 'u_wpref_id_fk');
    }
}