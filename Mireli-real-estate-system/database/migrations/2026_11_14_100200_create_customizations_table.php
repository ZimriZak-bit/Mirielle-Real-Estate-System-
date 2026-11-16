<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PAIR 3: customizations (stretch goal).
 *
 * One set of preferences per client per property, editable, and visible to
 * management (interface I7 decides who may customize: the house is Reserved or
 * Sold and the client is its buyer).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();

            $table->string('furniture_arrangement')->nullable();
            $table->string('colour_scheme')->nullable();
            $table->string('furnishing_status', 30)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['property_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customizations');
    }
};
