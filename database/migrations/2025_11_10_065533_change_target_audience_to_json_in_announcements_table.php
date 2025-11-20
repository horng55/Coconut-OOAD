<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new JSON column
        Schema::table('announcements', function (Blueprint $table) {
            $table->json('target_audience_new')->nullable()->after('target_audience');
        });

        // Migrate existing enum values to JSON array format
        $announcements = DB::table('announcements')->get();
        foreach ($announcements as $announcement) {
            $audience = $announcement->target_audience;
            if ($audience === 'all') {
                $jsonValue = json_encode(['all']);
            } else {
                $jsonValue = json_encode([$audience]);
            }
            DB::table('announcements')
                ->where('id', $announcement->id)
                ->update(['target_audience_new' => $jsonValue]);
        }

        // Drop old column and rename new one
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('target_audience');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->renameColumn('target_audience_new', 'target_audience');
        });

        // SQLite doesn't support MODIFY COLUMN, so we skip this for SQLite
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE announcements MODIFY COLUMN target_audience JSON NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back enum column
        Schema::table('announcements', function (Blueprint $table) {
            $table->enum('target_audience_old', ['all', 'teachers', 'students', 'parents', 'class'])->default('all')->after('target_audience');
        });

        // Migrate JSON back to enum (take first element)
        $announcements = DB::table('announcements')->get();
        foreach ($announcements as $announcement) {
            $jsonValue = json_decode($announcement->target_audience, true);
            $enumValue = is_array($jsonValue) && count($jsonValue) > 0 ? $jsonValue[0] : 'all';
            DB::table('announcements')
                ->where('id', $announcement->id)
                ->update(['target_audience_old' => $enumValue]);
        }

        // Drop JSON column and rename enum
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('target_audience');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->renameColumn('target_audience_old', 'target_audience');
        });
    }
};
