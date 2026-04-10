<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            // Replace boolean is_active with proper status enum + versioning
            $table->string('status', 20)->default('draft')->after('is_active');
            // ACTIVE = live on website | DRAFT = being edited | ARCHIVED = old versions
            $table->unsignedInteger('version')->default(1)->after('status');
            $table->text('change_summary')->nullable()->after('version'); // What was changed in this version
            $table->string('published_by', 100)->nullable()->after('change_summary');
            $table->timestamp('published_at')->nullable()->after('published_by');
        });

        // Migrate existing data: if is_active = true → status = 'active', else → 'archived'
        DB::table('landing_pages')->where('is_active', true)->update(['status' => 'active']);
        DB::table('landing_pages')->where('is_active', false)->update(['status' => 'archived']);
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn(['status', 'version', 'change_summary', 'published_by', 'published_at']);
        });
    }
};
