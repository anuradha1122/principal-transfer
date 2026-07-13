<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'district',
        'office_address',
        'telephone',
        'email',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function schools(): HasManyThrough
    {
        return $this->hasManyThrough(
            School::class,
            Division::class,
            'zone_id',
            'division_id',
            'id',
            'id'
        );
    }
}
