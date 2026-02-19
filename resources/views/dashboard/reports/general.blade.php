@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title d-flex justify-content-between align-items-center">
  <div>
    <h1><i class="fa fa-dashboard text-primary"></i> General Performance Overview</h1>
    <p>Strategic analysis and revenue trends</p>
  </div>
  <div class="d-print-none">
    <button onclick="window.print()" class="btn btn-secondary shadow-sm">
        <i class="fa fa-print"></i> Print Executive Summary
    </button>
  </div>
</div>

<!-- Advanced Report Filter -->
<div class="row mb-4 d-print-none">
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
    @media print {
        .d-print-none { display: none !important; }
        .app-content { margin: 0 !important; padding: 0 !important; background: white !important; }
        .card { border: 1px solid #ddd !important; background: white !important; color: black !important; }
        .text-white-50 { color: #666 !important; }
        .tile { box-shadow: none !important; border: 1px solid #eee !important; }
    }
</style>
@endsection

@endsection
