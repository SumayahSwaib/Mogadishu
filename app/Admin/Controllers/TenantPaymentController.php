<?php

namespace App\Admin\Controllers;

use App\Models\Landload;
use App\Models\Renting;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantPayment;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TenantPaymentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tenant Payments (Receipts)';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new TenantPayment());
        $grid->filter(function ($filter) {
            // Remove the default id filter
            $filter->disableIdFilter();

            /* $filter->equal('landload_id', 'Filter by landlord')
                ->select(
                    Landload::where([])->orderBy('name', 'Asc')->get()->pluck('name', 'id')
                ); */
            $filter->equal('tenant_id', 'Filter By Tenant')
                ->select(
                    Tenant::get_items()
                );
            $invoices = [];
            foreach (Renting::where([])->orderBy('id', 'desc')->get() as $key => $v) {
                $invoices[$v->id] = "#" . $v->id . " - ROOM: " . $v->room->name . ", Tenant: " . $v->tenant->name . " , Balance: UGX " . number_format($v->balance);
            }
            $filter->equal('renting_id', 'Filter by renting invoice')
                ->select(
                    $invoices
                );
            $filter->between('created_at', 'Filter by Date Created')->date();

            $filter->group('amount', function ($group) {
                $group->gt('greater than');
                $group->lt('less than');
                $group->equal('equal to');
            });
        });



        $grid->quickSearch('details')->placeholder('Search by details...');
        $grid->model()->orderBy('id', 'desc');
        $grid->disableBatchActions();
        $grid->column('id', __('ID'))->sortable();
        $grid->column('created_at', __('Date'))->display(function ($x) {
            return Utils::my_date_time($x);
        })->sortable();

        $grid->column('renting_id', __('Renting'))
            ->display(function ($x) {
                if ($this->renting == null) return $x;
                return Utils::my_date($this->renting->start_date) . " - " . Utils::my_date($this->renting->end_date) . " Invoice NO.0" . $this->renting_id;
            })
            ->sortable();
        $grid->column('tenant_id', __('Tenant'))->display(function ($x) {
            if ($this->tenant == null) {
                return $x;
            }
            return $this->tenant->name;
        })->sortable();

        $grid->column('amount', __('Amount (UGX)'))
            ->display(function ($x) {
                return number_format($x);
            })->totalRow(function ($x) {
                return  number_format($x);
            })->sortable();

        $grid->column('securty_deposit', __('Security Deposit (UGX)'))->display(function ($x) {
            return number_format($x);
        })->totalRow(function ($x) {
            return  number_format($x);
        })->sortable();

        //garbage_amount
        $grid->column('garbage_amount', __('Garbage Amount (UGX)'))->display(function ($x) {
            return number_format($x);
        })->totalRow(function ($x) {
            return  number_format($x);
        })->sortable();

        $grid->column('days_before', __('Days Before'))->display(function ($x) {
            return number_format($x);
        })->totalRow(function ($x) {
            return  number_format($x);
        })->sortable();




        /* $grid->column('landlord_amount', __('Landlord (UGX)'))
            ->display(function ($x) {
                return number_format($x);
            })->totalRow(function ($x) {
                return  number_format($x);
            })->sortable(); */

        /* $grid->column('commission_amount', __('Commision (UGX)'))
            ->display(function ($x) {
                return number_format($x);
            })->totalRow(function ($x) {
                return  number_format($x);
            })->sortable(); */

        /* $grid->column('commission_type', __('Commision Calculation'))
            ->display(function ($x) {
                if ($x == 'Percentage') {
                    return $this->commission_type_value . "%";
                } else {
                    return   $this->commission_type;
                }
            })->sortable(); */

        $grid->column('balance', __('Balance (UGX)'))->display(function ($b) {
            return  number_format($b);
        })->sortable();



        /* $grid->column('house_id', __('House'))
            ->display(function ($x) {
                return $this->house->name;
            })
            ->hide()
            ->sortable(); */
        $grid->column('room_id', __('Room'))
            ->display(function ($x) {
                if ($this->renting == null) {
                    return "No Renting";
                }
                if ($this->renting->room == null)
                    return "No room";
                return $this->renting->room->name;
            })->sortable();


        /* $grid->column('landload_id', __('Landlord'))->display(function ($x) {
            $loc = Landload::find($x);
            if ($loc != null) {
                return $loc->name;
            }
            return $x;
        })->sortable(); */



        $grid->column('details', __('Details'))->hide();

        $grid->column('print', __('PRINT RECEIPT'))->display(function () {
            $link = url('receipt?id=' . $this->id);
            return '<b><a target="_blank" href="' . $link . '">PRINT RECEIPT</a></b>';
        });


        $grid->column('payment_method', __('Payment method'))->hide();
        $grid->column('payment_destination', __('Payment destination'))->hide();
        $grid->column('transaction_number', __('Transaction number'))->hide();
        $grid->column('account_number', __('Account number'))->hide();

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
        $show = new Show(TenantPayment::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('renting_id', __('Renting id'));
        $show->field('tenant_id', __('Tenant id'));
        $show->field('amount', __('Amount'));
        $show->field('securty_deposit', __('securty_deposit'));
        $show->field('days_before', __('days_before'));
        $show->field('balance', __('Balance'));
        $show->field('details', __('Details'));
        $show->field('payment_method', __('Payment method'));
        $show->field('payment_destination', __('Payment destination'));
        $show->field('transaction_number', __('Transaction number'));
        $show->field('account_number', __('Account number'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new TenantPayment());

        $form->date('created_at', __('Date'))->default(date('Y-m-d'))->rules('required')->required();

        $invoices = [];
        foreach (Renting::where([])->orderBy('id', 'desc')->get() as $key => $v) {
            // if ($v->balance > 0) {
            //     continue;
            // }
            $invoices[$v->id] = "#" . $v->id . " - ROOM: " . $v->room->name . ", Tenant: " . $v->tenant->name . " , Balance: UGX " . number_format($v->balance);
        }
        $form->select('renting_id', __('Renting - Invoice'))
            ->options($invoices)
            ->rules('required')
            ->required();

        /* 
        $form->number('tenant_id', __('Tenant id')); 
        $form->number('balance', __('Balance'))->rules('required')->required();         
                $form->textarea('details', __('Details')); 
        */
        $form->decimal('amount', __('Amount Paid'))->rules('required')->required();
        $form->decimal('securty_deposit', __('Security Deposit'));
        $form->decimal('days_before', __('Days Before'));
        $form->decimal('garbage_amount', __('Garbage Amount')); 

        $form->radio('payment_method', __('Payment method'))
            ->options([
                'Cash' => 'Cash',
                'Bank' => 'Bank',
                'Mobile Money' => 'Mobile Money',
            ])
            ->when(['Mobile Money'], function ($form) {
                $form->text('account_number', __('Phone number'));
                $form->text('transaction_number', __('Transaction ID'));
            })
            ->when(['Bank'], function ($form) {
                $form->text('account_number', __('Bank Account number'));
                $form->text('transaction_number', __('Transaction ID'));
            })
            ->when(['Cash'], function ($form) {
                $form->text('payment_destination', __('Cash received by'));
            })
            ->rules('required')->required();
        //garbage_amount




        return $form;
    }
}
