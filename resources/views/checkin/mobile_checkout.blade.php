<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Guest Check-Out - PrimeLand Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #e77a3a;
            --secondary: #2c3e50;
            --light: #f8f9fa;
            --dark: #343a40;
            --success: #28a745;
            --danger: #dc3545;
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            background: #f0f2f5; 
            margin: 0; 
            color: var(--dark);
        }

        .header {
            background: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header h1 { font-size: 18px; margin: 0; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

        .container { max-width: 500px; margin: 0 auto; padding: 20px; }

        .card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        h2 { font-size: 22px; margin-bottom: 8px; font-weight: 700; color: var(--secondary); }
        p.subtitle { color: #666; font-size: 14px; margin-bottom: 24px; }

        .bill-summary {
            background: #fdfaf8;
            border: 1px solid #ffe8d9;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .bill-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .bill-row:last-child { border-bottom: none; font-weight: 700; font-size: 18px; color: var(--dark); }

        .service-list {
            margin-top: 20px;
        }

        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .service-info span { display: block; }
        .service-name { font-weight: 600; font-size: 14px; }
        .service-date { font-size: 11px; color: #999; }
        .service-price { font-weight: 700; color: var(--secondary); }

        .balance-badge {
            background: var(--danger);
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            font-weight: 700;
        }

        .balance-badge.paid { background: var(--success); }

        #sig-canvas {
            width: 100%;
            height: 200px;
            background: #fff;
            border: 1px dashed var(--primary);
            border-radius: 12px;
            touch-action: none;
            cursor: crosshair;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            border: none;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:disabled { opacity: 0.6; }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 3000;
            display: none;
        }

        .loader {
            width: 48px;
            height: 48px;
            border: 5px solid #FFF;
            border-bottom-color: var(--primary);
            border-radius: 50%;
            display: inline-block;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .success-screen { text-align: center; padding: 40px 20px; }
        .success-icon { font-size: 80px; color: var(--success); margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>PrimeLand Hotel</h1>
        <div style="font-size: 12px; color: #666;">Guest Check-Out & Billing</div>
    </div>

    <div class="container" id="main-content">
        <div id="checkout-form" class="card">
            <h2>Final Bill Detail</h2>
            <p class="subtitle">Hello, **{{ $booking->guest_name }}**. Please review your stay charges before completing your check-out.</p>
            
            <div class="bill-summary">
                <div class="bill-row">
                    <span>Booking Reference</span>
                    <span style="font-weight: 600; color: var(--primary);">{{ $booking->booking_reference }}</span>
                </div>
                <div class="bill-row">
                    <span>Room Charges ({{ $bill['nights'] }} Nights)</span>
                    <div style="text-align: right;">
                        <div style="font-weight: 600;">${{ number_format($bill['roomCharge'], 2) }}</div>
                        <div style="font-size: 11px; color: #666;">{{ number_format($bill['roomChargeTsh'], 0) }} TZS</div>
                    </div>
                </div>
                <div class="bill-row">
                    <span>Extra Services Used</span>
                    <div style="text-align: right;">
                        <div style="font-weight: 600;">${{ number_format($bill['servicesTotal'], 2) }}</div>
                        <div style="font-size: 11px; color: #666;">{{ number_format($bill['servicesTotalTsh'], 0) }} TZS</div>
                    </div>
                </div>
                <div class="bill-row" style="border-top: 1px solid #eee; padding-top: 10px; margin-top: 5px;">
                    <span style="font-weight: 600;">Total Amount</span>
                    <div style="text-align: right;">
                        <div style="font-weight: 700;">${{ number_format($bill['totalAmount'], 2) }}</div>
                        <div style="font-size: 11px; color: #666;">{{ number_format($bill['totalAmountTsh'], 0) }} TZS</div>
                    </div>
                </div>
                <div class="bill-row" style="color: var(--success);">
                    <span>Total Paid</span>
                    <div style="text-align: right;">
                        <div style="font-weight: 600;">-${{ number_format($bill['paidAmount'], 2) }}</div>
                        <div style="font-size: 11px; color: #666;">-{{ number_format($bill['paidAmountTsh'], 0) }} TZS</div>
                    </div>
                </div>
                <div class="bill-row" style="margin-top: 10px; border-top: 2px solid #ffd8c1; padding-top: 15px;">
                    <span style="font-weight: 700; color: var(--primary);">Balance Due</span>
                    <div style="text-align: right;">
                        <div style="font-weight: 800; color: var(--primary); font-size: 18px;">${{ number_format($bill['balance'], 2) }}</div>
                        <div style="font-size: 12px; color: #666; font-weight: 600;">{{ number_format($bill['balanceTsh'], 0) }} TZS</div>
                    </div>
                </div>
            </div>

            @if($bill['balance'] > 0)
            <div class="balance-badge">
                <i class="fa fa-exclamation-circle"></i> REMAINING: {{ number_format($bill['balanceTsh'], 0) }} TZS (${{ number_format($bill['balance'], 2) }})
            </div>
            @else
            <div class="balance-badge paid">
                <i class="fa fa-check-circle"></i> BILL SETTLED: {{ number_format($bill['totalAmountTsh'], 0) }} TZS PAID
            </div>
            @endif

            <h3>Extra Services Listing</h3>
            <div class="service-list">
                @forelse($bill['services'] as $service)
                <div class="service-item">
                    <div class="service-info">
                        <span class="service-name">{{ $service->service_specific_data['item_name'] ?? ($service->service->name ?? 'Service') }}</span>
                        <span class="service-date">{{ $service->created_at->format('M d, H:i') }}</span>
                    </div>
                    <span class="service-price">${{ number_format($service->total_price_tsh / ($bill['exchangeRate'] ?? 2500), 2) }}</span>
                </div>
                @empty
                <p style="text-align: center; color: #999; font-size: 13px;">No extra services used during this stay.</p>
                @endforelse
            </div>

            <div style="margin-top: 30px;">
                <h3 style="font-size: 16px; margin-bottom: 10px;">Check-Out Signature</h3>
                <p style="font-size: 12px; color: #666; margin-bottom: 10px;">By signing below, you acknowledge the bill and authorize your check-out.</p>
                <div style="position: relative;">
                    <canvas id="sig-canvas"></canvas>
                    <button id="clear-sig" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: #999;">
                        <i class="fa fa-undo"></i>
                    </button>
                </div>
            </div>

            <button class="btn btn-primary" style="margin-top: 30px;" id="finalize-btn">
                Finalize Check-Out <i class="fa fa-sign-out"></i>
            </button>
        </div>

        <div id="success-screen" class="card hidden success-screen">
            <div class="success-icon"><i class="fa fa-circle-check"></i></div>
            <h2>Hope to See You Soon!</h2>
            <p>Your check-out has been processed. A copy of your final receipt has been sent to your email.</p>
            <p style="font-size: 14px; color: #666; margin-top: 15px;">Thank you for staying at PrimeLand Hotel.</p>
            
            <a href="/" class="btn btn-outline" style="margin-top: 30px;">Return to Homepage</a>
        </div>
    </div>

    <div class="loading-overlay" id="loading">
        <span class="loader"></span>
        <p style="margin-top: 15px; font-weight: 600;">Processing check-out...</p>
    </div>

    <script>
        let signaturePad = null;

        // Init Signature
        function initSignature() {
            const canvas = document.getElementById('sig-canvas');
            const ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)', penColor: 'rgb(44, 62, 80)' });
        }

        window.onload = initSignature;
        document.getElementById('clear-sig').onclick = () => signaturePad.clear();

        document.getElementById('finalize-btn').onclick = async () => {
            if (signaturePad.isEmpty()) {
                Swal.fire('Required', 'Please sign to acknowledge the bill and complete check-out.', 'warning');
                return;
            }

            const { isConfirmed } = await Swal.fire({
                title: 'Finalize Check-Out?',
                text: "Are you sure you want to complete your check-out?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Finalize',
                confirmButtonColor: '#e77a3a'
            });

            if (!isConfirmed) return;

            document.getElementById('loading').style.display = 'flex';

            const payload = {
                checkout_signature: signaturePad.toDataURL()
            };

            try {
                const res = await fetch('{{ route("checkout.mobile.submit", $booking->checkout_token) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                document.getElementById('loading').style.display = 'none';

                if (data.success) {
                    document.getElementById('checkout-form').classList.add('hidden');
                    document.getElementById('success-screen').classList.remove('hidden');
                    window.scrollTo(0, 0);
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (err) {
                document.getElementById('loading').style.display = 'none';
                Swal.fire('Error', 'Connection failed.', 'error');
            }
        };
    </script>
</body>
</html>
