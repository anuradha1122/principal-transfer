<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'principal_profiles',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('user_id')
                    ->unique()
                    ->constrained()
                    ->restrictOnDelete();

                $table
                    ->foreignId('principal_registry_id')
                    ->nullable()
                    ->unique()
                    ->constrained('principal_registries')
                    ->nullOnDelete();

                $table
                    ->string('nic', 20)
                    ->unique();

                $table
                    ->string('employee_number', 50)
                    ->nullable()
                    ->unique();

                $table->string('full_name');

                $table
                    ->string('name_with_initials')
                    ->nullable();

                $table
                    ->enum('gender', [
                        'Male',
                        'Female',
                        'Other',
                    ])
                    ->nullable();

                $table
                    ->date('date_of_birth')
                    ->nullable();

                $table
                    ->string('mobile_number', 30)
                    ->nullable();

                $table
                    ->string('alternate_number', 30)
                    ->nullable();

                $table
                    ->string('personal_email')
                    ->nullable();

                $table
                    ->string('address_line_1')
                    ->nullable();

                $table
                    ->string('address_line_2')
                    ->nullable();

                $table
                    ->string('city', 100)
                    ->nullable();

                $table
                    ->string('postal_code', 20)
                    ->nullable();

                $table
                    ->enum('service_category', [
                        'Sri Lanka Principals Service',
                        'Sri Lanka Education Administrative Service',
                        'Other',
                    ])
                    ->default('Sri Lanka Principals Service');

                $table
                    ->string('service_grade', 100)
                    ->nullable();

                $table
                    ->date('first_appointment_date')
                    ->nullable();

                $table
                    ->date('principal_service_entry_date')
                    ->nullable();

                $table
                    ->date('retirement_date')
                    ->nullable();

                $table
                    ->enum('employment_status', [
                        'Active',
                        'Retired',
                        'Transferred Out',
                        'Suspended',
                        'Deceased',
                        'Other',
                    ])
                    ->default('Active');

                $table
                    ->text('qualifications_summary')
                    ->nullable();

                $table
                    ->text('notes')
                    ->nullable();

                $table
                    ->boolean('profile_completed')
                    ->default(false);

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
                    'employment_status',
                    'service_grade',
                ]);

                $table->index('full_name');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('principal_profiles');
    }
};
