<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $booking->booking_reference }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .primary-color { color: #e07632; }
        .text-light { color: #64748b; }
        .border-bottom { border-bottom: 2px solid #e07632; }
        
        .logo-section h1 {
            font-size: 24px;
            color: #e07632;
            margin: 0 0 4px 0;
            font-weight: bold;
        }
        .logo-section p {
            font-size: 11px;
            color: #64748b;
            margin: 0 0 2px 0;
        }
        
        .title-section {
            text-align: right;
        }
        .title-section h2 {
            font-size: 26px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 8px 0;
            text-transform: uppercase;
        }
        .ref-pill {
            display: inline-block;
            background: #f8fafc;
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .info-table {
            margin-top: 25px;
            margin-bottom: 25px;
        }
        .info-table td {
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        .info-table h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #e07632;
            letter-spacing: 0.5px;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .info-row {
            margin-bottom: 4px;
            font-size: 12px;
        }
        .info-row label {
            width: 80px;
            color: #64748b;
            display: inline-block;
        }
        .info-row span {
            font-weight: bold;
        }
        
        .items-table {
            margin-top: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        .items-table th {
            text-align: left;
            background: #f8fafc;
            padding: 10px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 10px;
            text-transform: uppercase;
            color: #475569;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .summary-table {
            margin-top: 20px;
        }
        .summary-table td {
            vertical-align: top;
        }
        .notes-box {
            background: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            font-size: 10px;
            width: 60%;
        }
        .totals-box {
            width: 35%;
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
        }
        .total-row {
            margin-bottom: 6px;
            font-size: 12px;
        }
        .total-row label {
            color: #64748b;
        }
        .total-row span {
            float: right;
            font-weight: bold;
        }
        .grand-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #e2e8f0;
            font-weight: bold;
            font-size: 16px;
            color: #e07632;
        }
        .paid-row {
            color: #10b981;
        }
        .due-row {
            color: #dc2626;
        }
        
        .signatures {
            margin-top: 40px;
            text-align: center;
        }
        .sig-box {
            width: 45%;
            display: inline-block;
            vertical-align: top;
        }
        .sig-line {
            width: 150px;
            margin: 0 auto;
            border-bottom: 1px solid #000;
            height: 40px;
            margin-bottom: 5px;
        }
        
        .policies {
            margin-top: 30px;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
        }
        .policy-col {
            width: 30%;
            display: inline-block;
            vertical-align: top;
            font-size: 8px;
            color: #64748b;
            padding-right: 10px;
        }
        .policy-col h4 {
            font-size: 9px;
            text-transform: uppercase;
            color: #e07632;
            margin: 0 0 4px 0;
        }
    </style>
</head>
<body>
    @php
        $nights = \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out);
        $totalUSD = $booking->total_price;
        $paidUSD = $booking->amount_paid ?? 0;
        $balanceUSD = max(0, $totalUSD - $paidUSD);
    @endphp

    <table class="header-table border-bottom" style="margin-bottom: 20px; padding-bottom: 15px;">
        <tr>
            <td class="logo-section">
                <h1>PRIMELAND HOTEL</h1>
                <p>Comfort in every Stay</p>
                <p>Moshi, Kilimanjaro, Tanzania</p>
                <p>Tel: +255 677 155 156 | Email: info@primelandhotel.com</p>
            </td>
            <td class="title-section">
                <h2>INVOICE</h2>
                <div class="ref-pill">{{ $booking->booking_reference }}</div>
                <p class="text-light" style="margin-top: 8px;">Date: {{ date('M d, Y') }}</p>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td>
                <h3>Billed To</h3>
                <div class="info-row"><label>Guest:</label> <span>{{ $booking->guest_name }}</span></div>
                <div class="info-row"><label>Email:</label> <span>{{ $booking->guest_email }}</span></div>
                <div class="info-row"><label>Phone:</label> <span>{{ $booking->guest_phone ?? 'N/A' }}</span></div>
            </td>
            <td>
                <h3>Stay Details</h3>
                <div class="info-row"><label>Dates:</label> <span>{{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</span></div>
                <div class="info-row"><label>Stay:</label> <span>{{ $nights }} Nights</span></div>
                <div class="info-row"><label>Room:</label> <span>{{ $booking->room->room_type }}</span></div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: center;">Nights</th>
                <th style="text-align: right;">Unit Price</th>
                <th style="text-align: right;">Total (USD)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong style="font-size: 13px;">Accommodation: {{ $booking->room->room_type }}</strong><br>
                    <span class="text-light" style="font-size: 11px;">Guest: {{ $booking->guest_name }}</span>
                </td>
                <td style="text-align: center;">{{ $nights }}</td>
                <td style="text-align: right;">${{ number_format($totalUSD / max(1, $nights), 2) }}</td>
                <td style="text-align: right; font-weight: bold;">${{ number_format($totalUSD, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td style="width: 60%;">
                <div class="notes-box">
                    <strong class="primary-color" style="display: block; margin-bottom: 4px;">Important Notes:</strong>
                    - 50% deposit required for confirmation.<br>
                    - Full pre-payment within 7 days of stay.<br>
                    - Standard Check-in is 2:00 PM, Check-out is 11:00 AM.<br>
                    @if($booking->special_requests)
                    <br><strong>Special Requests:</strong> {{ $booking->special_requests }}
                    @endif
                </div>
                
                <div class="signatures" style="margin-top: 30px;">
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <p style="font-size: 10px; color: #64748b;">Authorized Signature</p>
                    </div>
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <p style="font-size: 10px; color: #64748b;">Guest Signature</p>
                    </div>
                </div>
            </td>
            <td style="width: 40%; padding-left: 20px;">
                <div class="totals-box">
                    <div class="total-row">
                        <label>Subtotal:</label>
                        <span>${{ number_format($totalUSD, 2) }}</span>
                    </div>
                    <div class="total-row grand-total">
                        <label>Total Bill:</label>
                        <span>${{ number_format($totalUSD, 2) }}</span>
                    </div>
                    <div class="total-row paid-row" style="margin-top: 8px;">
                        <label>Amount Paid:</label>
                        <span>${{ number_format($paidUSD, 2) }}</span>
                    </div>
                    @if($balanceUSD > 0)
                    <div class="total-row due-row" style="margin-top: 5px; border-top: 1px dashed #e2e8f0; padding-top: 5px;">
                        <label>Balance Due:</label>
                        <span>${{ number_format($balanceUSD, 2) }}</span>
                    </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="policies">
        <div class="policy-col">
            <h4>Booking Policy</h4>
            <p>50% deposit required for confirmation. Remaining balance due on arrival.</p>
        </div>
        <div class="policy-col">
            <h4>Cancellation</h4>
            <p>30 days: Free | 14 days: 50% fee | 7 days: 100% fee. No-show: Full charge.</p>
        </div>
        <div class="policy-col">
            <h4>General</h4>
            <p>Room charges include VAT. Self-paid services (laundry, drinks) are extra.</p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px; font-size: 10px; color: #1e293b; border-top: 1px solid #f1f5f9; padding-top: 15px;">
        <p>This is a computer generated document. No physical signature required for electronic copy.</p>
        <p style="font-weight: bold; margin-top: 5px;">Powered By <span style="color: #940000;">EmCa Techonologies</span></p>
    </div>
</body>
</html>
