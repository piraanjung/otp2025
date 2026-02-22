<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('inv_items', function (Blueprint $table) {
        $table->id();

        // ✅ เชื่อม org
        $table->unsignedBigInteger('org_id_fk');

        // เชื่อมหมวดหมู่ (inv_categories)
        $table->unsignedBigInteger('inv_category_id_fk')->nullable();
        $table->foreign('inv_category_id_fk')->references('id')->on('inv_categories')->onDelete('set null');

        $table->string('name');
        $table->string('code')->nullable(); // Barcode
        $table->text('description')->nullable();
        $table->integer('min_stock')->default(0);
        $table->foreign('unit')->references('id')->on('inv_units')->onDelete('set null');

        $table->string('item_type')->default('GENERAL'); // 'GENERAL' หรือ 'CHEMICAL'
    $table->string('code')->unique(); // รหัสสินค้า (A001, B001)
    $table->string('name');
    $table->string('unit'); // หน่วยนับ
    $table->foreignId('inv_category_id_fk')->nullable()->constrained('inv_categories');
    $table->integer('min_stock')->default(0);
    $table->string('image_path')->nullable();

        // --- Logic ---
        $table->boolean('is_chemical')->default(false);
        $table->boolean('return_required')->default(false);
        $table->string('image_path')->nullable();

        // --- เคมี ---
        $table->date('expire_date')->nullable();

        $table->string('cas_number')->nullable(); // CAS No.
    $table->string('chemical_formula')->nullable(); // สูตรเคมี
    $table->string('grade')->nullable(); // เกรด (AR, Lab)
    $table->string('storage_condition')->nullable(); // การเก็บรักษา
    $table->string('msds_link')->nullable(); // ลิงก์ไฟล์ MSDS

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_items');
    }
};
