<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use PhpParser\Node\Expr\FuncCall;

trait ManagesTenantConnection
{
    /**
     * สร้าง Instance ใหม่ของ Model นี้ และกำหนด Connection Database ให้
     *
     * @param string $tenantId 'tenant_a' หรือ 'tenant_b'
     * @return Model|static
     * @throws \Exception
     */
    public static function setTenantConnection(string $tenantId): Model
    {
        $connectionName = $tenantId;

        if (Config("database.connections.{$connectionName}")) {
            // สร้าง instance ของ Model ที่เรียกใช้ Trait นี้
            $instance = new static; 
            // กำหนด Connection ให้กับ instance นั้น
            $instance->setConnection($connectionName);
            return $instance;
        } else {
             throw new \Exception("Database connection {$connectionName} not configured.");
        }
    }

    public static function configConnection($connectionName){
          // *** ขั้นตอนที่ 1: ตรวจสอบและเปลี่ยน Connection หลักของ Laravel ชั่วคราว ***
    // (วิธีนี้จะทำให้ทุก Query ที่ไม่ได้ระบุ Connection ใช้ค่านี้)
    if (Config::has("database.connections.{$connectionName}")) {
        // บันทึกค่า Default Connection เดิมไว้ ถ้าต้องการเปลี่ยนกลับทีหลัง
        $originalConnection = Config::get('database.default'); 
        
        Config::set('database.default', $connectionName); // 👈 เปลี่ยน Default!
    } else {
        // จัดการข้อผิดพลาดถ้า Connection ไม่มีอยู่จริง
        throw new \Exception("Tenant connection {$connectionName} not configured.");
    }
    }
}
