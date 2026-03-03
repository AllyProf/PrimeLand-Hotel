<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Bill - {{ $guestName }}</title>
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
            border-top: 5px solid #e07632;
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
            color: #e07632;
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
            background: #e07632;
            color: #fff;
            padding: 4px 10px;
            font-weight: 800;
            font-size: 12px;
            border-radius: 4px;
            margin-top: 5px;
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
        .item-row {
            margin: 15px 0;
            padding: 12px;
            background: #fcfcfc;
            border-left: 5px solid #e07632;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
        }
        .item-qty {
            font-weight: 800;
            color: #e07632;
            font-family: 'JetBrains Mono', monospace;
            margin-right: 12px;
        }
        .item-name { font-weight: 700; color: #1a1a1a; }
        .notes {
            margin-top: 10px;
            padding: 10px;
            background: #fff9f5;
            border-left: 2px solid #edaf82;
            font-style: italic;
            font-size: 11px;
            color: #d35400;
        }
        .status-box {
            margin: 20px 0;
            text-align: center;
            padding: 15px;
            border: 2px solid #333;
            background: #f8f9fa;
        }
        .status-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            display: block;
            margin-bottom: 5px;
        }
        .status-value {
            font-size: 16px;
            font-weight: 800;
            color: #1a1a1a;
        }
        .total-bill {
            font-size: 22px;
            font-weight: 800;
            color: #e07632;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #888;
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
            <p>Sokoine Road - Moshi Kilimanjaro - Tanzania</p>
            <p>Tel: 0677155157</p>
            <div class="label">
                @if(in_array(($order->payment_status ?? 'pending'), ['paid', 'room_charge']))
                    GUEST BILL RECEIPT
                @else
                    KITCHEN ORDER DOCKET
                @endif
            </div>
            <p style="margin-top: 5px;">{{ now()->format('M d, Y - h:i A') }}</p>
        </div>

        <!-- Order Information -->
        <div class="section">
            <div class="info-row">
                <span class="info-label">Guest Name:</span>
                <span class="info-value">{{ $guestName }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Location:</span>
                <span class="info-value">{{ str_replace('WALK-IN (', '', str_replace(')', '', $destination)) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Bill #:</span>
                <span class="info-value">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Served By:</span>
                <span class="info-value">
                    {{ $requestedBy }}
                    @if($order->paidBy)
                        | Payment recorded by: {{ $order->paidBy->name }}
                    @endif
                </span>
            </div>
        </div>

        <!-- Billing Items -->
        <div class="section" style="border-bottom: none;">
            <div class="item-row">
                <div class="item-info">
                    <span class="item-qty">{{ $order->quantity }}x</span>
                    <span class="item-name">{{ $itemName }}</span>
                </div>
                <span class="item-price">{{ number_format($order->unit_price_tsh) }}</span>
            </div>
            
            @if($note)
            <div class="notes">
                <strong>Notes:</strong> {{ $note }}
            </div>
            @endif
        </div>

        <!-- Total Section -->
        <div class="status-box">
            <div>
                @php
                    $status = strtoupper($order->payment_status ?? 'PENDING');
                    if($status === 'ROOM_CHARGE') $status = 'CHARGED TO ROOM';
                @endphp
                <span class="status-label">ORDER STATUS</span>
                <span class="status-value">{{ $status }}</span>
                
                @if(in_array($order->payment_status, ['paid', 'room_charge']))
                    <div style="font-size: 11px; margin-top: 8px; font-weight: normal; color: #666; border-top: 1px solid #ddd; padding-top: 8px; text-align: left;">
                        METHOD: <strong style="color:#1a1a1a;">{{ strtoupper(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}</strong><br>
                        REF #: <strong style="color:#1a1a1a;">{{ $order->payment_reference ?: 'N/A' }}</strong>
                    </div>
                @endif
            </div>
            <div class="total-bill">
                {{ number_format($order->total_price_tsh) }} TZS
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="font-weight: 800; color: #1a1a1a; margin-bottom: 5px;">Thank you for dining with us!</p>
            <p>Please keep this receipt for your records.</p>
            <p style="margin-top: 10px; font-weight: 700; color: #1a1a1a;">
                Powered By EmCa Techonologies LTD (www.emca.tech)
            </p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 12px 30px; font-size: 16px; background: #e07632; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i class="fa fa-print"></i> Print Bill
        </button>
        <button onclick="window.close()" style="padding: 12px 30px; font-size: 16px; background: #6c757d; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px; font-weight: bold;">
            Close
        </button>
    </div>

    <script>
        window.onload = function() {
            // Optional: window.print();
        };
    </script>
</body>
</html>
