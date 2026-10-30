<?php

namespace App\Services\Dispatch;

use Carbon\Carbon;

class TrafficEstimationService
{
    /**
     * Calculate estimated travel time in minutes based on distance, city and time of day
     *
     * @param float $distanceKm
     * @param string|null $city
     * @param Carbon|null $time
     * @return array{eta_minutes: float, traffic_multiplier: float, traffic_condition: string, base_speed_kmh: float}
     */
    public function calculateETA(float $distanceKm, ?string $city = 'Dhaka', ?Carbon $time = null): array
    {
        $time = $time ?? Carbon::now('Asia/Dhaka');
        $hour = (int) $time->format('G'); // 0-23
        $dayOfWeek = (int) $time->format('w'); // 0 (Sun) - 6 (Sat). Friday is 5.
        $isFriday = ($dayOfWeek === 5);

        $cityLower = strtolower($city ?? 'dhaka');
        $isMetropolitan = str_contains($cityLower, 'dhaka') || str_contains($cityLower, 'chittagong') || str_contains($cityLower, 'chattogram');

        // Determine traffic condition and multiplier
        if ($isFriday) {
            // Friday in Bangladesh has lighter morning traffic, heavier afternoon/evening
            if ($hour >= 13 && $hour <= 15) {
                $multiplier = 1.35;
                $condition = 'MODERATE';
            } elseif ($hour >= 17 && $hour <= 21) {
                $multiplier = 1.50;
                $condition = 'MODERATE';
            } else {
                $multiplier = 1.05;
                $condition = 'CLEAR';
            }
        } else {
            // Regular weekday in Bangladesh
            if (($hour >= 8 && $hour <= 11) || ($hour >= 17 && $hour <= 21)) {
                // Peak morning/evening office rush hours
                $multiplier = $isMetropolitan ? 1.75 : 1.35;
                $condition = 'HEAVY';
            } elseif ($hour >= 12 && $hour <= 16) {
                // Midday
                $multiplier = $isMetropolitan ? 1.30 : 1.15;
                $condition = 'MODERATE';
            } elseif ($hour >= 22 || $hour <= 6) {
                // Night hours: smooth flow
                $multiplier = 1.00;
                $condition = 'CLEAR';
            } else {
                $multiplier = 1.20;
                $condition = 'MODERATE';
            }
        }

        // Base emergency vehicle speed (higher with sirens, but capped in dense city streets)
        $baseSpeedKmh = $isMetropolitan ? 28.0 : 42.0;

        // Effective speed after applying traffic congestion
        $effectiveSpeedKmh = max(10.0, $baseSpeedKmh / $multiplier);

        // Travel time in minutes
        $travelTimeMinutes = ($distanceKm / $effectiveSpeedKmh) * 60;

        // Add 2 minutes standard crew vehicle turnaround/departure delay
        $etaMinutes = round($travelTimeMinutes + 2.0, 1);

        return [
            'eta_minutes' => $etaMinutes,
            'traffic_multiplier' => round($multiplier, 2),
            'traffic_condition' => $condition,
            'base_speed_kmh' => $baseSpeedKmh,
        ];
    }
}
