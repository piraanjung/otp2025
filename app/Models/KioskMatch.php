<?php

namespace App\Models;

use App\Models\KeptKaya\KpUserWastePreference;
use Illuminate\Database\Eloquent\Model;

class KioskMatch extends Model
{
    protected $table = 'kiosk_matches';

    protected $fillable = [
        "id",
        "kiosk_id",
        "user_id",
        "status",
        "expires_at"
    ];

    public function wastePreference()
{
    // เชื่อมไปหาตารางธนาคารขยะ
    return $this->hasOne(KpUserWastePreference::class);
}

public function kioskMatches()
{
    // ดูประวัติการสแกนตู้
    return $this->hasMany(KioskMatch::class);
}
}
