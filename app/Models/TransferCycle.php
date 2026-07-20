<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'transfer_type',
        'transfer_year',
        'application_open_date',
        'application_close_date',
        'effective_from_date',
        'minimum_service_years',
        'maximum_preferences',
        'allow_same_zone_preferences',
        'allow_other_zone_preferences',
        'allow_withdrawal',
        'status',
        'instructions',
        'eligibility_notes',
        'created_by',
        'updated_by',
        'published_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'transfer_year' => 'integer',
            'application_open_date' => 'date',
            'application_close_date' => 'date',
            'effective_from_date' => 'date',
            'minimum_service_years' => 'integer',
            'maximum_preferences' => 'integer',
            'allow_same_zone_preferences' => 'boolean',
            'allow_other_zone_preferences' => 'boolean',
            'allow_withdrawal' => 'boolean',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(TransferApplication::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'Published');
    }

    public function isApplicationOpen(): bool
    {
        return $this->status === 'Published'
            && today()->between(
                $this->application_open_date,
                $this->application_close_date
            );
    }

    public function hasApplicationClosed(): bool
    {
        return today()->greaterThan(
            $this->application_close_date
        );
    }
}
