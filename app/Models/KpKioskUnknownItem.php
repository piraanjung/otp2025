<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpKioskUnknownItem extends Model
{
    use HasFactory;

    protected $table = 'kp_kiosk_unknown_items';

    protected $fillable = [
        'kp_purchase_trans_id', // เชื่อมกับ Transaction Header เดิม
        'org_id_fk',
        'kiosk_id_fk',
        'user_id_fk',
        'detected_label',       // เก็บชื่อคลาสขยะจากหน้าตู้ เช่น PET_unknown_NoScreen_NoCap
        'confidence_score',     // เก็บค่า % ความมั่นใจตอน AI ตรวจจับ
        'image_path',           // พาร์ทรูปภาพขยะแปลกปลอมใบนี้
        'status'                // สถานะ: 'pending_review', 'verified'
    ];

    protected $casts = [
        'confidence_score' => 'integer',
    ];
}