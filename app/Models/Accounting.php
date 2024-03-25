<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    use HasFactory;

    protected $table = 'accounting';

    public function invoice_old()
    {
        return $this->hasMany(InvoiceOld::class, 'receipt_id', 'id');
    }

}
