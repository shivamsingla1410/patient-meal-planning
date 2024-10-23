<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meal_planning', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->date('planned_date');
            $table->integer('total_calories');
            $table->integer('total_fats');
            $table->integer('total_carbs');
            $table->integer('total_proteins');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->unique(['patient_id', 'planned_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_planning');
    }
};
