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
        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `booking_status` ENUM('pending', 'confirmed', 'cancelled', 'completed', 'rejected', 'waiting for media', 'confirmed without media') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `media_status` ENUM('pending', 'accepted', 'kept_pending', 'rejected', 'not_required') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `booking_status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `media_status` ENUM('pending', 'accepted', 'kept_pending', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};
