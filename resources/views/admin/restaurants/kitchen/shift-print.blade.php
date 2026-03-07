<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chef Shift Stock Sheet - {{ $shift->staff->name }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 14px; margin: 20px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .hotel-name { font-size: 22px; font-weight: bold; margin-bottom: 5px; }
        .report-title { font-size: 18px; text-transform: uppercase; letter-spacing: 2px; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px 0; }
        
        table.stock-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.stock-table th { border-bottom: 2px solid #000; padding: 8px; text-align: left; background: #f0f0f0; }
        table.stock-table td { border-bottom: 1px solid #ddd; padding: 8px; }
        
        .footer { margin-top: 50px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 60px; }
        .sig-box { width: 45%; border-top: 1px solid #000; text-align: center; padding-top: 5px; }
        
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px;">
            <i class="fa fa-print"></i> Print Stock Sheet
        </button>
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 5px; margin-left: 10px;">
            Go Back
        </button>
    </div>

    <div class="header">
        <div class="hotel-name">{{ $hotelName }}</div>
        <div class="report-title">Individual Shift Stock Sheet</div>
        <div>Kitchen Operations</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Staff Name:</strong></td>
            <td width="35%">{{ $shift->staff->name }}</td>
            <td width="15%"><strong>Shift ID:</strong></td>
            <td width="35%">#{{ $shift->id }}</td>
        </tr>
        <tr>
            <td><strong>Opened:</strong></td>
            <td>{{ $shift->opened_at->format('d M Y, H:i') }}</td>
            <td><strong>Closed:</strong></td>
            <td>{{ $shift->closed_at ? $shift->closed_at->format('d M Y, H:i') : 'N/A' }}</td>
        </tr>
        @if($shift->notes)
        <tr>
            <td colspan="4"><strong>Notes:</strong> {{ $shift->notes }}</td>
        </tr>
        @endif
    </table>

    <table class="stock-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th>Unit</th>
                <th style="text-align: center;">Opening</th>
                <th style="text-align: center;">Received</th>
                <th style="text-align: center;">Used</th>
                <th style="text-align: center;">Damaged</th>
                <th style="text-align: center;">Closing</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
            @if($row->usage > 0 || $row->received > 0 || $row->lost > 0)
            <tr>
                <td>{{ $row->name }}</td>
                <td>{{ $row->unit }}</td>
                <td style="text-align: center;">{{ number_format($row->opening_stock, 1) }}</td>
                <td style="text-align: center; color: green;">+{{ number_format($row->received, 1) }}</td>
                <td style="text-align: center; color: red;">-{{ number_format($row->usage, 1) }}</td>
                <td style="text-align: center; color: orange;">{{ number_format($row->lost, 1) }}</td>
                <td style="text-align: center;"><strong>{{ number_format($row->closing_stock, 1) }}</strong></td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><em>* This sheet acknowledges that the above items were utilized or received during the specified shift duration.</em></p>
        
        <div class="signatures">
            <div class="sig-box">
                <strong>{{ $shift->staff->name }}</strong><br>
                Chef Signature
            </div>
            <div class="sig-box">
                <strong>MANAGER SIGN-OFF</strong><br>
                Name & Signature
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 40px; font-size: 10px; color: #888;">
            Printed on {{ now()->format('d/m/Y H:i:s') }} | System ID: SH-{{ $shift->id }}
        </div>
    </div>
</body>
</html>
