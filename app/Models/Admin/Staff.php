<?php

namespace App\Models\Admin;

use App\Models\Tabwater\UndertakerSubzone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
use Spatie\Permission\Traits\HasRoles; // 1. นำเข้า Trait ของ Spatie
class Staff extends Model
{
    use HasFactory; use BelongsToOrganization;
    use HasRoles; // 2. เรียกใช้งาน Trait ตรงนี้
    protected $table = 'staffs';
    // protected $primaryKey = 'user_id';

    protected $fillable = [
        'id',
        'user_id',
        'org_id_fk',
        'status',
        'deleted',
        'created_at',
        'updated_at',
        'old_chashier_id',
        'old_user_id'
    ];

    function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id_fk', 'id');
    }

    public function undertaker_subzones()
    {
        return $this->hasMany(UndertakerSubzone::class, 'staff_id', 'id');
    }


    
    
}
