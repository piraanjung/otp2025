<?php

namespace App\Models\FoodWaste;

use Illuminate\Database\Eloquent\Model;

class MealItem extends Model {
    protected $table = 'meal_items';
    protected $fillable = ['meal_log_id', 'menu_name', 'category', 'calories', 'status'];

    // 🌟 รายการอาหารนี้ เป็นของมื้อไหน
    public function mealLog() {
        return $this->belongsTo(MealLog::class);
    }
}
