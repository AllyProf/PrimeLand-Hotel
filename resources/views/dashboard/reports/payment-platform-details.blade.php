@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-list"></i> {{ ucfirst($platform) }} Earnings Detail</h1>
    <p>Detailed transaction list for {{ $platform }} payments</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.revenue-breakdown') }}">Revenue Breakdown</a></li>
    <li class="breadcrumb-item"><a href="#">{{ ucfirst($platform) }} Details</a></li>
  </ul>
</div>

<!-- Report Filter -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.reports.payment-platform-report') }}" id="reportForm">
          <input type="hidden" name="platform" value="{{ $platform }}">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="report_type"><strong>Report Type:</strong></label>
                <select name="report_type" id="report_type" class="form-control" onchange="toggleDateInputs()">
                  <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Daily</option>
                  <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Weekly</option>
                  <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Monthly</option>
                  <option value="yearly" {{ $reportType == 'yearly' ? 'selected' : '' }}>Yearly</option>
                  <option value="custom" {{ $reportType == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
              </div>
            </div>
            <div class="col-md-3" id="singleDateContainer">
              <div class="form-group">
                <label for="date"><strong>Select Date:</strong></label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $reportDate }}">
              </div>
            </div>
            <div class="col-md-3" id="startDateContainer" style="display: {{ $reportType == 'custom' ? 'block' : 'none' }};">
              <div class="form-group">
                <label for="start_date"><strong>Start Date:</strong></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
              </div>
            </div>
            <div class="col-md-3" id="endDateContainer" style="display: {{ $reportType == 'custom' ? 'block' : 'none' }};">
              <div class="form-group">
                <label for="end_date"><strong>End Date:</strong></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fa fa-refresh"></i> Update Report
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title">{{ ucfirst($platform) }} Transactions: {{ $dateRange['label'] }}</h3>
        <div class="btn-group">
            <a class="btn btn-primary" href="{{ route('admin.reports.revenue-breakdown', ['report_type' => $reportType, 'date' => $reportDate, 'start_date' => $startDate, 'end_date' => $endDate]) }}"><i class="fa fa-chevron-left"></i> Back to Breakdown</a>
        </div>
      </div>
      <div class="tile-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="p-3 bg-light border rounded">
                    <h6 class="text-muted text-uppercase mb-2">Total Transactions</h6>
                    <h3 class="mb-0">{{ count($transactions) }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light border rounded">
                    <h6 class="text-muted text-uppercase mb-2">Total Earnings (TZS)</h6>
                    <h3 class="text-success mb-0">{{ number_format($totalTZS, 0) }} TZS</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light border rounded">
                    <h6 class="text-muted text-uppercase mb-2">Total Earnings (USD)</h6>
                    <h3 class="text-info mb-0">${{ number_format($totalUSD, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="transactionsTable">
            <thead class="table-dark">
              <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Description</th>
                <th>Method Code</th>
                <th class="text-end">Amount (TZS)</th>
                <th class="text-end">Amount (USD)</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($transactions as $t)
              <tr>
                <td>{{ \Carbon\Carbon::parse($t['date'])->format('M d, Y H:i') }}</td>
                <td><span class="badge badge-info">{{ $t['source'] }}</span></td>
                <td>{{ $t['description'] }}</td>
                <td><code class="text-primary">{{ strtoupper($t['method']) }}</code></td>
                <td class="text-end fw-bold">{{ number_format($t['amount_tsh'], 0) }}</td>
                <td class="text-end text-muted">${{ number_format($t['amount_usd'], 2) }}</td>
                <td>
                    @if($t['link'] !== '#')
                        <a href="{{ $t['link'] }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fa fa-external-link"></i> View</a>
                    @else
                        -
                    @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="fa fa-info-circle fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No transactions found for {{ $platform }} in this period.</p>
                </td>
              </tr>
              @endforelse
            </tbody>
            @if(count($transactions) > 0)
            <tfoot class="table-light">
                <tr class="fw-bold">
                    <td colspan="4" class="text-end">GRAND TOTAL</td>
                    <td class="text-end text-success">{{ number_format($totalTZS, 0) }}</td>
                    <td class="text-end text-info">${{ number_format($totalUSD, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
function toggleDateInputs() {
  const reportType = document.getElementById('report_type').value;
  const singleDateContainer = document.getElementById('singleDateContainer');
  const startDateContainer = document.getElementById('startDateContainer');
  const endDateContainer = document.getElementById('endDateContainer');
  
  if (reportType === 'custom') {
    singleDateContainer.style.display = 'none';
    startDateContainer.style.display = 'block';
    endDateContainer.style.display = 'block';
  } else {
    singleDateContainer.style.display = 'block';
    startDateContainer.style.display = 'none';
    endDateContainer.style.display = 'none';
  }
}
</script>
@endsection
