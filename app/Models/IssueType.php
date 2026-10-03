<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class IssueType extends Model
{
    use HasFactory;

    protected $table = 'issue_types';

    protected $fillable = [
        'system_type',
        'name',
        'is_active',
        'is_suggested',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_suggested' => 'boolean',
    ];
}
