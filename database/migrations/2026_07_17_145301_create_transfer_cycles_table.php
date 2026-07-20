<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_cycles', function (Blueprint $table): void {
            $table->id();

            $table->string('name');

            $table
                ->string('code', 50)
                ->unique();

            $table
                ->enum('transfer_type', [
                    'Annual',
                    'Special',
                    'Mutual',
                    'Administrative',
                ])
                ->default('Annual');

            $table->year('transfer_year');

            $table->date('application_open_date');

            $table->date('application_close_date');

            $table
                ->date('effective_from_date')
                ->nullable();

            $table
                ->unsignedInteger('minimum_service_years')
                ->default(3);

            $table
                ->unsignedInteger('maximum_preferences')
                ->default(3);

            $table
                ->boolean('allow_same_zone_preferences')
                ->default(true);

            $table
                ->boolean('allow_other_zone_preferences')
                ->default(true);

            $table
                ->boolean('allow_withdrawal')
                ->default(true);

            $table
                ->enum('status', [
                    'Draft',
                    'Published',
                    'Closed',
                    'Completed',
                    'Cancelled',
                ])
                ->default('Draft');

            $table
                ->text('instructions')
                ->nullable();

            $table
                ->text('eligibility_notes')
                ->nullable();

            $table
                ->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table
                ->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table
                ->timestamp('published_at')
                ->nullable();

            $table
                ->timestamp('closed_at')
                ->nullable();

            $table->timestamps();

            $table->index(
                [
                    'status',
                    'application_open_date',
                    'application_close_date',
                ],
                'tc_status_dates_idx'
            );

            $table->index(
                [
                    'transfer_year',
                    'transfer_type',
                ],
                'tc_year_type_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_cycles');
    }
};
