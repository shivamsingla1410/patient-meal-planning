<?php

namespace App\Console\Commands;

use App\Http\Controllers\MealPlanningController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class MealPlanning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:meal-planning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve planned meal data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Define your parameters
        $params = [
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+1 month')),
        ];

        // Create a new Request instance with the parameters
        $requestInstance = new Request($params);

        $controller = new MealPlanningController();
        $controller->getMonthlyStats($requestInstance);
        $this->info('Meal planning task executed successfully.');
    }
}
