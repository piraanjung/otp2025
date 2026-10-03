<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    // เพิ่มฟิลด์ทั้งหมดที่อนุญาตให้บันทึกแบบ Mass Assignment ตรงนี้ครับ
    protected $fillable = [
        'org_id_fk',
        'department',
        'name',
        'contact_person',
        'phone',
        'address',
    ];
}