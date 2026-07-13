<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id',
        'census_number',
        'name',
        'school_type',
        'gender_type',
        'school_level',
        'mediums',
        'address_line_1',
        'address_line_2',
        'city',
        'postal_code',
        'telephone',
        'email',
        'student_count',
        'teacher_count',
        'is_national_school',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'mediums' => 'array',
            'student_count' => 'integer',
            'teacher_count' => 'integer',
            'is_national_school' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function zone(): ?Zone
    {
        return $this->division?->zone;
    }
}
