<?php

namespace App\Services;

class NicService
{
    /**
     * Normalize a Sri Lankan NIC number.
     */
    public function normalize(?string $nic): string
    {
        $normalized = strtoupper(trim((string) $nic));

        return preg_replace(
            '/[^0-9VX]/',
            '',
            $normalized
        ) ?? '';
    }

    /**
     * Determine whether the NIC format is supported.
     */
    public function isValidFormat(?string $nic): bool
    {
        $normalized = $this->normalize($nic);

        return preg_match(
            '/^(?:[0-9]{9}[VX]|[0-9]{12})$/',
            $normalized
        ) === 1;
    }

    /**
     * Return the broad NIC format.
     */
    public function format(?string $nic): ?string
    {
        $normalized = $this->normalize($nic);

        if (preg_match('/^[0-9]{9}[VX]$/', $normalized)) {
            return 'old';
        }

        if (preg_match('/^[0-9]{12}$/', $normalized)) {
            return 'new';
        }

        return null;
    }
}
