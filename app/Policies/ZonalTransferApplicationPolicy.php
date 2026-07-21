<?php

namespace App\Policies;

use App\Models\TransferApplication;
use App\Models\User;

class ZonalTransferApplicationPolicy
{
    public function before(
        User $user,
        string $ability
    ): ?bool {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('Zonal Director')
            && $user->can('view zonal transfer applications')
            && $user->assigned_zone_id !== null;
    }

    public function view(
        User $user,
        TransferApplication $transferApplication
    ): bool {
        return $this->belongsToAssignedZone(
            $user,
            $transferApplication
        )
            && $user->can('view zonal transfer applications');
    }

    public function downloadPdf(
        User $user,
        TransferApplication $transferApplication
    ): bool {
        return $this->belongsToAssignedZone(
            $user,
            $transferApplication
        )
            && $user->can(
                'download zonal transfer application pdfs'
            )
            && $transferApplication->pdf_path !== null;
    }

    public function startZonalReview(
        User $user,
        TransferApplication $transferApplication
    ): bool {
        return $this->belongsToAssignedZone(
            $user,
            $transferApplication
        )
            && $user->can('review zonal transfer applications')
            && $transferApplication->canStartZonalReview();
    }

    public function approveZonalReview(
        User $user,
        TransferApplication $transferApplication
    ): bool {
        return $this->belongsToAssignedZone(
            $user,
            $transferApplication
        )
            && $user->can('approve zonal transfer applications')
            && $transferApplication->canReceiveZonalDecision();
    }

    public function rejectZonalReview(
        User $user,
        TransferApplication $transferApplication
    ): bool {
        return $this->belongsToAssignedZone(
            $user,
            $transferApplication
        )
            && $user->can('reject zonal transfer applications')
            && $transferApplication->canReceiveZonalDecision();
    }

    private function belongsToAssignedZone(
        User $user,
        TransferApplication $transferApplication
    ): bool {
        if (! $user->hasRole('Zonal Director')) {
            return false;
        }

        if ($user->assigned_zone_id === null) {
            return false;
        }

        return (int) $transferApplication->origin_zone_id
            === (int) $user->assigned_zone_id;
    }
}
