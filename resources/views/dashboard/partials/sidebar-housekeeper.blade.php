{{-- Housekeeper Sidebar Menu (Clean & Organized) --}}
@php
  $activePage = request()->path();
@endphp

{{-- 1. DASHBOARD --}}
<li>
    <a class="app-menu__item {{ str_contains($activePage, 'housekeeper/dashboard') ? 'active' : '' }}" href="{{ route('housekeeper.dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Dashboard</span>
    </a>
</li>

<li class="nav-header">Room Operations</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'housekeeper/rooms/cleaning') ? 'active' : '' }}" href="{{ route('housekeeper.rooms.cleaning') }}"><i class="app-menu__icon fa fa-broom"></i><span class="app-menu__label">Needs Cleaning</span></a></li>

<li class="treeview {{ str_contains($activePage, 'housekeeper/rooms') && !str_contains($activePage, 'cleaning') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-bed"></i><span class="app-menu__label">Rooms</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('housekeeper.rooms.status') }}"><i class="icon fa fa-info-circle"></i> Room Status</a></li>
        <li><a class="treeview-item" href="{{ route('housekeeper.room-issues') }}"><i class="icon fa fa-wrench"></i> Room Issues</a></li>
    </ul>
</li>

<li class="nav-header">Inventory</li>

<li class="treeview {{ str_contains($activePage, 'housekeeper/inventory') || str_contains($activePage, 'housekeeper/consumption') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-cubes"></i><span class="app-menu__label">HK Inventory</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('housekeeper.inventory') }}"><i class="icon fa fa-list"></i> Manage Stock</a></li>
        <li><a class="treeview-item" href="{{ route('housekeeper.consumption.index') }}"><i class="icon fa fa-file-text-o"></i> Consumption Reports</a></li>
    </ul>
</li>

<li class="treeview {{ str_contains($activePage, 'housekeeper/purchase-requests') ? 'is-expanded' : '' }}">
    <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-shopping-cart"></i><span class="app-menu__label">Purchasing</span><i class="treeview-indicator fa fa-angle-right"></i></a>
    <ul class="treeview-menu">
        <li><a class="treeview-item" href="{{ route('housekeeper.purchase-requests.create') }}"><i class="icon fa fa-plus-circle"></i> New Request</a></li>
        <li><a class="treeview-item" href="{{ route('housekeeper.purchase-requests.my') }}"><i class="icon fa fa-tasks"></i> My Requests</a></li>
        <li><a class="treeview-item" href="{{ route('housekeeper.purchase-requests.history') }}"><i class="icon fa fa-history"></i> Purchase History</a></li>
        <li><a class="treeview-item" href="{{ route('housekeeper.purchase-requests.templates') }}"><i class="icon fa fa-clone"></i> Request Templates</a></li>
    </ul>
</li>

<li class="nav-header">Account</li>

<li><a class="app-menu__item {{ str_contains($activePage, 'profile') ? 'active' : '' }}" href="{{ route('reception.profile') }}"><i class="app-menu__icon fa fa-user-circle"></i><span class="app-menu__label">My Profile</span></a></li>
