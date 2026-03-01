@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-line-chart"></i> Staff Performance Analytics</h1>
        <p>Expert insights and detailed productivity evaluation for hotel management</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item active">Performance Insights</li>
    </ul>
</div>

<!-- Main Navigation Tabs -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="tile p-0 mb-0" style="background: transparent; box-shadow: none;">
            <ul class="nav nav-tabs nav-fill user-tabs" id="performanceTabs" role="tablist" style="border: none;">
                <li class="nav-item">
                    <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab" style="border-radius: 10px 10px 0 0; font-weight: 600; padding: 15px;">
                        <i class="fa fa-dashboard"></i> Insights Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="directory-tab" data-toggle="tab" href="#directory" role="tab" style="border-radius: 10px 10px 0 0; font-weight: 600; padding: 15px;">
                        <i class="fa fa-users"></i> Staff Directory & Metrics
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="tab-content" id="performanceTabsContent">
    <!-- TAB 1: OVERVIEW DASHBOARD -->
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
        <!-- Summary Stats Section -->
        <div class="row">
            <div class="col-md-3">
                <div class="tile bg-primary text-white p-3 text-center mb-4 shadow-sm border-0 stat-card">
                    <div class="display-4 mb-2"><i class="fa fa-bolt"></i></div>
                    <h5 class="mb-1">Total Actions</h5>
                    <h3 class="font-weight-bold mb-0">{{ number_format($summary['total_actions']) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="tile bg-info text-white p-3 text-center mb-4 shadow-sm border-0 stat-card">
                    <div class="display-4 mb-2"><i class="fa fa-money"></i></div>
                    <h5 class="mb-1">Revenue Managed</h5>
                    <h3 class="font-weight-bold mb-0">TZS {{ number_format($summary['total_revenue']) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="tile bg-success text-white p-3 text-center mb-4 shadow-sm border-0 stat-card">
                    <div class="display-4 mb-2"><i class="fa fa-check-square-o"></i></div>
                    <h5 class="mb-1">Tasks Completed</h5>
                    <h3 class="font-weight-bold mb-0">{{ number_format($summary['tasks_completed']) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="tile bg-warning text-white p-3 text-center mb-4 shadow-sm border-0 stat-card">
                    <div class="display-4 mb-2"><i class="fa fa-user-circle-o"></i></div>
                    <h5 class="mb-1">Active Team</h5>
                    <h3 class="font-weight-bold mb-0">{{ $summary['active_staff'] }}</h3>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Productivity Chart -->
            <div class="col-md-8">
                <div class="tile shadow-sm border-0" style="border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <h3 class="tile-title mb-0"><i class="fa fa-area-chart text-primary"></i> Team Productivity Trend</h3>
                        <div class="btn-group shadow-sm">
                            <a href="{{ route('owner.performance.index', ['period' => 'day']) }}" class="btn btn-{{ $period == 'day' ? 'primary' : 'light' }} btn-sm px-3">Today</a>
                            <a href="{{ route('owner.performance.index', ['period' => 'week']) }}" class="btn btn-{{ $period == 'week' ? 'primary' : 'light' }} btn-sm px-3">Week</a>
                            <a href="{{ route('owner.performance.index', ['period' => 'month']) }}" class="btn btn-{{ $period == 'month' ? 'primary' : 'light' }} btn-sm px-3">Month</a>
                        </div>
                    </div>
                    <div class="embed-responsive embed-responsive-16by9">
                        <canvas class="embed-responsive-item" id="productivityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Spotlight & Leaders Sidebar -->
            <div class="col-md-4">
                <!-- Staff of the Week -->
                <div class="tile text-center p-4 text-white mb-4 shadow spotlight-hero" style="border-radius: 15px; position: relative; overflow: hidden;">
                    <div class="spotlight-overlay"></div>
                    <div style="position: relative; z-index: 1;">
                        <div class="badge badge-light text-primary mb-3 px-3 py-1 font-weight-bold" style="text-transform: uppercase; letter-spacing: 2px;">
                            <i class="fa fa-trophy mr-1"></i> Staff of the Week
                        </div>
                        <div class="mb-3">
                            @if($staffOfTheWeek && $staffOfTheWeek->profile_photo)
                                <img src="{{ asset('storage/' . $staffOfTheWeek->profile_photo) }}" class="rounded-circle border border-white" width="110" height="110" style="object-fit: cover; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                            @else
                                <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 40px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                    {{ $staffOfTheWeek ? substr($staffOfTheWeek->name, 0, 1) : '?' }}
                                </div>
                            @endif
                        </div>
                        <h4 class="font-weight-bold mb-1">{{ $staffOfTheWeek->name ?? 'Selection Pending' }}</h4>
                        <p class="mb-0 text-white-50">{{ $staffOfTheWeek->position ?? ucfirst($staffOfTheWeek->role ?? 'N/A') }}</p>
                        <div class="mt-3 p-2 bg-white-20 rounded" style="background: rgba(255,255,255,0.1);">
                            <small class="d-block uppercase mb-1" style="font-size: 10px; opacity: 0.8; letter-spacing: 1px;">Recent Impact</small>
                            <span class="h5 mb-0">{{ $staffOfTheWeek->week_stats['metric_value'] ?? 0 }} {{ $staffOfTheWeek->week_stats['metric_label'] ?? 'Actions' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Leaderboard Mini-Widget -->
                <div class="tile p-0 shadow-sm border-0 overflow-hidden" style="border-radius: 12px;">
                    <div class="bg-dark text-white p-3">
                        <h5 class="mb-0"><i class="fa fa-star text-warning"></i> Top Performers</h5>
                    </div>
                    <div class="p-3">
                        <ul class="list-group list-group-flush">
                            @foreach($topPerformers->take(3) as $top)
                            <li class="list-group-item d-flex align-items-center px-0 bg-transparent border-light">
                                <span class="badge badge-pill badge-primary mr-3">{{ $loop->iteration }}</span>
                                <div style="flex-grow:1">
                                    <h6 class="mb-0 text-dark">{{ $top->name }}</h6>
                                    <small class="text-muted">{{ $top->performance['metric_value'] }} {{ $top->performance['metric_label'] }}</small>
                                </div>
                                @if($loop->first) <i class="fa fa-crown text-warning"></i> @endif
                            </li>
                            @endforeach
                        </ul>
                        <div class="mt-3 text-center">
                            <a href="#directory" class="btn btn-sm btn-link font-weight-bold text-primary" onclick="$('#directory-tab').tab('show');">See Full Ranking <i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: STAFF DIRECTORY TABLE -->
    <div class="tab-pane fade" id="directory" role="tabpanel">
        <div class="row">
            <div class="col-md-12">
                <div class="tile shadow-sm border-0" style="border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="tile-title mb-0"><i class="fa fa-list-ul text-teal"></i> Performance Matrix ({{ $periodLabel }})</h3>
                        <div class="text-muted small">
                            <i class="fa fa-info-circle"></i> Click on a staff member to see their detailed audit logs.
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="staffTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Employee Details</th>
                                    <th>Department</th>
                                    <th>Impact Metric</th>
                                    <th>Total Actions</th>
                                    <th>Revenue Handled</th>
                                    <th>Activity Score</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffPerformance as $staff)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($staff->profile_photo)
                                                <img src="{{ asset('storage/' . $staff->profile_photo) }}" class="rounded-circle mr-3" width="40" height="40" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-teal text-white d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: #009688;">
                                                    {{ substr($staff->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-weight-bold">{{ $staff->name }}</div>
                                                <small class="text-muted">{{ $staff->position ?? '--' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-light border text-uppercase">{{ str_replace('_', ' ', $staff->role) }}</span></td>
                                    <td>
                                        <div class="font-weight-bold text-teal">{{ $staff->performance['metric_value'] }}</div>
                                        <small class="text-muted">{{ $staff->performance['metric_label'] }}</small>
                                    </td>
                                    <td>{{ $staff->performance['total_actions'] }}</td>
                                    <td>
                                        @if($staff->performance['revenue_handled'] > 0)
                                            <span class="text-success font-weight-bold">TZS {{ number_format($staff->performance['revenue_handled']) }}</span>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $score = min($staff->performance['total_actions'] * 2, 100);
                                            $c = $score > 70 ? 'success' : ($score > 40 ? 'info' : ($score > 10 ? 'warning' : 'danger'));
                                        @endphp
                                        <div class="progress" style="height: 10px; width: 100px; border-radius: 5px;">
                                            <div class="progress-bar bg-{{ $c }}" role="progressbar" style="width: {{ $score }}%"></div>
                                        </div>
                                        <small class="text-{{ $c }} font-weight-bold">{{ $score }}%</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('owner.performance.show', $staff->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                            <i class="fa fa-search"></i> Details
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Styling for organized look */
.nav-tabs .nav-link {
    color: #6c757d;
    background: #f8f9fa;
    border: none;
    margin-right: 5px;
    transition: all 0.3s;
}
.nav-tabs .nav-link.active {
    background: #fff;
    color: #009688;
    box-shadow: 0 -3px 0 #009688 inset;
}
.stat-card {
    border-radius: 12px;
    transition: all 0.3s;
}
.stat-card:hover {
    transform: scale(1.05);
}
.spotlight-hero {
    background: linear-gradient(135deg, #009688 0%, #00d2ff 100%) !important;
    border: none;
}
.spotlight-overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: url('{{ asset('dashboard_assets/img/pattern.png') }}');
    opacity: 0.1;
}
.bg-white-20 {
    background: rgba(255,255,255,0.2) !important;
}
.text-teal {
    color: #009688;
}
.bg-teal {
    background: #009688;
}
.user-tabs .nav-link i {
    margin-right: 8px;
}
#staffTable thead th {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    border: none;
}
#staffTable tr {
    transition: all 0.2s;
}
#staffTable tr:hover {
    background: rgba(0, 150, 136, 0.05) !important;
}
</style>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('dashboard_assets/js/plugins/chart.js') }}"></script>
<script type="text/javascript" src="{{ asset('dashboard_assets/js/plugins/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('dashboard_assets/js/plugins/dataTables.bootstrap.min.js') }}"></script>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize DataTable
        $('#staffTable').DataTable({
            "order": [[ 5, "desc" ]], // Sort by score by default
            "pageLength": 10
        });

        // Initialize Chart
        var pData = {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: "System Actions",
                    fillColor: "rgba(0, 150, 136, 0.15)",
                    strokeColor: "#009688",
                    pointColor: "#009688",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "#009688",
                    data: {!! json_encode($chartData['actions']) !!}
                },
                {
                    label: "Revenue Contribution (k)",
                    fillColor: "rgba(33, 150, 243, 0.15)",
                    strokeColor: "#2196f3",
                    pointColor: "#2196f3",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "#2196f3",
                    data: {!! json_encode($chartData['revenue']) !!}
                }
            ]
        };

        var ctx = $("#productivityChart").get(0).getContext("2d");
        var myLineChart = new Chart(ctx).Line(pData, {
            responsive: true,
            maintainAspectRatio: false,
            bezierCurve: true,
            datasetFill: true
        });

        // Ensure chart resizes correctly on tab change
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if (e.target.id === 'overview-tab') {
                // Redraw chart if needed
            }
        });
    });
</script>
@endsection
