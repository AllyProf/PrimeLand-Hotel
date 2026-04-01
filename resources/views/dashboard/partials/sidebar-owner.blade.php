{{-- Owner Sidebar Menu (Clean & Organized) --}}
@php
  $currentRoute = request()->route() ? request()->route()->getName() : '';
  $activePage = $activePage ?? request()->path();
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'owner/dashboard') ? 'active' : '' }}" href="{{ route('owner.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Executive Dashboard</span>
    </a>
</li>

<li class="nav-header">Finance & Performance</li>

<li class="treeview {{ str_contains($activePage, 'reports/') && !str_contains($activePage, 'stock') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-line-chart"></i><span class="app-menu__label">Business Insights</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('admin.reports.index') }}"><i class="icon fa fa-dashboard"></i> Reports Hub</a></li>
        <li><a class="treeview-item" href="{{ route('admin.reports.revenue-breakdown') }}"><i class="icon fa fa-money"></i> Revenue Analysis</a></li>
        <li><a class="treeview-item" href="{{ route('admin.reports.cash-flow') }}"><i class="icon fa fa-exchange"></i> Cash Flow</a></li>
        <li><a class="treeview-item" href="{{ route('admin.reports.profitability') }}"><i class="icon fa fa-pie-chart"></i> Profitability</a></li>
        <li><a class="treeview-item" href="{{ route('admin.reports.bookings.performance') }}"><i class="icon fa fa-bar-chart"></i> Booking Performance</a></li>
    </ul>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'admin/payments') ? 'active' : '' }}" href="{{ route('admin.payments') }}">
        <i class="app-menu__icon fa fa-credit-card"></i>
        <span class="app-menu__label">All Payment Logs</span>
    </a>
</li>

<li class="nav-header">Operations Review</li>

<li class="treeview {{ str_contains($activePage, 'owner/shift') || str_contains($activePage, 'admin/bookings') || str_contains($activePage, 'invoices') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-calendar-check-o"></i><span class="app-menu__label">Front Office Review</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('admin.bookings.index') }}"><i class="icon fa fa-list"></i> All Bookings</a></li>
        <li><a class="treeview-item" href="{{ route('owner.shift.history') }}"><i class="icon fa fa-history"></i> Shift History</a></li>
        <li><a class="treeview-item" href="{{ route('reception.invoices.index') }}"><i class="icon fa fa-file-text-o"></i> Invoices Issued</a></li>
    </ul>
</li>

<li class="treeview {{ str_contains($activePage, 'reports/daily-operations') || str_contains($activePage, 'admin/rooms/status') || str_contains($activePage, 'day-services/reports') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-tasks"></i><span class="app-menu__label">Operational Audits</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('admin.rooms.status') }}"><i class="icon fa fa-bed"></i> Room Status</a></li>
        <li><a class="treeview-item" href="{{ route('admin.reports.daily-operations') }}"><i class="icon fa fa-calendar-o"></i> Daily Audit</a></li>
        <li><a class="treeview-item" href="{{ route('reception.day-services.reports') }}"><i class="icon fa fa-coffee"></i> Day Services</a></li>
    </ul>
</li>

<li class="nav-header">Stock & Inventory</li>

<li class="treeview {{ str_contains($activePage, 'stock') || str_contains($activePage, 'inventory') || str_contains($activePage, 'shopping-list') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-cubes"></i><span class="app-menu__label">Inventory status</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('bar-keeper.stock.index') }}"><i class="icon fa fa-glass"></i> Bar Stock</a></li>
        <li><a class="treeview-item" href="{{ route('chef-master.inventory') }}"><i class="icon fa fa-cutlery"></i> Kitchen Stock</a></li>
        <li><a class="treeview-item" href="{{ route('admin.housekeeping-inventory') }}"><i class="icon fa fa-bed"></i> Housekeeping</a></li>
        <li><a class="treeview-item" href="{{ route('admin.restaurants.shopping-list.index') }}"><i class="icon fa fa-shopping-basket"></i> Market Lists</a></li>
    </ul>
</li>

<li class="nav-header">Human Resources</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'admin/users') ? 'active' : '' }}" href="{{ route('admin.users') }}"><i class="app-menu__icon fa fa-user-circle"></i><span class="app-menu__label">Staff Directory</span></a></li>

<li><a class="app-menu__item {{ str_contains($activePage, 'owner/performance') ? 'active' : '' }}" href="{{ route('owner.performance.index') }}"><i class="app-menu__icon fa fa-line-chart"></i><span class="app-menu__label">Staff Performance</span></a></li>

<li class="nav-header">Governance</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'messaging') ? 'active' : '' }}" href="{{ route('owner.sms.index') }}"><i class="app-menu__icon fa fa-envelope"></i><span class="app-menu__label">Messaging Center</span></a></li>
