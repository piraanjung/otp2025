<?php

namespace App\Models\Tabwater;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SequenceNumber extends Model
{
    use HasFactory;
    use BelongsToOrganization;
    protected $table = "sequence_number";
    protected $fillable = ["meter_id","user_id","meternumber","meter_address","undertake_zone_id",
    "undertake_subzone_id","acceptace_date", "status","metertype_id","owe_count", "payment_id",
    "discounttype", "recorder_id"];
}
