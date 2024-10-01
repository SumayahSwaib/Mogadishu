<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\LandloadPayment;
use App\Models\Renting;
use App\Models\TenantPayment;
use App\Models\Utils;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('generate-class', [MainController::class, 'generate_class']);
Route::get('process-things', [Utils::class, 'process_things']);

Route::get('cv', function () {
    //return view('print/print-admission-letter');
    $pdf = App::make('dompdf.wrapper');
    //$pdf->setOption(['DOMPDF_ENABLE_REMOTE' => false]);

    //$pdf->loadHTML(view('print/print-admission-letter'));
    $pdf->loadHTML(view('print/cv'));
    return $pdf->stream();
});
Route::get('ground-floor', function () {
    return view('floors/floor1');
});

Route::get('first-floor', function () {
    return view('floors/floor2');
});
Route::get('second-floor', function () {
    return view('floors/floor3');
});

Route::get('third-floor', function () {
    return view('floors/floor4');
});
Route::get('forth-floor', function () {
    return view('floors/floor5');
});
Route::get('fifth-floor', function () {
    return view('floors/floor6');
});



Route::get('invoice', function () {
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view('print/invoice'));
    return $pdf->stream();
});

//tenant receipts
Route::get('receipt', function () {
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view('print/receipt'));
    return $pdf->stream();
});

//landlords report
/* Route::get('landlord-report', function () {

    $report = \App\Models\LandLordReport::find(request()->id);

    /* if ($report == null) {
        die("Report not found.");
    }

    $landLord = \App\Models\Tenant::find($report->landload_id);
    if ($landLord == null) {
        die("Landlord not found.");
    }
    if ($landLord == null) {
        die("Landlord not found.");
    } 

    //start_date
    //end_date
    $start_date = Carbon::parse($report->start_date)->format('Y-m-d');
    $end_date = Carbon::parse($report->end_date)->format('Y-m-d');
    $tempTenantPayments = TenantPayment::where([
         'landload_id' => $landLord->id ])->whereBetween('created_at', [$start_date, $end_date])->get();

   // $buldings = [];
    //$buldings_ids = [];
    $total_income = 0;
    $total_commission = 0;
    $total_land_lord_disbashment = 0;
    $total_landlord_revenue = 0;
    $tenantPayments = [];
    foreach ($tempTenantPayments as $payment) {

        //created_at not in range of start_date and end_date continue
        if (Carbon::parse($payment->created_at)->format('Y-m-d') < $start_date || Carbon::parse($payment->created_at)->format('Y-m-d') > $end_date) {
            continue;
        }

        $total_income += $payment->amount;
        $total_commission += $payment->commission_amount;
        $total_landlord_revenue += $payment->landlord_amount;
        if (!in_array($payment->house_id, $buldings_ids)) {
            $buldings_ids[] = $payment->house_id;
            $buldings[] = $payment->house;
        }
    }



    $pdf = App::make('dompdf.wrapper');
    $rentings = Renting::where([
        //'landload_id' => $landLord->id
    ])->orderBy('start_date', 'DESC')
        ->limit(250)
        ->whereBetween('created_at', [$start_date, $end_date])
        ->get();


    $landlordPayments = LandloadPayment::where([
       // 'landload_id' => $landLord->id
    ])->orderBy('id', 'DESC')
        ->whereBetween('created_at', [$start_date, $end_date])
        ->get();

    $total_land_lord_disbashment = 0;
    foreach ($landlordPayments as $payment) {
        $total_land_lord_disbashment += $payment->amount;
    }

    $pdf->loadHTML(view('print/landlord-report-1', compact(
        'rentings',
        'landlordPayments',
       // 'landLord',
        'total_income',
        'buldings',
        'tenantPayments',
        'total_commission',
        'total_landlord_revenue',
        'total_land_lord_disbashment',
        'report',
        'start_date',
        'end_date'
    )));
    return $pdf->stream();
}); */


// used landloard report route
Route::get('landlord-report-1', function () {
    $report = \App\Models\LandLordReport::find(request()->id);

    if ($report == null) {
        die("Report not found.");
    }


    $start_date = Carbon::parse($report->start_date)->format('Y-m-d');
    $end_date = Carbon::parse($report->end_date)->format('Y-m-d');

    //display range

    /* $buldings = [];
    $buldings_ids = []; */


   
    $rentings = Renting::where([])->orderBy('start_date', 'ASC')
    /* ->whereBetween('start_date', [$start_date, $end_date]) */
    ->get();
    /* $is_overstay = Renting::where(['is_overstay'=>'Yes'])->orderBy('start_date', 'ASC')
    ->whereBetween('start_date', [$start_date, $end_date])
    ->get(); */

    $total_income = 0;

    foreach ($rentings as $renting) {

        $created_at = Carbon::parse($renting->created_at)->format('Y-m-d');
        //created_at not in range of start_date and end_date continue


        /* if (!in_array($renting->house_id, $buldings_ids)) {
            $buldings[] = $renting->house;
        } */
        $total_income += $renting->amount_paid;
    }


   

    $landlordPayments = LandloadPayment::where([])->orderBy('id', 'DESC')
        ->whereBetween('created_at', [$start_date, $end_date])
        ->get();

    $tenantPayments = TenantPayment::where([])
        ->whereBetween('created_at', [$start_date, $end_date])
        ->orderBy('id', 'DESC')
        ->get(); 

    $isView = true;
    $data = compact(
        'rentings',

        'tenantPayments',

        'total_income',
        //'buldings',
        'report',
        'start_date',
        'isView',
        'end_date'
    );

    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view("print.landlord-report-1", $data));
    return $pdf->stream();
       
});




Route::get('quotation', function () {
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view('print/quotation'));
    return $pdf->stream();
});

Route::get('delivery', function () {
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view('print/delivery'));
    return $pdf->stream();
});
