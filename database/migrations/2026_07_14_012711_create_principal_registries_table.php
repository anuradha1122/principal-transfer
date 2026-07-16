<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'principal_registries',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string('nic', 20);

                $table
                    ->string('normalized_nic', 12)
                    ->unique();

                $table
                    ->string('full_name')
                    ->nullable();

                $table
                    ->string('name_with_initials')
                    ->nullable();

                $table
                    ->foreignId('school_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                $table
                    ->enum('designation', [
                        'Principal',
                        'Deputy Principal',
                        'Assistant Principal',
                    ])
                    ->nullable();

                $table
                    ->string('employee_number', 50)
                    ->nullable();

                $table
                    ->enum('registration_status', [
                        'unregistered',
                        'registered',
                        'disabled',
                    ])
                    ->default('unregistered');

                $table
                    ->foreignId('registered_user_id')
                    ->nullable()
                    ->unique()
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->boolean('is_active')
                    ->default(true);

                $table
                    ->text('notes')
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
                    ->timestamp('registered_at')
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'registration_status',
                    'is_active',
                ]);

                $table->index([
                    'school_id',
                    'designation',
                ]);

                $table->index('employee_number');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('principal_registries');
    }
};
