<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Report — {{ $shift->staff->name ?? 'Staff' }} | {{ $shift->opened_at->format('d M Y') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --brand:      #e77a30;
            --brand-dark: #c45e18;
            --brand-light:#fff5ef;
            --text-dark:  #1a1a2e;
            --text-mid:   #555;
            --text-light: #888;
            --border:     #e2e2e2;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: var(--text-dark);
            background: #fff;
            font-size: 13px;
            line-height: 1.5;
        }

        /* ── PAGE WRAPPER ── */
        .page {
            max-width: 800px;
            margin: 0 auto;
            padding: 32px 40px;
            position: relative;
            min-height: 100vh;
        }

        /* ── WATERMARK ── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 100px;
            font-weight: 900;
            color: rgba(231, 122, 48, 0.09);
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
            z-index: 0;
            letter-spacing: 8px;
            text-align: center;
            line-height: 1.2;
        }

        /* All content above watermark */
        .page > *:not(.watermark) { position: relative; z-index: 1; }

        /* ── NO-PRINT TOOLBAR ── */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding: 14px 20px;
            background: var(--brand-light);
            border-left: 4px solid var(--brand);
            border-radius: 0 8px 8px 0;
        }
        .btn-print {
            background: var(--brand);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            letter-spacing: 0.3px;
        }
        .btn-print:hover { background: var(--brand-dark); }
        .btn-back {
            color: var(--text-mid);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 18px;
            border-bottom: 3px solid var(--brand);
            margin-bottom: 24px;
        }
        .header-left { display: flex; align-items: center; gap: 16px; }
        .hotel-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }
        .hotel-name {
            font-size: 22px;
            font-weight: 800;
            color: var(--brand);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            line-height: 1.1;
        }
        .hotel-sub {
            font-size: 11px;
            color: var(--text-light);
            margin-top: 3px;
        }
        .header-right {
            text-align: right;
        }
        .report-badge {
            background: var(--brand);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: inline-block;
            margin-bottom: 6px;
        }
        .report-date { font-size: 12px; color: var(--text-light); }

        /* ── REPORT TITLE ── */
        .report-title {
            text-align: center;
            margin-bottom: 22px;
        }
        .report-title h2 {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px dashed var(--border);
            display: inline-block;
            padding-bottom: 6px;
        }

        /* ── INFO GRID ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }
        .info-card {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }
        .info-card-header {
            background: var(--brand);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 6px 12px;
        }
        .info-card-body { padding: 10px 12px; }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px solid #f5f5f5;
            font-size: 12.5px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--text-light); }
        .info-value { font-weight: 600; }
        .badge-status {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }
        .badge-closed { background: #f0f0f0; color: #555; }
        .badge-open   { background: #e6f9ee; color: #1a7a3f; }

        /* ── SECTION HEADER ── */
        .section-header {
            background: var(--brand);
            color: #fff;
            padding: 8px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 6px 6px 0 0;
            margin-bottom: 0;
        }

        /* ── TABLES ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
            font-size: 12.5px;
            border: 1px solid var(--border);
            border-top: none;
            border-radius: 0 0 6px 6px;
            overflow: hidden;
        }
        .data-table thead th {
            background: #fafafa;
            color: var(--text-mid);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }
        .data-table thead th.r { text-align: right; }
        .data-table tbody tr:nth-child(even) { background: #fafafa; }
        .data-table tbody tr:hover { background: var(--brand-light); }
        .data-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .data-table td:last-child { border-bottom: none; }
        .data-table .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        .data-table .sub-row td { padding-left: 28px; color: var(--text-mid); }
        .data-table .section-group td {
            background: #f5f5f5;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            color: var(--brand);
            padding: 7px 12px;
            letter-spacing: 0.5px;
        }
        .data-table .total-row td {
            background: var(--brand);
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 12px;
        }
        .data-table .total-row td.amount { font-family: 'Courier New', monospace; }
        .diff-positive { color: #1a7a3f; }
        .diff-positive { color: #2e7d32; }
        .diff-negative { color: #c0392b; }
        .text-muted { color: var(--text-light); font-style: italic; }

        /* ── NOTES ── */
        .notes-box {
            border: 1px solid var(--border);
            border-radius: 0 0 6px 6px;
            padding: 12px 14px;
            min-height: 65px;
            font-size: 12.5px;
            color: var(--text-mid);
            margin-bottom: 24px;
        }

        /* ── SIGNATURES ── */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            gap: 30px;
        }
        .sig-box {
            flex: 1;
            text-align: center;
            padding-top: 10px;
            border-top: 1.5px solid var(--text-dark);
            font-size: 12px;
            color: var(--text-mid);
            font-weight: 500;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--text-light);
            font-size: 10.5px;
        }
        .footer strong { color: var(--brand); }

        /* ── PRINT OVERRIDES ── */
        @media print {
            .toolbar { display: none !important; }
            body { font-size: 12px; }
            .page { padding: 16px 24px; max-width: 100%; }
            .watermark { position: fixed; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body>

{{-- WATERMARK (only shown if shift is closed) --}}
@if($shift->status === 'closed')
<div class="watermark">
    CLOSED<br>
    <span style="font-size: 40px; letter-spacing: 2px;">{{ $shift->closed_at ? $shift->closed_at->format('d M Y') : '' }}</span>
</div>
@endif

<div class="page">

    {{-- TOOLBAR (no-print) --}}
    <div class="toolbar no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Print Report</button>
        <a href="{{ url()->previous() }}" class="btn-back">← Back</a>
        <span style="margin-left: auto; font-size: 12px; color: #888;">Shift #{{ $shift->id }} &mdash; Generated {{ now()->format('d M Y, H:i') }}</span>
    </div>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            <div>
                <div class="hotel-name">Prime Land Hotel</div>
                <div class="hotel-sub">Plot No. 123, Opposite Main Market, Tanzania</div>
                <div class="hotel-sub">Tel: +255 677 155 156 &nbsp;|&nbsp; info@primelandhotel.com</div>
            </div>
        </div>
        <div class="header-right">
            <span class="report-badge">Shift Report</span>
            <div class="report-date">Shift #{{ $shift->id }}</div>
            <div class="report-date" style="margin-top:4px;">{{ $shift->opened_at->format('d M Y') }}</div>
        </div>
    </div>

    <div class="report-title">
        <h2>Cash & Revenue Reconciliation Report</h2>
    </div>

    {{-- INFO GRID --}}
    <div class="info-grid">
        <div class="info-card">
            <div class="info-card-header">Staff Information</div>
            <div class="info-card-body">
                <div class="info-row">
                    <span class="info-label">Staff Name</span>
                    <span class="info-value">{{ $shift->staff->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Role</span>
                    <span class="info-value">{{ ucfirst($shift->staff->role ?? 'Staff') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Shift Status</span>
                    <span class="info-value">
                        <span class="badge-status {{ $shift->status === 'closed' ? 'badge-closed' : 'badge-open' }}">
                            {{ strtoupper($shift->status) }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card-header">Session Timeline</div>
            <div class="info-card-body">
                <div class="info-row">
                    <span class="info-label">Opened At</span>
                    <span class="info-value">{{ $shift->opened_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Closed At</span>
                    <span class="info-value">
                        @if($shift->closed_at)
                            {{ $shift->closed_at->format('d M Y, H:i') }}
                        @else
                            <span class="text-muted">Still Active</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Duration</span>
                    <span class="info-value">
                        @php
                            $end  = $shift->closed_at ?? now();
                            $mins = $shift->opened_at->diffInMinutes($end);
                            echo intdiv($mins, 60) . 'h ' . ($mins % 60) . 'm';
                        @endphp
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- PAYMENT BREAKDOWN TABLE --}}
    <div class="section-header">Payment Collection Breakdown</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Category / Platform</th>
                <th class="r">Expected Amount (TZS)</th>
                <th class="r">Actual Amount (TZS)</th>
                <th class="r">Difference</th>
            </tr>
        </thead>
        <tbody>

            {{-- CASH --}}
            <tr class="section-group"><td colspan="4">💵 Cash Payments Total</td></tr>
            <tr>
                <td style="padding-left:28px;">Total Cash Collected</td>
                @php $cashExpected = $shift->closing_cash_expected ?? 0; $cashActual = $shift->closing_cash_actual ?? 0; $cashDiff = $cashActual - $cashExpected; @endphp
                <td class="amount">{{ number_format($cashExpected, 0) }}</td>
                <td class="amount">{{ number_format($cashActual, 0) }}</td>
                <td class="amount {{ $cashDiff >= 0 ? 'diff-positive' : 'diff-negative' }}">
                    {{ ($cashDiff >= 0 ? '+' : '') }}{{ number_format($cashDiff, 0) }}
                </td>
            </tr>

            {{-- MOBILE --}}
            @php $mobileTotal = array_sum($breakdown['mobile']); @endphp
            <tr class="section-group"><td colspan="4">📱 Mobile Money Total</td></tr>
            <tr>
                <td style="padding-left:28px;">Total Mobile Collected</td>
                <td class="amount">{{ number_format($mobileTotal, 0) }}</td>
                <td class="amount">{{ number_format($mobileTotal, 0) }}</td>
                <td class="amount">0</td>
            </tr>

            {{-- BANK --}}
            @php $bankTotal = array_sum($breakdown['bank']); @endphp
            <tr class="section-group"><td colspan="4">🏦 Bank Transfers Total</td></tr>
            <tr>
                <td style="padding-left:28px;">Total Bank Collected</td>
                <td class="amount">{{ number_format($bankTotal, 0) }}</td>
                <td class="amount">{{ number_format($bankTotal, 0) }}</td>
                <td class="amount">0</td>
            </tr>

            {{-- CARD --}}
            @php $cardTotal = array_sum($breakdown['card']); @endphp
            <tr class="section-group"><td colspan="4">💳 Card Payments Total</td></tr>
            <tr>
                <td style="padding-left:28px;">Total Card Collected</td>
                <td class="amount">{{ number_format($cardTotal, 0) }}</td>
                <td class="amount">{{ number_format($cardTotal, 0) }}</td>
                <td class="amount">0</td>
            </tr>

            {{-- STAFF BREAKDOWN (RE-INTEGRATED INTO MAIN TABLE) --}}
            @if(!empty($servicePaymentsByStaff))
                <tr class="section-group"><td colspan="4">🍽️ Restaurant & Bar Collections by Staff</td></tr>
                @foreach($servicePaymentsByStaff as $sData)
                <tr class="sub-row">
                    <td>• {{ $sData['name'] }}</td>
                    <td class="amount">{{ number_format($sData['total']) }}</td>
                    <td class="amount">--</td>
                    <td class="amount">--</td>
                </tr>
                @endforeach
            @endif

            {{-- GRAND TOTAL --}}
            @php
                $nonCashTotal = $mobileTotal + $bankTotal + $cardTotal;
                $grandTotalExpected = ($cashExpected) + $nonCashTotal;
                $grandTotalActual = ($cashActual) + $nonCashTotal;
                $grandTotalDiff = $cashActual - $cashExpected; 
            @endphp
            <tr class="total-row">
                <td>GRAND TOTAL REVENUE</td>
                <td class="amount">TZS {{ number_format($grandTotalExpected, 0) }}</td>
                <td class="amount">TZS {{ number_format($grandTotalActual, 0) }}</td>
                <td class="amount {{ $grandTotalDiff >= 0 ? '' : 'diff-negative' }}">
                    {{ ($grandTotalDiff >= 0 ? '+' : '') }}{{ number_format($grandTotalDiff, 0) }}
                </td>
            </tr>
        </tbody>
    </table>



    {{-- NOTES --}}
    <div class="section-header">Shift Notes</div>
    <div class="notes-box">
        {{ $shift->notes ?: 'No notes provided for this shift.' }}
    </div>

    {{-- SIGNATURES --}}
    <div class="signatures">
        <div class="sig-box">Receptionist Signature<br><small style="color:#bbb;">{{ $shift->staff->name ?? '' }}</small></div>
        <div class="sig-box">Manager / Accountant Signature</div>
        <div class="sig-box">Verified & Approved By</div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Printed on {{ now()->format('d M Y, H:i:s') }} &nbsp;|&nbsp; <strong>PrimeLand Hotel</strong> Management System &nbsp;|&nbsp; Confidential</p>
        @if($shift->status === 'closed' && $shift->closed_at)
        <p style="margin-top:4px;">This shift was <strong>closed</strong> on {{ $shift->closed_at->format('d M Y') }} at {{ $shift->closed_at->format('H:i') }}.</p>
        @endif
    </div>

</div>

<script>
    window.onload = function () {
        if (window.location.search.includes('autoprint=true')) {
            window.print();
        }
    };
</script>
</body>
</html>
