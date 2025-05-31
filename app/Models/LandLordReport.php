<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandLordReport extends Model
{
    use HasFactory;

    //boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model = self::genDate($model);
        });
        static::updating(function ($model) {
            $model = self::genDate($model);
        });
    }

    //public static gen date
    public static function genDate($model)
    {
        if ($model->target_type == 'Monthly') {
            if (($model->target_month) == null) {
                throw new \Exception('Target month is NULL for monthly report.');
            }
            if (($model->target_month) == null || strlen(($model->target_month)) < 3) {
                throw new \Exception('Target month is empty or too short for monthly report. => ' . $model->target_month);
            }

            $moth_start = $model->target_month . '-01';
            $moth_start = Carbon::parse($moth_start);
            if (!$moth_start) {
                throw new \Exception('Invalid target month format. Expected format: YYYY-MM');
            }
            $start_of_month = $moth_start->copy()->startOfMonth();
            $end_of_month = $moth_start->copy()->endOfMonth();

            $model->start_date = $start_of_month->format('Y-m-d');
            $model->end_date = $end_of_month->format('Y-m-d');
            $model->target_year = $moth_start->format('Y');

            // Debug summary (remove or comment out in production)
            /* echo "Start Date: " . $model->start_date . "<br>";
            echo "End Date: " . $model->end_date . "<br>";
            echo "Target Year: " . $model->target_year . "<br>";
            echo "Target Month: " . $model->target_month . "<br>"; */
        } else if ($model->target_type == 'Yearly') {
            if (($model->target_year) == null || strlen(($model->target_year)) < 4) {
                throw new \Exception('Target year is empty or too short for yearly report. => ' . $model->target_year);
            }

            $start_of_year = Carbon::parse($model->target_year . '-01-01');
            $end_of_year = Carbon::parse($model->target_year . '-12-31');

            $model->start_date = $start_of_year->format('Y-m-d');
            $model->end_date = $end_of_year->format('Y-m-d');
        } else if ($model->target_type == 'Custom') {

            //start_date and end_date should be set. validate them if they are valid dates and range is valid
            if (empty($model->start_date) || empty($model->end_date)) {
                throw new \Exception('Start date and end date must be set for custom report.');
            }
            $start_date = Carbon::parse($model->start_date);
            $end_date = Carbon::parse($model->end_date);
            if (!$start_date || !$end_date) {
                throw new \Exception('Invalid start date or end date format. Expected format: YYYY-MM-DD');
            }
            if ($start_date->greaterThan($end_date)) {
                throw new \Exception('Start date cannot be greater than end date.');
            }
            $model->start_date = $start_date->format('Y-m-d');
            $model->end_date = $end_date->format('Y-m-d');
            $model->target_year = $start_date->format('Y'); // Set target year to the start date year
            $model->target_month = $start_date->format('Y-m'); // Set target month to the start date month 

        } else {
            throw new \Exception('Invalid target type. Expected "Monthly" or "Yearly or "Custom". But got: ' . $model->target_type);
        }
        return $model;
    }
}
  /*
         "id" => 36
    "start_date" => null
    "end_date" => null
    "regenerate_report" => "Yes"
    "total_income" => null
    "total_expense" => null
    "total_payment" => null
    "target_type" => "Monthly"
    "target_month" => "2025-04"
    "target_year" => "2024" 
         */