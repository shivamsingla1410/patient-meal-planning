<?php

namespace Database\Seeders;

use App\Models\MealPlanning;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class MealPlanningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $patients = range(1, 100); // Simulating patient IDs

        for ($i = 0; $i < 1000; $i++) {
            do {
                $patientId = $faker->randomElement($patients);
                $plannedDate = $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d');
                $exists = MealPlanning::where('patient_id', $patientId)
                    ->where('planned_date', $plannedDate)
                    ->exists(); // Check if the combination exists
            } while ($exists);

            // Create the record once uniqueness is confirmed
            MealPlanning::create([
                'patient_id' => $patientId,
                'planned_date' => $plannedDate,
                'total_calories' => $faker->numberBetween(1000, 3000),
                'total_fats' => $faker->numberBetween(50, 100),
                'total_carbs' => $faker->numberBetween(200, 400),
                'total_proteins' => $faker->numberBetween(50, 150),
                'is_active' => true,
                'created_by' => 1, // Admin user or fixed user ID
                'updated_by' => null
            ]);
        }
    }
}
