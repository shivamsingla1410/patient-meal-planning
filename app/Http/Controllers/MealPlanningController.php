<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MealPlanning;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MealPlanningController extends Controller
{
    public function getMonthlyStats(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Fetch the meal planning stats grouped by month and year
        $mealPlans = MealPlanning::selectRaw('
                MONTH(planned_date) as month,
                YEAR(planned_date) as year,
                COUNT(*) as total_days_planned,
                AVG(total_calories) as avg_total_calories,
                AVG(total_fats) as avg_total_fats,
                AVG(total_carbs) as avg_total_carbs,
                AVG(total_proteins) as avg_total_proteins
            ')
            ->whereBetween('planned_date', [$startDate, $endDate])
            ->groupByRaw('YEAR(planned_date), MONTH(planned_date)')
            ->get();

        $data = [];

        foreach ($mealPlans as $mealPlan) {
            // Calculate the days of the month
            $daysInMonth = Carbon::parse("{$mealPlan->year}-{$mealPlan->month}-01")->daysInMonth;

            // Get the planned days for that month
            $plannedDays = MealPlanning::whereYear('planned_date', $mealPlan->year)
                ->whereMonth('planned_date', Carbon::parse($mealPlan->month)->month)
                ->pluck('planned_date')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('d F Y');
                });

            // Calculate the skipped days
            $skippedDays = array_diff(
                range(1, $daysInMonth),
                $plannedDays->map(function ($date) {
                    return Carbon::parse($date)->day;
                })->toArray()
            );

            $monthName = date('F', mktime(0, 0, 0, $mealPlan->month, 1)); // Generate month name from timestamp

            // Format the response data
            $data[] = [
                'month' => "{$monthName} {$mealPlan->year}",
                'planned_percentage' => round(($mealPlan->total_days_planned / $daysInMonth) * 100) . ' %',
                'avg_total_calories' => $mealPlan->avg_total_calories,
                'avg_total_carbs' => $mealPlan->avg_total_carbs,
                'avg_total_protein' => $mealPlan->avg_total_proteins,
                'avg_total_fat' => $mealPlan->avg_total_fats,
                'days_planning_skipped' => array_map(function ($day) use ($mealPlan, $monthName) {
                    return $day . ' ' . $monthName . ' ' . $mealPlan->year;
                }, $skippedDays)
            ];
        }

        return response()->json(['data' => $data]);
    }
}
