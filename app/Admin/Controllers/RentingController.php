<?php

namespace App\Admin\Controllers;

use App\Models\Landload;
use App\Models\Renting;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RentingController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Renting - Invoices';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Renting());
        // $grid->disableCreateButton();
        //add button on tools that ipen s process-billings to new tab
        $grid->tools(function ($tools) {
            $tools->append('<a href="' . url('process-billings') . '" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-refresh"></i> Process Billings</a>');
        });

        $grid->filter(function ($filter) {
            // Remove the default id filter
            $filter->disableIdFilter();
            /*  $filter->equal('landload_id', 'Filter by landlord')
                ->select(
                    Landload::where([])->orderBy('name', 'Asc')->get()->pluck('name', 'id')
                ); */
            $filter->equal('tenant_id', 'Filter By Tenant')
                ->select(
                    Tenant::get_items()
                );

            $filter->equal('room_id', 'Filter by room')
                ->select(
                    Room::get_all_rooms()
                );
            $filter->between('created_at', 'Filter by Date Created')->date();
            $filter->between('start_date', 'Filter by Start Date')->date();
            $filter->between('end_date', 'Filter by End Date')->date();
            $filter->group('balance', function ($group) {
                $group->gt('greater than');
                $group->lt('less than');
                $group->equal('equal to');
            });
        });


        $grid->model()->orderBy('id', 'desc');
        $grid->disableBatchActions();
        $grid->column('id', __('ID'))->sortable();
        $grid->column('created_at', __('Created'))->display(function ($x) {
            return Utils::my_date_4($x);
        })->sortable();

        /*  $grid->column('house_id', __('House'))
            ->display(function ($x) {
                return $this->house->name;
            })
            ->hide()
            ->sortable(); */
        $grid->column('room_id', __('Room'))
            ->display(function ($x) {
                return $this->room->name;
            })->sortable();
        $grid->column('tenant_id', __('Tenant'))
            ->display(function ($x) {
                return $this->tenant->name;
            })->sortable();
        $grid->column('start_date', __('Start date'))->sortable();
        $grid->column('end_date', __('End date'))
            ->display(function ($x) {
                return Utils::my_date_4($x);
            })->sortable();
        $grid->column('is_overstay', __('Overstay'))
            ->filter(['Yes' => 'Overstayed', 'No' => 'Within time'])
            ->dot([
                'Yes' => 'danger',
                'No' => 'success'
            ])
            ->sortable();
        $grid->column('number_of_months', __('Months'))
            ->hide()
            ->sortable();
        //total rent amount rent_amount
        $grid->column('rent_amount', __('Rent Amount (UGX)'))
            ->display(function ($x) {
                return number_format($x);
            })->totalRow(function ($x) {
                return  number_format($x);
            })->sortable();
        //security_fee
        $grid->column('security_fee', __('Security Fee (UGX)'))
            ->display(function ($x) {
                return number_format($x);
            })->totalRow(function ($x) {
                return  number_format($x);
            })->sortable();

        //garbage_fee
        $grid->column('garbage_fee', __('Garbage Fee (UGX)'))
            ->display(function ($x) {
                return number_format($x);
            })->totalRow(function ($x) {
                return  number_format($x);
            })->sortable();

        $grid->column('payable_amount', __('Payable amount (UGX)'))
            ->display(function ($x) {
                return number_format($x);
            })
            ->totalRow(function ($x) {
                return  number_format($x);
            })->sortable();

        $grid->column('receipts', __('Receipts (UGX)'))
            ->display(function ($x) {
                $x = $this->payments->sum('amount');
                $x = number_format($x);
                return '<a target="_blank" title="View These Receipts" class="d-block text-left  " style="font-size: 16px; text-align: center;" href="' . admin_url('tenant-payments?renting_id=' . $this->id) . '" ><b>' . $x . '</b></a>';
            });


        $grid->column('balance', __('Balance (UGX)'))
            ->display(function ($x) {
                return number_format($x);
            })->totalRow(function ($x) {
                return  number_format($x);
            })->sortable();



        /* $grid->column('landload_id', __('Landlord'))->display(function ($x) {
            $loc = Landload::find($x);
            if ($loc != null) {
                return $loc->name;
            }
            return $x;
        })->sortable(); */

        $grid->column('invoice_status', __('STATUS'))
            ->filter(['Active' => 'Active', 'Not Active' => 'Not Active'])
            ->label([
                'Not Active' => 'danger',
                'Active' => 'success'
            ])->sortable();
        $grid->column('is_in_house', __('In House'))->hide();
        $grid->column('remarks', __('Remarks'))->editable();



        /* 

fully_paid		
commision_type	
commision_type_value		
update_billing	
	
	
invoice_as_been_billed
*/
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
        $show = new Show(Renting::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('house_id', __('House id'));
        $show->field('tenant_id', __('Tenant id'));
        $show->field('start_date', __('Start date'));
        $show->field('end_date', __('End date'));
        $show->field('number_of_months', __('Number of months'));
        $show->field('discount', __('Discount'));
        $show->field('payable_amount', __('Payable amount'));
        $show->field('balance', __('Balance'));
        $show->field('is_in_house', __('Is in house'));
        $show->field('is_overstay', __('Is overstay'));
        $show->field('remarks', __('Remarks'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Renting());

        /*     $r = Renting::find(199);
        $r->remarks .= " - " . $r->id;
        $r->invoice_status .= "Active";
        $r->save();
        die("done");  */

        if ($form->isCreating()) {

            $room_id = request()->get('room_id');
            $room = null;
            if ($room_id != null) {
                $room = Room::find($room_id);
            }

            if ($room != null) {

                //if room not vacant, then redirect to room list
                if ($room->status != 'Vacant') {
                    admin_error("Room '{$room->name}' is not vacant, please select another room.", 'error');
                    return redirect(admin_url('rooms'));
                }

                $form->hidden('room_id')->default($room->id);
                $form->display('room_name', __('Appartment'))->default(
                    "#" . $room->id . " - " . $room->name . ", " . $room->house->name . " - UGX " . number_format($room->price)
                );
            } else {
                $form->select('room_id', __('Appartment'))->options(Room::get_vacant_rooms())
                    ->rules('required')
                    ->required();
            }


            $form->select('tenant_id', __('Tenant'))->options(Tenant::get_items())
                ->rules('required')
                ->required();
        } else {

            $form->select('room_id', __('Appartment'))->options(function ($x) {
                $r = Room::where('id', $x)->first();
                return [
                    $r->id => $r->name
                ];
            })->readOnly();
            $form->select('tenant_id', __('Tenant'))->options(Tenant::get_items())
                ->options(function ($x) {
                    $r = Tenant::where('id', $x)->first();
                    return [
                        $r->id => $r->name
                    ];
                })->readOnly();
        }
        $form->date('start_date', __('Start date'))->rules('required')
            ->required();
        $form->decimal('number_of_months', __('Number of months'))
            ->rules('required')
            ->required();
        $form->hidden('discount', 'discount')->default(0);
        if (!$form->isCreating()) {
            $form->divider();
            $form->radio('update_billing', __('Update billing'))
                ->options(['Yes' => 'Yes', 'No' => 'No'])
                ->rules('required')
                ->default('No');
        }
        $form->radio('invoice_status', __('Invoice_status'))
            ->options([
                'Active' => 'Active',
                'Not Active' => 'Not Active',
            ])
            ->rules('required')
            ->default('Active');

        //security_fee
        $form->decimal('security_fee', __('Security Fee (UGX)'))
            ->rules('required')
            ->required()
            ->help('This is the security fee for the room, it is refundable at the end of the renting period. Terms and conditions apply.');

        //garbage_fee
        $form->decimal('garbage_fee', __('Garbage Fee (UGX)'))
            ->default(0)
            ->rules('required')
            ->required()
            ->help('This is the garbage fee for the room, it is not refundable. It is used to cover the cost of garbage collection and disposal.');
        $form->textarea('remarks', __('Remarks'));
        return $form;
    }
}
