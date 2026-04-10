<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->string('booking_code', 50)->nullable()->index();
            $table->string('action', 50);                // e.g. 'auto_expired', 'manual_paid', 'cancel', 'refund'
            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('triggered_by', 80)->default('system'); // 'system', 'admin:{name}', 'xendit_webhook'
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_audit_logs');
    }
};
