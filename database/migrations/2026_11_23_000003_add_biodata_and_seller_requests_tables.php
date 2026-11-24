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
            $table->text('description')->nullable()->after('location');
            $table->string('opening_hours')->default('08:00 - 17:00 WIB')->after('description');
            $table->string('contact_phone')->nullable()->after('opening_hours');
            $table->string('bank_info')->nullable()->after('contact_phone');
        });

        Schema::create('seller_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('applicant_name');
            $table->string('email');
            $table->string('phone');
            $table->string('stall_name');
            $table->string('stall_code')->nullable();
            $table->string('stall_location');
            $table->text('stall_description')->nullable();
            $table->string('opening_hours')->default('08:00 - 17:00 WIB');
            $table->string('contact_phone')->nullable();
            $table->string('bank_info')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_requests');

        Schema::table('stalls', function (Blueprint $table) {
            $table->dropColumn(['description', 'opening_hours', 'contact_phone', 'bank_info']);
        });
    }
};
