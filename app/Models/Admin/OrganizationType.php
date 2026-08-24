<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;


class OrganizationType extends Model
{
    protected $fillable = ['name', 'code', 'status'];

    protected $table = 'organization_types';

    // ความสัมพันธ์: หนึ่งประเภทมีได้หลายหน่วยงาน
    public function organizations()
    {
        return $this->hasMany(Organization::class, 'org_type_id');
    }
}
