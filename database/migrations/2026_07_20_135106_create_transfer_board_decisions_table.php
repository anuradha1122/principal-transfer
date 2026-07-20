<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'transfer_board_decisions',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('transfer_application_id')
                    ->unique()
                    ->constrained('transfer_applications')
                    ->cascadeOnDelete();

                $table
                    ->foreignId('reviewer_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->enum('decision', [
                        'Pending',
                        'Approved',
                        'Rejected',
                        'Waitlisted',
                    ])
                    ->default('Pending');

                $table
                    ->foreignId('recommended_school_id')
                    ->nullable()
                    ->constrained('schools')
                    ->nullOnDelete();

                $table
                    ->date('effective_date')
                    ->nullable();

                $table
                    ->string('appointment_type', 100)
                    ->nullable();

                $table
                    ->string('decision_reference', 150)
                    ->nullable();

                $table
                    ->text('remarks')
                    ->nullable();

                $table
                    ->text('rejection_reason')
                    ->nullable();

                $table
                    ->text('waitlist_reason')
                    ->nullable();

                $table
                    ->timestamp('review_started_at')
                    ->nullable();

                $table
                    ->timestamp('decided_at')
                    ->nullable();

                $table->timestamps();

                $table->index(
                    [
                        'decision',
                        'reviewer_id',
                    ],
                    'board_decision_reviewer_idx'
                );

                $table->index(
                    [
                        'recommended_school_id',
                        'effective_date',
                    ],
                    'board_school_effective_idx'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'transfer_board_decisions'
        );
    }
};
