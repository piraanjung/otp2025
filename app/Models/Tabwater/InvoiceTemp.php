<?php

namespace App\Models\Tabwater;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceTemp extends Model
{
    use HasFactory;
    protected $table = 'invoice_temp';

    protected $fillable = [
                'id',
                'meter_id_fk',
                'inv_period_id_fk',
                'lastmeter',
                'currentmeter',
                'water_used',
                'paid',
                'vat',
                'totalpaid',
                'status',
                'recorder_id'
    ];
}
