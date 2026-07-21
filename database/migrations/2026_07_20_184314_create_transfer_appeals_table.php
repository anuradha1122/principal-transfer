<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_appeals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transfer_application_id')
                ->constrained('transfer_applications')
                ->restrictOnDelete();

            $table->foreignId('principal_profile_id')
                ->constrained('principal_profiles')
                ->restrictOnDelete();

            $table->string('appeal_number', 50)->unique();

            $table->string('appeal_reason', 150);
            $table->text('appeal_details');
            $table->text('requested_outcome');

            $table->string('status', 40)->default('Draft');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('review_started_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('resubmitted_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->foreignId('reviewer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('clarification_request')->nullable();
            $table->text('clarification_response')->nullable();

            $table->string('decision_outcome', 40)->nullable();
            $table->text('decision_remarks')->nullable();
            $table->text('rejection_reason')->nullable();

            /*
             * Revised outcome fields are intentionally stored separately.
             * They must never overwrite the original TransferBoardDecision.
             */
            $table->foreignId('revised_school_id')
                ->nullable()
                ->constrained('schools')
                ->restrictOnDelete();

            $table->date('revised_effective_date')->nullable();
            $table->string('revised_appointment_type', 100)->nullable();
            $table->string('revised_decision_reference', 100)->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(
                ['transfer_application_id', 'status'],
                'ta_app_status_idx'
            );

            $table->index(
                ['principal_profile_id', 'status'],
                'pa_principal_status_idx'
            );

            $table->index(
                ['status', 'submitted_at'],
                'ta_status_submitted_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_appeals');
    }
};
