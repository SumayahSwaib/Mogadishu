<?php

namespace App\Admin\Controllers;

use App\Models\House;
use App\Models\Location;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class HouseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Estates';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new House());

        $grid->filter(function ($filter) {
            // Remove the default id filter
            $filter->disableIdFilter();
            // Landlord filter retired together with the Landload model.

            $filter->equal('region_id', 'Filter by district')
                ->select(function ($value) {
                    return SelectOptionsController::districtOption($value);
                })
                ->config('minimumInputLength', 0)
                ->ajax(admin_url('select-options/districts'));

            $filter->equal('area_id', 'Filter by Area')
                ->select(function ($value) {
                    return SelectOptionsController::subCountyOption($value);
                })
                ->config('minimumInputLength', 0)
                ->ajax(admin_url('select-options/sub-counties'));
        });


        $grid->quickSearch('name')->placeholder('Search by name....');
        /* Counts/price range come from SQL aggregates: loading the Room models
           would fire Room's name_text accessor (and a house lookup) per row. */
        $grid->model()
            ->withCount(['rooms', 'occupied_rooms', 'vacant_rooms'])
            ->withMin('rooms as rooms_min_price', 'price')
            ->withMax('rooms as rooms_max_price', 'price')
            ->orderBy('id', 'desc');
        $grid->disableBatchActions();
        $grid->column('id', __('No.'))->sortable();

        $grid->column('image', __('Photo'))
            ->lightbox(['width' => 50, 'height' => 50])
            ->sortable();

        $grid->column('name', __('Estate Name'))->sortable();
        // Landlord column retired together with the Landload model.
        $grid->column('region_id', __('Region'))->display(function ($x) {
            $loc = Location::find($x);
            if ($loc != null) {
                return $loc->name_text;
            }
            return $x;
        })->hide();
        $grid->column('area_id', __('Area'))->display(function ($x) {
            $loc = Location::find($x);
            if ($loc != null) {
                return $loc->name_text;
            }
            return $x;
        })->sortable();
        $grid->column('rooms_count', __('No. of Rooms'))
            ->display(function ($x) {
                $x = (int) ($this->rooms_count ?? 0);
                return '<a target="_blank" title="View These Rooms" class="d-block text-left  text-primary" style="font-size: 16px; text-align: center;" href="' . admin_url('rooms?house_id=' . $this->id) . '" ><b>' . $x . '</b></a>';
            });
        $grid->column('_rooms', __('Occupied Rooms'))
            ->display(function () {
                $x = (int) ($this->occupied_rooms_count ?? 0);
                return '<a target="_blank" title="View These Rooms" class="d-block text-left  text-primary" style="font-size: 16px; text-align: center;" href="' . admin_url('rooms?house_id=' . $this->id) . '" ><b>' . $x . '</b></a>';
            });
        $grid->column('v_rooms', __('Vancant Rooms'))
            ->display(function () {
                $x = (int) ($this->vacant_rooms_count ?? 0);
                return '<a target="_blank" title="View These Rooms" class="d-block text-left text-primary" style="font-size: 16px; text-align: center;" href="' . admin_url('rooms?house_id=' . $this->id) . '" ><b>' . $x . '</b></a>';
            });
        $grid->column('range', __('Price Range (UGX)'))
            ->display(function ($x) {
                return Utils::number_format($this->rooms_min_price, '') . " - " . Utils::number_format($this->rooms_max_price, '');
            });
        $grid->column('address', __('Address'))->hide();

        $grid->column('attachment', __('Attachment'))->hide();
        $grid->column('details', __('Details'))->hide();
        $grid->column('created_at', __('Registered'))->display(function ($x) {
            return Utils::my_date($x);
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
        $show = new Show(House::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('landload_id', __('Landload id'));
        $show->field('region_id', __('Region id'));
        $show->field('area_id', __('Area id'));
        $show->field('name', __('Name'));
        $show->field('address', __('Address'));
        $show->field('image', __('Image'));
        $show->field('attachment', __('Attachment'));
        $show->field('details', __('Details'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new House());

        // Landlord select retired together with the Landload model.

        $form->select('area_id', __('Select Area'))
            ->options(function ($id) {
                return SelectOptionsController::subCountyOption($id);
            })
            ->config('minimumInputLength', 0)
            ->ajax(admin_url('select-options/sub-counties'))
            ->rules('required');

        $form->text('name', __('Estate Name'))->rules('required');
        $form->text('address', __('Full Address'))->rules('required');
        $form->image('image', __('Image'));
        $form->file('attachment', __('Attachment'));
        $form->quill('details', __('Details'));

        $form->hidden('region_id', __('Region id'))->default(1);
        return $form;
    }
}
