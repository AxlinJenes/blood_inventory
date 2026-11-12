<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Refrigerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemperatureAnalysisController extends Controller
{
    public function dailyStatistics(Request $request, $refrigeratorId)
    {
        $date = $request->query('date', now()->toDateString());

        $refrigerator = Refrigerator::findOrFail($refrigeratorId);

        $statistics = DB::table('temperature_logs')
            ->where('refrigerator_id', $refrigeratorId)
            ->whereDate('recorded_at', $date)
            ->selectRaw("
                AVG(temperature) as average_temperature,
                MAX(temperature) as highest_temperature,
                MIN(temperature) as lowest_temperature,
                COUNT(*) as total_readings,

                COUNT(DISTINCT DATE_FORMAT(recorded_at, '%Y-%m-%d %H:%i'))
                    as monitored_minutes,

                COUNT(DISTINCT CASE
                    WHEN temperature < 2 OR temperature > 6
                    THEN DATE_FORMAT(recorded_at, '%Y-%m-%d %H:%i')
                END) as unsafe_minutes
            ")
            ->first();

        $totalMinutes = 1440;

        $monitoredMinutes = (int) $statistics->monitored_minutes;

        $unsafeMinutes = (int) $statistics->unsafe_minutes;

        $missingMinutes = $totalMinutes - $monitoredMinutes;

        $riskPercentage = $monitoredMinutes > 0
            ? ($unsafeMinutes / $monitoredMinutes) * 100
            : null;

        return response()->json([
            'refrigerator' => $refrigerator->name,
            'date' => $date,
            'average_temperature' => round($statistics->average_temperature ?? 0, 2),
            'highest_temperature' => $statistics->highest_temperature,
            'lowest_temperature' => $statistics->lowest_temperature,
            'total_readings' => $statistics->total_readings,
            'total_minutes' => $totalMinutes,
            'expected_minutes' => $totalMinutes,
            'monitored_minutes' => $monitoredMinutes,
            'missing_minutes' => $missingMinutes,
            'unsafe_minutes' => $unsafeMinutes,
            'risk_percentage' => $riskPercentage !== null
                ? round($riskPercentage, 2)
                : null,
        ]);
    }
}