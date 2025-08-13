{{-- resources/views/print/landlord-report-1.blade.php --}}
@php
    use App\Models\Utils;
    $totalRentAmount = 0;
    $totalSecurityDeposit = 0;
    $totalRemainingDays = 0;
    $totalGarbageAmount = 0;
    $totalIncomeSubtotal = 0;
    foreach ($tenantPayments as $trans) {
        $totalRentAmount += $trans->rent_amount ?? 0;
        $totalSecurityDeposit += $trans->securty_deposit ?? 0;
        $totalRemainingDays += $trans->days_before ?? 0;
        $totalGarbageAmount += $trans->garbage_amount ?? 0;
    }
    $totalIncomeSubtotal = $totalRentAmount + $totalSecurityDeposit + $totalRemainingDays + $totalGarbageAmount;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} – Tenant Payments Report</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.7cm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            font-size: 0.7rem;
            line-height: 1.1;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        h1,
        h2,
        h3 {
            margin: 0;
            padding: 0;
        }

        .mb-1 {
            margin-bottom: 0.2rem;
        }

        .mb-2 {
            margin-bottom: 0.4rem;
        }

        .mt-1 {
            margin-top: 0.2rem;
        }

        .mt-2 {
            margin-top: 0.4rem;
        }

        hr {
            border: 0;
            border-top: 2px solid #0c5a0c;
            margin: 0.5rem 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
            font-size: 0.65rem;
        }

        .table th,
        .table td {
            border: 1px solid #0c5a0c;
            padding: 0.2rem 0.25rem;
            vertical-align: top;
        }

        .table th {
            background-color: #0c5a0c;
            color: #fff;
            text-transform: uppercase;
            font-weight: 600;
            font-size: 0.6rem;
            text-align: center;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table tbody tr:hover {
            background-color: #f0f8f0;
        }

        .summary {
            border: 2px solid #0c5a0c;
            border-radius: 6px;
            background: #f8fdf8;
            padding: 0.6rem;
            max-width: 400px;
            margin: 1rem auto 0;
            font-size: 0.7rem;
        }

        .summary h3 {
            font-size: 0.85rem;
            color: #0c5a0c;
            margin-bottom: 0.4rem;
            border-bottom: 1px dashed #0c5a0c;
            padding-bottom: 0.2rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.2rem;
        }

        .detail-item .label {
            font-weight: 600;
        }

        .detail-item .value {
            font-weight: 700;
        }

        .amount-positive {
            color: #0d7377;
        }

        .amount-negative {
            color: #d63031;
        }

        .total-row {
            background-color: #e8f5e8 !important;
            font-weight: 700;
            border-top: 2px solid #0c5a0c;
        }

        .total-row td {
            border-top: 2px solid #0c5a0c;
            font-weight: 700;
        }
    </style>
</head>

<body>

    <h1 class="text-center" style="font-size:1.4rem; color:#0c5a0c; margin-bottom: 0.1rem;">
        NDEGE ESTATE LIMITED
    </h1>
    <p class="text-center mb-1" style="font-size:0.7rem; color:#666; margin-bottom: 0.1rem;">
        Dealers in Rental Houses | P.O.BOX: 28044 - Kampala - Uganda
    </p>
    <h2 class="text-center mb-1" style="font-size:1rem; color:#d81e1e;">
        Tenant Payments Report
    </h2>
    <p class="text-center mb-2" style="font-size:0.75rem;">
        Period: {{ Utils::my_date($start_date) }} – {{ Utils::my_date($end_date) }}
    </p>

    <hr>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 4%;">S/N</th>
                <th style="width: 10%;">Date</th>
                <th style="width: 15%;">Tenant</th>
                <th style="width: 8%;">Room</th>
                <th style="width: 11%;">Rent</th>
                <th style="width: 11%;">Security</th>
                <th style="width: 11%;">Rem. Days</th>
                <th style="width: 11%;">Garbage</th>
                <th style="width: 11%;">Total</th>
                <th style="width: 8%;">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
                $totalAmount = 0;
                $totalBalance = 0;
            @endphp
            @foreach ($tenantPayments as $trans)
                @php
                    $i++;
                    $rentAmount = $trans->rent_amount ?? 0;
                    $securityDeposit = $trans->securty_deposit ?? 0;
                    $remainingDays = $trans->days_before ?? 0;
                    $garbageAmount = $trans->garbage_amount ?? 0;
                    $totalPayment = $trans->amount ?? 0;
                    $balance = $trans->balance ?? 0;
                    
                    $totalAmount += $totalPayment;
                    $totalBalance += $balance;
                @endphp
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td>{{ Utils::my_date($trans->created_at) }}</td>
                    <td>{{ optional($trans->tenant)->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ optional(optional($trans->renting)->room)->name ?? 'N/A' }}</td>
                    <td class="text-right">
                        @if($rentAmount > 0)
                            {{ number_format($rentAmount) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($securityDeposit > 0)
                            {{ number_format($securityDeposit) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($remainingDays > 0)
                            {{ number_format($remainingDays) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($garbageAmount > 0)
                            {{ number_format($garbageAmount) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right amount-positive">{{ number_format($totalPayment) }}</td>
                    <td class="text-right {{ $balance >= 0 ? 'amount-positive' : 'amount-negative' }}">
                        {{ number_format($balance) }}
                    </td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-center"><strong>TOTALS</strong></td>
                <td class="text-right"><strong>{{ number_format($totalRentAmount) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalSecurityDeposit) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalRemainingDays) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGarbageAmount) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalAmount) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalBalance) }}</strong></td>
            </tr>
        </tbody>
    </table>



    {{-- Expenses Table --}}
    @if (isset($expenses) && $expenses->count())
        <h3 class="mt-2" style="font-size: 0.85rem; color: #0c5a0c; margin-bottom: 0.3rem;">Expenses Breakdown</h3>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 6%;">S/N</th>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 40%;">Particulars</th>
                    <th style="width: 22%;">Category</th>
                    <th style="width: 20%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $j = 0; 
                    $totalExpensesCalculated = 0;
                @endphp
                @foreach ($expenses as $exp)
                    @php 
                        $j++; 
                        $totalExpensesCalculated += $exp->amount;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $j }}</td>
                        <td>{{ date('d M, Y', strtotime($exp->expense_date)) }}</td>
                        <td>{{ $exp->name }}</td>
                        <td>{{ $exp->category }}</td>
                        <td class="text-right amount-negative">{{ number_format($exp->amount) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-center"><strong>TOTAL EXPENSES</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalExpensesCalculated) }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <p class="text-center mt-2" style="font-size: 0.7rem; color: #666; font-style: italic;">No expenses recorded for this period.</p>
        @php $totalExpensesCalculated = 0; @endphp
    @endif

    <section class="summary">
        <h3>FINANCIAL SUMMARY</h3>
        <table style="width:100%; border:none; background:transparent; font-size:inherit;">
            <tr>
                <td colspan="2" style="border:none; padding:0; text-align:center; font-weight:700; color:#0c5a0c; font-size:0.75rem; padding-bottom:0.3rem;">
                    INCOME BREAKDOWN
                </td>
            </tr>
            <tr>
                <td class="label" style="font-weight:600; border:none; padding:0.15rem 0;">Rent Payments:</td>
                <td class="value amount-positive" style="font-weight:700; text-align:right; border:none; padding:0.15rem 0;">
                    UGX {{ number_format($totalRentAmount) }}
                </td>
            </tr>
            <tr>
                <td class="label" style="font-weight:600; border:none; padding:0.15rem 0;">Security Deposits:</td>
                <td class="value amount-positive" style="font-weight:700; text-align:right; border:none; padding:0.15rem 0;">
                    UGX {{ number_format($totalSecurityDeposit) }}
                </td>
            </tr>
            <tr>
                <td class="label" style="font-weight:600; border:none; padding:0.15rem 0;">Remaining Days:</td>
                <td class="value amount-positive" style="font-weight:700; text-align:right; border:none; padding:0.15rem 0;">
                    UGX {{ number_format($totalRemainingDays) }}
                </td>
            </tr>
            <tr>
                <td class="label" style="font-weight:600; border:none; padding:0.15rem 0;">Garbage Fees:</td>
                <td class="value amount-positive" style="font-weight:700; text-align:right; border:none; padding:0.15rem 0;">
                    UGX {{ number_format($totalGarbageAmount) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border:none; padding:0;">
                    <hr style="margin:0.3rem 0; border-top: 1px solid #0c5a0c;">
                </td>
            </tr>
            <tr>
                <td class="label" style="font-weight:700; border:none; padding:0.15rem 0; color:#0c5a0c;">Income Subtotal:</td>
                <td class="value amount-positive" style="font-weight:700; text-align:right; border:none; padding:0.15rem 0; color:#0c5a0c;">
                    UGX {{ number_format($totalIncomeSubtotal) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border:none; padding:0;">
                    <hr style="margin:0.3rem 0; border-top: 2px solid #0c5a0c;">
                </td>
            </tr>
            <tr>
                <td class="label" style="font-weight:600; border:none; padding:0.15rem 0;">Total Expenses:</td>
                <td class="value amount-negative" style="font-weight:700; text-align:right; border:none; padding:0.15rem 0;">
                    UGX {{ number_format(isset($totalExpensesCalculated) ? $totalExpensesCalculated : ($totalExpenses ?? 0)) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border:none; padding:0;">
                    <hr style="margin:0.3rem 0; border-top: 2px solid #0c5a0c;">
                </td>
            </tr>
            <tr style="background-color: #f0f8f0;">
                <td class="label" style="font-weight:700; border:none; padding:0.2rem 0; color:#0c5a0c; font-size:0.8rem;">FINAL BALANCE:</td>
                <td class="value" style="font-weight:700; text-align:right; border:none; padding:0.2rem 0; font-size:0.8rem; 
                    color: {{ ($totalIncomeSubtotal - (isset($totalExpensesCalculated) ? $totalExpensesCalculated : ($totalExpenses ?? 0))) >= 0 ? '#0d7377' : '#d63031' }};">
                    UGX {{ number_format($totalIncomeSubtotal - (isset($totalExpensesCalculated) ? $totalExpensesCalculated : ($totalExpenses ?? 0))) }}
                </td>
            </tr>
        </table>
    </section>

</body>

</html>
