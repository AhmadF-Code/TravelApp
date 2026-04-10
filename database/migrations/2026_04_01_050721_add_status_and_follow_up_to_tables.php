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
        if (!Schema::hasColumn('schedules', 'status')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->string('status')->default('active')->after('quota');
            });
        }

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'follow_up_status')) {
                $table->string('follow_up_status')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bookings', 'follow_up_note')) {
                $table->text('follow_up_note')->nullable()->after('follow_up_status');
            }
            if (!Schema::hasColumn('bookings', 'refund_amount')) {
                $table->decimal('refund_amount', 15, 2)->default(0)->after('follow_up_note');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['follow_up_status', 'follow_up_note', 'refund_amount']);
        });
    }
};
