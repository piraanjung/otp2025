<?php

namespace App\Models\FoodWaste;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodwasteBinStocks extends Model
{
    use HasFactory;
    use BelongsToOrganization;
    protected $table = 'foodwaste_bin_stocks';

    protected $fillable = [
        'id',
        'bin_code',
        'bin_type',
        'org_id_fk',
        'description',
        'status'
    ];

    public function foodwaste_bin(){
        return $this->hasOne(FoodwasteBin::class, 'bin_code_fk');
    }
}
