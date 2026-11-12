<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BloodBag;
use App\Models\Refrigerator;
use App\Models\TemperatureLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

public function index()
{
    // Total blood bags
    $totalBloodBags = BloodBag::count();

    // Available stock grouped by blood group
    $availableStockByGroup = BloodBag::where('status', 'Available')
        ->where('expiry_date', '>=', now())
        ->select('blood_group', DB::raw('COUNT(*) as total'))
        ->groupBy('blood_group')
        ->get();

    // Today's temperature readings
    $totalTemperatureReadings = TemperatureLog::whereDate(
        'recorded_at',
        today()
    )->count();

    // Safe temperature readings
    $safeTemperatureReadings = TemperatureLog::whereDate(
        'recorded_at',
        today()
    )->whereBetween('temperature', [2, 6])->count();

    // Refrigerator health score
    $refrigeratorHealthScore = $totalTemperatureReadings > 0
        ? ($safeTemperatureReadings / $totalTemperatureReadings) * 100
        : 0;

    // Critical temperature alerts
    $criticalTemperatureAlerts = TemperatureLog::whereDate(
        'recorded_at',
        today()
    )->where(function ($query) {
        $query->where('temperature', '<', 2)
            ->orWhere('temperature', '>', 8);
    })->count();

    // Average temperature today
    $averageTemperatureToday = TemperatureLog::whereDate(
        'recorded_at',
        today()
    )->avg('temperature');

    $averageTemperatureToday = $averageTemperatureToday !== null
        ? round($averageTemperatureToday, 2)
        : 0;

    // Total expired blood bags
    $totalExpiredBags = BloodBag::where('expiry_date', '<', now())
        ->count();

    // Active refrigerators
    $activeRefrigerators = Refrigerator::count();

    return response()->json([
        'success' => true,
        'message' => 'Dashboard analytics fetched successfully',
        'data' => [
            'total_blood_bags' => $totalBloodBags,
            'available_stock_by_group' => $availableStockByGroup,
            'refrigerator_health_score' => round($refrigeratorHealthScore, 2),
            'critical_temperature_alerts' => $criticalTemperatureAlerts,
            'average_temperature_today' => $averageTemperatureToday,
            'total_expired_bags' => $totalExpiredBags,
            'active_refrigerators' => $activeRefrigerators,
        ]
    ]);
}

}
