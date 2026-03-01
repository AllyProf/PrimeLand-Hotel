@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-user"></i> {{ $staff->name }} - Performance Details</h1>
        <p>Detailed activity analysis and operational impact for {{ $staff->role }}</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('owner.performance.index') }}">Staff Performance</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ul>
</div>

<div class="row">
    <!-- Summary Sidebar -->
    <div class="col-md-4">
        <div class="tile p-0 overflow-hidden" style="border-radius: 10px;">
            <div class="p-4 text-center bg-light border-bottom">
                @if($staff->profile_photo)
                    <img src="{{ asset('storage/' . $staff->profile_photo) }}" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white mx-auto mb-3 shadow" style="width: 120px; height: 120px; font-size: 48px;">
                        {{ substr($staff->name, 0, 1) }}
                    </div>
                @endif
                <h4 class="mb-0">{{ $staff->name }}</h4>
                <p class="badge badge-primary px-3 py-2 mt-2">{{ strtoupper(str_replace('_', ' ', $staff->role)) }}</p>
                
                <div class="mt-3 text-left pl-3">
                    <p class="mb-1"><strong><i class="fa fa-envelope text-muted mr-2"></i> Email:</strong> {{ $staff->email }}</p>
                    <p class="mb-1"><strong><i class="fa fa-phone text-muted mr-2"></i> Phone:</strong> {{ $staff->phone ?? 'N/A' }}</p>
                    <p class="mb-1"><strong><i class="fa fa-calendar text-muted mr-2"></i> Hired At:</strong> {{ $staff->hire_date ? $staff->hire_date->format('M d, Y') : 'N/A' }}</p>
                </div>
            </div>
            
            <div class="p-4">
                <h6 class="text-muted text-uppercase font-weight-bold mb-3">Key Metrics (This Month)</h6>
                
                <div class="metric-row d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span>{{ $stats['metric_label'] }}</span>
                    <span class="font-weight-bold text-primary">{{ $stats['metric_value'] }}</span>
                </div>
                
                <div class="metric-row d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span>Total Actions Logged</span>
                    <span class="font-weight-bold text-success">{{ $stats['total_actions'] }}</span>
                </div>

                @if($stats['revenue_handled'] > 0)
                <div class="metric-row d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span>Revenue Managed</span>
                    <span class="font-weight-bold text-info">TZS {{ number_format($stats['revenue_handled']) }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Activity Log -->
    <div class="col-md-8">
        <div class="tile" style="border-radius: 10px;">
            <h3 class="tile-title"><i class="fa fa-history"></i> Recent System Activity</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Time</th>
                            <th>Action</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td>
                                <div class="text-dark">{{ $activity->created_at->format('M d, H:i') }}</div>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @php
                                    $badgeColor = 'secondary';
                                    if(str_contains($activity->action, 'create')) $badgeColor = 'success';
                                    if(str_contains($activity->action, 'delete')) $badgeColor = 'danger';
                                    if(str_contains($activity->action, 'update')) $badgeColor = 'warning';
                                    if(str_contains($activity->action, 'check')) $badgeColor = 'info';
                                @endphp
                                <span class="badge badge-{{ $badgeColor }}">{{ strtoupper($activity->action) }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $activity->description }}</div>
                                @if($activity->model_type)
                                    <small class="text-muted">{{ class_basename($activity->model_type) }} #{{ $activity->model_id }}</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center p-4 text-muted">No recent activity found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
