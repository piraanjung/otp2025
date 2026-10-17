<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
class InvUnit extends Model
{
    use HasFactory; use BelongsToOrganization;
    
    protected $table = 'inv_units';
    protected $fillable = ['org_id_fk', 'name'];
}