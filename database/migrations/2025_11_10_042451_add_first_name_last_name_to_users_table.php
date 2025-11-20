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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
        });

        // Migrate existing name data to first_name and last_name
        DB::table('users')->get()->each(function ($user) {
            $nameParts = explode(' ', $user->name, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
            
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ]);
        });

        // Make first_name and last_name required, then drop name column
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
        });

        // Migrate first_name and last_name back to name
        DB::table('users')->get()->each(function ($user) {
            $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'name' => $fullName,
                ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
