<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('xendit_invoice_id', 'midtrans_snap_token');
            $table->renameColumn('xendit_invoice_url', 'midtrans_redirect_url');
            $table->string('midtrans_order_id')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('midtrans_snap_token', 'xendit_invoice_id');
            $table->renameColumn('midtrans_redirect_url', 'xendit_invoice_url');
        });
    }
};
