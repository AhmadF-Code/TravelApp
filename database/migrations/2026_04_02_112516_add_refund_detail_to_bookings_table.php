<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'refund_completed_at')) {
                $table->timestamp('refund_completed_at')->nullable()->after('refund_amount');
            }
            if (!Schema::hasColumn('bookings', 'refund_proof_image')) {
                $table->string('refund_proof_image')->nullable()->after('refund_completed_at');
            }
            if (!Schema::hasColumn('bookings', 'refund_processed_by_id')) {
                $table->foreignId('refund_processed_by_id')->nullable()->constrained('users')->after('refund_proof_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['refund_processed_by_id']);
            $table->dropColumn(['refund_completed_at', 'refund_proof_image', 'refund_processed_by_id']);
        });
    }
};
