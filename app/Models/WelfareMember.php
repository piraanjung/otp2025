<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelfareMember extends Model
{
    use HasFactory;

    protected $table = 'welfare_members';

    protected $fillable = [
        'org_id_fk',
        'user_id',
        'membership_type',
        'status',
        'registered_at',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}