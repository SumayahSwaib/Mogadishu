<?php

namespace App\Admin\Controllers;

use App\Models\Tenant;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TenantController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tenants';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Tenant());

        $grid->quickSearch('name')->placeholder('Search by name....');
        $grid->model()->orderBy('id', 'desc');
        $grid->disableBatchActions();
        $grid->column('id', __('ID'))->sortable();
        $grid->column('created_at', __('Date'))->display(function ($x) {
            return Utils::my_date_time($x);
        })->sortable();
        $grid->column('image', __('Photo'))
        ->lightbox(['width' => 60, 'height' => 60])
            ->sortable();

        $grid->column('name', __('Name'))->sortable();
        $grid->column('email', __('Email'));
        $grid->column('phone_number', __('Phone number'));

        $grid->column('rentings', __('Total Rentings (UGX)'))
            ->display(function ($x) {
                $rentings = $this->rentings->sum('payable_amount');
                return "<a class=\"d-block text-center\" title=\"Click to view these invoinces\" target=\"_blank\" href='" . admin_url('rentings') . "?tenant_id={$this->id}'><b>" . number_format($rentings) . "</b></a>";
            });

        $grid->column('payments', __('Total Payments (UGX)'))
            ->display(function ($x) {
                $rentings = $this->payments->sum('amount');
                return "<a class=\"d-block text-center\" title=\"Click to view these receipts\" target=\"_blank\" href='" . admin_url('tenant-payments') . "?tenant_id={$this->id}'><b>" . number_format($rentings) . "</b></a>";
            });

        $grid->column('balance', __('Balance (UGX)'))
            ->display(function ($x) {
                $color = 'danger';
                if ($x >= 0) {
                    $color = 'success';
                }
                return '<a target="_blank" title="View Invoinces" class="d-block text-left " style="font-size: 16px; text-align: center;" href="' . admin_url('rentings?tenant_id=' . $this->id) . '" ><b>' . number_format($x) . '</b></a>';
            })->totalRow(function ($x) {
                return  number_format($x);
            })->sortable();

        $grid->column('phone_number_2', __('NIN'))->hide();
        $grid->column('address', __('Details'))->hide();


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
        $show = new Show(Tenant::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('region_id', __('Region id'));
        $show->field('area_id', __('Area id'));
        $show->field('name', __('Name'));
        $show->field('gender', __('Gender'));
        $show->field('address', __('Address'));
        $show->field('phone_number_2', __('NIN'));
        $show->field('phone_number', __('Phone Number'));
        $show->field('attachment', __('Attachment'));
        $show->field('details', __('Details'));
        $show->field('image', __('Photo'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Tenant());

        $form->text('name', __('Name'))->rules('required');
             /*   $form->radio('gender', __('Gender'))->options([
            'Male' => 'Male',
            'Female' => 'Female',
        ])->rules('required');  */
        $form->text('email', __('Email'));
       
        $form->text('phone_number', __('Phone number'))->rules('required');
        $form->text('phone_number_2', __('NIN'))->rules('required');
        $form->text('address', __('Details'))->rules('required');
        $form->image('image', __('Photo'));
        return $form;
    }
}
