@extends('dashboard.layouts.reports')

@section('reports-content')
@include('dashboard.reports.partials.receipt-styles')

<div class="app-title d-flex justify-content-between align-items-center d-print-none">
  <div>
    <h1><i class="fa fa-dashboard text-primary"></i> General Performance Overview</h1>
    <p>Strategic analysis and revenue trends</p>
  </div>
</div>

<!-- DASHBOARD VIEW (Screen Only) -->
<div class="d-print-none">

<!-- Advanced Report Filter -->
<div class="row mb-4">
  <div class="col-md-12">
    <div class="tile border-0 shadow-sm" style="border-radius: 15px;">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.reports.general') }}" id="reportForm" class="row align-items-end">
          <div class="col-md-3">
            <div class="form-group mb-0">
              <label for="period" class="font-weight-bold">Analysis Period:</label>
              <select name="period" id="period" class="form-control rounded-pill border-primary" onchange="toggleDateInputs()">
                <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Range</option>
              </select>
            </div>
          </div>
          <div class="col-md-3" id="startDateContainer" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
            <div class="form-group mb-0">
              <label for="start_date" class="font-weight-bold">From:</label>
              <input type="date" name="start_date" id="start_date" class="form-control rounded-pill" value="{{ $startDate }}">
            </div>
          </div>
          <div class="col-md-3" id="endDateContainer" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
            <div class="form-group mb-0">
              <label for="end_date" class="font-weight-bold">To:</label>
              <input type="date" name="end_date" id="end_date" class="form-control rounded-pill" value="{{ $endDate }}">
            </div>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary btn-block rounded-pill shadow-sm">
              <i class="fa fa-refresh mr-1"></i> Recalculate Data
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Key Performance Indicators (KPIs) -->
<div class="row mb-4">
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #4e54c8 0%, #8f94fb 100%);">
      <div class="card-body p-3 text-white">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="text-white-50 text-uppercase mb-1 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Total Revenue</h6>
                <h3 class="mb-0 font-weight-bold">{{ number_format($totalRevenueTZS, 0) }}</h3>
                <small class="text-white-50">TSH (Full Period)</small>
            </div>
            <div class="rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.2);">
                <i class="fa fa-money fa-lg"></i>
            </div>
        </div>
        <div class="mt-3 pt-2 border-top border-white-50">
            <small>Avg: {{ number_format($totalRevenueTZS / max(1, count($trendLabels)), 0) }} / unit</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #11998e 0%, #38ef7d 100%);">
      <div class="card-body p-3 text-white">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="text-white-50 text-uppercase mb-1 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Reservations</h6>
                <h3 class="mb-0 font-weight-bold">{{ $totalBookings }}</h3>
                <small class="text-white-50">Confirmed Bookings</small>
            </div>
            <div class="rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.2);">
                <i class="fa fa-calendar-check-o fa-lg"></i>
            </div>
        </div>
        <div class="mt-3 pt-2 border-top border-white-50">
            <small>Conversion: Stable Performance</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #f85032 0%, #f16232 100%);">
      <div class="card-body p-3 text-white">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="text-white-50 text-uppercase mb-1 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Occupancy Avg</h6>
                <h3 class="mb-0 font-weight-bold">{{ $occupancyRate }}%</h3>
                <small class="text-white-50">{{ $occupiedRooms }} / {{ $totalRooms }} Rooms</small>
            </div>
            <div class="rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.2);">
                <i class="fa fa-bed fa-lg"></i>
            </div>
        </div>
        <div class="mt-3 pt-2 border-top border-white-50">
            <div class="progress" style="height: 6px; background: rgba(255,255,255,0.2);">
                <div class="progress-bar bg-white" style="width: {{ $occupancyRate }}%;"></div>
            </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #1d2b64 0%, #f8cdda 100%);">
      <div class="card-body p-3 text-white">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="text-white-50 text-uppercase mb-1 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Booking Value</h6>
                <h3 class="mb-0 font-weight-bold">{{ $totalBookings > 0 ? number_format($roomRevenueTZS / $totalBookings, 0) : 0 }}</h3>
                <small class="text-white-50">Avg TZS per Booking</small>
            </div>
            <div class="rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.2);">
                <i class="fa fa-calculator fa-lg"></i>
            </div>
        </div>
        <div class="mt-3 pt-2 border-top border-white-50">
            <small>Revenue Efficiency Score: High</small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Charts Infrastructure -->
<div class="row mb-4">
    <div class="col-md-8 mb-4">
        <div class="tile h-100 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="tile-title mb-0 font-weight-bold text-dark"><i class="fa fa-area-chart text-primary mr-2"></i> Revenue Performance Trend</h4>
                <div class="text-muted small">Grouped by {{ $period == 'year' ? 'Month' : 'Day' }}</div>
            </div>
            <div class="tile-body">
                <canvas id="revenueTrendChart" style="height: 350px !important;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="tile h-100 border-0 shadow-sm" style="border-radius: 15px;">
            <h4 class="tile-title font-weight-bold text-dark mb-4"><i class="fa fa-pie-chart text-danger mr-2"></i> Revenue Sources</h4>
            <div class="tile-body">
                <canvas id="sourceChart" style="max-height: 250px;"></canvas>
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted font-weight-bold" style="font-size: 0.8rem;"><i class="fa fa-circle mr-2" style="color: #4e54c8;"></i> ROOM BOOKINGS</span>
                        <span class="font-weight-bold">{{ number_format($roomRevenueTZS, 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted font-weight-bold" style="font-size: 0.8rem;"><i class="fa fa-circle mr-2" style="color: #11998e;"></i> F&B SERVICES</span>
                        <span class="font-weight-bold">{{ number_format($serviceRevenueTZS, 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted font-weight-bold" style="font-size: 0.8rem;"><i class="fa fa-circle mr-2" style="color: #f85032;"></i> DAY SERVICES</span>
                        <span class="font-weight-bold">{{ number_format($dayServiceRevenueTZS, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="tile border-0 shadow-sm" style="border-radius: 15px;">
            <h4 class="tile-title font-weight-bold text-dark mb-4"><i class="fa fa-bar-chart text-warning mr-2"></i> Reservation Volume Analytics</h4>
            <div class="tile-body">
                <canvas id="volumeChart" style="height: 200px !important;"></canvas>
            </div>
        </div>
    </div>
</div>

</div>{{-- end d-print-none --}}

<!-- OFFICIAL PRINTABLE VERSION (Hidden on Screen) -->
<div class="d-none d-print-block">
    <div class="receipt-report-container">
        <!-- Header -->
        <div class="receipt-report-header">
            <div class="logo-container">
                <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="PrimeLand Logo">
            </div>
            <h1>PRIMELAND HOTEL</h1>
            <div style="line-height: 1.6;">
                <p><strong>Location:</strong> Sokoine Road - Moshi, Kilimanjaro - Tanzania</p>
                <p><strong>Mobile/WhatsApp:</strong> 0677-155-156 / +255 677-155-157</p>
                <p><strong>Email:</strong> info@primelandhotel.co.tz / infoprimelandhotel@gmail.com</p>
            </div>
            <p style="margin-top: 20px; font-size: 22px; font-weight: bold; color: #e07632; text-decoration: underline;">GENERAL REVENUE PERFORMANCE REPORT</p>
        </div>

        <div class="receipt-report-number">
            <strong>Report #:</strong> GEN-RPT-{{ date('Ymd') }}-{{ strtoupper(substr(md5('gen'.$period.$startDate.$endDate), 0, 6)) }}
        </div>

        <div class="receipt-report-title">
            PERIOD:
            @if($period == 'today') Today — {{ \Carbon\Carbon::parse($startDate)->format('l, F d, Y') }}
            @elseif($period == 'week') This Week — {{ \Carbon\Carbon::parse($startDate)->format('d M') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            @elseif($period == 'month') This Month — {{ \Carbon\Carbon::parse($startDate)->format('F Y') }}
            @elseif($period == 'year') This Year — {{ \Carbon\Carbon::parse($startDate)->format('Y') }}
            @else {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            @endif
        </div>

        <div class="receipt-two-column">
            <div class="receipt-column">
                <div class="receipt-info-section">
                    <h3>1. Executive Summary</h3>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Report Period:</span>
                        <span class="receipt-info-value">{{ ucfirst($period) }}</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Total Reservations:</span>
                        <span class="receipt-info-value">{{ $totalBookings }} Bookings</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Occupancy Rate:</span>
                        <span class="receipt-info-value">{{ $occupancyRate }}%</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Rooms Occupied:</span>
                        <span class="receipt-info-value">{{ $occupiedRooms }} / {{ $totalRooms }} Rooms</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Avg Revenue / Booking:</span>
                        <span class="receipt-info-value">{{ $totalBookings > 0 ? number_format($roomRevenueTZS / $totalBookings, 0) : 0 }} TZS</span>
                    </div>
                </div>
            </div>
            <div class="receipt-column">
                <div class="receipt-info-section">
                    <h3>2. Revenue Overview</h3>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Room Bookings:</span>
                        <span class="receipt-info-value">{{ number_format($roomRevenueTZS) }} TZS</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">F&B Services:</span>
                        <span class="receipt-info-value">{{ number_format($serviceRevenueTZS) }} TZS</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Day Services:</span>
                        <span class="receipt-info-value">{{ number_format($dayServiceRevenueTZS) }} TZS</span>
                    </div>
                    <div class="receipt-info-row mt-2 pt-2" style="border-top: 2px solid #e07632;">
                        <span class="receipt-info-label"><strong>GRAND TOTAL:</strong></span>
                        <span class="receipt-info-value"><strong class="amount-highlight">{{ number_format($totalRevenueTZS) }} TZS</strong></span>
                    </div>
                    <div style="text-align: right; margin-top: 5px;">
                        <small class="text-muted">≈ ${{ number_format($totalRevenueUSD, 2) }} USD</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="receipt-info-section">
            <h3>3. Revenue Source Breakdown</h3>
            <table class="receipt-details-table">
                <thead>
                    <tr>
                        <th>Revenue Stream</th>
                        <th class="text-right">Amount (TZS)</th>
                        <th class="text-right">% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Room Bookings & Accommodation</td>
                        <td class="text-right">{{ number_format($roomRevenueTZS) }}</td>
                        <td class="text-right">{{ $totalRevenueTZS > 0 ? round(($roomRevenueTZS / $totalRevenueTZS) * 100, 1) : 0 }}%</td>
                    </tr>
                    <tr>
                        <td>Food, Drinks & Room Service (F&B)</td>
                        <td class="text-right">{{ number_format($serviceRevenueTZS) }}</td>
                        <td class="text-right">{{ $totalRevenueTZS > 0 ? round(($serviceRevenueTZS / $totalRevenueTZS) * 100, 1) : 0 }}%</td>
                    </tr>
                    <tr>
                        <td>Pool, Spa & Day Activities</td>
                        <td class="text-right">{{ number_format($dayServiceRevenueTZS) }}</td>
                        <td class="text-right">{{ $totalRevenueTZS > 0 ? round(($dayServiceRevenueTZS / $totalRevenueTZS) * 100, 1) : 0 }}%</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td><strong>TOTAL REVENUE</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalRevenueTZS) }}</strong></td>
                        <td class="text-right"><strong>100%</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="receipt-info-section">
            <div class="recommendation-box info">
                <h4><i class="fa fa-info-circle"></i> Auditor's Note</h4>
                <p>This report consolidates data from room bookings, POS service requests, and day service entries for the {{ ucfirst($period) }} period.
                All figures reflect payments and completions recorded in the management system and are subject to final audit verification.</p>
            </div>
        </div>

        <!-- Signature Grid -->
        <div style="display: flex; justify-content: space-between; margin-top: 60px; padding: 0 40px;">
            <div style="width: 200px; text-align: center;">
                <div style="border-top: 1px solid #333; margin-bottom: 5px;"></div>
                <strong style="font-size: 11px;">Accountant / Controller</strong>
                <p style="font-size: 10px; margin-top: 3px; color: #666;">(Signature & Date)</p>
            </div>
            <div style="width: 200px; text-align: center;">
                <div style="border-top: 1px solid #333; margin-bottom: 5px;"></div>
                <strong style="font-size: 11px;">Hotel Manager</strong>
                <p style="font-size: 10px; margin-top: 3px; color: #666;">(Signature & Date)</p>
            </div>
        </div>

        <div class="receipt-footer">
            <p><strong>PrimeLand Hotel Management System</strong></p>
            <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }} by {{ auth('staff')->user()->name }}</p>
            <p class="powered-by">Powered By EmCa Technologies</p>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const trendLabels = {!! json_encode($trendLabels) !!};
    const trendRevenue = {!! json_encode($trendRevenue) !!};
    const trendBookings = {!! json_encode($trendBookings) !!};

    // 1. REVENUE TREND (Smoothed Area Chart)
    const revCtx = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(revCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Total Revenue (TSH)',
                data: trendRevenue,
                fill: true,
                backgroundColor: 'rgba(78, 84, 200, 0.1)',
                borderColor: '#4e54c8',
                borderWidth: 3,
                pointBackgroundColor: '#4e54c8',
                pointRadius: 4,
                tension: 0.4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    padding: 12,
                    callbacks: {
                        label: function(ctx) {
                            return 'Revenue: ' + new Intl.NumberFormat().format(ctx.raw) + ' TZS';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5] },
                    ticks: {
                        callback: function(v) { return (v/1000).toFixed(0) + 'k'; }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. REVENUE SOURCES (Luxury Donut)
    const sourceCtx = document.getElementById('sourceChart').getContext('2d');
    new Chart(sourceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Rooms', 'F&B', 'Activity'],
            datasets: [{
                data: [{{ $roomRevenueTZS }}, {{ $serviceRevenueTZS }}, {{ $dayServiceRevenueTZS }}],
                backgroundColor: ['#4e54c8', '#11998e', '#f85032'],
                borderWidth: 0,
                hoverOffset: 20
            }]
        },
        options: {
            cutout: '75%',
            plugins: { 
                legend: { display: false },
                tooltip: { padding: 12 }
            }
        }
    });

    // 3. VOLUME CHART (Gradient Bar)
    const volCtx = document.getElementById('volumeChart').getContext('2d');
    const grad = volCtx.createLinearGradient(0, 0, 0, 400);
    grad.addColorStop(0, '#f1c40f');
    grad.addColorStop(1, '#e67e22');

    new Chart(volCtx, {
        type: 'bar',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Bookings',
                data: trendBookings,
                backgroundColor: grad,
                borderRadius: 5,
                barThickness: 20
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
});

function toggleDateInputs() {
  const period = document.getElementById('period').value;
  $('#startDateContainer, #endDateContainer').toggle(period === 'custom');
}
</script>
<style>
    .tile { transition: all 0.3s ease; }
    .card:hover { transform: translateY(-5px); transition: transform 0.3s; }
</style>
@endsection

@endsection
