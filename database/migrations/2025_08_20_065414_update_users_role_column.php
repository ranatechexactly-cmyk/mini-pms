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
        // Update the column with the correct enum values and default
        \DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'developer') NOT NULL DEFAULT 'developer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the column to a simple string
        \DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(191) DEFAULT 'developer'");
    }
};
