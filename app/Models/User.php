<?php

namespace App\Models;

use App\Models\Admin\District;
use App\Models\Admin\Province;
use App\Models\Admin\Staff;
use App\Models\Admin\Subzone;
use App\Models\Admin\Tambon;
use App\Models\Admin\Zone;
use App\Models\KeptKaya\UserWastePreference;
use App\Models\KeptKaya\WasteBin;
use App\Models\KeptKaya\AnnualCollectionPayment;
use App\Models\KeptKaya\KpPurchaseTransaction;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'settings_id_fk',
        'username',
        'prefix',
        'firstname',
        'lastname',
        'name',
        'email',
        'password',
        'id_card',
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

    public function user_profile()
    {
        return $this->hasOne('App\Models\User', 'user_id', 'id');
    }
    public function usercategory()
    {
        return $this->belongsTo('App\Usercategory', 'user_cat_id');
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
        return $this->hasMany(UserMerterInfo::class, 'user_id', 'id');
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
        return $this->hasOne(UserWastePreference::class);
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

     public function purchaseTransactions(): HasMany
    {
        // Assuming kp_user_id_fk is the foreign key in the kp_purchase_transactions table
        return $this->hasMany(KpPurchaseTransaction::class, 'kp_user_id_fk', 'id');
    }
    
}
