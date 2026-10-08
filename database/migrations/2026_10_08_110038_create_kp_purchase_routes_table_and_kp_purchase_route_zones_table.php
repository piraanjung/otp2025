<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ตารางกลุ่มเขตรับซื้อขยะ
        Schema::create('kp_purchase_routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id_fk');
            $table->string('route_name'); // เช่น "เขตที่ 1 (โซน ม.1 + ม.2)"
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('org_id_fk')
                  ->references('id')
                  ->on('organizations')
                  ->onDelete('cascade');
        });

        // 2. ตารางจับคู่เขตรับซื้อกับโซนสมาชิก (Pivot Table)
        Schema::create('kp_purchase_route_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kp_purchase_route_id');
            $table->unsignedBigInteger('zone_id'); // อ้างอิง id จากตาราง zones เดิม
            $table->timestamps();

            $table->foreign('kp_purchase_route_id')
                  ->references('id')
                  ->on('kp_purchase_routes')
                  ->onDelete('cascade');

            $table->foreign('zone_id')
                  ->references('id')
                  ->on('zones')
                  ->onDelete('cascade');
        });

        // 3. เพิ่ม FK kp_purchase_route_id เข้าตาราง kp_purchase_transactions
        Schema::table('kp_purchase_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('kp_purchase_route_id')->nullable()->after('org_id_fk');
            $table->foreign('kp_purchase_route_id')
                  ->references('id')
                  ->on('kp_purchase_routes')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('kp_purchase_transactions', function (Blueprint $table) {
            $table->dropForeign(['kp_purchase_route_id']);
            $table->dropColumn('kp_purchase_route_id');
        });
        Schema::dropIfExists('kp_purchase_route_zones');
        Schema::dropIfExists('kp_purchase_routes');
    }
};