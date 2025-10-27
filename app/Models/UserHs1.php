<?php

namespace App\Models;

use App\Models\Admin\District;
use App\Models\Admin\Organization;
use App\Models\Admin\Province;
use App\Models\Admin\Staff;
use App\Models\Admin\Subzone;
use App\Models\Admin\Tambon;
use App\Models\Admin\Zone;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\KeptKaya\WasteBin;
use App\Models\KeptKaya\AnnualCollectionPayment;
use App\Models\Tabwater\TwMeterInfos;
use App\Models\Tabwater\UndertakerSubzone;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\FoodWaste\FoodWasteUserPreference;
use App\Models\FoodWaste\FoodWasteBin;
use App\Models\Roles\HS1Permission;
use App\Models\Roles\HS1Role;

class UserHs1 extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    public $connection = 'envsogo_hs1';
    protected $fillable = [
        'id',
        'org_id_fk',
        'username',
        'prefix',
        'firstname',
        'lastname',
        'name',
        'email',
        'password',
        'id_card',
        'line_id',
        'image',
        'phone',
        'gender',
        'role_id',
        'address',
        'zone_id',
        'subzone_id',
        'tambon_code',
        'district_code',
        'province_code',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $table = 'users';



    public function roles() 
    {
        return $this->morphToMany(
            HS1Role::class, // ใช้ HSRole Model ที่กำหนด connection
            'model',
            'model_has_roles',
            'model_id',
            'role_id'
        );
    }
    
    /**
     * Override ความสัมพันธ์ permissions เพื่อใช้ HSPermission Model (ถ้าใช้)
     */
    public function permissions()
    {
        return $this->morphToMany(
            HS1Permission::class, // ใช้ HSPermission Model
            'model',
            'model_has_permissions',
            'model_id',
            'permission_id'
        );
    }
    public function org(){
        return $this->belongsTo(Organization::class, 'org_id_fk', 'id');
    }

    public function user_zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    public function user_subzone()
    {
        return $this->belongsTo(Subzone::class, 'subzone_id', 'id');
    }

    public function undertaker_subzone()
    {
        return $this->hasMany(UndertakerSubzone::class, 'twman_id');
    }

    public function usermeterinfos()
    {
        return $this->hasMany(TwMeterInfos::class, 'user_id', 'id');
    }
    public function user_province()
    {
        return $this->belongsTo(Province::class, 'province_code');
    }

    public function user_district()
    {
        return $this->belongsTo(District::class, 'district_code');
    }

    public function user_tambon()
    {
        return $this->belongsTo(Tambon::class, 'tambon_code');
    }


    public function wastePreference()
    {
        return $this->hasOne(KpUserWastePreference::class);
    }

    public function wasteBins()
    {
        return $this->hasMany(WasteBin::class,'user_id');
    }

    public function annualCollectionPayments()
    {
        return $this->hasMany(AnnualCollectionPayment::class);
    }

    // New Relationship for Waste Bank Transactions where user is a member
    public function wasteBankTransactionsAsMember()
    {
        return $this->hasMany(WasteBankTransaction::class, 'user_id');
    }

    // New Relationship for Waste Bank Transactions where user is a staff
    public function wasteBankTransactionsAsStaff()
    {
        return $this->hasMany(WasteBankTransaction::class, 'staff_id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function foodwastePreference()
    {
        return $this->hasOne(FoodWasteUserPreference::class);
    }

    public function foodwasteBins()
    {
        return $this->hasMany(FoodWasteBin::class,'u_pref_id_fk');
    }
    
}
