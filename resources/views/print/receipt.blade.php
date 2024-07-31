<?php
$link = public_path('css/bootstrap-print.css');
use App\Models\Utils;
use App\Models\TenantPayment;

$receipt = TenantPayment::find($_GET['id']);
$logo_link = public_path('/logo-1.png');
$sign = public_path('/sign.jpg');
$imagelink = url('floorimages/logo-1.png' );

/* dd($receipt->details);
 */?>

<!DOCTYPE html>
<html lang="en">

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('css/bootstrap-print.css') }}">
    <title>Payment receipt</title>
    <style>
        @page {
            size: A4 portrait;
        }
    </style>
</head>

<body>
    <div class="receipt  p-3 pb-4" style="border: solid black .2rem;">
        <table class="w-100 ">
            <tbody>
                <tr>
                    {{-- <td style="width: 18%;" class="pr-2">
                        <img class="img-fluid" src="{{$imagelink}}">
                    </td> --}}
                    <td class=" text-center">
                        <p class="p-0 m-0" style="font-size: 2.5rem;"><b>NDEGE ESTATE LIMITED</b></p>
                        <p class="mt-1">Dealers in Rental Houses</p>
                        <p class="mt-1">P.O.BOX: <b>28044 - Kampala - Uganda</b>
                        
                        </p>
                    </td>
                    <td style="width: 15%; text-align: right;">
                        <b>No. <span style="color: red;">{{ $receipt->id }}</span></b>
                        <br><br><br>
                    </td>
                </tr>
            </tbody>
        </table>

        <h2 class="text-center h4 mb-4 mt-2"><u>RECEIPT &#40;ROOM NUMBER {{$receipt->renting->room->name }}&#41;</u></h2>

        <p class="text-right mb-2"><b>{{ Utils::my_date($receipt->created_at) }}</b></p>
        <p>Received sum of <b>UGX {{ number_format($receipt->amount +$receipt->securty_deposit+ $receipt->days_before) }}</b> in words:
            <b>{{ Utils::convert_number_to_words($receipt->amount +$receipt->securty_deposit+ $receipt->days_before) }}</b>  From<b>  {{ $receipt->tenant->name}} </b></p>
        
           <p class="mt-3 mb-1">
            Rent Amount : <b>UGX {{ number_format($receipt->amount) }}</b> 
                {{ $receipt->details }} 
            
           </p>

         
             
          
                @if($receipt->days_before > 0)
            
                <p class="mt-1 mb-1">Payment of the remaining days of the month: <b>UGX {{ number_format($receipt->days_before) }}</b></p>
            
            @endif 

            @if($receipt->securty_deposit > 0)
            
            <p class="mt-1 mb-1">Security Deposit: <b>UGX {{ number_format($receipt->securty_deposit) }}</b></p>
        
            @endif
        </p>
           
       </p>
       <p class="mt-3 mb-3">Balance: <b>UGX {{ number_format($receipt->balance) }}</b></p>
       
 
  
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="  d-inline p-2 px-3"
                        style="font-weight: 800; font-size: 1.4rem; border: solid black .2rem;">
                        UGX {{ number_format($receipt->amount +$receipt->securty_deposit+ $receipt->days_before) }}
                    </div>
                </td>
                <td class="text-right">
                    Approved by <b>.............................</b> 
                </td>
            </tr>
        </table>


    </div>
</body>

</html>
