@php
    // Support both manager and owner contexts
    $historyRoute = $historyRoute ?? 'reception.shift.history';
    $printRoute   = $printRoute   ?? 'reception.shift.print';
    $dashRoute    = $dashRoute    ?? ($role === 'manager' ? 'admin.dashboard' : 'reception.dashboard');
@endphp

@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-history"></i> Shift History & Monitoring</h1>
    <p>Monitor operational cycles and financial reconciliations</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route($role === 'manager' ? 'admin.dashboard' : 'reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Shift History</li>
  </ul>
</div>

@if(session('info'))
<div class="alert alert-info alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <i class="fa fa-info-circle mr-2"></i> {{ session('info') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <i class="fa fa-exclamation-circle mr-2"></i> {{ session('error') }}
</div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="tile shadow-sm border-0" style="border-radius: 12px;">
            <div class="tile-title-w-btn">
                <h3 class="title">
                    Active & Past Sessions
                    @php $activeFilters = array_filter($filters ?? []); @endphp
                    @if(count($activeFilters) > 0)
                        <span class="badge badge-primary ml-2" style="font-size: 11px;">{{ count($activeFilters) }} filter(s) active</span>
                    @endif
                </h3>
                <div class="btn-group">
                    <button class="btn btn-outline-primary {{ count($activeFilters ?? []) > 0 ? 'active' : '' }}" type="button" data-toggle="collapse" data-target="#filterBox">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    @if($role !== 'manager')
                    <a href="{{ route('reception.shift.open') }}" class="btn btn-primary"><i class="fa fa-plus"></i> New Shift</a>
                    @endif
                </div>
            </div>

            {{-- Filter Panel --}}
            <div class="collapse {{ count($activeFilters ?? []) > 0 ? 'show' : '' }} mb-3" id="filterBox">
                <form action="{{ route($historyRoute) }}" method="GET" class="card card-body bg-light border-0" style="border-radius: 10px;">
                    <div class="row">

                        {{-- Staff Filter --}}
                        <div class="col-md-3 mt-2">
                            <label class="small font-weight-bold text-uppercase text-muted">Staff Member</label>
                            <select name="staff_id" class="form-control">
                                <option value="">All Staff</option>
                                @foreach($allStaff as $staff)
                                    <option value="{{ $staff->id }}" {{ ($filters['staff_id'] ?? '') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ $staff->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Filter --}}
                        <div class="col-md-2 mt-2">
                            <label class="small font-weight-bold text-uppercase text-muted">Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="open" {{ ($filters['status'] ?? '') === 'open' ? 'selected' : '' }}>Currently Open</option>
                                <option value="closed" {{ ($filters['status'] ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        {{-- Single Date Filter --}}
                        <div class="col-md-2 mt-2" id="singleDateWrap">
                            <label class="small font-weight-bold text-uppercase text-muted">Specific Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $filters['date'] ?? '' }}">
                        </div>

                        {{-- Date Range: From --}}
                        <div class="col-md-2 mt-2" id="dateFromWrap">
                            <label class="small font-weight-bold text-uppercase text-muted">From Date</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                        </div>

                        {{-- Date Range: To --}}
                        <div class="col-md-2 mt-2" id="dateToWrap">
                            <label class="small font-weight-bold text-uppercase text-muted">To Date</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                        </div>

                    </div>

                    {{-- Quick Date Shortcuts --}}
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bold text-uppercase text-muted d-block mb-1">Quick Range</label>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary quick-date" data-range="today"><i class="fa fa-calendar-o"></i> Today</button>
                                <button type="button" class="btn btn-outline-secondary quick-date" data-range="yesterday">Yesterday</button>
                                <button type="button" class="btn btn-outline-secondary quick-date" data-range="week">This Week</button>
                                <button type="button" class="btn btn-outline-secondary quick-date" data-range="month">This Month</button>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa fa-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('reception.shift.history') }}" class="btn btn-secondary ml-2">
                                <i class="fa fa-times"></i> Clear All
                            </a>
                        </div>
                    </div>

                </form>
            </div>

            {{-- Active Filter Summary Tags --}}
            @if(count($activeFilters ?? []) > 0)
            <div class="mb-3 px-3">
                @if(!empty($filters['date']))
                    <span class="badge badge-light border mr-2 px-2 py-1"><i class="fa fa-calendar"></i> Date: {{ \Carbon\Carbon::parse($filters['date'])->format('d M Y') }}</span>
                @endif
                @if(!empty($filters['date_from']) || !empty($filters['date_to']))
                    <span class="badge badge-light border mr-2 px-2 py-1"><i class="fa fa-calendar"></i>
                        {{ !empty($filters['date_from']) ? \Carbon\Carbon::parse($filters['date_from'])->format('d M Y') : '...' }}
                        →
                        {{ !empty($filters['date_to']) ? \Carbon\Carbon::parse($filters['date_to'])->format('d M Y') : '...' }}
                    </span>
                @endif
                @if(!empty($filters['status']))
                    <span class="badge badge-{{ $filters['status'] === 'open' ? 'success' : 'secondary' }} mr-2 px-2 py-1">Status: {{ ucfirst($filters['status']) }}</span>
                @endif
                @if(!empty($filters['staff_id']))
                    @php $selectedStaff = $allStaff->firstWhere('id', $filters['staff_id']); @endphp
                    <span class="badge badge-info mr-2 px-2 py-1"><i class="fa fa-user"></i> {{ $selectedStaff->name ?? 'Staff #'.$filters['staff_id'] }}</span>
                @endif
            </div>
            @endif

            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="shiftTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Staff Member</th>
                                <th>Session Start</th>
                                <th>Session End</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Opening Cash</th>
                                <th>Closing Cash</th>
                                <th>Total Revenue (TZS)</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shifts as $shift)
                            <tr>
                                <td>
                                    <strong>{{ $shift->staff->name ?? 'N/A' }}</strong><br>
                                    <small class="badge badge-secondary">{{ strtoupper(str_replace('_', ' ', $shift->staff->role ?? 'Staff')) }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $shift->opened_at->format('d M Y') }}</small><br>
                                    <strong>{{ $shift->opened_at->format('H:i') }}</strong>
                                </td>
                                <td>
                                    @if($shift->closed_at)
                                        <small class="text-muted">{{ $shift->closed_at->format('d M Y') }}</small><br>
                                        <strong>{{ $shift->closed_at->format('H:i') }}</strong>
                                    @else
                                        <span class="text-success"><i class="fa fa-circle fa-xs"></i> In Progress...</span>
                                    @endif
                                </td>
                                <td>
                                    @if($shift->closed_at)
                                        @php
                                            $mins = $shift->opened_at->diffInMinutes($shift->closed_at);
                                            $hours = intdiv($mins, 60);
                                            $rem = $mins % 60;
                                        @endphp
                                        <span class="text-muted">{{ $hours }}h {{ $rem }}m</span>
                                    @else
                                        @php
                                            $mins = $shift->opened_at->diffInMinutes(now());
                                            $hours = intdiv($mins, 60);
                                            $rem = $mins % 60;
                                        @endphp
                                        <span class="text-success">{{ $hours }}h {{ $rem }}m so far</span>
                                    @endif
                                </td>
                                <td>
                                    @if($shift->status === 'open')
                                        <span class="badge badge-success px-3 py-2"><i class="fa fa-spinner fa-spin"></i> ACTIVE</span>
                                    @else
                                        <span class="badge badge-secondary px-3 py-2"><i class="fa fa-check"></i> CLOSED</span>
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($shift->staff->role ?? '', ['head_chef', 'chef', 'bar_keeper']))
                                        <span class="text-muted">N/A</span>
                                    @else
                                        {{ number_format($shift->opening_cash ?? 0, 0) }} TZS
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($shift->staff->role ?? '', ['head_chef', 'chef', 'bar_keeper']))
                                        <span class="text-muted">N/A</span>
                                    @elseif($shift->status === 'closed')
                                        {{ number_format($shift->closing_cash_actual ?? 0, 0) }} TZS
                                        @php
                                            $diff = ($shift->closing_cash_actual ?? 0) - ($shift->closing_cash_expected ?? 0);
                                        @endphp
                                        @if($diff != 0)
                                            <br><small class="{{ $diff > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 0) }} TZS
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($shift->staff->role ?? '', ['head_chef', 'chef', 'bar_keeper']))
                                        <span class="text-muted">N/A</span>
                                    @else
                                        @php
                                            $expectedTotal = (float)($shift->closing_cash_expected ?? 0) +
                                                             (float)($shift->total_mobile_expected ?? 0) +
                                                             (float)($shift->total_card_expected ?? 0) +
                                                             (float)($shift->total_bank_expected ?? 0) +
                                                             (float)($shift->total_online_expected ?? 0);
                                        @endphp
                                        @if($shift->status === 'closed')
                                            <strong>{{ number_format($expectedTotal, 0) }} TZS</strong>
                                        @else
                                            <span class="text-muted">Pending Close</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        @if($shift->status === 'closed')
                                            @if(in_array($shift->staff->role ?? '', ['head_chef', 'chef']))
                                                <a href="{{ route('chef-master.reports', ['shift_id' => $shift->id]) }}" class="btn btn-sm btn-info" target="_blank" title="View Shift Report">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            @elseif(in_array($shift->staff->role ?? '', ['bar_keeper']))
                                                <a href="{{ route('bar-keeper.reports', ['shift_id' => $shift->id]) }}" class="btn btn-sm btn-info" target="_blank" title="View Shift Report">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            @else
                                                <a href="{{ route($printRoute, $shift->id) }}" class="btn btn-sm btn-info" target="_blank" title="Print Cash Report">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if(in_array($role, ['reception']) && $shift->status === 'open' && $shift->staff_id === Auth::guard('staff')->id())
                                            <a href="{{ route('reception.shift.close') }}" class="btn btn-sm btn-warning" title="Close My Shift">
                                                <i class="fa fa-sign-out"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center p-5">
                                    <i class="fa fa-folder-open-o fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No shifts found for the selected filters.</p>
                                    @if(count($activeFilters ?? []) > 0)
                                        <a href="{{ route('reception.shift.history') }}" class="btn btn-sm btn-outline-secondary mt-2">Clear Filters</a>
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $shifts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tile { border-radius: 12px; }
    .table thead th { border-top: none; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge { border-radius: 4px; font-weight: 600; }
    .quick-date { border-radius: 4px !important; }
    .quick-date.active-range { background-color: #007bff; color: #fff; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateInput    = document.querySelector('input[name="date"]');
    const dateFrom     = document.querySelector('input[name="date_from"]');
    const dateTo       = document.querySelector('input[name="date_to"]');

    // Quick range preset buttons
    document.querySelectorAll('.quick-date').forEach(btn => {
        btn.addEventListener('click', function () {
            const range = this.dataset.range;
            const today = new Date();
            const fmt   = d => d.toISOString().slice(0, 10);

            // Clear single date
            dateInput.value = '';

            if (range === 'today') {
                dateFrom.value = fmt(today);
                dateTo.value   = fmt(today);
            } else if (range === 'yesterday') {
                const y = new Date(today); y.setDate(y.getDate() - 1);
                dateFrom.value = fmt(y);
                dateTo.value   = fmt(y);
            } else if (range === 'week') {
                const start = new Date(today);
                start.setDate(today.getDate() - today.getDay());
                dateFrom.value = fmt(start);
                dateTo.value   = fmt(today);
            } else if (range === 'month') {
                const start = new Date(today.getFullYear(), today.getMonth(), 1);
                dateFrom.value = fmt(start);
                dateTo.value   = fmt(today);
            }

            document.querySelectorAll('.quick-date').forEach(b => b.classList.remove('active-range'));
            this.classList.add('active-range');
        });
    });

    // Auto-clear range when single date is set
    dateInput.addEventListener('change', function () {
        if (this.value) {
            dateFrom.value = '';
            dateTo.value   = '';
        }
    });

    // Auto-clear single date when range is set
    [dateFrom, dateTo].forEach(el => {
        el.addEventListener('change', function () {
            if (this.value) dateInput.value = '';
        });
    });
});
</script>
@endsection
