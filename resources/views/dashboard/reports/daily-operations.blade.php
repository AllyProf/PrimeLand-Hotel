@extends('dashboard.layouts.reports')

@section('reports-content')
@include('dashboard.reports.partials.receipt-styles')

<div class="app-title d-flex justify-content-between align-items-center d-print-none">
  <div>
    <h1><i class="fa fa-calendar-day"></i> Daily Operations Report</h1>
    <p>Operational snapshot for {{ $selectedDate->format('d M, Y') }}</p>
  </div>
  <div>
    <button onclick="window.print()" class="btn btn-secondary shadow-sm">
        <i class="fa fa-print"></i> Print Official Report
    </button>
  </div>
</div>

<!-- DASHBOARD VIEW (Screen Only) -->
<div class="d-print-none">
    <!-- Date Selector -->
    <div class="row mb-4">
      <div class="col-md-12">
        <div class="tile border-0 shadow-sm" style="border-radius: 15px;">
          <div class="tile-body">
            <form method="GET" action="{{ route('admin.reports.daily-operations') }}" class="row align-items-end">
              <div class="col-md-4">
                <div class="form-group mb-0">
                  <label for="date" class="font-weight-bold"><i class="fa fa-search mr-1"></i> Analyze Another Date:</label>
                  <input type="date" name="date" id="date" class="form-control rounded-pill border-primary" value="{{ $selectedDate->format('Y-m-d') }}">
                </div>
              </div>
              <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-block rounded-pill shadow-sm">
                  <i class="fa fa-refresh mr-1"></i> Update View
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Today's Premium Summary Cards -->
    <div class="row mb-4">
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #4e54c8 0%, #8f94fb 100%);">
          <div class="card-body p-3 text-white">
            <div class="d-flex align-items-center mb-2">
              <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2);">
                <i class="fa fa-calendar-plus-o fa-lg"></i>
              </div>
              <div>
                <h6 class="text-white-50 text-uppercase mb-0 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">New Bookings</h6>
                <h3 class="mb-0 font-weight-bold">{{ $todayNewBookings }}</h3>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">Confirmed today: <strong>{{ $todayConfirmedBookings }}</strong></div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #11998e 0%, #38ef7d 100%);">
          <div class="card-body p-3 text-white">
            <div class="d-flex align-items-center mb-2">
              <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2);">
                <i class="fa fa-sign-in fa-lg"></i>
              </div>
              <div>
                <h6 class="text-white-50 text-uppercase mb-0 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Check-ins</h6>
                <h3 class="mb-0 font-weight-bold">{{ $todayCheckIns }}</h3>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">Guests checked in today</div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #f85032 0%, #f16232 100%);">
          <div class="card-body p-3 text-white">
            <div class="d-flex align-items-center mb-2">
              <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2);">
                <i class="fa fa-bed fa-lg"></i>
              </div>
              <div>
                <h6 class="text-white-50 text-uppercase mb-0 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Occupancy</h6>
                <h3 class="mb-0 font-weight-bold">{{ $occupancyRate }}%</h3>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">Rooms occupied: <strong>{{ $occupiedRooms }} / {{ $totalRooms }}</strong></div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100" style="background: linear-gradient(45deg, #1d2b64 0%, #f8cdda 100%);">
          <div class="card-body p-3 text-white">
            <div class="d-flex align-items-center mb-2">
              <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2);">
                <i class="fa fa-money fa-lg"></i>
              </div>
              <div>
                <h6 class="text-white-50 text-uppercase mb-0 font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Total Daily Income</h6>
                <h4 class="mb-0 font-weight-bold" style="font-size: 1.1rem;">{{ number_format($grandTotalRevenueTZS, 0) }} TZS</h4>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">≈ ${{ number_format($grandTotalRevenueTZS / $exchangeRate, 2) }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <!-- Revenue Analytics -->
      <div class="col-md-7">
        <div class="tile h-100 border-0 shadow-sm" style="border-radius: 15px;">
          <h4 class="tile-title border-bottom pb-2 font-weight-bold text-dark">
            <i class="fa fa-pie-chart text-primary mr-2"></i> Revenue Breakdown
          </h4>
          <div class="tile-body">
            <div class="row">
              <div class="col-md-6">
                <canvas id="revenueChart" style="max-height: 250px;"></canvas>
              </div>
              <div class="col-md-6">
                <div class="mt-4">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted"><i class="fa fa-circle mr-2" style="color: #4e54c8;"></i> Bookings:</span>
                    <span class="font-weight-bold">{{ number_format($todayRevenueTZS, 0) }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted"><i class="fa fa-circle mr-2" style="color: #e67e22;"></i> Bar:</span>
                    <span class="font-weight-bold">{{ number_format($barRevenueTZS, 0) }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted"><i class="fa fa-circle mr-2" style="color: #ed213a;"></i> Kitchen:</span>
                    <span class="font-weight-bold">{{ number_format($kitchenRevenueTZS, 0) }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted"><i class="fa fa-circle mr-2" style="color: #2b5876;"></i> Day Services:</span>
                    <span class="font-weight-bold">{{ number_format($todayDayServiceRevenueTZS, 0) }}</span>
                  </div>
                  <div class="mt-3 pt-3 border-top d-flex justify-content-between">
                    <strong class="text-dark">Grand Total:</strong>
                    <strong class="text-primary h5 mb-0">{{ number_format($grandTotalRevenueTZS, 0) }} TZS</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Operational Health -->
      <div class="col-md-5">
        <div class="tile h-100 border-0 shadow-sm" style="border-radius: 15px;">
          <h4 class="tile-title border-bottom pb-2 font-weight-bold text-dark">
            <i class="fa fa-heartbeat text-danger mr-2"></i> Operational Health
          </h4>
          <div class="tile-body">
            <div class="mb-4">
              <label class="d-flex justify-content-between small font-weight-bold text-muted mb-1">
                <span>OCCUPANCY RATE</span>
                <span>{{ $occupancyRate }}%</span>
              </label>
              <div class="progress" style="height: 10px; border-radius: 10px;">
                <div class="progress-bar transition-all" role="progressbar" 
                     style="width: {{ $occupancyRate }}%; background: linear-gradient(to right, #11998e, #38ef7d); border-radius: 10px;"></div>
              </div>
            </div>

            <div class="mb-4">
              @php 
                $issueResPercent = $todayIssuesCount > 0 ? ($todayIssuesResolved / $todayIssuesCount) * 100 : 100;
              @endphp
              <label class="d-flex justify-content-between small font-weight-bold text-muted mb-1">
                <span>ISSUE RESOLUTION RATE</span>
                <span>{{ round($issueResPercent) }}%</span>
              </label>
              <div class="progress" style="height: 10px; border-radius: 10px;">
                <div class="progress-bar transition-all" role="progressbar" 
                     style="width: {{ $issueResPercent }}%; background: linear-gradient(to right, #f85032, #f16232); border-radius: 10px;"></div>
              </div>
            </div>

            <div class="list-group list-group-flush mt-3">
              <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                <span class="text-muted"><i class="fa fa-cutlery mr-2"></i> Served Orders:</span>
                <span class="badge badge-soft-primary px-3 rounded-pill">{{ $todayServiceRequestsCompleted }} / {{ $todayServiceRequestsCount }}</span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                <span class="text-muted"><i class="fa fa-umbrella mr-2"></i> Day Services:</span>
                <span class="badge badge-soft-info px-3 rounded-pill">{{ $todayDayServicesPaid }} / {{ $todayDayServicesCount }}</span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                <span class="text-muted"><i class="fa fa-sign-out mr-2"></i> Checked-out:</span>
                <span class="badge badge-soft-secondary px-3 rounded-pill">{{ $todayCheckOuts }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
        <!-- Forecast Section -->
        <div class="col-md-6 mb-4">
            <div class="tile h-100 border-0 shadow-sm" style="border-radius: 15px; background: rgba(255, 253, 230, 0.4);">
                <h4 class="tile-title border-bottom pb-2 font-weight-bold text-dark">
                    <i class="fa fa-arrow-right text-success mr-2"></i> Tomorrow's Forecast
                </h4>
                <div class="tile-body">
                    <div class="row text-center mb-3">
                        <div class="col-6 border-right">
                            <small class="text-muted d-block uppercase font-weight-bold">CHECK-INS</small>
                            <h2 class="font-weight-bold text-primary mb-0">{{ $tomorrowCheckIns }}</h2>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block uppercase font-weight-bold">CHECK-OUTS</small>
                            <h2 class="font-weight-bold text-danger mb-0">{{ $tomorrowCheckOuts }}</h2>
                        </div>
                    </div>
                    <div class="bg-white p-3 rounded-lg border text-center shadow-sm">
                        <small class="text-muted uppercase font-weight-bold d-block">EXPECTED BOOKING REVENUE</small>
                        <h3 class="font-weight-bold text-dark mt-1">
                            {{ number_format($tomorrowExpectedRevenueTZS, 0) }} <small class="text-muted">TSH</small>
                        </h3>
                        <span class="badge badge-success px-4 py-2 rounded-pill font-weight-bold mt-2">
                            ≈ ${{ number_format($tomorrowExpectedRevenueTZS / $exchangeRate, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attention Needed -->
        <div class="col-md-6 mb-4">
            <div class="tile h-100 border-0 shadow-sm" style="border-radius: 15px;">
                <h4 class="tile-title border-bottom pb-2 font-weight-bold text-dark">
                    <i class="fa fa-exclamation-circle text-warning mr-2"></i> Active Concerns
                </h4>
                <div class="tile-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 text-center border rounded-lg hover-shadow-sm transition-all h-100">
                                <i class="fa fa-hourglass-half fa-2x text-warning mb-2"></i>
                                <h5 class="font-weight-bold">{{ $pendingServiceRequests }}</h5>
                                <small class="text-muted">Pending Orders</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 text-center border rounded-lg hover-shadow-sm transition-all h-100">
                                <i class="fa fa-warning fa-2x text-danger mb-2"></i>
                                <h5 class="font-weight-bold">{{ $pendingIssues }}</h5>
                                <small class="text-muted">Open Issues</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 text-center border rounded-lg hover-shadow-sm transition-all h-100">
                                <i class="fa fa-money fa-2x text-info mb-2"></i>
                                <h5 class="font-weight-bold">{{ $pendingPayments }}</h5>
                                <small class="text-muted">Dues to Collect</small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-light border mt-2 small text-muted italic">
                        <i class="fa fa-info-circle mr-1"></i> These figures represent the current all-time pending backlog that needs resolution.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                <p><strong>Mobile/WhatsApp:</strong> 0677-155-156</p>
                <p><strong>Email:</strong> info@primelandhotel.com / infoprimelandhotel@gmail.com</p>
            </div>
            <p style="margin-top: 20px; font-size: 24px; font-weight: bold; color: #e07632; text-decoration: underline;">DAILY OPERATIONS REPORT</p>
        </div>

        <div class="receipt-report-number">
            <strong>Report #:</strong> OPS-D-{{ $selectedDate->format('Ymd') }}-{{ strtoupper(substr(md5('ops'.$selectedDate->timestamp), 0, 6)) }}
        </div>

        <div class="receipt-report-title">
            OPERATIONAL SNAPSHOT - {{ $selectedDate->format('l, F d, Y') }}
        </div>

        <div class="receipt-two-column">
            <div class="receipt-column">
                <div class="receipt-info-section">
                    <h3>1. Performance Overview</h3>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Check-ins:</span>
                        <span class="receipt-info-value">{{ $todayCheckIns }} Guests</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Check-outs:</span>
                        <span class="receipt-info-value">{{ $todayCheckOuts }} Guests</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Occupancy Rate:</span>
                        <span class="receipt-info-value">{{ $occupancyRate }}% ({{ $occupiedRooms }}/{{ $totalRooms }} Rooms)</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">New Reservations:</span>
                        <span class="receipt-info-value">{{ $todayNewBookings }} ({{ $todayConfirmedBookings }} Confirmed)</span>
                    </div>
                </div>
            </div>
            <div class="receipt-column">
                <div class="receipt-info-section">
                    <h3>2. Financial Summary</h3>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Booking Revenue:</span>
                        <span class="receipt-info-value">{{ number_format($todayRevenueTZS) }} TZS</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Bar Sales:</span>
                        <span class="receipt-info-value">{{ number_format($barRevenueTZS) }} TZS</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Kitchen Sales:</span>
                        <span class="receipt-info-value">{{ number_format($kitchenRevenueTZS) }} TZS</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Day Services:</span>
                        <span class="receipt-info-value">{{ number_format($todayDayServiceRevenueTZS) }} TZS</span>
                    </div>
                    <div class="receipt-info-row mt-2 pt-2 border-top">
                        <span class="receipt-info-label"><strong>GRAND TOTAL:</strong></span>
                        <span class="receipt-info-value"><strong class="amount-highlight">{{ number_format($grandTotalRevenueTZS) }} TZS</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="receipt-info-section">
            <h3>3. Service & Quality Metrics</h3>
            <table class="receipt-details-table">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th class="text-center">Count</th>
                        <th class="text-center">Completed/Resolved</th>
                        <th class="text-right">Success Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Food & Beverage Orders</td>
                        <td class="text-center">{{ $todayServiceRequestsCount }}</td>
                        <td class="text-center">{{ $todayServiceRequestsCompleted }}</td>
                        <td class="text-right">{{ $todayServiceRequestsCount > 0 ? round(($todayServiceRequestsCompleted/$todayServiceRequestsCount)*100) : 100 }}%</td>
                    </tr>
                    <tr>
                        <td>Day Activities & Pool</td>
                        <td class="text-center">{{ $todayDayServicesCount }}</td>
                        <td class="text-center">{{ $todayDayServicesPaid }}</td>
                        <td class="text-right">{{ $todayDayServicesCount > 0 ? round(($todayDayServicesPaid/$todayDayServicesCount)*100) : 100 }}%</td>
                    </tr>
                    <tr>
                        <td>Maintenance & Guest Issues</td>
                        <td class="text-center">{{ $todayIssuesCount }}</td>
                        <td class="text-center">{{ $todayIssuesResolved }}</td>
                        <td class="text-right">{{ $issueResPercent }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="receipt-info-section">
            <h3>4. Forward Forecast ({{ $selectedDate->copy()->addDay()->format('d M, Y') }})</h3>
            <div class="receipt-two-column mb-0">
                <div class="receipt-column">
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Expected Arrivals:</span>
                        <span class="receipt-info-value">{{ $tomorrowCheckIns }} Guests</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Expected Departures:</span>
                        <span class="receipt-info-value">{{ $tomorrowCheckOuts }} Guests</span>
                    </div>
                </div>
                <div class="receipt-column">
                    <div class="receipt-info-row">
                        <span class="receipt-info-label">Projected Revenue:</span>
                        <span class="receipt-info-value"><strong>{{ number_format($tomorrowExpectedRevenueTZS) }} TZS</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature Grid -->
        <div style="display: flex; justify-content: space-between; margin-top: 50px; padding: 0 40px;">
            <div style="width: 200px; text-align: center;">
                <div style="border-top: 1px solid #333; margin-bottom: 5px;"></div>
                <strong style="font-size: 11px;">Reporting Officer</strong>
            </div>
            <div style="width: 200px; text-align: center;">
                <div style="border-top: 1px solid #333; margin-bottom: 5px;"></div>
                <strong style="font-size: 11px;">Hotel Manager</strong>
            </div>
        </div>

        <div class="receipt-footer">
            <p><strong>PrimeLand Hotel Management System</strong></p>
            <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }} by {{ auth('staff')->user()->name }}</p>
            <p class="powered-by">Powered By <a href="https://www.emca.tech" target="_blank" style="color: #940000; font-weight: bold; text-decoration: none;">EmCa Techonologies</a></p>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Revenue Breakdown Donut
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Bookings', 'Bar', 'Kitchen', 'Day Services'],
            datasets: [{
                data: [
                    {{ $todayRevenueTZS }},
                    {{ $barRevenueTZS }},
                    {{ $kitchenRevenueTZS }},
                    {{ $todayDayServiceRevenueTZS }}
                ],
                backgroundColor: ['#4e54c8', '#e67e22', '#ed213a', '#2b5876'],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) label += ': ';
                            label += new Intl.NumberFormat().format(context.raw) + ' TSH';
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
<style>
    .transition-all { transition: all 0.3s ease; }
    .hover-shadow-sm:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .badge-soft-primary { background: rgba(78, 84, 200, 0.1); color: #4e54c8; font-weight: 700; }
    .badge-soft-info { background: rgba(43, 88, 118, 0.1); color: #2b5876; font-weight: 700; }
    .badge-soft-secondary { background: rgba(108, 117, 125, 0.1); color: #6c757d; font-weight: 700; }
</style>
@endsection

@endsection
