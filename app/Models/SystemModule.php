<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemModule extends Model
{
    use HasFactory;

    protected $table = 'system_modules';

    protected $fillable = [
        'system_type',
        'title',
        'icon',
        'description',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // เชื่อมความสัมพันธ์กับประเภทปัญหา (IssueType)
    public function issueTypes()
    {
        return $this->hasMany(IssueType::class, 'system_type', 'system_type');
    }
}