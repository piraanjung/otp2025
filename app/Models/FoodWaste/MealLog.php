<?php

namespace App\Models\FoodWaste;

use Illuminate\Database\Eloquent\Model;

class MealLog extends Model {
    protected $table = 'foodwast_meal_logs';
    protected $fillable = ['user_id', 'photo_path', 'total_calories' , 'status'];

    // 🌟 1 มื้อ มีได้หลายรายการอาหาร
    public function items() {
        return $this->hasMany(MealItem::class);
    }
}
