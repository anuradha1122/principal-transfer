<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_applications', function (Blueprint $table): void {
            $table->id();

            $table
                ->foreignId('transfer_cycle_id')
                ->constrained()
                ->restrictOnDelete();

            $table
                ->foreignId('principal_profile_id')
                ->constrained()
                ->restrictOnDelete();

            $table
                ->foreignId('current_appointment_id')
                ->nullable()
                ->constrained('principal_appointments')
                ->nullOnDelete();

            $table
                ->string('application_number', 50)
                ->nullable()
                ->unique();

            /*
             * Snapshot fields preserve what was true when the application
             * was submitted, even if the profile changes later.
             */
            $table->string('principal_name');
            $table->string('nic', 20);
            $table
                ->string('employee_number', 50)
                ->nullable();

            $table
                ->foreignId('current_school_id')
                ->nullable()
                ->constrained('schools')
                ->nullOnDelete();

            $table
                ->string('current_designation', 100)
                ->nullable();

            $table
                ->string('service_grade', 100)
                ->nullable();

            $table
                ->date('current_appointment_start_date')
                ->nullable();

            $table
                ->unsignedInteger('current_school_service_months')
                ->default(0);

            $table
                ->enum('transfer_reason', [
                    'Long Service',
                    'Medical',
                    'Spouse Employment',
                    'Family Requirement',
                    'Travel Difficulty',
                    'Personal Request',
                    'Mutual Transfer',
                    'Administrative Reason',
                    'Other',
                ]);

            $table
                ->text('reason_details');

            $table
                ->boolean('has_medical_reason')
                ->default(false);

            $table
                ->boolean('has_spouse_employment_reason')
                ->default(false);

            $table
                ->boolean('is_mutual_transfer')
                ->default(false);

            $table
                ->string('mutual_principal_nic', 20)
                ->nullable();

            $table
                ->enum('status', [
                    'Draft',
                    'Submitted',
                    'Zonal Review',
                    'Zonal Approved',
                    'Zonal Rejected',
                    'Provincial Review',
                    'Provincial Approved',
                    'Provincial Rejected',
                    'Board Review',
                    'Approved',
                    'Rejected',
                    'Waitlisted',
                    'Withdrawn',
                    'Cancelled',
                ])
                ->default('Draft');

            $table
                ->timestamp('submitted_at')
                ->nullable();

            $table
                ->timestamp('withdrawn_at')
                ->nullable();

            $table
                ->text('withdrawal_reason')
                ->nullable();

            $table
                ->boolean('declaration_accepted')
                ->default(false);

            $table
                ->text('principal_remarks')
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

            $table->index(
                [
                    'transfer_cycle_id',
                    'principal_profile_id',
                    'status',
                ],
                'ta_cycle_profile_status_idx'
            );

            $table->index([
                'status',
                'submitted_at',
            ]);

            $table->index([
                'current_school_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_applications');
    }
};
