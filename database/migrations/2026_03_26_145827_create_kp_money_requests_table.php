<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('kp_money_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('kp_money_requests', 'batch_id_fk')) {
                $table->unsignedBigInteger('batch_id_fk')->nullable()->after('user_id');
                $table->foreign('batch_id_fk')->references('id')->on('kp_withdraw_batches')->onDelete('set null');
            }

            if (!Schema::hasColumn('kp_money_requests', 'hold_status')) {
                $table->enum('hold_status', ['held', 'released', 'settled'])->default('held')->after('status');
            }

            if (!Schema::hasColumn('kp_money_requests', 'proxy_id_card')) {
                $table->string('proxy_id_card', 20)->nullable()->after('proxy_name');
            }

            if (!Schema::hasColumn('kp_money_requests', 'proxy_relationship')) {
                $table->string('proxy_relationship', 100)->nullable()->after('proxy_id_card');
            }

            if (!Schema::hasColumn('kp_money_requests', 'signature_path')) {
                $table->string('signature_path', 255)->nullable()->after('completed_at');
            }

            if (!Schema::hasColumn('kp_money_requests', 'payout_method')) {
                $table->enum('payout_method', ['cash', 'transfer'])->default('cash')->after('signature_path');
            }

            if (!Schema::hasColumn('kp_money_requests', 'payout_note')) {
                $table->text('payout_note')->nullable()->after('payout_method');
            }
        });
    }

    public function down(): void {
        Schema::table('kp_money_requests', function (Blueprint $table) {
            $table->dropForeign(['batch_id_fk']);
            $table->dropColumn([
                'batch_id_fk',
                'hold_status',
                'proxy_id_card',
                'proxy_relationship',
                'signature_path',
                'payout_method',
                'payout_note',
            ]);
        });
    }
};