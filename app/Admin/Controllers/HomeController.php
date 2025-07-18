<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\House;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Renting;
use App\Models\TenantPayment;
use App\Models\Utils;
use Carbon\Carbon;
use Dflydev\DotAccessData\Util;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;

class HomeController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        // Current admin user
        $user = Admin::user();

        // Overall metrics
        $totalRooms     = Room::count();
        $occupiedRooms  = Room::where('status', 'Occupied')->count();
        $vacantRooms    = Room::where('status', 'Vacant')->count();
        $totalTenants   = Tenant::count();
        $totalRentings  = Renting::count();
        $totalPayments  = TenantPayment::sum('amount');

        // Last 30 days
        $periodEnd      = Carbon::now()->endOfDay();
        $periodStart    = Carbon::now()->subDays(30)->startOfDay();
        $newRooms       = Room::whereBetween('created_at', [$periodStart, $periodEnd])->count();
        $newTenants     = Tenant::whereBetween('created_at', [$periodStart, $periodEnd])->count();
        $newRentings    = Renting::whereBetween('start_date', [$periodStart, $periodEnd])->count();
        $recentPayments = TenantPayment::whereBetween('created_at', [$periodStart, $periodEnd])->sum('amount');

        // Header
        $content
            ->title(env('APP_NAME') . ' - Dashboard')
            ->description('Hello ' . $user->username . '!');



        // Get all unique floors from rooms
        $floors = Utils::get_floors();

        // Floor overview using a single widget view
        $content->row(function (Row $row) use ($floors) {
            foreach ($floors as $floor) {
                $row->column(4, function (Column $column) use ($floor) {
                    $rooms = Room::where('floor', $floor)->get();
                    $FLOOR = Floor::where('name', $floor)->first();
                    $range = "";
                    if ($FLOOR != null) {
                        $range = $FLOOR->range;
                    }

                    $column->append(
                        view('widgets.dashboard-floor-overview', [
                            'floor' => $floor,
                            'rooms' => $rooms,
                            'range' => $range,
                        ])
                    );
                });
            }
        });

        // Title widget
        $content->row(function (Row $row) {
            $row->column(12, function (Column $column) {
                $column->append(view('widgets.dashboard-title', [
                    'title'     => env('APP_NAME'),
                    'sub_title' => 'Dashboard',
                    'icon'      => 'fa fa-dashboard',
                    'color'     => 'bg-aqua',
                ]));
            });
        });

        // Load houses with rooms & their rentings
        $houses = House::with([
            'rooms' => function ($q) {
                $q->orderBy('name');
            },
            'rooms.rentings'             // eager-load to avoid N+1
        ])->get();

        // For each room, grab its latest renting (by end_date)
        foreach ($houses as $house) {
            foreach ($house->rooms as $room) {
                if ($room->latest_renting == null) {
                    $room->latest_renting = null;
                }
                $room->latest_renting = $room
                    ->rentings
                    ->sortByDesc('end_date')
                    ->first();
            }
        }

        // Inject our grid at the very top
        $content->row(function (Row $row) use ($houses) {
            $row->column(12, function (Column $col) use ($houses) {
                $col->append(
                    view('admin.dashboard.house-room-grid', compact('houses'))
                );
            });
        });




        // Key stats widgets
        $content->row(function (Row $row) use (
            $totalRooms,
            $occupiedRooms,
            $vacantRooms,
            $totalTenants,
            $totalRentings,
            $totalPayments
        ) {
            $stats = [
                ['count' => $totalRooms,    'label' => 'Total Rooms',       'view' => 'widgets.dashboard-image'],
                ['count' => $occupiedRooms, 'label' => 'Occupied Rooms',    'view' => 'widgets.dashboard-image1'],
                ['count' => $vacantRooms,   'label' => 'Vacant Rooms',      'view' => 'widgets.dashboard-image2'],
                ['count' => $totalTenants,  'label' => 'Total Tenants',     'view' => 'widgets.dashboard-image3'],
                ['count' => $totalRentings, 'label' => 'Total Rentings',    'view' => 'widgets.dashboard-image4'],
                ['count' => $totalPayments, 'label' => 'Total Payments (UGX)', 'view' => 'widgets.dashboard-image5'],
            ];

            foreach ($stats as $stat) {
                $row->column(2, function (Column $column) use ($stat) {
                    $column->append(view($stat['view'], [
                        'count' => $stat['count'],
                        'label' => $stat['label'],
                    ]));
                });
            }
        });

        return $content;
    }
}
