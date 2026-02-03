<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
class Kiosk extends Model
{
    use HasFactory;
    use BelongsToOrganization;
    // ระบุว่า id ไม่ใช่ auto-increment และเป็น string
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'org_id_fk',
        'name',
        'location',
        'lat',
        'lng',
        'status',
        'total_waste_count',
        'last_online_at'
    ];

    public function waste_types(){

    }
}
