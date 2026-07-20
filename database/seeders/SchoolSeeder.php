<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\School;
use Illuminate\Database\Seeder;
use RuntimeException;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $ratnapuraDivision = Division::query()
            ->where('code', 'RAT-DIV-01')
            ->first();

        $kuruwitaDivision = Division::query()
            ->where('code', 'RAT-DIV-02')
            ->first();

        if (! $ratnapuraDivision) {
            throw new RuntimeException(
                'Ratnapura Division was not found. Run DivisionSeeder first.'
            );
        }

        if (! $kuruwitaDivision) {
            throw new RuntimeException(
                'Kuruwita Division was not found. Run DivisionSeeder first.'
            );
        }

        $schools = [
            [
                'division_id' => $ratnapuraDivision->id,
                'census_number' => '1901010',
                'name' => 'Ratnapura Central College',
                'school_type' => '1AB',
                'gender_type' => 'Mixed',
                'school_level' => 'Primary and Secondary',
                'mediums' => ['Sinhala', 'English'],
                'address_line_1' => 'Ratnapura',
                'address_line_2' => null,
                'city' => 'Ratnapura',
                'postal_code' => null,
                'telephone' => null,
                'email' => null,
                'student_count' => 1800,
                'teacher_count' => 95,
                'is_national_school' => false,
                'is_active' => true,
            ],
            [
                'division_id' => $ratnapuraDivision->id,
                'census_number' => '1901020',
                'name' => 'Ferguson High School',
                'school_type' => '1AB',
                'gender_type' => 'Girls',
                'school_level' => 'Primary and Secondary',
                'mediums' => ['Sinhala', 'English'],
                'address_line_1' => 'Ratnapura',
                'address_line_2' => null,
                'city' => 'Ratnapura',
                'postal_code' => null,
                'telephone' => null,
                'email' => null,
                'student_count' => 1600,
                'teacher_count' => 90,
                'is_national_school' => false,
                'is_active' => true,
            ],
            [
                'division_id' => $ratnapuraDivision->id,
                'census_number' => '1901030',
                'name' => 'Sivali Central College',
                'school_type' => '1AB',
                'gender_type' => 'Mixed',
                'school_level' => 'Primary and Secondary',
                'mediums' => ['Sinhala'],
                'address_line_1' => 'Hidellana',
                'address_line_2' => 'Ratnapura',
                'city' => 'Ratnapura',
                'postal_code' => null,
                'telephone' => null,
                'email' => null,
                'student_count' => 1400,
                'teacher_count' => 80,
                'is_national_school' => false,
                'is_active' => true,
            ],
            [
                'division_id' => $kuruwitaDivision->id,
                'census_number' => '1902010',
                'name' => 'Kuruwita Central College',
                'school_type' => '1AB',
                'gender_type' => 'Mixed',
                'school_level' => 'Primary and Secondary',
                'mediums' => ['Sinhala', 'English'],
                'address_line_1' => 'Kuruwita',
                'address_line_2' => null,
                'city' => 'Kuruwita',
                'postal_code' => null,
                'telephone' => null,
                'email' => null,
                'student_count' => 1500,
                'teacher_count' => 85,
                'is_national_school' => false,
                'is_active' => true,
            ],
            [
                'division_id' => $kuruwitaDivision->id,
                'census_number' => '1902020',
                'name' => 'Paradise Maha Vidyalaya',
                'school_type' => '1C',
                'gender_type' => 'Mixed',
                'school_level' => 'Primary and Secondary',
                'mediums' => ['Sinhala'],
                'address_line_1' => 'Paradise',
                'address_line_2' => 'Kuruwita',
                'city' => 'Kuruwita',
                'postal_code' => null,
                'telephone' => null,
                'email' => null,
                'student_count' => 650,
                'teacher_count' => 42,
                'is_national_school' => false,
                'is_active' => true,
            ],
        ];

        foreach ($schools as $school) {
            School::query()->updateOrCreate(
                [
                    'census_number' => $school['census_number'],
                ],
                $school
            );
        }
    }
}
