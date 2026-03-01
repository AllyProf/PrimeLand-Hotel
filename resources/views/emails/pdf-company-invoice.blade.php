<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Corporate Invoice - {{ $company->name }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
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
            font-size: 10px;
            color: #64748b;
            margin: 0 0 2px 0;
        }
        
        .title-section {
            text-align: right;
        }
        .title-section h2 {
            font-size: 24px;
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
            font-size: 11px;
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
            font-size: 10px;
            text-transform: uppercase;
            color: #e07632;
            letter-spacing: 0.5px;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .info-row {
            margin-bottom: 4px;
            font-size: 11px;
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
            margin-top: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        .items-table th {
            text-align: left;
            background: #f8fafc;
            padding: 8px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 9px;
            text-transform: uppercase;
            color: #475569;
        }
        .items-table td {
            padding: 8px;
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
            font-size: 9px;
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
            font-size: 11px;
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
            font-size: 14px;
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
        .badge {
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-company { background-color: #d1ecf1; color: #0c5460; }
        .badge-self { background-color: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    @php
        $totalUSD = $companyCharges + $selfPaidCharges;
        $paidUSD = $totalCompanyPaid ?? 0;
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
                <h2>CORPORATE INVOICE</h2>
                <div class="ref-pill">GRP-{{ strtoupper(Str::random(6)) }}</div>
                <p class="text-light" style="margin-top: 8px;">Date: {{ date('M d, Y') }}</p>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td>
                <h3>Invoiced To</h3>
                <div class="info-row"><label>Company:</label> <span>{{ $company->name }}</span></div>
                <div class="info-row"><label>Email:</label> <span>{{ $company->email }}</span></div>
                <div class="info-row"><label>Phone:</label> <span>{{ $company->phone ?? 'N/A' }}</span></div>
            </td>
            <td>
                <h3>Stay Summary</h3>
                <div class="info-row"><label>Period:</label> <span>{{ $checkIn->format('M d') }} - {{ $checkOut->format('M d, Y') }}</span></div>
                <div class="info-row"><label>Total Days:</label> <span>{{ $nights }} Nights</span></div>
                <div class="info-row"><label>Group Size:</label> <span>{{ count($bookings) }} Guests</span></div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Guest Information</th>
                <th>Room Details</th>
                <th style="text-align: center;">Rate</th>
                <th style="text-align: right;">Charges (USD)</th>
                <th style="text-align: center;">Responsibility</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
            <tr>
                <td><strong>{{ $booking->guest_name }}</strong></td>
                <td>{{ $booking->room->room_number }} ({{ $booking->room->room_type }})</td>
                <td style="text-align: center;">${{ number_format($booking->total_price / $nights, 2) }}</td>
                <td style="text-align: right;">${{ number_format($booking->total_price, 2) }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $booking->payment_responsibility === 'company' ? 'badge-company' : 'badge-self' }}">
                        {{ $booking->payment_responsibility === 'company' ? 'Company' : 'Self' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td style="width: 60%;">
                <div class="notes-box">
                    <strong class="primary-color" style="display: block; margin-bottom: 4px;">Corporate Invoice Notes:</strong>
                    - This invoice covers group accommodation and pre-arranged services.<br>
                    - Services marked as 'Self' must be settled by individual guests.<br>
                    - Payment settlement period: 15 days net.<br>
                    @if($generalNotes)
                    <br><strong>Note:</strong> {{ $generalNotes }}
                    @endif
                </div>
                
                <div class="signatures" style="margin-top: 30px;">
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <p style="font-size: 9px; color: #64748b;">Hotel Authorized Signature</p>
                    </div>
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <p style="font-size: 9px; color: #64748b;">Company Representative</p>
                    </div>
                </div>
            </td>
            <td style="width: 40%; padding-left: 20px;">
                <div class="totals-box">
                    <div class="total-row">
                        <label>Accommodation Total:</label>
                        <span>${{ number_format($totalUSD, 2) }}</span>
                    </div>
                    <div class="total-row grand-total">
                        <label>Group Total:</label>
                        <span>${{ number_format($totalUSD, 2) }}</span>
                    </div>
                    <div class="total-row paid-row" style="margin-top: 8px;">
                        <label>Total Payments:</label>
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

    <div style="text-align: center; margin-top: 40px; font-size: 9px; color: #1e293b; border-top: 1px solid #f1f5f9; padding-top: 15px;">
        <p>Thank you for choosing PrimeLand Hotel for your group stay.</p>
        <p style="font-weight: bold; margin-top: 5px;">Powered By <span style="color: #940000;">EmCa Techonologies</span></p>
    </div>
</body>
</html>
