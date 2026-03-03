<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walk-in Docket - {{ $guestName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        body {
            font-family: 'Inter', sans-serif;
            padding: 10px;
            max-width: 380px;
            margin: 0 auto;
            background: #f8f9fa;
            color: #1a1a1a;
            line-height: 1.4;
        }
        .docket {
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-top: 5px solid #e77a31;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 22px;
            font-weight: 800;
            color: #e77a31;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 11px;
            color: #666;
            margin: 1px 0;
        }
        .label {
            display: inline-block;
            background: #e77a31;
            color: #fff;
            padding: 4px 10px;
            font-weight: 800;
            font-size: 12px;
            border-radius: 4px;
            margin-top: 5px;
            text-transform: uppercase;
        }
        .section {
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 12px;
        }
        .info-label { color: #666; }
        .info-value { font-weight: 700; text-align: right; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th {
            text-align: left;
            border-bottom: 2px solid #333;
            padding: 8px;
            font-size: 11px;
            text-transform: uppercase;
            color: #333;
            background: #f8f9fa;
        }
        td {
            padding: 10px 8px;
            font-size: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .total-section {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #333;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: 800;
            color: #e77a31;
            margin: 10px 0;
        }
        .payment-status-badge {
            text-align: center;
            padding: 8px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .paid-badge { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .unpaid-badge { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #888;
        }
        .footer-credit {
            font-size: 10px;
            color: #1a1a1a;
            font-weight: 700;
            margin-top: 10px;
        }
        @media print {
            body { background: #fff; padding: 0; margin: 0; }
            .docket { box-shadow: none; border-top: none; padding: 10px; width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="docket">
        <!-- Header -->
        <div class="header">
            <h1>PRIMELAND HOTEL</h1>
            <p>Sokoine Road - Moshi, Kilimanjaro - Tanzania</p>
            <p>Tel: 0677155157</p>
            <div class="label">WALK-IN SALE DOCKET</div>
        </div>

        <!-- Docket Info -->
        <div class="section">
            <div class="info-row">
                <span class="info-label">Guest Name:</span>
                <span class="info-value">{{ $guestName }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date & Time:</span>
                <span class="info-value">{{ $serviceRequest->created_at->format('M d, Y - h:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Docket #:</span>
                <span class="info-value">{{ str_pad($serviceRequest->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Served By:</span>
                <span class="info-value">Bar Keeper</span>
            </div>
            @if($serviceRequest->paidBy)
            <div class="info-row">
                <span class="info-label">Payment Recorded By:</span>
                <span class="info-value">{{ $serviceRequest->paidBy->name }}</span>
            </div>
            @endif
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 50%;">Item Description</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 15%;" class="text-right">Unit Price</th>
                    <th style="width: 15%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->service_specific_data['item_name'] ?? $item->service->name ?? 'Item' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price_tsh) }}</td>
                    <td class="text-right">{{ number_format($item->total_price_tsh) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span>TOTAL AMOUNT:</span>
                <span>{{ number_format($totalAmount) }} TZS</span>
            </div>
            <div style="text-align: center; margin-top: 10px;">
                @if($serviceRequest->payment_status === 'paid')
                    <div class="payment-status-badge paid-badge">✓ PAID</div>
                @else
                    <div class="payment-status-badge unpaid-badge">UNPAID - PENDING PAYMENT</div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your patronage!</strong></p>
            <p>This is a computer-generated docket.</p>
            <p class="footer-credit">
                Powered By EmCa Techonologies LTD (www.emca.tech)
            </p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 16px; background: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fa fa-print"></i> Print Docket
        </button>
        <button onclick="window.close()" style="padding: 10px 30px; font-size: 16px; background: #6c757d; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>

    <script>
        // Auto-focus print dialog on load
        window.onload = function() {
            // Uncomment the line below to auto-print on load
            // window.print();
        };
    </script>
</body>
</html>
