<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class InvItemDetail extends Model
{
    use HasFactory;
    protected $table = 'inv_item_details';

    protected $fillable = [
        'inv_item_id_fk',
        'lot_number',
        'reference_doc',
        'supplier_id_fk',
        'serial_number',
        'initial_qty',
        'current_qty',
        'conversion_rate',
        'expire_date',
        'received_date',
        'received_by',
        'location_id_fk',
        'status'
    ];

    // เชื่อมกลับไปหาแม่
    public function item()
    {
        return $this->belongsTo(InvItem::class, 'inv_item_id_fk', 'id');
    }
    public function location()
    {
        return $this->belongsTo(InvLocation::class, 'location_id_fk');
    }
}
