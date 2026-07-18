<?php

namespace App\Services;

use App\Models\ShippingZone;

class ShippingService
{
    public function findZoneByDistrict(string $district): ?ShippingZone
    {
        $normalized = $this->normalizeDistrict($district);

        return ShippingZone::where('is_active', true)
            ->get()
            ->first(function (ShippingZone $zone) use ($normalized) {
                return collect($zone->districts)->contains(function ($d) use ($normalized) {
                    return $this->normalizeDistrict($d) === $normalized;
                });
            });
    }

    public function calculateCost(ShippingZone $zone, int $totalWeightGrams): float
    {
        $totalKg = (int) ceil($totalWeightGrams / 1000);

        return round((float) $zone->base_cost + ($totalKg * (float) $zone->per_kg_cost), 2);
    }

    public function getActiveZones()
    {
        return ShippingZone::where('is_active', true)->orderBy('zone_name')->get();
    }

    private function normalizeDistrict(string $district): string
    {
        return mb_strtolower(trim($district));
    }
}
