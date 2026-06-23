<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvItemDetail extends Model
{
    use HasFactory;
    protected $table = 'inv_item_details';

    protected $fillable = [
        'inv_item_id_fk', 'lot_number', 'serial_number',
        'initial_qty', 'current_qty',
        'expire_date', 'received_date', 'status'
    ];

    // เชื่อมกลับไปหาแม่
    public function item()
    {
        return $this->belongsTo(InvItem::class, 'inv_item_id_fk', 'id');
    }
}