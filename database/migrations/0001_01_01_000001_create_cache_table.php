<?php
/**
 * Migration to create the `cache` and `cache_locks` tables.
 *
 * The `cache` table is used to store cached data with the following columns:
 * - `key`: A unique string identifier for the cache entry (primary key).
 * - `value`: The cached data stored as medium text.
 * - `expiration`: An integer representing the expiration timestamp of the cache entry.
 *
 * The `cache_locks` table is used to manage locks for cache operations with the following columns:
 * - `key`: A unique string identifier for the lock (primary key).
 * - `owner`: A string representing the owner of the lock.
 * - `expiration`: An integer representing the expiration timestamp of the lock.
 *
 * The `up` method creates these tables, while the `down` method drops them.
 */

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
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
