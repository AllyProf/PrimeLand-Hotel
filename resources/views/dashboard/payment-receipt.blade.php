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
            --text-main: #1e293b;
            --text-light: #64748b;
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
        }

        /* Compact Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .logo-section h1 {
            font-size: 22px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 4px;
        }

        .logo-section p {
            font-size: 12px;
            color: var(--text-light);
            margin-bottom: 2px;
        }

        .receipt-title-section {
            text-align: right;
        }

        .receipt-title-section h2 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .ref-pill {
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
            color: #475569;
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

        /* Policies - Bottom Mini Text */
        .policies-footer {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .policy-item h4 {
            font-size: 9px;
            text-transform: uppercase;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .policy-item p {
            font-size: 8px;
            color: var(--text-light);
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
        
        $groupRoomTotalUSD = 0;
        foreach($displayBookings as $b) { $groupRoomTotalUSD += $b->total_price; }

        $totalPaidUSD = ($isCorporate && isset($totalCompanyPaid)) ? $totalCompanyPaid : ($booking->amount_paid ?? 0);
        
        if (isset($isGuestWithSelfPaidServices) && $isGuestWithSelfPaidServices) {
            $grandTotalUSD = $guestServicePayments ?? 0;
            $grandPaidUSD = $guestServicePayments ?? 0;
        } else {
            $grandTotalUSD = $groupRoomTotalUSD;
            $grandPaidUSD = $totalPaidUSD;
        }
        
        $balanceUSD = max(0, $grandTotalUSD - $grandPaidUSD);
        $currentExchangeRate = $exchangeRate ?? 2500;
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
        <div class="header">
            <div class="logo-section">
                <h1>PRIMELAND HOTEL</h1>
                <p>Comfort in every Stay</p>
                <p><i class="fa fa-map-marker"></i> Moshi, Kilimanjaro, Tanzania</p>
                <p><i class="fa fa-phone"></i> 0677155157 | <i class="fa fa-envelope"></i> info@primelandhotel.com</p>
            </div>
            <div class="receipt-title-section">
                <h2>RECEIPT</h2>
                <div class="ref-pill">
                    {{ $booking->booking_reference }}-{{ date('Ymd') }}
                </div>
                <p style="margin-top: 8px; font-weight: 500; color: var(--text-light);">Date: {{ now()->format('M d, Y') }}</p>
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
                    <div class="info-row"><label>Phone:</label> <span>{{ $booking->guest_phone ?? 'N/A' }}</span></div>
                @endif
            </div>
            <div class="info-col">
                <h3>Stay Details</h3>
                @if($isCorporate && $booking->company)
                    <div class="info-row"><label>Check-in:</label> <span>{{ $booking->check_in->format('M d, Y') }}</span></div>
                    <div class="info-row"><label>Check-out:</label> <span>{{ $booking->check_out->format('M d, Y') }}</span></div>
                @else
                    <div class="info-row"><label>Dates:</label> <span>{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}</span></div>
                    <div class="info-row"><label>Room:</label> <span>{{ $booking->room->room_number }} ({{ $booking->room->room_type }})</span></div>
                @endif
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Nights</th>
                    <th style="text-align: right;">USD Total</th>
                    <th style="text-align: right;">TZS Total</th>
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
                        <td style="text-align: right;">${{ number_format($gs->total_price_tsh / $currentExchangeRate, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($gs->total_price_tsh, 0) }} TZS</td>
                    </tr>
                    @endforeach
                @else
                    @foreach($displayBookings as $b)
                    @php
                        $bookingExchangeRate = $b->locked_exchange_rate ?? $currentExchangeRate;
                        $totalPriceTZS = $b->total_price * $bookingExchangeRate;
                    @endphp
                    <tr>
                        <td class="item-desc">
                            <strong>Accommodation: {{ $b->room->room_type }}</strong>
                            <small>Guest: {{ $b->guest_name }}</small>
                        </td>
                        <td style="text-align: center;">{{ $b->check_in->diffInDays($b->check_out) }}</td>
                        <td style="text-align: right;">${{ number_format($b->total_price, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($totalPriceTZS, 0) }} TZS</td>
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
                        <p style="font-size: 11px; color: #64748b;">Hotel Authorized Signature</p>
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
                        <p style="font-size: 11px; color: #64748b;">
                            Guest Signature
                            @if($booking->checkout_signature_path)
                                <br><span style="font-size: 9px; color: #10b981;">(Check-Out Signature)</span>
                            @elseif($booking->guest_signature_path)
                                <br><span style="font-size: 9px; color: #64748b;">(Check-In Signature)</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="summary-box">
                <div class="summary-row">
                    <label>Subtotal (USD):</label>
                    <span style="text-align: right;">${{ number_format($grandTotalUSD, 2) }} <br><small style="color: #64748b;">{{ number_format($grandTotalUSD * $currentExchangeRate, 0) }} TZS</small></span>
                </div>
                <div class="summary-row grand-total">
                    <label>Total Balance:</label>
                    <span style="text-align: right;">${{ number_format($grandTotalUSD, 2) }} <br><small style="font-size: 12px; color: var(--text-light); font-weight: normal;">{{ number_format($grandTotalUSD * $currentExchangeRate, 0) }} TZS</small></span>
                </div>
                <div class="summary-row paid" style="align-items: center;">
                    <label style="margin-top: 10px;">Amount Paid:</label>
                    <span style="text-align: right; margin-top: 10px;">${{ number_format($grandPaidUSD, 2) }} <br><small style="font-size: 11px; font-weight: normal;">{{ number_format($grandPaidUSD * $currentExchangeRate, 0) }} TZS</small></span>
                </div>
                @if($balanceUSD > 0.1)
                <div class="summary-row" style="color: #dc2626; font-weight: 700; margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border);">
                    <label>Due Balance:</label>
                    <span style="text-align: right;">${{ number_format($balanceUSD, 2) }} <br><small style="font-size: 11px; font-weight: normal;">{{ number_format($balanceUSD * $currentExchangeRate, 0) }} TZS</small></span>
                </div>
                @else
                <div class="summary-row" style="color: #10b981; font-weight: 700; margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border);">
                    <label>Due Balance:</label>
                    <span style="text-align: right;">$0.00 <br><small style="font-size: 11px; font-weight: normal;">0 TZS</small></span>
                </div>
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
