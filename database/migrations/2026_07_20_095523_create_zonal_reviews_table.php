<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zonal_reviews', function (Blueprint $table): void {
            $table->id();

            $table
                ->foreignId('transfer_application_id')
                ->constrained('transfer_applications')
                ->cascadeOnDelete();

            $table
                ->foreignId('zone_id')
                ->constrained('zones')
                ->restrictOnDelete();

            $table
                ->foreignId('reviewer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('recommendation', 100)->nullable();
            $table->string('decision', 30)->nullable();
            $table->text('remarks')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamp('review_started_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->unique(
                'transfer_application_id',
                'zonal_reviews_application_unique'
            );

            $table->index(
                ['zone_id', 'decision'],
                'zonal_reviews_zone_decision_idx'
            );

            $table->index(
                ['reviewer_id', 'reviewed_at'],
                'zonal_reviews_reviewer_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zonal_reviews');
    }
};
