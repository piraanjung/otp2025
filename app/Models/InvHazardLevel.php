<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvHazardLevel extends Model
{
    use HasFactory;
    protected $table = 'inv_hazard_levels';
    protected $fillable = [
    'org_id_fk',
    'name',
    'code',
    'description',
    'image_path'];
}
