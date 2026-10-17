<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpBarcodeList extends Model
{
    use HasFactory;

    protected $table = 'kp_barcodelist';
  protected $fillable = [
        'barcode_number',
        'product_name',
        'description'
    ];

}
