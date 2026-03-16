<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ ($booking->status === 'pending') ? 'Proforma Invoice' : 'Invoice' }} - {{ $booking->booking_reference }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
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
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .hotel-info p {
            margin: 2px 0;
            color: #64748b;
            font-size: 11px;
        }
        .invoice-title {
            text-align: right;
            vertical-align: top;
        }
        .invoice-title h2 {
            margin: 0;
            font-size: 32px;
            color: #1e293b;
            font-weight: 900;
        }
        .invoice-title .ref {
            font-size: 14px;
            color: #64748b;
            margin-top: 5px;
        }

        .meta-section {
            width: 100%;
            margin-bottom: 40px;
        }
        .meta-section td {
            width: 50%;
            vertical-align: top;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .info-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-right: 10px;
            min-height: 100px;
        }
        .info-card.right {
            margin-right: 0;
            margin-left: 10px;
        }
        .info-card p {
            margin: 4px 0;
        }
        .info-card strong {
            color: #1e293b;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .items-table th {
            text-align: left;
            padding: 12px 15px;
            background: #1e293b;
            color: #ffffff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table .desc strong {
            display: block;
            font-size: 14px;
            color: #1e293b;
        }
        .items-table .desc span {
            font-size: 12px;
            color: #64748b;
        }

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
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #1e293b;
        }
        .notes-area p {
            font-size: 11px;
            color: #64748b;
            margin: 5px 0;
        }
        .totals-area {
            width: 40%;
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
        }
        .total-row {
            width: 100%;
            margin-bottom: 8px;
        }
        .total-row td {
            padding: 4px 0;
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
            padding-top: 15px;
            margin-top: 10px;
            font-size: 20px;
            color: #f97316;
        }

        .footer {
            position: fixed;
            bottom: 40px;
            left: 40px;
            right: 40px;
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
        $nights = \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out);
        $isTanzanian = ($booking->guest_type === 'tanzanian');
        $currency = $isTanzanian ? 'TZS ' : '$';
        $exchangeRate = $booking->locked_exchange_rate ?? 2500;
        $multiplier = $isTanzanian ? $exchangeRate : 1;
        
        $totalVal = $booking->total_price * $multiplier;
        $paidVal = ($booking->amount_paid ?? 0) * $multiplier;
        $balanceVal = max(0, $totalVal - $paidVal);
        $fmt = $isTanzanian ? 0 : 2;
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
                        <h2>{{ ($booking->status === 'pending') ? 'PROFORMA' : 'INVOICE' }}</h2>
                        <div class="ref">#{{ $booking->booking_reference }}</div>
                        <div class="ref">{{ date('F d, Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="meta-section">
            <tr>
                <td>
                    <div class="section-title">BILL TO</div>
                    <div class="info-card">
                        <p><strong>{{ $booking->guest_name }}</strong></p>
                        <p>{{ $booking->guest_email }}</p>
                        <p>{{ $booking->guest_phone ?? 'No Phone Provided' }}</p>
                    </div>
                </td>
                <td>
                    <div class="section-title">STAY DETAILS</div>
                    <div class="info-card right">
                        <p><strong>Check-in:</strong> {{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</p>
                        <p><strong>Check-out:</strong> {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</p>
                        <p><strong>Duration:</strong> {{ $nights }} Night(s)</p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center; width: 80px;">Nights</th>
                    <th style="text-align: right; width: 120px;">Rate</th>
                    <th style="text-align: right; width: 120px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="desc">
                        <strong>Accommodation: {{ $booking->room->room_type }}</strong>
                        <span>Stay for {{ $booking->guest_name }}</span>
                    </td>
                    <td style="text-align: center;">{{ $nights }}</td>
                    <td style="text-align: right;">{{ $currency }}{{ number_format($totalVal / max(1, $nights), $fmt) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ $currency }}{{ number_format($totalVal, $fmt) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="summary-section">
            <tr>
                <td class="notes-area">
                    @if($booking->status === 'pending')
                    <h4>Status: Quotation</h4>
                    <p>This document is a formal quotation for your upcoming stay. Prices are valid for 48 hours.</p>
                    @endif
                    
                    @if($booking->special_requests)
                    <h4>Special Requests</h4>
                    <p>{{ $booking->special_requests }}</p>
                    @endif

                    <div style="margin-top: 20px;">
                        <h4>Policies</h4>
                        <p>• 50% deposit required for confirmation.</p>
                        <p>• Full cancellation allowed 30 days before arrival.</p>
                    </div>
                </td>
                <td class="totals-area">
                    <table class="total-row">
                        <tr>
                            <td class="label">Subtotal</td>
                            <td class="value">{{ $currency }}{{ number_format($totalVal, $fmt) }}</td>
                        </tr>
                    </table>
                    <table class="total-row">
                        <tr>
                            <td class="label">Amount Paid</td>
                            <td class="value">{{ $currency }}{{ number_format($paidVal, $fmt) }}</td>
                        </tr>
                    </table>
                    <table class="total-row grand-total">
                        <tr>
                            <td class="label">TOTAL DUE</td>
                            <td class="value">{{ $currency }}{{ number_format($balanceVal, $fmt) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for choosing <strong>PrimeLand Hotel</strong>. We look forward to your stay.</p>
            <p>Powered By <span style="color: #940000; font-weight: bold;">EmCa Technologies</span></p>
        </div>
    </div>
</body>
</html>
