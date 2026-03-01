@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> Owner Overview</h1>
        <p>High-level hotel operations and financial summary</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item active">Owner Dashboard</li>
    </ul>
</div>

<div class="row">
    <!-- Monthly Revenue -->
    <div class="col-md-6 col-lg-3">
        <div class="widget-small primary coloured-icon">
            <i class="icon fa fa-money fa-3x"></i>
            <div class="info">
                <h4>Monthly Revenue</h4>
                <p><b>TSh {{ number_format($monthlyRevenue, 0) }}</b></p>
            </div>
        </div>
    </div>
    
    <!-- Today's Revenue -->
    <div class="col-md-6 col-lg-3">
        <div class="widget-small info coloured-icon">
            <i class="icon fa fa-calendar-check-o fa-3x"></i>
            <div class="info">
                <h4>Today's Revenue</h4>
                <p><b>TSh {{ number_format($todayRevenue, 0) }}</b></p>
            </div>
        </div>
    </div>

    <!-- Monthly Expenses -->
    <div class="col-md-6 col-lg-3">
        <div class="widget-small danger coloured-icon">
            <i class="icon fa fa-shopping-cart fa-3x"></i>
            <div class="info">
                <h4>Monthly Expenses</h4>
                <p><b>TSh {{ number_format($monthlyShopping, 0) }}</b></p>
            </div>
        </div>
    </div>

    <!-- Occupancy -->
    <div class="col-md-6 col-lg-3">
        <div class="widget-small success coloured-icon">
            <i class="icon fa fa-bed fa-3x"></i>
            <div class="info">
                <h4>Occupancy</h4>
                <p><b>{{ number_format($occupancyRate, 1) }}%</b></p>
                <small>{{ $occupiedRooms }}/{{ $totalRooms }} Rooms</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Trend Chart -->
    <div class="col-md-8">
        <div class="tile">
            <h3 class="tile-title"><i class="fa fa-line-chart"></i> Revenue Trend (Last 7 Days)</h3>
            <div class="embed-responsive embed-responsive-16by9">
                <canvas class="embed-responsive-item" id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue Source Chart -->
    <div class="col-md-4">
        <div class="tile">
            <h3 class="tile-title"><i class="fa fa-pie-chart"></i> Revenue Sources</h3>
            <div class="embed-responsive embed-responsive-16by9">
                <canvas class="embed-responsive-item" id="sourceChart"></canvas>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Recent Finalized Shopping Lists</h3>

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>List Name</th>
                            <th>Finalized Date</th>
                            <th>Total Cost</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentShopping as $list)
                        <tr>
                            <td><strong>{{ $list->name }}</strong></td>
                            <td>{{ $list->updated_at->format('d M, Y') }}</td>
                            <td>TSh {{ number_format($list->total_actual_cost, 0) }}</td>
                            <td><span class="badge badge-success">Completed</span></td>
                            <td>
                                <a href="{{ route('admin.restaurants.shopping-list.show', $list->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-eye"></i> View Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No finalized shopping lists found this month.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('dashboard_assets/js/plugins/chart.js') }}"></script>
<script type="text/javascript">
    var data = {
        labels: {!! json_encode($last7Days) !!},
        datasets: [
            {
                label: "Total Revenue (TSh)",
                fillColor: "rgba(33, 150, 243, 0.2)",
                strokeColor: "rgba(33, 150, 243, 1)",
                pointColor: "rgba(33, 150, 243, 1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(33, 150, 243, 1)",
                data: {!! json_encode($revenueData) !!}
            }
        ]
    };

    var pdata = [
        {
            value: {{ $roomSource }},
            color: "#46BFBD",
            highlight: "#5AD3D1",
            label: "Rooms"
        },
        {
            value: {{ $barSource }},
            color: "#F7464A",
            highlight: "#FF5A5E",
            label: "Bar/Drinks"
        },
        {
            value: {{ $foodSource }},
            color: "#FDB45C",
            highlight: "#FFC870",
            label: "Food/Kitchen"
        }
    ];

    var ctxl = $("#revenueChart").get(0).getContext("2d");
    var lineChart = new Chart(ctxl).Line(data);

    var ctxp = $("#sourceChart").get(0).getContext("2d");
    var pieChart = new Chart(ctxp).Pie(pdata);
</script>
@endsection
