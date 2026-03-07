<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt - {{ $booking->booking_reference }} - PrimeLand Hotel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #e07632;
            --text-main: #000000;
            --text-light: #000000;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
        body {
            background-color: #f1f5f9;
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            line-height: 1.4;
            padding: 30px 10px;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden; /* important for preventing watermark overflow */
        }

        .receipt-container::before {
            content: "PRIMELAND HOTEL";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 60px;
            font-weight: 900;
            color: rgba(224, 118, 50, 0.06); /* Primary color, ultra low opacity */
            white-space: nowrap;
            z-index: 0;
            pointer-events: none;
        }

        .receipt-container > * {
            position: relative;
            z-index: 1;
        }

        /* Centered Header */
        .header {
            text-align: center;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .logo-section {
            margin-bottom: 15px;
        }

        .logo-section h1 {
            font-size: 26px;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 4px;
        }

        .logo-section p {
            font-size: 13px;
            color: var(--text-main);
            margin-bottom: 2px;
        }

        .receipt-title-section {
            text-align: center;
        }

        .receipt-title-section h2 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .ref-pill {
            display: inline-block;
            background: var(--bg-light);
            padding: 8px 15px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
        }

        /* Info Grid */
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 40px;
        }

        .info-col {
            flex: 1;
        }

        .info-col h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--primary);
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 4px;
        }

        .info-row {
            margin-bottom: 4px;
            display: flex;
        }

        .info-row label {
            width: 100px;
            color: var(--text-light);
            font-weight: 500;
        }

        .info-row span {
            font-weight: 600;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .items-table th {
            text-align: left;
            background: #f8fafc;
            padding: 10px 12px;
            border-bottom: 2px solid var(--border);
            font-size: 11px;
            text-transform: uppercase;
            color: var(--text-main);
        }

        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .item-desc strong {
            display: block;
            font-size: 13px;
        }

        .item-desc small {
            color: var(--text-light);
        }

        /* Summary Section */
        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .payment-notes {
            flex: 1;
            padding-right: 40px;
        }

        .summary-box {
            width: 360px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .summary-row.grand-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid var(--border);
            font-weight: 700;
            font-size: 16px;
            color: var(--primary);
        }

        .summary-row.paid {
            color: #10b981;
            font-weight: 600;
        }

        .signature-area {
            text-align: center;
            margin-top: 40px;
            border-top: 1px dashed var(--border);
            padding-top: 20px;
        }

        .sig-line {
            width: 200px;
            margin: 0 auto;
            border-bottom: 2px solid #000;
            height: 40px;
            margin-bottom: 8px;
        }

        /* Policies - Vertically Arranged */
        .policies-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }
        
        .policy-item {
            margin-bottom: 12px;
        }

        .policy-item h4 {
            font-size: 10px;
            text-transform: uppercase;
            color: var(--text-main);
            font-weight: 700;
            margin-bottom: 4px;
        }

        .policy-item p {
            font-size: 9px;
            color: var(--text-main);
            line-height: 1.3;
        }

        .no-print-bar {
            max-width: 800px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary { background: var(--primary); color: white; }

        @media print {
            @page {
                size: A4;
                margin: 0.5cm !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: none !important;
                padding: 10px !important;
                width: 100% !important;
            }
            .header {
                margin-bottom: 15px !important;
            }
            .info-grid {
                margin-bottom: 15px !important;
            }
            .items-table {
                margin-bottom: 15px !important;
            }
            .footer-section {
                /* Combine notes and summary side by side even more tightly */
            }
            .policies-footer {
                margin-top: 15px !important;
            }
        }
    </style>
</head>
<body>
    @php
        $isCorporate = $booking->is_corporate_booking && $booking->company_id;
        $isStaffView = auth()->guard('staff')->check();
        
        $displayBookings = collect([$booking]);
        if ($isCorporate && isset($allCompanyBookings) && $isStaffView) {
            $displayBookings = $allCompanyBookings;
        }
        
        $isTzGuest = ($booking->guest_type === 'tanzanian');
        
        $groupRoomTotalBase = 0;
        foreach($displayBookings as $b) { $groupRoomTotalBase += $b->total_price; }

        $totalPaidBase = ($isCorporate && isset($totalCompanyPaid)) ? $totalCompanyPaid : ($booking->amount_paid ?? 0);
        
        if (isset($isGuestWithSelfPaidServices) && $isGuestWithSelfPaidServices) {
            $grandTotalBase = $guestServicePayments ?? 0;
            $grandPaidBase = $guestServicePayments ?? 0;
        } else {
            $grandTotalBase = $groupRoomTotalBase;
            $grandPaidBase = $totalPaidBase;
        }
        
        $balanceBase = max(0, $grandTotalBase - $grandPaidBase);
        $currentExchangeRate = $exchangeRate ?? 2500;
        
        // Final figures based on guest type
        if ($isTzGuest) {
            $grandTotalTZS = $grandTotalBase;
            $grandPaidTZS = $grandPaidBase;
            $balanceTZS = $balanceBase;
            
            // Standard conversions to USD for background storage/ref
            $grandTotalUSD = $grandTotalBase / $currentExchangeRate;
            $grandPaidUSD = $grandPaidBase / $currentExchangeRate;
            $balanceUSD = $balanceBase / $currentExchangeRate;
        } else {
            $grandTotalUSD = $grandTotalBase;
            $grandPaidUSD = $grandPaidBase;
            $balanceUSD = $balanceBase;
            
            $grandTotalTZS = $grandTotalBase * $currentExchangeRate;
            $grandPaidTZS = $grandPaidBase * $currentExchangeRate;
            $balanceTZS = $balanceBase * $currentExchangeRate;
        }
    @endphp

    <div class="no-print-bar">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fa fa-print"></i> PRINT RECEIPT
        </button>
        <button class="btn" onclick="window.close()" style="background: #e2e8f0;">
            <i class="fa fa-times"></i> CLOSE
        </button>
    </div>

    <div class="receipt-container">
        <div class="header" style="text-align: center; border-bottom: 2px solid var(--primary); padding-bottom: 20px; margin-bottom: 25px;">
            <h1 style="font-size: 26px; color: var(--primary); font-weight: 800; margin-bottom: 4px;">PRIMELAND HOTEL</h1>
            <p style="font-size: 13px; color: var(--text-main); margin-bottom: 25px; font-weight: 500;">Comfort in every Stay</p>
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="text-align: left;">
                    <p style="margin-bottom: 16px; font-size: 12px; font-weight: 600; color: var(--text-main); line-height: 24px;"><i class="fa fa-map-marker"></i> Moshi, Kilimanjaro, Tanzania</p>
                    <p style="margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-main);"><i class="fa fa-phone"></i> 0677155157</p>
                    <p style="font-size: 12px; font-weight: 600; color: var(--text-main);"><i class="fa fa-envelope"></i> info@primelandhotel.com</p>
                </div>
                <div style="text-align: right;">
                    <h2 style="font-size: 24px; line-height: 24px; font-weight: 800; color: var(--text-main); margin-bottom: 16px;">RECEIPT</h2>
                    <div class="ref-pill" style="display: inline-block; background: var(--bg-light); padding: 8px 15px; border: 1px solid var(--border); border-radius: 6px; font-weight: 600; font-size: 12px;">
                        {{ $booking->booking_reference }}-{{ date('Ymd') }}
                    </div>
                    <p style="margin-top: 8px; font-weight: 500; color: var(--text-main); font-size: 12px;">Date: {{ now()->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-col">
                <h3>Billed To</h3>
                @if($isCorporate && $booking->company)
                    <div class="info-row"><label>Company:</label> <span>{{ $booking->company->name }}</span></div>
                    <div class="info-row"><label>Email:</label> <span>{{ $booking->company->email }}</span></div>
                @else
                    <div class="info-row"><label>Guest:</label> <span>{{ $booking->guest_name }}</span></div>
                    <div class="info-row"><label>Email:</label> <span>{{ $booking->guest_email ?? 'N/A' }}</span></div>
                    <div class="info-row"><label>Phone:</label> <span>{{ $booking->guest_phone ?? 'N/A' }}</span></div>
                @endif
            </div>
            <div class="info-col">
                <h3>Stay Details</h3>
                @if($isCorporate && $booking->company)
                    <div class="info-row"><label>Check-in:</label> <span>{{ $booking->check_in->format('M d, Y') }}</span></div>
                    <div class="info-row"><label>Check-out:</label> <span>{{ $booking->check_out->format('M d, Y') }}</span></div>
                @else
                    <div class="info-row"><label>Check-in:</label> <span>{{ $booking->check_in->format('M d, Y') }}</span></div>
                    <div class="info-row"><label>Check-out:</label> <span>{{ $booking->check_out->format('M d, Y') }}</span></div>
                    <div class="info-row"><label>Room Type:</label> <span>{{ $booking->room->room_type }} ({{ $booking->room->room_number }})</span></div>
                @endif
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Nights</th>
                    @if($isTzGuest)
                    <th style="text-align: right;">Total (TZS)</th>
                    @else
                    <th style="text-align: right;">Total (USD)</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if(isset($isGuestWithSelfPaidServices) && $isGuestWithSelfPaidServices)
                    @php
                        $guestServices = $booking->serviceRequests()->where('payment_status', 'paid')->with('service')->get();
                    @endphp
                    @foreach($guestServices as $gs)
                    <tr>
                        <td class="item-desc">
                            <strong>{{ $gs->service->name }}</strong>
                            @if($gs->quantity > 1) <small>Quantity: {{ $gs->quantity }}</small> @endif
                        </td>
                        <td style="text-align: center;">-</td>
                        @if($isTzGuest)
                        <td style="text-align: right;">{{ number_format($gs->total_price_tsh, 0) }} TZS</td>
                        @else
                        <td style="text-align: right;">${{ number_format($gs->total_price_tsh / $currentExchangeRate, 2) }}</td>
                        @endif
                    </tr>
                    @endforeach
                @else
                    @foreach($displayBookings as $b)
                    @php
                        $bookingExchangeRate = $b->locked_exchange_rate ?? $currentExchangeRate;
                        $lineTotal = $isTzGuest ? $b->total_price : ($b->total_price * $bookingExchangeRate);
                    @endphp
                    <tr>
                        <td class="item-desc">
                            <strong>Accommodation: {{ $b->room->room_type }}</strong>
                            <small>Guest: {{ $b->guest_name }}</small>
                        </td>
                        <td style="text-align: center;">{{ $b->check_in->diffInDays($b->check_out) }}</td>
                        @if($isTzGuest)
                        <td style="text-align: right;">{{ number_format($lineTotal, 0) }} TZS</td>
                        @else
                        <td style="text-align: right;">${{ number_format($b->total_price, 2) }}</td>
                        @endif
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="footer-section">
            <div class="payment-notes">
                <div style="background: #f8fafc; padding: 12px; border-radius: 6px; font-size: 11px;">
                    <strong style="color: var(--primary); display: block; margin-bottom: 4px;">Payment Method:</strong>
                    @if(isset($isGuestWithCompanyPaidServices) && $isGuestWithCompanyPaidServices)
                        Room charges billed to <strong>{{ $booking->company->name }}</strong>.
                    @else
                        {{ ucfirst($booking->payment_method ?? 'Bank/Mobile') }}
                        @if($booking->payment_transaction_id) | Ref: {{ $booking->payment_transaction_id }} @endif
                    @endif
                </div>
                
                <div class="signature-area" style="display: flex; justify-content: space-between; align-items: flex-end; gap: 30px; flex-wrap: wrap;">
                    {{-- Hotel Authorized Signature --}}
                    <div style="text-align: center; flex: 1; min-width: 160px;">
                        <div class="sig-line"></div>
                        <p style="font-size: 11px; color: var(--text-main);">Hotel Authorized Signature</p>
                    </div>
                    {{-- Guest Signature --}}
                    @php
                        $guestSigPath = $booking->checkout_signature_path ?? $booking->guest_signature_path ?? null;
                    @endphp
                    <div style="text-align: center; flex: 1; min-width: 160px;">
                        @if($guestSigPath)
                            <img src="{{ asset($guestSigPath) }}" 
                                 style="max-height: 60px; max-width: 200px; display: block; margin: 0 auto 4px; border-bottom: 2px solid #000; padding-bottom: 4px;"
                                 alt="Guest Signature">
                        @else
                            <div class="sig-line"></div>
                        @endif
                        <p style="font-size: 11px; color: var(--text-main);">
                            Guest Signature
                            @if($booking->checkout_signature_path)
                                <br><span style="font-size: 9px; color: var(--text-main);">(Check-Out Signature)</span>
                            @elseif($booking->guest_signature_path)
                                <br><span style="font-size: 9px; color: var(--text-main);">(Check-In Signature)</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="summary-box">
                @if($isTzGuest)
                <div class="summary-row">
                    <label>Subtotal:</label>
                    <span style="text-align: right;">{{ number_format($grandTotalTZS, 0) }} TZS</span>
                </div>
                <div class="summary-row grand-total">
                    <label>Total Balance:</label>
                    <span style="text-align: right;">{{ number_format($grandTotalTZS, 0) }} TZS</span>
                </div>
                <div class="summary-row paid" style="align-items: center;">
                    <label style="margin-top: 10px;">Amount Paid:</label>
                    <span style="text-align: right; margin-top: 10px;">{{ number_format($grandPaidTZS, 0) }} TZS</span>
                </div>
                @if($balanceTZS > 1)
                <div class="summary-row" style="color: #dc2626; font-weight: 700; margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border);">
                    <label>Due Balance:</label>
                    <span style="text-align: right;">{{ number_format($balanceTZS, 0) }} TZS</span>
                </div>
                @else
                <div class="summary-row" style="color: #10b981; font-weight: 700; margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border);">
                    <label>Due Balance:</label>
                    <span style="text-align: right;">0 TZS</span>
                </div>
                @endif
                @else
                <div class="summary-row">
                    <label>Subtotal:</label>
                    <span style="text-align: right;">${{ number_format($grandTotalUSD, 2) }}</span>
                </div>
                <div class="summary-row grand-total">
                    <label>Total Balance:</label>
                    <span style="text-align: right;">${{ number_format($grandTotalUSD, 2) }}</span>
                </div>
                <div class="summary-row paid" style="align-items: center;">
                    <label style="margin-top: 10px;">Amount Paid:</label>
                    <span style="text-align: right; margin-top: 10px;">${{ number_format($grandPaidUSD, 2) }}</span>
                </div>
                @if($balanceUSD > 0.1)
                <div class="summary-row" style="color: #dc2626; font-weight: 700; margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border);">
                    <label>Due Balance:</label>
                    <span style="text-align: right;">${{ number_format($balanceUSD, 2) }}</span>
                </div>
                @else
                <div class="summary-row" style="color: #10b981; font-weight: 700; margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border);">
                    <label>Due Balance:</label>
                    <span style="text-align: right;">$0.00</span>
                </div>
                @endif
                @endif
            </div>
        </div>

        <div class="policies-footer">
            <div class="policy-item">
                <h4>Booking Policy</h4>
                <p>50% deposit required for confirmation. Full pre-payment within 7 days of stay.</p>
            </div>
            <div class="policy-item">
                <h4>Cancellation</h4>
                <p>30 days: Free | 14 days: 50% fee | 7 days: 100% fee. No-show: Full charge.</p>
            </div>
            <div class="policy-item">
                <h4>General</h4>
                <p>Room charges include VAT. Self-paid services (laundry, drinks) are extra.</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #1e293b; font-weight: 700;">
            Powered By EmCa Techonologies LTD (www.emca.tech)</div>
    </div>
</body>
</html>
