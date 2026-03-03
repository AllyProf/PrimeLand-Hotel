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
            position: relative;
            overflow: hidden;
            border-top: 5px solid #e07632;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 5px;
            color: #e07632;
            text-transform: uppercase;
        }
        .header p {
            font-size: 11px;
            color: #666;
            margin: 1px 0;
        }
        .section {
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
        }
        .section-title {
            font-weight: 800;
            font-size: 13px;
            margin-bottom: 10px;
            text-align: center;
            text-transform: uppercase;
            color: #333;
            background: #f8f9fa;
            padding: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 12px;
        }
        .item-row {
            margin: 12px 0;
            font-size: 13px;
            padding: 10px;
            background: #fcfcfc;
            border-left: 4px solid #e07632;
            border-bottom: 1px solid #f0f0f0;
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            margin-bottom: 4px;
            font-size: 14px;
        }
        .item-qty {
            color: #e07632;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
        }
        .item-note {
            font-size: 11px;
            font-style: italic;
            color: #d35400;
            margin-top: 6px;
            padding-left: 8px;
            border-left: 2px solid #edaf82;
        }
        .total-section {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #333;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: 800;
            color: #1a1a1a;
            margin: 10px 0;
        }
        .grand-total-label { color: #e07632; }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #888;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .docket {
                box-shadow: none;
                border-top: none;
                padding: 10px;
                width: 100%;
            }
            .no-print {
                display: none;
            }
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 80px;
            color: rgba(40, 167, 69, 0.1);
            font-weight: 900;
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="docket">
        @php
            $activeOrders = $orders->filter(fn($o) => strtolower($o->status) !== 'cancelled');
            $isPaid = $activeOrders->isNotEmpty() && $activeOrders->every(fn($o) => in_array($o->payment_status, ['paid', 'room_charge']));
        @endphp
        
        @if($isPaid)
            <div class="watermark">PAID</div>
        @endif
        <div class="header">
            <h1>PrimeLand Hotel</h1>
            <p>Moshi, Kilimanjaro, Tanzania</p>
            <p>Tel: 0677155157</p>
            <div class="bill-label">GUEST BILL</div>
        </div>

        <div class="section">
            <div class="info-row">
                <span class="info-label">Guest:</span>
                <span class="info-value">{{ $guestName }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Location:</span>
                <span class="info-value">{{ $destination }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Served By:</span>
                <span class="info-value">
                    {{ $requestedBy }}
                    @php
                        $paidOrder = $orders->whereIn('payment_status', ['paid', 'room_charge'])->first();
                    @endphp
                    @if($paidOrder && $paidOrder->paidBy)
                        | Payment Recorded By: {{ $paidOrder->paidBy->name }}
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ $first->requested_at->format('M d, Y H:i') }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Items Ordered</div>
            @php
                $groupedItems = $orders->groupBy(function($item) {
                     $itemName = $item->service_specific_data['item_name'] ?? $item->service->name;
                     return $itemName . ($item->status === 'cancelled' ? ' [CANCELLED]' : '');
                });
            @endphp

            @foreach($groupedItems as $itemName => $items)
                @php
                    $isGroupCancelled = $items->first()->status === 'cancelled';
                    $qty = $items->sum('quantity');
                    $total = $isGroupCancelled ? 0 : $items->sum('total_price_tsh');
                    $unitPrice = $qty > 0 ? ($items->sum('total_price_tsh') / $qty) : 0;
                    
                    // Collect and clean notes
                    $notes = [];
                    foreach($items as $item) {
                        $rawNote = $item->guest_request;
                        
                        // Fallback to reception notes if guest_request is empty
                        if (!$rawNote && $item->reception_notes && str_contains($item->reception_notes, '- Msg: ')) {
                             $parts = explode('- Msg: ', $item->reception_notes);
                             $rawNote = $parts[1] ?? null;
                        }
                        
                        if ($rawNote) {
                            // Clean system messages (everything after |)
                            $cleanNote = trim(explode('|', $rawNote)[0]);
                            // Also remove "Completed by Kitchen" if it appears directly
                            $cleanNote = trim(explode('Completed by', $cleanNote)[0]);
                            
                            if ($cleanNote && !in_array($cleanNote, $notes)) {
                                $notes[] = $cleanNote;
                            }
                        }
                    }
                @endphp
                <div class="item-row" style="{{ $isGroupCancelled ? 'opacity: 0.6; border-left: 3px solid #ccc; background: #eee;' : '' }}">
                    <div class="item-header">
                        <span>{{ $itemName }} @if($isGroupCancelled) <span style="color: #666;">(CANCELLED)</span> @endif</span>
                        <span>{{ number_format($total) }} TZS</span>
                    </div>
                    <div class="info-row">
                        <span class="item-qty">Qty: {{ $qty }}</span>
                        <span>@ {{ number_format($unitPrice) }} TZS</span>
                    </div>
                    @foreach($notes as $note)
                        <div class="item-note">Note: {{ $note }}</div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="total-section">
            <div class="total-row">
                <span class="grand-total-label">TOTAL AMOUNT:</span>
                <span class="grand-total-value">{{ number_format($totalAmount) }} TZS</span>
            </div>
            
            @php
                $paidOrder = $orders->whereIn('payment_status', ['paid', 'room_charge'])->first();
                $paymentMethod = $paidOrder->payment_method ?? null;
                $paymentRef = $paidOrder->payment_reference ?? null;
                $paidAmount = $orders->whereIn('payment_status', ['paid', 'room_charge'])->sum('total_price_tsh');
                $pendingAmount = $totalAmount - $paidAmount;
            @endphp

            @if($paidAmount > 0)
                <div class="info-row" style="color: #27ae60; font-weight: bold; justify-content: space-between; font-size: 14px; margin-top: 5px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                    <span>PAID AMOUNT:</span>
                    <span>{{ number_format($paidAmount) }} TZS</span>
                </div>
                <div class="info-row" style="font-size: 12px; margin-top: 5px;">
                    <span>Method:</span>
                    <span style="font-weight: bold;">{{ strtoupper(str_replace('_', ' ', $paymentMethod ?? 'N/A')) }}</span>
                </div>
                <div class="info-row" style="font-size: 12px; margin-top: 2px;">
                    <span>Ref #:</span>
                    <span style="font-weight: bold;">{{ $paymentRef ?: 'N/A' }}</span>
                </div>
            @endif

            @if($pendingAmount > 10)
                <div class="total-row" style="color: #d35400; border-top: 1px dashed #ccc; padding-top: 5px; margin-top: 10px;">
                    <span>OUTSTANDING:</span>
                    <span>{{ number_format($pendingAmount) }} TZS</span>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>Thank you for dining with us!</p>
            <p style="margin-top: 5px;">Printed: {{ now()->format('M d, Y H:i') }}</p>
            <p style="margin-top: 8px; font-weight: 700; color: #1e293b; font-size: 10px;">
                Powered By EmCa Techonologies LTD (www.emca.tech)
            </p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #e07632; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            🖨️ Print Bill
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px;">
            Close
        </button>
    </div>

    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
