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
        Schema::table('users', function (Blueprint $table) {
            // $table->foreignId('org_id_fk')->constrained('organizations')
            //     ->after('id')->onDelete('cascade'); 

            $table->foreignId('zone_id')->constrained('zones')->comment('หมู่หรือชุมชน แล้วแต่พื้นที่จะแยก')
                ->after('lastname')->nullable()->onDelete('cascade'); 
            
            $table->foreignId('subzone_id')->constrained('subzones')
                ->after('lastname')->nullable()->onDelete('cascade'); 
            
            $table->foreignId('tambon_code')->constrained('tambons')
                ->after('lastname')->nullable()->onDelete('cascade'); 
            $table->foreignId('district_code')->constrained('districts')
                ->after('lastname')->nullable()->onDelete('cascade'); 
            $table->foreignId('province_code')->constrained('provinces')
                ->after('lastname')->nullable()->onDelete('cascade'); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->dropForeign(['org_id_fk']);
            // $table->dropColumn('org_id_fk');

            $table->dropForeign(['zone_id']);
            $table->dropColumn('zone_id');

            $table->dropForeign(['subzone_id']);
            $table->dropColumn('subzone_id');

            $table->dropForeign(['tambon_code']);
            $table->dropColumn('tambon_code');
            $table->dropForeign(['district_code']);
            $table->dropColumn('district_code');
            $table->dropForeign(['province_code']);
            $table->dropColumn('province_code');
        });
    }
};
