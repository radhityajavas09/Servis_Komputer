<?php

namespace App\Services;

use App\Models\Servis;

class NomorServisGenerator
{
    /**
     * Generate next service number within a database transaction.
     */
    public function generate(): string
    {
        $latest = Servis::query()
            ->lockForUpdate()
            ->orderByDesc('id_servis')
            ->value('no_servis');

        return $this->nextFrom($latest);
    }

    /**
     * Calculate the next service number string based on the latest number.
     */
    public function nextFrom(?string $latestNoServis): string
    {
        if (empty($latestNoServis)) {
            return 'SRV-001';
        }

        if (preg_match('/SRV-(\d+)/', $latestNoServis, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        if ($nextNumber <= 999) {
            return sprintf('SRV-%03d', $nextNumber);
        }

        return sprintf('SRV-%d', $nextNumber);
    }
}
