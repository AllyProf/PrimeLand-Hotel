{{-- Owner Sidebar Menu --}}
@php
  $currentRoute = request()->route() ? request()->route()->getName() : '';
  $activePage = $activePage ?? request()->path();
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'owner/dashboard') ? 'active' : '' }}" href="{{ route('owner.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard Overview</span>
    </a>
</li>

{{-- 2. FINANCE & REPORTS --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Finance & Reports</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'admin/reports/index') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Reports Dashboard</span>
    </a>
</li>

<li class="treeview {{ (str_contains($activePage, 'bar-keeper/reports') || str_contains($activePage, 'chef-master/reports') || str_contains($activePage, 'housekeeper/reports')) ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-clipboard"></i><span class="app-menu__label">Daily Stock Sheets</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item {{ str_contains($activePage, 'bar-keeper/reports') ? 'active' : '' }}" href="{{ route('bar-keeper.reports') }}"><i class="icon fa fa-glass"></i> Bar Report</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'chef-master/reports') ? 'active' : '' }}" href="{{ route('chef-master.reports') }}"><i class="icon fa fa-cutlery"></i> Kitchen Report</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'housekeeper/reports') ? 'active' : '' }}" href="{{ route('housekeeper.reports') }}"><i class="icon fa fa-bed"></i> Housekeeping Report</a></li>
    </ul>
</li>

<li class="treeview {{ str_contains($activePage, 'reports/bookings') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-calendar"></i><span class="app-menu__label">Booking Reports</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item {{ str_contains($activePage, 'room-occupancy') ? 'active' : '' }}" href="{{ route('admin.reports.bookings.room-occupancy') }}"><i class="icon fa fa-bed"></i> Room Occupancy</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'bookings/performance') ? 'active' : '' }}" href="{{ route('admin.reports.bookings.performance') }}"><i class="icon fa fa-line-chart"></i> Booking Performance</a></li>
    </ul>
</li>

<li class="treeview {{ (str_contains($activePage, 'revenue-breakdown') || str_contains($activePage, 'cash-flow') || str_contains($activePage, 'profitability')) ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-money"></i><span class="app-menu__label">Financial Reports</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item {{ str_contains($activePage, 'revenue-breakdown') ? 'active' : '' }}" href="{{ route('admin.reports.revenue-breakdown') }}"><i class="icon fa fa-line-chart"></i> Revenue Breakdown</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'cash-flow') ? 'active' : '' }}" href="{{ route('admin.reports.cash-flow') }}"><i class="icon fa fa-exchange"></i> Cash Flow</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'profitability') ? 'active' : '' }}" href="{{ route('admin.reports.profitability') }}"><i class="icon fa fa-pie-chart"></i> Profitability</a></li>
    </ul>
</li>

<li class="treeview {{ (str_contains($activePage, 'daily-operations') || str_contains($activePage, 'reports/general') || str_contains($activePage, 'day-services/reports')) ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-cogs"></i><span class="app-menu__label">Operational Reports</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item {{ str_contains($activePage, 'daily-operations') ? 'active' : '' }}" href="{{ route('admin.reports.daily-operations') }}"><i class="icon fa fa-calendar-check-o"></i> Daily Operations</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'reports/general') ? 'active' : '' }}" href="{{ route('admin.reports.general') }}"><i class="icon fa fa-dashboard"></i> General Overview</a></li>
        <li><a class="treeview-item {{ str_contains($activePage, 'day-services/reports') ? 'active' : '' }}" href="{{ route('reception.day-services.reports') }}"><i class="icon fa fa-coffee"></i> Day Services</a></li>
    </ul>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'admin/payments') ? 'active' : '' }}" href="{{ route('admin.payments') }}">
        <i class="app-menu__icon fa fa-credit-card"></i>
        <span class="app-menu__label">All Payments</span>
    </a>
</li>

{{-- 3. OPERATIONS OVERVIEW --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Operations Overview</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'admin/bookings') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
        <i class="app-menu__icon fa fa-calendar-check-o"></i>
        <span class="app-menu__label">All Bookings</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'admin/rooms/status') ? 'active' : '' }}" href="{{ route('admin.rooms.status') }}">
        <i class="app-menu__icon fa fa-bed"></i>
        <span class="app-menu__label">Room Status</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'shifts/history') ? 'active' : '' }}" href="{{ route('owner.shift.history') }}">
        <i class="app-menu__icon fa fa-history"></i>
        <span class="app-menu__label">Shift History</span>
    </a>
</li>

{{-- 4. STOCK & PROCUREMENT --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Stock & Procurement</li>

<li class="treeview {{ (str_contains($activePage, 'shopping-list') || str_contains($activePage, 'bar-keeper/stock')) ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-cubes"></i><span class="app-menu__label">Inventory status</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('admin.restaurants.shopping-list.index') }}"><i class="icon fa fa-shopping-basket"></i> Market Lists</a></li>
        <li><a class="treeview-item" href="{{ route('bar-keeper.stock.index') }}"><i class="icon fa fa-glass"></i> Bar Stock</a></li>
        <li><a class="treeview-item" href="{{ route('chef-master.inventory') }}"><i class="icon fa fa-cutlery"></i> Kitchen Stock</a></li>
        <li><a class="treeview-item" href="{{ route('admin.housekeeping-inventory') }}"><i class="icon fa fa-bed"></i> Housekeeping Stock</a></li>
    </ul>

</li>

{{-- 5. STAFF --}}
<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Staff Management</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'admin/users') ? 'active' : '' }}" href="{{ route('admin.users') }}">
        <i class="app-menu__icon fa fa-user-secret"></i>
        <span class="app-menu__label">Staff Directory</span>
    </a>
</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'owner/performance') ? 'active' : '' }}" href="{{ route('owner.performance.index') }}">
        <i class="app-menu__icon fa fa-line-chart"></i>
        <span class="app-menu__label">Staff Performance</span>
    </a>
</li>

{{-- 6. COMMUNICATIONS --}}

<li class="treeview-item-header" style="padding: 10px 20px; color: #999; font-size: 11px; text-transform: uppercase; font-weight: 600; margin-top: 10px;">Communications</li>

<li>
    <a class="app-menu__item {{ str_contains($activePage, 'messaging') ? 'active' : '' }}" href="{{ route('owner.sms.index') }}">
        <i class="app-menu__icon fa fa-envelope"></i>
        <span class="app-menu__label">Messaging Center</span>
    </a>
</li>

