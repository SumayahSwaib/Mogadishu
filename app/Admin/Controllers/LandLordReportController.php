<?php

namespace App\Admin\Controllers;

use App\Models\Landload;
use App\Models\LandLordReport;
use App\Models\Tenant;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LandLordReportController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Landloard Report';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LandLordReport());



        /* $grid->filter(function ($filter) {
            // Remove the default id filter
            $filter->disableIdFilter();
            $filter->equal('landload_id', 'Filter by landlord')
                ->select(
                    Tenant::where([])->orderBy('name', 'Asc')->get()->pluck('name', 'id')
                );
        }); */

        $grid->model()->orderBy('id', 'desc');
        $grid->disableBatchActions();
        $grid->column('id', __('ID'))->sortable();
        /* $grid->column('landload_id', __('Customer'))
            //display landload name

            ->display(function ($x) {
                $y = Tenant::find($x);
                if ($y == null) {
                    
                    $this->delete();
                    return 'Deleted';
                }
                
                return $y->name;
                // $y->name;

            })->sortable(); */

        $grid->column('start_date', __('Start Date'))
            ->display(function ($x) {
                return date('d M Y', strtotime($x));
            })->sortable();

        $grid->column('end_date', __('End Date'))
            ->display(function ($x) {
                return date('d M Y', strtotime($x));
            })->sortable();

        $grid->column('report', __('Report'))
            ->display(function ($x) {
                return "<a class=\"d-block text-primary text-center\" target=\"_blank\" href='" . url('landlord-report-1') . "?id={$this->id}'><b>PRINT REPORT</b></a>";
                $url = "<a style=' line-height: 10px;' class=\"p-0 m-0 mb-2 d-block text-primary text-center\" target=\"_blank\" href='" . url('landlord-report-1') . "?id={$this->id}'><b>PRINT REPORT (Design 1)</b></a>";
                $url .= "<a  style=' line-height: 10px;' class=\"d-block text-primary text-center\" target=\"_blank\" href='" . url('landlord-report-1') . "?id={$this->id}'><b>PRINT REPORT (Design 2)</b></a><br>";
                return $url;
            })->sortable();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(LandLordReport::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        //  $show->field('landload_id', __('Landload id'));
        $show->field('land_lord_name', __('Land lord name'));
        $show->field('land_lord_email', __('Land lord email'));
        $show->field('land_lord_phone', __('Land lord phone'));
        $show->field('land_lord_address', __('Land lord address'));
        $show->field('start_date', __('Start date'));
        $show->field('end_date', __('End date'));
        $show->field('regenerate_report', __('Regenerate report'));
        $show->field('total_income', __('Total income'));
        $show->field('total_expense', __('Total expense'));
        $show->field('total_payment', __('Total payment'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new LandLordReport());

        /* $lastRec = LandLordReport::orderBy('id', 'desc')->first();
        $lastRec->land_lord_address .= '.';
        $lastRec->target_type = 'Custom';
        $lastRec->start_date = $lastRec->end_date;
        $lastRec->end_date = '2020-12-31';
        $lastRec->save();
        $model = $lastRec;
        echo "Start Date: " . $model->start_date . "<br>";
        echo "End Date: " . $model->end_date . "<br>";
        echo "Target Year: " . $model->target_year . "<br>";
        echo "Target Month: " . $model->target_month . "<br>"; */



        if ($form->isEditing()) {
            $form->radioCard('regenerate_report', __('Regenerate Report'))
                ->options(['Yes' => 'Yes', 'No' => 'No'])
                ->default('No');
        } else {
            $form->hidden('regenerate_report')->default('Yes');
        }

        /* $form->decimal('total_expense', 'Total Expense (UGX)')
            ->rules('required');
 */
        $form->disableCreatingCheck();

        $form->disableViewCheck();
        $form->radio('target_type', 'Target Type')
            ->options(['Monthly' => 'Monthly', 'Yearly' => 'Yearly', 'Custom' => 'Custom'])
            ->when('Yearly', function (Form $form) {
                //5 years ago to next year
                $years_ago = [];
                for ($i = 0; $i < 5; $i++) {
                    $years_ago[date('Y', strtotime("-$i year"))] = date('Y', strtotime("-$i year"));
                }
                $form->select('target_year', 'Target Year')
                    ->options($years_ago)
                    ->rules('required');
            })
            ->when('Monthly', function (Form $form) {
                $months_ago = [];
                for ($i = 0; $i < 12; $i++) {
                    $months_ago[date('Y-m', strtotime("-$i month"))] = date('F Y', strtotime("-$i month"));
                }
                $form->select('target_month', 'Target Month')
                    ->options($months_ago)
                    ->rules('required');
            })
            ->when('Custom', function (Form $form) {
                $form->dateRange('start_date', 'end_date', 'Report Date Range')
                    ->rules('required');
            });



        // $first = LandLordReport::first();
        // dd($first);

        /* 
            $table->string('target_month')->nullable();
            $table->string('target_year')->nullable();

                "id" => 17
    "created_at" => "2024-01-09 08:32:23"
    "updated_at" => "2024-01-09 08:32:23"
    "landload_id" => 68
    "land_lord_name" => null
    "land_lord_email" => null
    "land_lord_phone" => null
    "land_lord_address" => null
    "start_date" => "2024-01-01"
    "end_date" => "2024-01-31"
    "regenerate_report" => "Yes"
    "total_income" => null
    "total_expense" => 0
    "total_payment" => null
        */


        return $form;
    }
}
