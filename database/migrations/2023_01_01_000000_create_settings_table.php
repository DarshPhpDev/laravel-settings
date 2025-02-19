<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the settings table for storing application configuration.
 */
class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     * Creates a new table to store key-value settings.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('settings.database.table', 'settings'), function (Blueprint $table) {
            $table->string('key')->primary();        // Unique identifier for the setting
            $table->text('value')->nullable();       // The setting value, allowing null
            $table->timestamps();                    // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     * Drops the settings table.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('settings.database.table', 'settings'));
    }
}