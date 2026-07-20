<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provincial_reviews', function (Blueprint $table): void {
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
                    'Returned to Zone',
                ])
                ->default('Pending');

            $table
                ->string('recommendation', 255)
                ->nullable();

            $table
                ->text('remarks')
                ->nullable();

            $table
                ->text('rejection_reason')
                ->nullable();

            $table
                ->text('return_reason')
                ->nullable();

            $table
                ->timestamp('review_started_at')
                ->nullable();

            $table
                ->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'decision',
                'reviewer_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provincial_reviews');
    }
};
