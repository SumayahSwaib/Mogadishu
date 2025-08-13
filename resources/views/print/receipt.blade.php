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
            margin: 0.8cm;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            background: #fff;
            color: #222;
        }

        .receipt {
            border: 1px solid #222;
            border-radius: 4px;
            max-width: 100%;
            margin: 8px auto;
            background: #fafbfc;
            padding: 16px 18px 14px 18px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        .receipt-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .company-title {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }

        .company-info {
            font-size: 0.85rem;
            color: #555;
        }

        .receipt-no {
            font-size: 0.95rem;
            color: #b00;
            font-weight: 600;
            text-align: right;
        }

        .receipt-title {
            font-size: 1rem;
            font-weight: 600;
            margin: 10px 0 6px 0;
            letter-spacing: 0.5px;
        }

        .receipt-details {
            margin-bottom: 10px;
        }

        .receipt-details p {
            margin: 0 0 4px 0;
            line-height: 1.3;
        }

        .amount-box {
            display: inline-block;
            font-weight: 700;
            font-size: 1rem;
            border: 1px solid #222;
            border-radius: 3px;
            padding: 4px 12px;
            background: #f5f5f5;
            margin-top: 6px;
        }

        .approved {
            text-align: right;
            font-size: 0.85rem;
            padding-top: 10px;
        }

        .balance {
            margin: 10px 0 0 0;
            font-size: 0.95rem;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-1 {
            margin-top: 3px;
        }

        .mt-2 {
            margin-top: 6px;
        }

        .mt-3 {
            margin-top: 10px;
        }

        .mb-1 {
            margin-bottom: 3px;
        }

        .mb-2 {
            margin-bottom: 6px;
        }

        .mb-3 {
            margin-bottom: 10px;
        }

        .pb-2 {
            padding-bottom: 6px;
        }

        .pb-4 {
            padding-bottom: 12px;
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
                    {{ number_format($receipt->amount) }}</b>
                in words:
                <b>{{ Utils::convert_number_to_words($receipt->amount) }}</b>
                from <b>{{ $receipt->tenant->name }}</b> {!! $receipt->details !!} distributed as follows;-
            </p>
            @if (!empty($receipt->rent_amount) && $receipt->rent_amount > 0)
                <p class="mt-2">
                    Rent: <b>UGX {{ number_format($receipt->rent_amount) }}</b>
                </p>
            @endif

            @if (((int) $receipt->securty_deposit) > 0)
                <p>Security Deposit: <b>UGX {{ number_format($receipt->securty_deposit) }}</b></p>
            @endif
            @if ((int) $receipt->days_before > 0)
                <p>Remaining days: <b>UGX {{ number_format($receipt->days_before) }}</b></p>
            @endif

            @if (((int) $receipt->garbage_amount) > 0)
                <p>Garbage Amount: <b>UGX {{ number_format($receipt->garbage_amount) }}</b></p>
            @endif
        </div>

        <div class="balance">
            Balance: <b>UGX {{ number_format($receipt->balance) }}</b>
        </div>

        <table style="width: 100%; margin-top: 10px;">
            <tr>
                <td>
                    <div class="amount-box">
                        UGX {{ number_format($receipt->amount) }}
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
