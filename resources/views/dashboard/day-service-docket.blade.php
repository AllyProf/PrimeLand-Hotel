<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Bill - {{ $dayService->service_reference }}</title>
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
        .container {
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-top: 5px solid #e77a3a;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header h2 {
            font-size: 22px;
            font-weight: 800;
            color: #e77a3a;
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
            background: #e77a3a;
            color: #fff;
            padding: 4px 10px;
            font-weight: 800;
            font-size: 12px;
            border-radius: 4px;
            margin-top: 5px;
            text-transform: uppercase;
        }
        .info-section {
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
        .items-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        .items-table th {
            text-align: left;
            border-bottom: 2px solid #333;
            padding: 8px;
            font-size: 11px;
            text-transform: uppercase;
            color: #333;
            background: #f8f9fa;
        }
        .items-table td {
            padding: 10px 8px;
            font-size: 13px;
            border-bottom: 1px solid #f0f0f0;
        }
        .totals {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #333;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-weight: 800;
            font-size: 14px;
        }
        .balance-due {
            font-size: 20px;
            color: #e77a3a;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        .signature-section {
            margin-top: 25px;
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin: 10px auto 5px;
            width: 80%;
        }
        .footer-credit {
            font-size: 10px;
            color: #1a1a1a;
            font-weight: 700;
            margin-top: 15px;
        }
        @media print {
            body { background: #fff; padding: 0; margin: 0; }
            .container { box-shadow: none; border-top: none; padding: 10px; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>PRIMELAND HOTEL</h2>
            <p>Sokoine Road - Moshi Kilimanjaro - Tanzania</p>
            <p>Tel: 0677155157</p>
            <div class="label">SERVICE DOCKET / BILL</div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Ref:</span>
                <span class="info-value">{{ $dayService->service_reference }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ $dayService->service_date->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Guest:</span>
                <span class="info-value">{{ strtoupper($dayService->guest_name) }}</span>
            </div>
            @if($dayService->guest_phone)
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ str_replace('+255+255', '+255', $dayService->guest_phone) }}</span>
            </div>
            @endif
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                {{-- Package Items --}}
                @if($showPackage ?? true)
                @php
                    $packageItems = $dayService->package_items ?? [];
                    $commonLabels = ['food'=>'Food', 'swimming'=>'Swimming', 'drinks'=>'Drinks', 'decoration'=>'Decor'];
                @endphp
                @if(!empty($packageItems))
                    @foreach($packageItems as $key => $price)
                        @if($price > 0)
                        <tr>
                            <td>{{ $commonLabels[$key] ?? ucfirst($key) }}</td>
                            <td style="text-align: right;">{{ number_format($price) }}</td>
                        </tr>
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td>{{ $dayService->service_type_name }}</td>
                        <td style="text-align: right;">{{ number_format($dayService->amount) }}</td>
                    </tr>
                @endif
                @endif

                {{-- Consumption --}}
                @php
                    $barConsumption = $dayService->serviceRequests ?? collect();
                    // Exclude cancelled items
                    $barConsumption = $barConsumption->filter(fn($item) => strtolower($item->status ?? "") !== 'cancelled');
                    
                    if(isset($filterCategories) && !empty($filterCategories)) {
                        $barConsumption = $barConsumption->filter(function($item) use ($filterCategories) {
                            return $item->service && in_array($item->service->category, $filterCategories);
                        });
                    }
                    
                    // Exclude items that have been merged/billed into the main day service bill
                    $barConsumption = $barConsumption->filter(fn($item) => strtolower($item->status ?? '') !== 'billed');
                    $totalConsumption = $barConsumption->sum('total_price_tsh');
                @endphp
                @if($barConsumption->isNotEmpty())
                    <tr><td colspan="2" style="padding-top:10px; font-weight:bold; font-style:italic;">-- Consumption --</td></tr>
                    @foreach($barConsumption as $item)
                    <tr>
                        <td>
                            {{ $item->service_specific_data['item_name'] ?? $item->service->name ?? 'Item' }} (x{{ $item->quantity }})
                        </td>
                        <td style="text-align: right;">{{ number_format($item->total_price_tsh) }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="totals">
            @php
                $mainAmount = ($showPackage ?? true) ? $dayService->amount : 0;
                $calcConsumption = $dayService->serviceRequests ?? collect();
                $calcConsumption = $calcConsumption->filter(fn($item) => strtolower($item->status ?? "") !== 'cancelled');

                if(isset($filterCategories) && !empty($filterCategories)) {
                    $calcConsumption = $calcConsumption->filter(function($item) use ($filterCategories) {
                        return $item->service && in_array($item->service->category, $filterCategories);
                    });
                }
                
                $calcConsumption = $calcConsumption->filter(fn($item) => strtolower($item->status ?? '') !== 'billed');
                
                $totalConsumption = $calcConsumption->sum('total_price_tsh');
                $totalBill = $mainAmount + $totalConsumption;
                
                $packagePaid = ($showPackage ?? true) ? ($dayService->amount_paid ?? 0) : 0;
                $consumptionPaid = $calcConsumption->where('payment_status', 'paid')->sum('total_price_tsh');
                $totalPaid = $packagePaid + $consumptionPaid;
                
                $balanceDue = $totalBill - $totalPaid;
            @endphp

            <div class="total-row">
                <span>Total Bill:</span>
                <span>{{ number_format($totalBill) }} TZS</span>
            </div>
            
            @if($totalPaid > 0)
            <div class="total-row" style="color: #28a745;">
                <span>Total Paid:</span>
                <span>{{ number_format($totalPaid) }} TZS</span>
            </div>
            @endif

            @if($balanceDue > 0)
            <div class="total-row balance-due">
                <span>BALANCE DUE:</span>
                <span>{{ number_format($balanceDue) }} TZS</span>
            </div>
            @endif
        </div>

        <div class="signature-section">
            <p style="font-size: 11px;">Guest Signature</p>
            <div class="signature-line"></div>
            
            <p style="font-size: 11px; margin-top: 15px;">Served By: <strong>{{ auth()->user()->name ?? 'Staff' }}</strong></p>
            <div class="signature-line" style="border-color: #e77a3a;"></div>
            
            <p class="footer-credit">
                Powered By EmCa Techonologies LTD (www.emca.tech)
            </p>
        </div>
    </div>
    <script>
        window.onload = function() { window.print(); };
    </script>
</body>
</html>
