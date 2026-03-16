<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Corporate Invoice - {{ $company->name }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #334155;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .container {
            padding: 40px;
        }
        .header {
            border-bottom: 4px solid #f97316;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .hotel-info h1 {
            color: #f97316;
            margin: 0;
            font-size: 26px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .hotel-info p {
            margin: 2px 0;
            color: #64748b;
            font-size: 10px;
        }
        .invoice-title {
            text-align: right;
            vertical-align: top;
        }
        .invoice-title h2 {
            margin: 0;
            font-size: 28px;
            color: #1e293b;
            font-weight: 900;
        }
        .invoice-title .ref {
            font-size: 12px;
            color: #64748b;
            margin-top: 5px;
        }

        .meta-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .meta-section td {
            width: 50%;
            vertical-align: top;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .info-card {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-right: 10px;
            min-height: 80px;
        }
        .info-card.right {
            margin-right: 0;
            margin-left: 10px;
        }
        .info-card p {
            margin: 2px 0;
        }
        .info-card strong {
            color: #1e293b;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            text-align: left;
            padding: 10px 12px;
            background: #1e293b;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-company { background-color: #d1ecf1; color: #0c5460; }
        .badge-self { background-color: #fff3cd; color: #856404; }

        .summary-section {
            width: 100%;
        }
        .summary-section td {
            vertical-align: top;
        }
        .notes-area {
            width: 60%;
            padding-top: 10px;
        }
        .notes-area h4 {
            margin: 0 0 8px 0;
            font-size: 11px;
            color: #1e293b;
        }
        .notes-area p {
            font-size: 10px;
            color: #64748b;
            margin: 4px 0;
        }
        .totals-area {
            width: 40%;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }
        .total-row {
            width: 100%;
            margin-bottom: 6px;
        }
        .total-row td {
            padding: 2px 0;
        }
        .total-row .label {
            color: #64748b;
        }
        .total-row .value {
            text-align: right;
            font-weight: bold;
            color: #1e293b;
        }
        .grand-total td {
            border-top: 2px solid #e2e8f0;
            padding-top: 10px;
            margin-top: 5px;
            font-size: 16px;
            color: #f97316;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            font-size: 10px;
            color: #94a3b8;
        }
        .footer strong {
            color: #64748b;
        }
    </style>
</head>
<body>
    @php
        // Determine currency by the first booking's guest type
        $firstBooking = $bookings[0] ?? null;
        $isTanzanian = ($firstBooking && $firstBooking->guest_type === 'tanzanian');
        $currency = $isTanzanian ? 'TZS ' : '$';
        $exchangeRate = ($firstBooking && $firstBooking->locked_exchange_rate) ? $firstBooking->locked_exchange_rate : 2500;
        $multiplier = $isTanzanian ? $exchangeRate : 1;
        $fmt = $isTanzanian ? 0 : 2;

        $totalVal = ($companyCharges + $selfPaidCharges) * $multiplier;
        $paidVal = ($totalCompanyPaid ?? 0) * $multiplier;
        $balanceVal = max(0, $totalVal - $paidVal);
    @endphp

    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td class="hotel-info">
                        <h1>PRIMELAND HOTEL</h1>
                        <p>Comfort in every Stay</p>
                        <p>Moshi, Kilimanjaro, Tanzania</p>
                        <p>+255 677 155 156 | info@primelandhotel.com</p>
                    </td>
                    <td class="invoice-title">
                        <h2>{{ (isset($generalNotes) && str_contains(strtolower($generalNotes), 'proforma')) ? 'PROFORMA' : 'CORPORATE INVOICE' }}</h2>
                        <div class="ref">GRP-{#{{ strtoupper(Str::random(6)) }}#}</div>
                        <div class="ref">{{ date('M d, Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="meta-section">
            <tr>
                <td>
                    <div class="section-title">INVOICED TO</div>
                    <div class="info-card">
                        <p><strong>{{ $company->name }}</strong></p>
                        <p>{{ $company->email }}</p>
                        <p>{{ $company->phone ?? 'N/A' }}</p>
                    </div>
                </td>
                <td>
                    <div class="section-title">STAY SUMMARY</div>
                    <div class="info-card right">
                        <p><strong>Period:</strong> {{ $checkIn->format('M d') }} - {{ $checkOut->format('M d, Y') }}</p>
                        <p><strong>Duration:</strong> {{ $nights }} Nights</p>
                        <p><strong>Group Size:</strong> {{ count($bookings) }} Guests</p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Guest Information</th>
                    <th>Room Details</th>
                    <th style="text-align: center;">Rate</th>
                    <th style="text-align: right;">Amount</th>
                    <th style="text-align: center;">Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td><strong>{{ $booking->guest_name }}</strong></td>
                    <td>{{ $booking->room->room_number }} ({{ $booking->room->room_type }})</td>
                    <td style="text-align: center;">{{ $currency }}{{ number_format(($booking->total_price * $multiplier) / $nights, $fmt) }}</td>
                    <td style="text-align: right;">{{ $currency }}{{ number_format($booking->total_price * $multiplier, $fmt) }}</td>
                    <td style="text-align: center;">
                        <span class="badge {{ $booking->payment_responsibility === 'company' ? 'badge-company' : 'badge-self' }}">
                            {{ $booking->payment_responsibility === 'company' ? 'Corp' : 'Self' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-section">
            <tr>
                <td class="notes-area">
                    @if(isset($generalNotes) && str_contains(strtolower($generalNotes), 'proforma'))
                    <h4>Status: Proforma Quotation</h4>
                    <p>This document itemizes the estimated costs for your corporate group booking.</p>
                    @endif
                    
                    @if($generalNotes)
                    <h4>Notes</h4>
                    <p>{{ $generalNotes }}</p>
                    @endif

                    <div style="margin-top: 15px;">
                        <h4>Corporate Policies</h4>
                        <p>• Group accommodation must be confirmed with 50% deposit.</p>
                        <p>• Final rooming list required 7 days before arrival.</p>
                    </div>
                </td>
                <td class="totals-area">
                    <table class="total-row">
                        <tr>
                            <td class="label">Total Group Value</td>
                            <td class="value">{{ $currency }}{{ number_format($totalVal, $fmt) }}</td>
                        </tr>
                    </table>
                    <table class="total-row">
                        <tr>
                            <td class="label">Payments Received</td>
                            <td class="value">{{ $currency }}{{ number_format($paidVal, $fmt) }}</td>
                        </tr>
                    </table>
                    <table class="total-row grand-total">
                        <tr>
                            <td class="label">GROUP BALANCE</td>
                            <td class="value">{{ $currency }}{{ number_format($balanceVal, $fmt) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for choosing <strong>PrimeLand Hotel</strong> for your corporate group.</p>
            <p>Powered By <span style="color: #940000; font-weight: bold;">EmCa Technologies</span></p>
        </div>
    </div>
</body>
</html>
