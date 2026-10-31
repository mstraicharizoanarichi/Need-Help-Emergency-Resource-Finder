<?php

namespace App\Services\Analytics;

use App\Models\EmergencyDispatch;
use Carbon\Carbon;

class ResponseTimePredictionService
{
    /**
     * Predict emergency vehicle response time using empirical-statistical features
     *
     * @param float $distanceKm
     * @param array{city?: string, time?: Carbon, weather?: string, emergency_type?: string} $context
     * @return array{
     *     predicted_eta_minutes: float,
     *     confidence_interval: array{min_minutes: float, max_minutes: float},
     *     congestion_risk: string,
     *     feature_contributions: array<string, float>,
     *     model_version: string
     * }
     */
    public function predict(float $distanceKm, array $context = []): array
    {
        $city = strtolower($context['city'] ?? 'dhaka');
        $time = $context['time'] ?? Carbon::now('Asia/Dhaka');
        $weather = strtolower($context['weather'] ?? 'clear');

        $isDhaka = str_contains($city, 'dhaka');
        $isChittagong = str_contains($city, 'chittagong') || str_contains($city, 'chattogram');
        $isMetropolitan = $isDhaka || $isChittagong;

        // 1. Crew Preparation & Departure Latency (T_prep)
        $tPrep = 2.5; // Average 2.5 minutes from dispatch alert to wheels moving

        // 2. Base Distance Transit Component (T_transit)
        // Non-linear piecewise distance model
        if ($distanceKm <= 3.0) {
            // High-density urban alleyways and intersections
            $ratePerKm = $isMetropolitan ? 2.3 : 1.8;
            $tTransit = $distanceKm * $ratePerKm;
        } elseif ($distanceKm <= 15.0) {
            // Main urban arterial corridors
            $ratePerKm = $isMetropolitan ? 1.7 : 1.3;
            $tTransit = (3.0 * ($isMetropolitan ? 2.3 : 1.8)) + (($distanceKm - 3.0) * $ratePerKm);
        } else {
            // Suburban & Highway mix
            $tTransit = (3.0 * 2.3) + (12.0 * 1.7) + (($distanceKm - 15.0) * 0.95);
        }

        // 3. Temporal Congestion Multiplier (C_time)
        $hour = (float) $time->format('G') + ((float) $time->format('i') / 60.0);
        $dayOfWeek = (int) $time->format('w'); // 0 (Sun) - 6 (Sat). Friday = 5.
        $isFriday = ($dayOfWeek === 5);

        $trafficPenaltyMinutes = 0.0;
        $congestionMultiplier = 1.0;

        if ($isFriday) {
            if ($hour >= 13.0 && $hour <= 15.5) {
                // Post-Jummah movement
                $congestionMultiplier = 1.35;
            } elseif ($hour >= 17.5 && $hour <= 21.0) {
                $congestionMultiplier = 1.45;
            } else {
                $congestionMultiplier = 1.05;
            }
        } else {
            // Dual Gaussian peak curves for Dhaka weekday traffic
            // Peak 1: 8:00 AM - 10:30 AM (Morning rush)
            $morningPeak = exp(-0.5 * pow(($hour - 9.2) / 1.4, 2));
            // Peak 2: 5:00 PM - 8:30 PM (Evening rush)
            $eveningPeak = exp(-0.5 * pow(($hour - 18.5) / 1.8, 2));

            $maxUrbanBoost = $isMetropolitan ? 0.85 : 0.40;
            $peakIntensity = max($morningPeak, $eveningPeak);

            $congestionMultiplier = 1.0 + ($peakIntensity * $maxUrbanBoost);

            // Midday baseline
            if ($hour >= 12.0 && $hour <= 16.0 && $congestionMultiplier < 1.25) {
                $congestionMultiplier = $isMetropolitan ? 1.25 : 1.12;
            }

            // Night reduction (10:30 PM - 6:00 AM)
            if ($hour >= 22.5 || $hour <= 6.0) {
                $congestionMultiplier = 0.95;
            }
        }

        $trafficPenaltyMinutes = max(0.0, ($tTransit * $congestionMultiplier) - $tTransit);

        // 4. Weather Friction Multiplier (C_weather)
        $weatherMultiplier = match ($weather) {
            'heavy_rain', 'storm' => 1.40, // Dhaka road waterlogging penalty
            'rain', 'monsoon' => 1.20,
            'fog' => 1.25,
            default => 1.00,
        };

        $weatherPenaltyMinutes = max(0.0, ($tTransit * $weatherMultiplier) - $tTransit);

        // 5. Raw Statistical Model Output
        $rawPredictedEta = $tPrep + $tTransit + $trafficPenaltyMinutes + $weatherPenaltyMinutes;

        // 6. Empirical Bayesian Regularization from Historical Dispatches
        $historicalAvg = EmergencyDispatch::whereNotNull('actual_arrival_minutes')
            ->where('actual_arrival_minutes', '>', 1.0)
            ->where('actual_arrival_minutes', '<', 120.0)
            ->avg('actual_arrival_minutes');

        if ($historicalAvg && $historicalAvg > 0) {
            // Apply 80% model prediction + 20% empirical baseline smoothing
            $finalEta = (0.80 * $rawPredictedEta) + (0.20 * $historicalAvg);
        } else {
            $finalEta = $rawPredictedEta;
        }

        $finalEta = max(3.0, round($finalEta, 1));

        // 7. Confidence Interval (Estimated 90% Bound: +/- 18% variance)
        $variance = max(1.5, $finalEta * 0.18);
        $minMinutes = max(2.0, round($finalEta - $variance, 1));
        $maxMinutes = round($finalEta + ($variance * 1.2), 1);

        // 8. Congestion Risk Level
        $effectiveMultiplier = $congestionMultiplier * $weatherMultiplier;
        $risk = match (true) {
            $effectiveMultiplier >= 1.60 => 'SEVERE',
            $effectiveMultiplier >= 1.35 => 'HIGH',
            $effectiveMultiplier >= 1.15 => 'MODERATE',
            default => 'LOW',
        };

        return [
            'predicted_eta_minutes' => $finalEta,
            'confidence_interval' => [
                'min_minutes' => $minMinutes,
                'max_minutes' => $maxMinutes,
            ],
            'congestion_risk' => $risk,
            'feature_contributions' => [
                'base_preparation_minutes' => $tPrep,
                'distance_transit_minutes' => round($tTransit, 1),
                'traffic_congestion_delay' => round($trafficPenaltyMinutes, 1),
                'weather_friction_delay' => round($weatherPenaltyMinutes, 1),
            ],
            'model_version' => 'BD-EMS-ML-v1.2',
        ];
    }
}
