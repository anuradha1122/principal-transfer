<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class NotificationRecipientService
{
    public function zonalDirectors(
        ?int $zoneId
    ): Collection {
        if (! $zoneId) {
            return collect();
        }

        return User::query()
            ->role('Zonal Director')
            ->where(
                'assigned_zone_id',
                $zoneId
            )
            ->where(
                'is_active',
                true
            )
            ->get();
    }

    public function provincialDirectors(): Collection
    {
        return User::query()
            ->role('Provincial Director')
            ->where(
                'is_active',
                true
            )
            ->get();
    }

    public function transferBoardMembers(): Collection
    {
        return User::query()
            ->role('Transfer Board Member')
            ->where(
                'is_active',
                true
            )
            ->get();
    }

    public function superAdmins(): Collection
    {
        return User::query()
            ->role('Super Admin')
            ->where(
                'is_active',
                true
            )
            ->get();
    }

    public function mergeUnique(
        Collection ...$groups
    ): Collection {
        return collect($groups)
            ->flatten(1)
            ->unique('id')
            ->values();
    }
}
