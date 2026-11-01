<?php

namespace Tests\Feature;

use App\Services\Analytics\ResponseTimePredictionService;
use Carbon\Carbon;
use Tests\TestCase;

class ResponseTimePredictionTest extends TestCase
{
    public function test_response_time_prediction_returns_realistic_eta_and_confidence_interval(): void
    {
        $service = new ResponseTimePredictionService();
        $result = $service->predict(8.0, ['city' => 'Dhaka']);

        $this->assertArrayHasKey('predicted_eta_minutes', $result);
        $this->assertArrayHasKey('confidence_interval', $result);
        $this->assertArrayHasKey('congestion_risk', $result);
        $this->assertArrayHasKey('feature_contributions', $result);
        $this->assertEquals('BD-EMS-ML-v1.2', $result['model_version']);

        $this->assertGreaterThan(5.0, $result['predicted_eta_minutes']);
        $this->assertLessThan($result['confidence_interval']['max_minutes'], $result['confidence_interval']['min_minutes']);
    }

    public function test_rush_hour_congestion_generates_higher_eta_and_elevated_risk(): void
    {
        $service = new ResponseTimePredictionService();

        // 9:15 AM Monday morning rush in Dhaka
        $rushTime = Carbon::parse('2026-09-07 09:15:00', 'Asia/Dhaka');
        $rushResult = $service->predict(10.0, ['city' => 'Dhaka', 'time' => $rushTime]);

        // 2:30 AM Tuesday night in Dhaka
        $nightTime = Carbon::parse('2026-09-08 02:30:00', 'Asia/Dhaka');
        $nightResult = $service->predict(10.0, ['city' => 'Dhaka', 'time' => $nightTime]);

        $this->assertGreaterThan($nightResult['predicted_eta_minutes'], $rushResult['predicted_eta_minutes']);
        $this->assertContains($rushResult['congestion_risk'], ['HIGH', 'SEVERE']);
        $this->assertEquals('LOW', $nightResult['congestion_risk']);
    }

    public function test_heavy_rain_increases_weather_delay_contribution(): void
    {
        $service = new ResponseTimePredictionService();
        $clearTime = Carbon::parse('2026-09-07 14:00:00', 'Asia/Dhaka');

        $clearResult = $service->predict(12.0, ['city' => 'Dhaka', 'time' => $clearTime, 'weather' => 'clear']);
        $rainResult = $service->predict(12.0, ['city' => 'Dhaka', 'time' => $clearTime, 'weather' => 'heavy_rain']);

        $this->assertGreaterThan(
            $clearResult['feature_contributions']['weather_friction_delay'],
            $rainResult['feature_contributions']['weather_friction_delay']
        );
        $this->assertGreaterThan($clearResult['predicted_eta_minutes'], $rainResult['predicted_eta_minutes']);
    }

    public function test_short_distance_accounts_for_urban_prep_latency(): void
    {
        $service = new ResponseTimePredictionService();
        $result = $service->predict(1.5, ['city' => 'Dhaka']);

        // Even 1.5 km requires crew alert, boarding and transit
        $this->assertGreaterThanOrEqual(4.0, $result['predicted_eta_minutes']);
        $this->assertEquals(2.5, $result['feature_contributions']['base_preparation_minutes']);
    }
}
