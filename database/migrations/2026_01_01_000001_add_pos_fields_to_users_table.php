<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add POS-specific columns to the users table:
     * - google_id for OAuth login
     * - role enum for access control
     * - avatar for profile picture
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id', 255)->nullable()->after('email');
            $table->enum('role', ['admin', 'petugas'])->default('petugas')->after('google_id');
            $table->string('avatar', 255)->nullable()->after('role');
            // Allow null password for Google OAuth users
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'role', 'avatar']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
