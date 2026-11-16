<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * STARTER PACK: properties and catalogue. Pair 2 (Claire & Norman) owns this table.
 *
 * It is committed here because the starter pack migrations were never copied into
 * this repository and the viewings table has nothing to point at without it.
 * Pair 2: keep this as the ONE properties migration and add your changes as new
 * migration files. Do not add a second create_properties_table migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location')->index();
            $table->unsignedTinyInteger('bedrooms')->default(1);
            $table->string('size', 50)->nullable();
            $table->text('amenities')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('deposit_amount', 12, 2);
            $table->string('status', 20)->default('available')->index();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
