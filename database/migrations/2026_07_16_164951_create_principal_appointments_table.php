<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'principal_appointments',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('principal_profile_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table
                    ->foreignId('school_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table
                    ->enum('designation', [
                        'Principal',
                        'Deputy Principal',
                        'Assistant Principal',
                    ]);

                $table
                    ->enum('appointment_type', [
                        'Permanent',
                        'Acting',
                        'Temporary',
                        'Attached',
                    ])
                    ->default('Permanent');

                $table
                    ->string('appointment_number', 100)
                    ->nullable();

                $table
                    ->date('appointment_date');

                $table
                    ->date('start_date');

                $table
                    ->date('end_date')
                    ->nullable();

                $table
                    ->boolean('is_current')
                    ->default(false);

                $table
                    ->string('reason_for_end')
                    ->nullable();

                $table
                    ->text('remarks')
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

                $table->timestamps();

                $table->index([
                    'principal_profile_id',
                    'is_current',
                ]);

                $table->index([
                    'school_id',
                    'is_current',
                ]);

                $table->index([
                    'start_date',
                    'end_date',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('principal_appointments');
    }
};
