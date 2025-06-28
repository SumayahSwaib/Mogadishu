{{-- resources/views/print/landlord-report-1.blade.php --}}
@php
    use App\Models\Utils;
    $totalSecurityFee = 0;
    foreach ($tenantPayments as $trans) {
        $totalSecurityFee += optional($trans->renting)->security_fee ?? 0;
    }
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} – Tenant Payments Report</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1cm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            font-size: 0.85rem;
            line-height: 1.2;
        }

        .text-center {
            text-align: center;
        }

        h1,
        h2,
        h3 {
            margin: 0;
            padding: 0;
        }

        .mb-1 {
            margin-bottom: 0.25rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mt-1 {
            margin-top: 0.25rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        hr {
            border: 0;
            border-top: 2px solid #0c5a0c;
            margin: 0.75rem 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.75rem;
            font-size: 0.8rem;
        }

        .table th,
        .table td {
            border: 1px solid #0c5a0c;
            padding: 0.3rem 0.4rem;
        }

        .table th {
            background-color: #0c5a0c;
            color: #fff;
            text-transform: uppercase;
            font-weight: 600;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .summary {
            border: 2px solid #0c5a0c;
            border-radius: 6px;
            background: #f8fdf8;
            padding: 0.8rem;
            max-width: 360px;
            margin: 1.5rem auto 0;
            font-size: 0.85rem;
        }

        .summary h3 {
            font-size: 1rem;
            color: #0c5a0c;
            margin-bottom: 0.5rem;
            border-bottom: 1px dashed #0c5a0c;
            padding-bottom: 0.3rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.3rem;
        }

        .detail-item .label {
            font-weight: 600;
        }

        .detail-item .value {
            font-weight: 700;
        }
    </style>
</head>

<body>

    <h1 class="text-center" style="font-size:1.8rem; color:#0c5a0c;">
        {{ config('app.name') }}
    </h1>
    <h2 class="text-center mb-1" style="font-size:1.1rem; color:#d81e1e;">
        Tenant Payments Report
    </h2>
    <p class="text-center mb-2" style="font-size:0.9rem;">
        {{ Utils::my_date($start_date) }} – {{ Utils::my_date($end_date) }}
    </p>

    <hr>

    <table class="table">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Date</th>
                <th>Tenant</th>
                <th>Amount</th>
                <th>Security Fee</th>
                <th>Room</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
                $totalIncome = 0;
                $totalBalance = 0;
            @endphp
            @foreach ($tenantPayments as $trans)
                @php
                    $i++;
                    $amt = $trans->amount;
                    $fee = optional($trans->renting)->security_fee ?? 0;
                    $bal = optional($trans->renting)->balance ?? 0;
                    $totalIncome += $amt;
                    $totalBalance += $bal;
                @endphp
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ Utils::my_date($trans->created_at) }}</td>
                    <td>{{ optional($trans->tenant)->name ?? 'N/A' }}</td>
                    <td style="text-align:right;">{{ number_format($amt) }}</td>
                    <td style="text-align:right;">({{ number_format($fee) }})</td>
                    <td>{{ optional(optional($trans->renting)->room)->name ?? 'N/A' }}</td>
                    <td style="text-align:right;">{{ number_format($bal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>



    {{-- Expenses Table --}}
    @if (isset($expenses) && $expenses->count())
        <h3 class="mt-2">Expenses</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Date</th>
                    <th>Particulars</th>
                    <th>Category</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $j = 0; @endphp
                @foreach ($expenses as $exp)
                    @php $j++; @endphp
                    <tr>
                        <td>{{ $j }}</td>
                        <td>{{ date('d M, Y', strtotime($exp->expense_date)) }}</td>
                        <td>{{ $exp->name }}</td>
                        <td>{{ $exp->category }}</td>
                        <td style="text-align:right;">{{ number_format($exp->amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-center mt-2">No expenses recorded for this period.</p>
    @endif

    <div class="summary">
        <h3>Summary</h3>
        <div class="detail-item">
            <span class="label">Total Income:</span>
            <span class="value text-success">UGX {{ number_format($totalIncome) }}</span>
        </div>
        <div class="detail-item">
            <span class="label">Total Security Fees:</span>
            <span class="value text-info">UGX {{ number_format($totalSecurityFee) }}</span>
        </div>
        <div class="detail-item">
            <span class="label">Total Balance:</span>
            <span class="value text-primary">UGX {{ number_format($totalBalance) }}</span>
        </div>

        <div class="detail-item">
            <span class="label">Total Expenses:</span>
            <span class="value text-danger">UGX {{ number_format($totalExpenses) }}</span>
        </div>
        <div class="detail-item">
            <span class="label">Total Expense:</span>
            <span class="value text-danger">UGX {{ number_format($report->total_expense) }}</span>
        </div>
    </div>

</body>

</html>
