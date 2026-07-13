<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table): void {
            $table->id();

            $table
                ->foreignId('division_id')
                ->constrained()
                ->restrictOnDelete();

            $table
                ->string('census_number', 30)
                ->unique();

            $table->string('name', 255);

            $table
                ->enum('school_type', [
                    '1AB',
                    '1C',
                    'Type 2',
                    'Type 3',
                    'Other',
                ])
                ->nullable();

            $table
                ->enum('gender_type', [
                    'Mixed',
                    'Boys',
                    'Girls',
                ])
                ->default('Mixed');

            $table
                ->enum('school_level', [
                    'Primary',
                    'Secondary',
                    'Primary and Secondary',
                ])
                ->nullable();

            $table
                ->json('mediums')
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
                ->string('telephone', 30)
                ->nullable();

            $table
                ->string('email')
                ->nullable();

            $table
                ->unsignedInteger('student_count')
                ->nullable();

            $table
                ->unsignedInteger('teacher_count')
                ->nullable();

            $table
                ->boolean('is_national_school')
                ->default(false);

            $table
                ->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'division_id',
                'is_active',
            ]);

            $table->index([
                'school_type',
                'gender_type',
            ]);

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
