<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit schools') ?? false;
    }

    public function rules(): array
    {
        $schoolId = $this->route('school')?->id;

        return [
            'division_id' => [
                'required',
                Rule::exists('divisions', 'id'),
            ],
            'census_number' => [
                'required',
                'string',
                'max:30',
                Rule::unique('schools', 'census_number')
                    ->ignore($schoolId),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'school_type' => [
                'nullable',
                Rule::in([
                    '1AB',
                    '1C',
                    'Type 2',
                    'Type 3',
                    'Other',
                ]),
            ],
            'gender_type' => [
                'required',
                Rule::in([
                    'Mixed',
                    'Boys',
                    'Girls',
                ]),
            ],
            'school_level' => [
                'nullable',
                Rule::in([
                    'Primary',
                    'Secondary',
                    'Primary and Secondary',
                ]),
            ],
            'mediums' => [
                'nullable',
                'array',
            ],
            'mediums.*' => [
                Rule::in([
                    'Sinhala',
                    'Tamil',
                    'English',
                ]),
            ],
            'address_line_1' => [
                'nullable',
                'string',
                'max:255',
            ],
            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],
            'telephone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'student_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'teacher_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'is_national_school' => [
                'required',
                'boolean',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }
}
