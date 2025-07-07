<?php
use App\Models\Utils;
use App\Models\TenantPayment;

$receipt = TenantPayment::find($_GET['id']);
$imagelink = url('floorimages/logo-1.png');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('css/bootstrap-print.css') }}">
    <title>Payment Receipt</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            background: #fff;
            color: #222;
        }

        .receipt {
            border: 2px solid #222;
            border-radius: 8px;
            max-width: 700px;
            margin: 30px auto;
            background: #fafbfc;
            padding: 32px 36px 28px 36px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .receipt-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .company-title {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .company-info {
            font-size: 0.98rem;
            color: #555;
        }

        .receipt-no {
            font-size: 1.1rem;
            color: #b00;
            font-weight: 600;
            text-align: right;
        }

        .receipt-title {
            font-size: 1.15rem;
            font-weight: 600;
            margin: 18px 0 10px 0;
            letter-spacing: 1px;
        }

        .receipt-details {
            margin-bottom: 18px;
        }

        .receipt-details p {
            margin: 0 0 7px 0;
            line-height: 1.5;
        }

        .amount-box {
            display: inline-block;
            font-weight: 700;
            font-size: 1.15rem;
            border: 2px solid #222;
            border-radius: 6px;
            padding: 7px 18px;
            background: #f5f5f5;
            margin-top: 10px;
        }

        .approved {
            text-align: right;
            font-size: 0.97rem;
            padding-top: 18px;
        }

        .balance {
            margin: 18px 0 0 0;
            font-size: 1.05rem;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-1 {
            margin-top: 6px;
        }

        .mt-2 {
            margin-top: 12px;
        }

        .mt-3 {
            margin-top: 18px;
        }

        .mb-1 {
            margin-bottom: 6px;
        }

        .mb-2 {
            margin-bottom: 12px;
        }

        .mb-3 {
            margin-bottom: 18px;
        }

        .pb-2 {
            padding-bottom: 12px;
        }

        .pb-4 {
            padding-bottom: 24px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <table style="width: 100%;">
            <tr class="receipt-header">
                <td style="width: 80%;">
                    <div class="company-title">NDEGE ESTATE LIMITED</div>
                    <div class="company-info">Dealers in Rental Houses</div>
                    <div class="company-info">P.O.BOX: <b>28044 - Kampala - Uganda</b></div>
                </td>
                <td style="width: 20%;" class="receipt-no">
                    No.KA <span>{{ $receipt->id }}</span>
                </td>
            </tr>
        </table>

        <div class="receipt-title text-center">
            <u>RECEIPT (ROOM NUMBER {{ $receipt->renting->room->name }})</u>
        </div>

        <div class="receipt-details">
            <div class="text-right mb-2"><b>{{ Utils::my_date($receipt->created_at) }}</b></div>
            <p>
                Received sum of <b>UGX
                    {{ number_format($receipt->amount + $receipt->securty_deposit + $receipt->days_before) }}</b>
                in words:
                <b>{{ Utils::convert_number_to_words($receipt->amount + $receipt->securty_deposit + $receipt->days_before) }}</b>
                from <b>{{ $receipt->tenant->name }}</b>
            </p>
            <p class="mt-2">
                Rent Amount: <b>UGX {{ number_format($receipt->amount) }}</b>
                {!! $receipt->details !!}
            </p>
            <br>
            @if ((int) $receipt->days_before > 0)
                <p>Payment of the remaining days of the month: <b>UGX {{ number_format($receipt->days_before) }}</b></p>
            @endif
            @if ($receipt->securty_deposit > 0)
                <p>Security Deposit: <b>UGX {{ number_format($receipt->securty_deposit) }}</b></p>
            @endif
            {{-- garbage_amount --}}
            @if ($receipt->garbage_amount > 0)
                <p>Garbage Amount: <b>UGX {{ number_format($receipt->garbage_amount) }}</b></p>
            @endif
            

        </div>

        <div class="balance">
            Balance: <b>UGX {{ number_format($receipt->balance) }}</b>
        </div>

        <table style="width: 100%; margin-top: 18px;">
            <tr>
                <td>
                    <div class="amount-box">
                        UGX {{ number_format($receipt->amount + $receipt->securty_deposit + $receipt->days_before) }}
                    </div>
                </td>
                <td class="approved">
                    Approved by <b>.............................</b>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
