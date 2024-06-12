<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Models\Candidate;
use App\Models\Garden;
use App\Models\Group;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Location;
use App\Models\Person;
use App\Models\Product;
use App\Models\Renting;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantPayment;
use App\Models\Utils;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Auth;
use SplFileObject;

class HomeController extends Controller
{
    public function index(Content $content)
    {

        $u = Auth::user();
        $content
            ->title('MOGADISHU RESIDENCE - Dashboard')
            ->description('Hello ' . $u->username . "!");
         


        $content->row(function (Row $row) {
            $row->column(4, function (Column $column) {
                $column->append(view('widgets.dashboard-groundfloor', [
                   
                    'rooms' => Room::where('floor', 'Floor1')->get()
                ]));
            });
            $row->column(4, function (Column $column) {
                $column->append(view('widgets.dashboard-floor1', [
                    'rooms' => Room::where('floor', 'Floor2')->get()
                    
                ]));
            });
            $row->column(4, function (Column $column) {
                $column->append(view('widgets.dashboard-floor2', [
                    'rooms' => Room::where('floor', 'Floor3')->get()
                   
                ]));
            });
            
        });
        $content->row(function (Row $row) {
            $row->column(4, function (Column $column) {
                $column->append(view('widgets.dashboard-floor3', [
                    'rooms' => Room::where('floor', 'Floor4')->get()
                ]));
            });
            $row->column(4, function (Column $column) {
                $column->append(view('widgets.dashboard-floor4', [
                    'rooms' => Room::where('floor', 'Floor5')->get()
                    
                ]));
            });
            $row->column(4, function (Column $column) {
                $column->append(view('widgets.dashboard-floor5', [
                    'rooms' => Room::where('floor', 'Floor6')->get()
                    
                ]));
            });
           
        });
        // titlle
        $content->row(function (Row $row) {
            $row->column(12, function (Column $column) {
                $column->append(view('widgets.dashboard-title', [
                    'title' => 'MOGADISHU RESIDENCE',
                    'sub_title' => 'Dashboard',
                    'icon' => 'fa fa-dashboard',
                    'color' => 'bg-aqua'
                ]));
            });
        });
        
        $content->row(function (Row $row) {
         
           
          $row->column(2, function (Column $column) {
                $column->append(view('widgets.dashboard-image', [
                   
                ]));
            }); 
            $row->column(2, function (Column $column) {
                $column->append(view('widgets.dashboard-image1', [
                    
                ]));
            }); 
            $row->column(2, function (Column $column) {
                $column->append(view('widgets.dashboard-image2', [
                    
                ]));
            }); 
            $row->column(2, function (Column $column) {
                $column->append(view('widgets.dashboard-image3', [
                    /* 'rooms' => Room::all(),
                    'tenants' => Tenant::all(),
                    'rentings' => Renting::all(),
                    'payments' => TenantPayment::all() */
                ]));
            });
            $row->column(2, function (Column $column) {
                $column->append(view('widgets.dashboard-image4', [
                    
                ]));
            });
            $row->column(2, function (Column $column) {
                $column->append(view('widgets.dashboard-image5', [
                    
                ]));
            });
           
        });

        
        return $content;

        // end of the dashboard
        $content->row(function (Row $row) {
            $row->column(4, function (Column $column) {
                $column->append(view('widgets.dashboard-floor1', [
                    //'rooms' => Room::all()
                    'rooms' => Room::where('floor', 'Floor1')->get()
                ]));
            });
            $row->column(4, function (Column $column) {
                $column->append(view('widgets.dashboard-floor2', [
                    'rooms' => Room::where('floor', 'Floor2')->get()
                    /* 'rooms' => Room::all(),
                    'tenants' => Tenant::all(),
                    'rentings' => Renting::all() */
                ]));
            });
            $row->column(4, function (Column $column) {
                $column->append(view('widgets.dashboard-floor3', [
                    'rooms' => Room::where('floor', 'Floor2')->get()
                    /* 'rooms' => Room::all(),
                    'tenants' => Tenant::all(),
                    'rentings' => Renting::all() */
                ]));
            });
            /* $row->column(3, function (Column $column) {
                $min = new Carbon();
                $max = new Carbon();
                $max->subDays(0);
                $min->subDays((30));

                $column->append(view('widgets.dashboard-this-month', [
                    'rooms' => Room::whereBetween('created_at', [$min, $max])->get(),
                    'tenants' => Tenant::whereBetween('created_at', [$min, $max])->get(),
                    'rentings' => Renting::whereBetween('start_date', [$min, $max])->get(),
                    'payments' => TenantPayment::whereBetween('created_at', [$min, $max])->get()
                ]));
            }); */
            /* $row->column(3, function (Column $column) {
                $column->append(view('widgets.dashboard-all-time', [
                    'rooms' => Room::all(),
                    'tenants' => Tenant::all(),
                    'rentings' => Renting::all(),
                    'payments' => TenantPayment::all()
                ]));
            }); */
        });
        $content->row(function (Row $row) {
            $row->column(6, function (Column $column) {
                $column->append(view('widgets.by-categories', []));
            });
            $row->column(6, function (Column $column) {

                $column->append(view('widgets.by-districts', []));
                // $column->append(Dashboard::dashboard_events());
            });
        });


        return $content;

        $u = Admin::user();


        $content->row(function (Row $row) {
            $row->column(3, function (Column $column) {
                $column->append(view('widgets.box-5', [
                    'is_dark' => false,
                    'title' => 'New members',
                    'sub_title' => 'Joined 30 days ago.',
                    'number' => number_format(rand(100, 600)),
                    'link' => 'javascript:;'
                ]));
            });
            $row->column(3, function (Column $column) {
                $column->append(view('widgets.box-5', [
                    'is_dark' => false,
                    'title' => 'Products & Services',
                    'sub_title' => 'All time.',
                    'number' => number_format(rand(1000, 6000)),
                    'link' => 'javascript:;'
                ]));
            });
            $row->column(3, function (Column $column) {
                $column->append(view('widgets.box-5', [
                    'is_dark' => false,
                    'title' => 'Job oppotunities',
                    'sub_title' => rand(100, 400) . ' jobs posted 7 days ago.',
                    'number' => number_format(rand(1000, 6000)),
                    'link' => 'javascript:;'
                ]));
            });
            $row->column(3, function (Column $column) {
                $column->append(view('widgets.box-5', [
                    'is_dark' => true,
                    'title' => 'System traffic',
                    'sub_title' => rand(100, 400) . ' mobile app, ' . rand(100, 300) . ' web browser.',
                    'number' => number_format(rand(100, 6000)),
                    'link' => 'javascript:;'
                ]));
            });
        });




        $content->row(function (Row $row) {
            $row->column(6, function (Column $column) {
                $column->append(view('widgets.by-categories', []));
            });
            $row->column(6, function (Column $column) {
                $column->append(view('widgets.by-districts', []));
            });
        });



        $content->row(function (Row $row) {
            $row->column(6, function (Column $column) {
                $column->append(Dashboard::dashboard_members());
            });
            $row->column(3, function (Column $column) {
                $column->append(Dashboard::dashboard_events());
            });
            $row->column(3, function (Column $column) {
                $column->append(Dashboard::dashboard_news());
            });
        });




        return $content;
        return $content
            ->title('Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }
}
