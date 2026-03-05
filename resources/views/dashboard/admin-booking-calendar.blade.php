@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar"></i> Booking Calendar</h1>
    <p>View all room bookings and occupancy at a glance</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Bookings</a></li>
    <li class="breadcrumb-item"><a href="#">Calendar</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title">Filter Calendar</h3>
        <div class="btn-group">
            <a href="{{ route('admin.bookings.manual.create') }}" class="btn btn-primary px-4"><i class="fa fa-plus"></i> New Booking</a>
        </div>
      </div>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div>
                <input type="text" id="calendarSearch" class="form-control" placeholder="Search Guest or Room...">
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="d-flex">
              <select id="jumpMonth" class="form-control mr-2">
                @for ($m=1; $m<=12; $m++)
                  <option value="{{ $m-1 }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
              </select>
              <select id="jumpYear" class="form-control mr-2">
                @for ($y=date('Y'); $y<=date('Y')+2; $y++)
                  <option value="{{ $y }}">{{ $y }}</option>
                @endfor
              </select>
              <button onclick="jumpToDate()" class="btn btn-info">Jump</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
        <h3 class="tile-title">Status Legend</h3>
        <div class="row">
          <div class="col-md-3 col-sm-6 mb-2">
            <div class="d-flex align-items-center">
              <span style="display: inline-block; width: 15px; height: 15px; background: #dc3545; border-radius: 3px; margin-right: 10px;"></span>
              <div>
                <strong style="font-size: 13px;">Occupied</strong>
                <div class="text-muted" style="font-size: 11px;">Checked In</div>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-2">
            <div class="d-flex align-items-center">
              <span style="display: inline-block; width: 15px; height: 15px; background: #28a745; border-radius: 3px; margin-right: 10px;"></span>
              <div>
                <strong style="font-size: 13px;">Confirmed</strong>
                <div class="text-muted" style="font-size: 11px;">Paid</div>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-2">
            <div class="d-flex align-items-center">
              <span style="display: inline-block; width: 15px; height: 15px; background: #ffc107; border-radius: 3px; margin-right: 10px;"></span>
              <div>
                <strong style="font-size: 13px;">Pending Payment</strong>
                <div class="text-muted" style="font-size: 11px;">Awaiting Payment</div>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-2">
            <div class="d-flex align-items-center">
              <span style="display: inline-block; width: 15px; height: 15px; background: #17a2b8; border-radius: 3px; margin-right: 10px;"></span>
              <div>
                <strong style="font-size: 13px;">Partial Payment</strong>
                <div class="text-muted" style="font-size: 11px;">Partially Paid</div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Day Summary Modal -->
<div class="modal fade" id="daySummaryModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold"><i class="fa fa-calendar-check-o mr-2 text-primary"></i> Day Summary: <span id="summaryDateLabel"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0">
        <!-- Stats Row -->
        <div class="row no-gutters text-center border-bottom bg-light">
          <div class="col-3 py-3 border-right">
            <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px;">Available</small>
            <strong id="statAvailable" class="h4 mb-0 text-success">0</strong>
          </div>
          <div class="col-3 py-3 border-right">
            <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px;">Occupied</small>
            <strong id="statOccupied" class="h4 mb-0 text-danger">0</strong>
          </div>
          <div class="col-3 py-3 border-right">
            <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px;">Cleaning</small>
            <strong id="statCleaning" class="h4 mb-0 text-warning">0</strong>
          </div>
          <div class="col-3 py-3">
            <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px;">Maint.</small>
            <strong id="statMaintenance" class="h4 mb-0 text-secondary">0</strong>
          </div>
        </div>
        
        <div class="p-3 bg-white border-bottom">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
            <input type="text" id="modalRoomFilter" class="form-control" placeholder="Search room # or type...">
          </div>
        </div>

        <div class="p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
          <div class="row" id="summaryRoomsGrid">
            <!-- Dynamically populated -->
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold"><i class="fa fa-info-circle mr-2 text-info"></i> Booking Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4" id="bookingDetailsContent">
        <!-- Content will be loaded dynamically -->
      </div>
      <input type="hidden" id="currentBookingId" value="">
      <div class="modal-footer">
        <button type="button" id="editBookingBtn" class="btn btn-primary d-none"><i class="fa fa-edit"></i> Edit Booking</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* SweetAlert2 Custom Styling - Brand Colors */
.swal2-popup {
    border-radius: 10px !important;
    font-family: "Century Gothic", AppleGothic, sans-serif;
}

.swal2-title {
    font-weight: 600 !important;
    color: #333 !important;
}

.swal2-confirm {
    background-color: #e77a3a !important;
    border-color: #e77a3a !important;
    font-weight: 600 !important;
    border-radius: 5px !important;
    padding: 10px 24px !important;
    transition: all 0.3s !important;
}

.swal2-confirm:hover {
    background-color: #d66a2a !important;
    border-color: #d66a2a !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.swal2-cancel {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    font-weight: 600 !important;
    border-radius: 5px !important;
    padding: 10px 24px !important;
}

.swal2-cancel:hover {
    background-color: #5a6268 !important;
    border-color: #5a6268 !important;
}

.swal2-select {
    border-radius: 5px !important;
    border: 1px solid #ddd !important;
    padding: 8px 12px !important;
}

.swal2-select:focus {
    border-color: #e77a3a !important;
    box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25) !important;
}

/* Custom Utilities */
.bg-primary-light { background-color: rgba(231, 122, 58, 0.1); }
.shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.rounded-xl { border-radius: 12px; }
</style>
<style>
/* Custom Calendar Styling */
.fc {
    font-family: "Century Gothic", AppleGothic, sans-serif;
}

.fc-header-toolbar {
    margin-bottom: 1.5em;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.fc-button {
    background: #e77a3a !important;
    border-color: #e77a3a !important;
    color: white !important;
    font-weight: 600;
    padding: 8px 15px;
    border-radius: 5px;
    transition: all 0.3s;
}

.fc-button:hover {
    background: #d66a2a !important;
    border-color: #d66a2a !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.fc-button-active {
    background: #c55a1a !important;
    border-color: #c55a1a !important;
}

.fc-today-button {
    background: #28a745 !important;
    border-color: #28a745 !important;
}

.fc-today-button:hover {
    background: #218838 !important;
    border-color: #218838 !important;
}

.fc-daygrid-day {
    border-color: #e0e0e0;
    transition: background-color 0.2s;
}

.fc-daygrid-day:hover {
    background-color: #f8f9fa;
}

.fc-day-today {
    background-color: #fff3cd !important;
}

.fc-col-header-cell {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
    padding: 10px;
    border-color: #e0e0e0;
}

.fc-daygrid-day-number {
    padding: 8px;
    font-weight: 500;
    color: #333;
}

.fc-day-today .fc-daygrid-day-number {
    background: #e77a3a;
    color: white;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.fc-event {
    border-radius: 5px !important;
    padding: 2px 5px;
    cursor: pointer;
    transition: all 0.2s;
}

.fc-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.3) !important;
    z-index: 10;
}

.fc-popover {
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    border: none;
}

.fc-popover-header {
    background: #e77a3a;
    color: white;
    padding: 10px;
    border-radius: 8px 8px 0 0;
}

.fc-more-link {
    color: #e77a3a;
    font-weight: 600;
}

.fc-more-link:hover {
    color: #d66a2a;
    text-decoration: underline;
}

/* Modal Enhancements */
.modal-content {
    border-radius: 10px;
    overflow: hidden;
}

.modal-body table {
    margin-bottom: 0;
}

.modal-body table td {
    padding: 10px;
    border-color: #e0e0e0;
}

.modal-body h6 {
    color: #e77a3a;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e77a3a;
}

.badge {
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 5px;
}

/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Quick Actions Bar - Mobile */
  .row.align-items-center .col-md-8,
  .row.align-items-center .col-md-4 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .row.align-items-center .col-md-4 {
    margin-top: 15px;
    text-align: left !important;
  }
  
  .row.align-items-center .col-md-4 .btn {
    width: 100%;
  }
  
  /* Legend - Mobile */
  .col-md-3.col-sm-6 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 15px;
  }
  
  /* Calendar Container */
  .tile-body {
    padding: 15px !important;
  }
  
  /* FullCalendar Responsive */
  .fc-header-toolbar {
    flex-direction: column;
    gap: 10px;
    padding: 10px !important;
  }
  
  .fc-toolbar-chunk {
    width: 100%;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 5px;
  }
  
  .fc-toolbar-chunk:first-child {
    order: 2; /* Move prev/next/today to middle */
  }
  
  .fc-toolbar-chunk:nth-child(2) {
    order: 1; /* Move title to top */
    width: 100%;
    margin-bottom: 10px;
  }
  
  .fc-toolbar-chunk:last-child {
    order: 3; /* Move view buttons to bottom */
    width: 100%;
    justify-content: center;
  }
  
  .fc-toolbar-title {
    font-size: 18px !important;
    text-align: center;
  }
  
  .fc-button {
    padding: 6px 12px !important;
    font-size: 13px !important;
  }
  
  .fc-button-group .fc-button {
    padding: 6px 10px !important;
  }
  
  /* Calendar Grid - Mobile */
  .fc-col-header-cell {
    padding: 8px 4px !important;
    font-size: 12px;
  }
  
  .fc-daygrid-day-number {
    padding: 4px !important;
    font-size: 13px;
  }
  
  .fc-day-today .fc-daygrid-day-number {
    width: 24px;
    height: 24px;
    font-size: 12px;
  }
  
  .fc-event {
    font-size: 10px !important;
    padding: 2px 4px !important;
    margin: 1px 0;
  }
  
  .fc-event-title {
    font-size: 10px !important;
  }
  
  /* Modal - Mobile */
  .modal-dialog.modal-lg {
    max-width: calc(100% - 20px);
    margin: 10px;
  }
  
  .modal-body .row .col-md-6 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 20px;
  }
  
  .modal-body table {
    font-size: 14px;
  }
  
  .modal-body table td {
    padding: 8px;
    font-size: 13px;
  }
  
  .modal-header {
    padding: 15px !important;
  }
  
  .modal-footer {
    padding: 15px !important;
  }
  
  .modal-footer .btn {
    width: 100%;
    margin-bottom: 10px;
  }
  
  .modal-footer .btn:last-child {
    margin-bottom: 0;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .fc-toolbar-title {
    font-size: 16px !important;
  }
  
  .fc-button {
    padding: 5px 8px !important;
    font-size: 12px !important;
  }
  
  .fc-button-group .fc-button {
    padding: 5px 8px !important;
    font-size: 11px !important;
  }
  
  .fc-col-header-cell {
    padding: 6px 2px !important;
    font-size: 11px;
  }
  
  .fc-daygrid-day-number {
    font-size: 12px;
  }
  
  .fc-event {
    font-size: 9px !important;
    padding: 1px 3px !important;
  }
  
  .fc-event-title {
    font-size: 9px !important;
  }
  
  /* Quick Actions Bar */
  .row.align-items-center h4 {
    font-size: 16px !important;
  }
  
  .row.align-items-center p {
    font-size: 13px !important;
  }
  
  /* Legend */
  .col-md-3.col-sm-6 {
    margin-bottom: 12px;
  }
  
  /* Calendar Container Padding */
  .tile-body > div:last-child {
    padding: 15px !important;
  }
}

/* Tablet */
@media (min-width: 768px) and (max-width: 991px) {
  .fc-toolbar-title {
    font-size: 20px !important;
  }
  
  .fc-button {
    padding: 7px 12px !important;
  }
  
  .modal-dialog.modal-lg {
    max-width: 90%;
  }
}
</style>
<script>
let calendar;
let allRoomSummaryData = [];

// Jump to Date function
function jumpToDate() {
    const month = document.getElementById('jumpMonth').value;
    const year = document.getElementById('jumpYear').value;
    const date = new Date(year, month, 1);
    if (calendar) {
        calendar.gotoDate(date);
    }
}

// Global scope functions for events
function showDaySummary(dateStr) {
    $('#summaryDateLabel').text(new Date(dateStr).toLocaleDateString('en-GB', { day:'numeric', month:'short', year:'numeric' }));
    $('#summaryRoomsGrid').html('<div class="col-12 text-center py-5"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-2">Fetching room states...</p></div>');
    $('#daySummaryModal').modal('show');

    fetch(`{{ route("admin.bookings.calendar.summary", [], false) }}?date=${dateStr}`)
        .then(res => res.json())
        .then(data => {
            allRoomSummaryData = data.rooms_list;
            $('#statAvailable').text(data.available);
            $('#statOccupied').text(data.occupied);
            $('#statCleaning').text(data.pending_cleaning);
            $('#statMaintenance').text(data.maintenance);
            renderRoomGrid();
        })
        .catch(err => {
            $('#summaryRoomsGrid').html('<div class="col-12 text-center text-danger py-5"><i class="fa fa-exclamation-triangle fa-2x mb-2"></i><br>Failed to load data. Please try again.</div>');
        });
}

function renderRoomGrid(filter = '') {
    const term = filter.toLowerCase();
    const container = $('#summaryRoomsGrid');
    container.empty();

    const filtered = allRoomSummaryData.filter(r => 
        r.room_number.toString().includes(term) || 
        r.room_type.toLowerCase().includes(term)
    );

    if (filtered.length === 0) {
        container.html('<div class="col-12 text-center py-5 text-muted"><i class="fa fa-search fa-2x mb-2"></i><br>No rooms matching filter found.</div>');
        return;
    }

    filtered.forEach(room => {
        let statusColor = '#28a745'; // Available
        let statusClass = 'success';
        let statusIcon = 'check-circle';
        let subText = 'Available';
        let actionBtn = `<button class="btn btn-sm btn-outline-success" onclick="createBooking('${room.room_number}')">Book</button>`;

        if (room.status === 'occupied' || room.status === 'reserved') {
            statusColor = room.status === 'occupied' ? '#dc3545' : '#009688';
            statusClass = room.status === 'occupied' ? 'danger' : 'primary';
            statusIcon = 'user';
            subText = room.guest || 'Reserved';
            actionBtn = `<button class="btn btn-sm btn-info" onclick="viewBookingDetails(${room.booking_id})">Info</button>`;
        } else if (room.status === 'dirty') {
            statusColor = '#ffc107';
            statusClass = 'warning';
            statusIcon = 'tint';
            subText = 'Cleaning';
            actionBtn = '';
        } else if (room.status === 'maintenance') {
            statusColor = '#6c757d';
            statusClass = 'secondary';
            statusIcon = 'wrench';
            subText = 'Maint.';
            actionBtn = '';
        }

        const card = `
            <div class="col-6 col-md-4 mb-3 px-1">
                <div class="tile p-2 mb-0 shadow-sm border" style="border-radius: 8px;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge badge-light px-2" style="font-size:12px">#${room.room_number}</span>
                        <i class="fa fa-${statusIcon} text-${statusClass}"></i>
                    </div>
                    <div class="mb-2">
                        <div class="text-truncate font-weight-bold" style="font-size:12px">${room.room_type}</div>
                        <div class="text-${statusClass} small text-truncate" style="font-size:10px">${subText}</div>
                    </div>
                    <div class="text-right">
                       ${actionBtn}
                    </div>
                </div>
            </div>
        `;
        container.append(card);
    });
}

function createBooking(roomNumber) {
    const dateLabel = $('#summaryDateLabel').text();
    // Pre-select room by number if the route supports it, or just pass date
    window.location.href = `{{ route("admin.bookings.manual.create") }}?check_in=${dateLabel}&room_number=${roomNumber}`;
}

function viewBookingDetails(id) {
    $('#daySummaryModal').modal('hide');
    setTimeout(() => showBookingDetails(id), 300);
}

function showBookingDetails(id) {
    const modal = $('#bookingDetailsModal');
    const content = $('#bookingDetailsContent');
    const editBtn = $('#editBookingBtn');
    
    content.html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2">Fetching booking details...</p></div>');
    modal.modal('show');

    fetch(`{{ route("admin.bookings.details", ["booking" => "__ID__"], false) }}`.replace("__ID__", id))
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw new Error(data.message);
            const b = data.booking;
            
            let statusBadge = `<span class="badge badge-${b.status === 'confirmed' ? 'success' : (b.status === 'pending' ? 'warning' : 'danger')}">${b.status.toUpperCase()}</span>`;
            let paymentBadge = `<span class="badge badge-${b.payment_status === 'paid' ? 'success' : (b.payment_status === 'partial' ? 'info' : 'warning')}">${b.payment_status.toUpperCase()}</span>`;
            let checkInBadge = `<span class="badge badge-${b.check_in_status === 'checked_in' ? 'danger' : (b.check_in_status === 'checked_out' ? 'secondary' : 'light')}">${b.check_in_status.replace('_', ' ').toUpperCase()}</span>`;

            let html = `
                <div class="row">
                    <div class="col-md-6 border-right">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary-light p-2 rounded mr-3"><i class="fa fa-user text-primary" style="width:20px; text-align:center"></i></div>
                            <div>
                                <h6 class="mb-0 font-weight-bold">Guest Information</h6>
                                <small class="text-muted">Primary Guest Details</small>
                            </div>
                        </div>
                        <table class="table table-sm table-borderless mt-2">
                            <tr><td class="text-muted" width="100">Name:</td><td class="font-weight-bold">${b.guest_name}</td></tr>
                            <tr><td class="text-muted">Email:</td><td>${b.guest_email}</td></tr>
                            <tr><td class="text-muted">Phone:</td><td>${b.guest_phone || 'N/A'}</td></tr>
                            <tr><td class="text-muted">Reference:</td><td><code class="text-primary font-weight-bold">${b.booking_reference}</code></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success-light p-2 rounded mr-3" style="background:rgba(40,167,69,0.1); color:#28a745"><i class="fa fa-bed" style="width:20px; text-align:center"></i></div>
                            <div>
                                <h6 class="mb-0 font-weight-bold">Room & Stay</h6>
                                <small class="text-muted">Booking period & Room #</small>
                            </div>
                        </div>
                        <table class="table table-sm table-borderless mt-2">
                            <tr><td class="text-muted" width="100">Room:</td><td class="font-weight-bold">#${b.room.room_number} (${b.room.room_type})</td></tr>
                            <tr><td class="text-muted">Check-in:</td><td class="text-success font-weight-bold">${b.check_in}</td></tr>
                            <tr><td class="text-muted">Check-out:</td><td class="text-danger font-weight-bold">${b.check_out}</td></tr>
                            <tr><td class="text-muted">Guests:</td><td>${b.number_of_guests} Person(s)</td></tr>
                        </table>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 font-weight-bold"><i class="fa fa-info-circle mr-2 text-primary"></i>Status & Billing</h6>
                            <h5 class="mb-0 text-primary font-weight-bold">$${parseFloat(b.total_price).toFixed(2)}</h5>
                        </div>
                        <div class="d-flex flex-wrap" style="gap:20px">
                            <div>
                                <small class="text-muted d-block mb-1">Booking Status</small>
                                ${statusBadge}
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1">Payment</small>
                                ${paymentBadge}
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1">Check-in Status</small>
                                ${checkInBadge}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            content.html(html);
            editBtn.removeClass('d-none').attr('onclick', `window.location.href='/admin/bookings/${id}/edit'`);
            document.getElementById('currentBookingId').value = id;
        })
        .catch(err => {
            content.html(`<div class="alert alert-danger p-4 text-center"><i class="fa fa-exclamation-circle fa-2x mb-2"></i><br>Error fetching details: ${err.message}</div>`);
        });
}

// Initialization
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var isMobile = window.innerWidth <= 767;
        var initialView = 'dayGridMonth';
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: initialView,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            firstDay: 1,
            height: 'auto',
            aspectRatio: isMobile ? 1.2 : 1.8,
            editable: false,
            dayMaxEvents: true,
            moreLinkClick: 'popover',
            eventDisplay: 'block',
            eventTextColor: '#ffffff',
            eventBorderColor: 'transparent',
            eventBackgroundColor: '#e77a3a',
            dayHeaderFormat: { weekday: 'short' },
            dateClick: function(info) {
                showDaySummary(info.dateStr);
            },
            events: @json($calendarEvents),
            eventClick: function(info) {
                const props = info.event.extendedProps;
                showBookingDetails(props.booking_id);
            },
            eventContent: function(arg) {
                var props = arg.event.extendedProps;
                var guestName = props.guest_name.length > 15 ? props.guest_name.substring(0, 15) + '...' : props.guest_name;
                
                return {
                    html: `<div class="p-1 px-2 rounded shadow-xs" style="background:${arg.event.backgroundColor}; font-size: 11px;">
                            <i class="fa fa-bed"></i> <b>R${props.room_number}</b> ${guestName}
                           </div>`
                };
            }
        });
        calendar.render();

        // Search Filter Logic
        document.getElementById('calendarSearch').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const events = calendar.getEvents();
            
            events.forEach(event => {
                const props = event.extendedProps;
                const roomMatches = (props.room_number || '').toString().includes(term);
                const guestMatches = (props.guest_name || '').toLowerCase().includes(term);
                const typeMatches = (props.room_type || '').toLowerCase().includes(term);
                const refMatches = (props.booking_reference || '').toLowerCase().includes(term);
                
                if (term === '' || roomMatches || guestMatches || typeMatches || refMatches) {
                    event.setProp('display', 'auto');
                } else {
                    event.setProp('display', 'none');
                }
            });
        });

        // Modal Room Filter
        const modalRoomFilter = document.getElementById('modalRoomFilter');
        if (modalRoomFilter) {
            modalRoomFilter.addEventListener('input', function(e) {
                renderRoomGrid(e.target.value);
            });
        }
    }
});
</script>
@endsection

