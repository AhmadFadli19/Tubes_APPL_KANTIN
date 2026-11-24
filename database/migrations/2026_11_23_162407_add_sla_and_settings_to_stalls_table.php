<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->string('culinary_category')->nullable()->default('Makanan Berat & Olahan Ayam')->after('description');
            $table->integer('max_active_queues')->default(15)->after('culinary_category');
            $table->integer('sla_minutes')->default(8)->after('max_active_queues');
            $table->boolean('auto_accept_qris')->default(true)->after('sla_minutes');
            $table->boolean('sound_notification')->default(true)->after('auto_accept_qris');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->dropColumn([
                'culinary_category',
                'max_active_queues',
                'sla_minutes',
                'auto_accept_qris',
                'sound_notification',
            ]);
        });
    }
};
