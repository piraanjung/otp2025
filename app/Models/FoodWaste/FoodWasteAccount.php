<?php

namespace App\Models\FoodWaste;

use App\Models\FoodWaste\FoodWasteUserPreference;
use Illuminate\Database\Eloquent\Model;

class FoodWasteAccount extends Model {
    protected $fillable = ['fw_pref_id_fk', 'points_balance', 'money_balance', 'total_weight_contributed'];

    public function preference() {
        return $this->belongsTo(FoodWasteUserPreference::class, 'fw_pref_id_fk');
    }
}
