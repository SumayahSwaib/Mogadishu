<?php

namespace App\Admin\Controllers;

use App\Models\Expense;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ExpenseController extends AdminController
{
    protected $title = 'Expenses';

    protected function grid()
    {
        $grid = new Grid(new Expense());

        // Disable batch delete/edit
        $grid->disableBatchActions();
        $grid->model()->orderBy('id', 'desc');

        // Quick search on name & description
        $grid->quickSearch('name', 'description')->placeholder('Search by particulars or description...');

        // Filters
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();

            $filter->equal('category', 'Category')
                ->select([
                    'Salary' => 'Salary',
                    'Security Deposit Refund' => 'Security Deposit Refund',
                    'Transport' => 'Transport',
                    'Bank Expenditure (DFCU)' => 'Bank Expenditure (DFCU)',
                    'Meals' => 'Meals',
                    'Brokers' => 'Brokers',
                    'Garbage Collectors' => 'Garbage Collectors',
                    'Others' => 'Others',
                ]);

            $filter->between('expense_date', 'Expense Date')->date();
        });

        // Default ordering
        $grid->model()->orderBy('expense_date', 'desc');

        // Columns
        $grid->column('id', 'ID')->sortable();

        $grid->column('created_at', 'Created')
            ->display(function ($v) {
                return date('d M, Y', strtotime($v));
            })
            ->sortable();

        $grid->column('expense_date', 'Expense Date')
            ->display(function ($v) {
                return date('d M, Y', strtotime($v));
            })
            ->sortable();

        $grid->column('name', 'Particulars')->sortable();

        $grid->column('category', 'Category')
            ->display(function ($c) {
                // simple badge styling
                $color = match ($c) {
                    'Salary'                         => 'primary',
                    'Security Deposit Refund'        => 'success',
                    'Transport'                      => 'info',
                    'Bank Expenditure (DFCU)'        => 'warning',
                    'Meals'                          => 'danger',
                    'Brokers', 'Garbage Collectors'  => 'secondary',
                    default                          => 'light',
                };
                return "<span class='badge bg-{$color}'>{$c}</span>";
            });

        $grid->column('description', 'Description')->limit(40);

        $grid->column('amount', 'Amount (UGX)')
            ->display(function ($amt) {
                return number_format($amt);
            })
            ->totalRow(function ($amt) {
                return number_format($amt);
            })
            ->sortable();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Expense::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('created_at', 'Created at')->as(function ($v) {
            return date('d M, Y H:i', strtotime($v));
        });
        $show->field('expense_date', 'Expense date')->as(function ($v) {
            return date('d M, Y', strtotime($v));
        });
        $show->field('name', 'Particulars');
        $show->field('category', 'Category');
        $show->field('description', 'Description');
        $show->field('amount', 'Amount (UGX)')->as(fn($v) => number_format($v));

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Expense());

        $form->text('name', 'Particulars')->rules('required');
        $form->select('category', 'Category')->options([
            'Salary'                         => 'Salary',
            'Security Deposit Refund'        => 'Security Deposit Refund',
            'Transport'                      => 'Transport',
            'Bank Expenditure (DFCU)'        => 'Bank Expenditure (DFCU)',
            'Meals'                          => 'Meals',
            'Brokers'                        => 'Brokers',
            'Garbage Collectors'             => 'Garbage Collectors',
            'Others'                         => 'Others',
        ])->rules('required');
        $form->textarea('description', 'Description')->rows(3)->rules('required');
        $form->decimal('amount', 'Amount (UGX)')->rules('required|numeric|min:0');
        $form->date('expense_date', 'Expense date')->default(date('Y-m-d'))->rules('required|date');

        return $form;
    }
}
