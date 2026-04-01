{{-- Bar Keeper Sidebar Menu (Clean & Organized) --}}
@php
  $activePage = request()->path();
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/dashboard') ? 'active' : '' }}" href="{{ route('bar-keeper.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

<li class="nav-header">Bar Operations</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/stock') ? 'active' : '' }}" href="{{ route('bar-keeper.stock.index') }}"><i class="app-menu__icon fa fa-glass"></i><span class="app-menu__label">Bar Stock Control</span></a></li>

<li><a class="app-menu__item {{ str_contains($activePage, 'orders/monitor') ? 'active' : '' }}" href="{{ route('reception.orders.monitor') }}"><i class="app-menu__icon fa fa-television"></i><span class="app-menu__label">Order Monitor</span></a></li>

<li class="nav-header">Shift & Sales</li>

<li class="treeview {{ str_contains($activePage, 'bar-keeper/shift') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-lock"></i><span class="app-menu__label">Shift Management</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('bar-keeper.shift.open') }}"><i class="icon fa fa-key"></i> Open Shift</a></li>
        <li><a class="treeview-item" href="{{ route('bar-keeper.shift.close') }}"><i class="icon fa fa-sign-out"></i> Close Shift</a></li>
    </ul>
</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'bar-keeper/reports') ? 'active' : '' }}" href="{{ route('bar-keeper.reports') }}"><i class="app-menu__icon fa fa-file-text-o"></i><span class="app-menu__label">Daily Stock Sheet</span></a></li>

<li class="nav-header">Purchasing</li>

<li class="treeview {{ str_contains($activePage, 'bar-keeper/purchase-requests') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-shopping-cart"></i><span class="app-menu__label">Purchase Requests</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('bar-keeper.purchase-requests.create') }}"><i class="icon fa fa-plus-circle"></i> New Request</a></li>
        <li><a class="treeview-item" href="{{ route('bar-keeper.purchase-requests.my') }}"><i class="icon fa fa-tasks"></i> My Requests</a></li>
        <li><a class="treeview-item" href="{{ route('bar-keeper.purchase-requests.history') }}"><i class="icon fa fa-history"></i> History</a></li>
        <li><a class="treeview-item" href="{{ route('bar-keeper.purchase-requests.templates') }}"><i class="icon fa fa-clone"></i> Templates</a></li>
    </ul>
</li>

<li class="nav-header">Account</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}" href="{{ route('reception.profile') }}"><i class="app-menu__icon fa fa-user-circle"></i><span class="app-menu__label">My Profile</span></a></li>
