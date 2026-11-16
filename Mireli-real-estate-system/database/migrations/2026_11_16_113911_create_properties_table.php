<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->integer('bedrooms');
            $table->string('size');
            $table->text('amenities')->nullable();
            $table->text('description')->nullable();
            
            $table->decimal('price', 15, 2); 
            $table->decimal('deposit_amount', 15, 2);
            
            $table->string('status')->default('available'); 
            
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('properties');
    }
};