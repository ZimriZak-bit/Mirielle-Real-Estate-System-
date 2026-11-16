<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PAIR 3: viewings.
 *
 * One row per viewing request. The status flow is frozen by interface I10:
 * requested -> approved -> completed, requested -> rejected,
 * approved -> reschedule_requested -> approved, and any live viewing -> cancelled.
 * Cancelled and rejected rows stay in the table as history but no longer hold
 * their slot, so the clash check is done in code and not with a UNIQUE key.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viewings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();

            // The slot the viewing is booked for, and the time the client asked to move it to.
            $table->dateTime('scheduled_at');
            $table->dateTime('proposed_at')->nullable();

            $table->string('status', 30)->default('requested');
            $table->text('client_note')->nullable();
            $table->text('reschedule_note')->nullable();
            $table->text('management_note')->nullable();
            $table->dateTime('completed_at')->nullable();

            // Who approved, rejected, moved or completed it (audit trail).
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // The clash check reads this pair; the management queue filters on status.
            $table->index(['property_id', 'scheduled_at']);
            $table->index(['client_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viewings');
    }
};
