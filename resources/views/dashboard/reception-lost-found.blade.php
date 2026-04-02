@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-suitcase"></i> Lost & Found Management</h1>
    <p>Manage and track forgotten guest items across the hotel</p>
  </div>
  <ul class="app-breadcrumb breadcrumb d-none d-md-flex">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Lost & Found</li>
  </ul>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="fa fa-check-circle"></i> {{ session('success') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

<div class="row">
    <div class="col-md-12">
        <!-- Advanced Filter & Search Tile -->
        <div class="tile shadow-sm mb-4">
            <div class="tile-title-w-btn border-bottom pb-2 mb-3">
                <h3 class="title"><i class="fa fa-search"></i> Search & Filters</h3>
                <div class="btn-group d-none d-md-inline-block">
                    <!-- Placeholder for desktop actions -->
                </div>
            </div>
            <form action="{{ route('reception.lost-found.index') }}" method="GET" id="advancedFilterForm">
                <div class="row">
                    <div class="col-md-4 col-12 mb-2">
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold d-block d-md-none">Search Items</label>
                            <input type="text" name="search" id="realTimeSearch" class="form-control" placeholder="Search item, room, or guest..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="form-group mb-0">
                             <label class="small font-weight-bold d-block d-md-none">Status</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="found" {{ request('status') === 'found' ? 'selected' : '' }}>Found (Unclaimed)</option>
                                <option value="claimed" {{ request('status') === 'claimed' ? 'selected' : '' }}>Claimed</option>
                                <option value="disposed" {{ request('status') === 'disposed' ? 'selected' : '' }}>Disposed</option>
                                <option value="donated" {{ request('status') === 'donated' ? 'selected' : '' }}>Donated</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold d-block d-md-none">Period</label>
                            <select name="period" class="form-control" onchange="this.form.submit()">
                                <option value="">All Time</option>
                                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Last 30 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 col-12 text-center">
                        <button class="btn btn-primary btn-block d-none d-md-inline-block" type="submit"><i class="fa fa-filter"></i> Apply</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Desktop View (Standard Template Table) -->
        <div class="tile shadow-sm d-none d-lg-block">
            <div class="tile-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Item ID</th>
                                <th>Found At</th>
                                <th>Room</th>
                                <th>Item Name</th>
                                <th>Guest (Potential)</th>
                                <th>Status</th>
                                <th>Storage Location</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="lfTableBody">
                            @forelse($items as $item)
                            <tr class="{{ $item->status === 'found' ? 'table-warning-light' : '' }} search-row">
                                <td>LF-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $item->found_at->format('M d, Y H:i') }}</td>
                                <td><strong>Room {{ $item->room->room_number ?? 'N/A' }}</strong></td>
                                <td><strong>{{ $item->item_name }}</strong></td>
                                <td>{{ $item->guest_name ?? ($item->booking->guest_name ?? 'Unknown') }}</td>
                                <td>
                                    <span class="badge badge-{{ $item->status === 'found' ? 'warning' : ($item->status === 'claimed' ? 'success' : 'secondary') }}">
                                        {{ strtoupper($item->status) }}
                                    </span>
                                </td>
                                <td>{{ $item->storage_location ?? 'Not Stored' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info view-lf-btn" 
                                            onclick="openManageModal({{ $item->id }}, '{{ addslashes($item->item_name) }}', '{{ addslashes($item->description) }}', '{{ $item->room->room_number ?? 'N/A' }}', '{{ addslashes($item->guest_name ?? ($item->booking->guest_name ?? 'Unknown')) }}', '{{ $item->guest_phone ?? ($item->booking->guest_phone ?? 'N/A') }}', '{{ $item->found_at->format('M d, Y H:i') }}', '{{ $item->status }}', '{{ $item->storage_location }}', '{{ addslashes($item->reception_notes) }}', '{{ $item->image_path ? asset($item->image_path) : '' }}', '{{ $item->claimed_at ? $item->claimed_at->format('M d, Y H:i') : '' }}', '{{ addslashes($item->claimed_by_name) }}', '{{ $item->processor->name ?? '' }}', '{{ $item->booking->booking_reference ?? 'N/A' }}', '{{ $item->booking ? ($item->booking->check_in->format('M d') . ' - ' . $item->booking->check_out->format('M d, Y')) : 'N/A' }}')">
                                        <i class="fa fa-pencil"></i> Manage
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center py-5">No items found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mobile View (Built-in Design Cards/Tiles) -->
        <div class="d-block d-lg-none" id="mobileCardsContainer">
            @forelse($items as $item)
            <div class="tile shadow-sm mb-3 search-row p-3 border-left" style="border-left: 5px solid {{ $item->status === 'found' ? '#ffc107' : ($item->status === 'claimed' ? '#28a745' : '#6c757d') }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <small class="text-muted d-block">LF-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</small>
                        <h5 class="mb-0 font-weight-bold text-dark">{{ $item->item_name }}</h5>
                        <p class="mb-0 text-primary font-weight-bold">Room {{ $item->room->room_number ?? 'N/A' }}</p>
                    </div>
                    <span class="badge badge-{{ $item->status === 'found' ? 'warning' : ($item->status === 'claimed' ? 'success' : 'secondary') }}">
                        {{ strtoupper($item->status) }}
                    </span>
                </div>
                
                <div class="row align-items-center mb-0 mt-2 border-top pt-2">
                    <div class="col-8">
                        <p class="mb-0 small text-dark"><strong>Guest:</strong> {{ $item->guest_name ?? ($item->booking->guest_name ?? 'Unknown') }}</p>
                        <p class="mb-0 small text-muted"><i class="fa fa-calendar-check-o"></i> {{ $item->found_at->format('M d, H:i') }}</p>
                    </div>
                    <div class="col-4 text-right">
                        @if($item->image_path)
                            <img src="{{ asset($item->image_path) }}" class="rounded border shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded border d-inline-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                <i class="fa fa-camera"></i>
                            </div>
                        @endif
                    </div>
                </div>
                
                <button type="button" class="btn btn-primary btn-block btn-sm mt-3 font-weight-bold" 
                        onclick="openManageModal({{ $item->id }}, '{{ addslashes($item->item_name) }}', '{{ addslashes($item->description) }}', '{{ $item->room->room_number ?? 'N/A' }}', '{{ addslashes($item->guest_name ?? ($item->booking->guest_name ?? 'Unknown')) }}', '{{ $item->guest_phone ?? ($item->booking->guest_phone ?? 'N/A') }}', '{{ $item->found_at->format('M d, Y H:i') }}', '{{ $item->status }}', '{{ $item->storage_location }}', '{{ addslashes($item->reception_notes) }}', '{{ $item->image_path ? asset($item->image_path) : '' }}', '{{ $item->claimed_at ? $item->claimed_at->format('M d, Y H:i') : '' }}', '{{ addslashes($item->claimed_by_name) }}', '{{ $item->processor->name ?? '' }}', '{{ $item->booking->booking_reference ?? 'N/A' }}', '{{ $item->booking ? ($item->booking->check_in->format('M d') . ' - ' . $item->booking->check_out->format('M d, Y')) : 'N/A' }}')">
                    <i class="fa fa-cog mr-1"></i> MANAGE
                </button>
            </div>
            @empty
            <div class="tile text-center py-5">
                <i class="fa fa-search fa-3x text-muted mb-3"></i>
                <p class="text-muted">No items matching criteria</p>
            </div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $items->appends(request()->input())->links() }}
        </div>
    </div>
</div>

<!-- Modal for Managing Lost & Found Item -->
<div class="modal fade" id="manageLFModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold"><i class="fa fa-edit"></i> Manage Item: <span id="modal_lf_id"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="lfUpdateForm" action="" method="POST">
                @csrf
                <div class="modal-body text-dark font-weight-bold">
                    <div class="row">
                        <div class="col-md-5 col-12 text-center mb-3">
                            <div class="bg-light rounded p-2 border">
                                <img id="manage_modal_image" src="" class="img-fluid rounded shadow-sm mb-2" style="max-height: 250px; display: none;">
                                <div id="manage_no_image" class="py-5 text-muted border-dashed">
                                    <i class="fa fa-camera fa-4x mb-2"></i>
                                    <p>No photo provided</p>
                                </div>
                                <h4 id="manage_modal_item_name" class="font-weight-bold mt-2"></h4>
                                <span id="manage_modal_status_badge" class="badge"></span>
                            </div>
                        </div>
                        <div class="col-md-7 col-12">
                            <ul class="nav nav-tabs mb-2" id="modalTab" role="tablist">
                                <li class="nav-item"><a class="nav-link active py-2" id="found-tab" data-toggle="tab" href="#foundInfo" role="tab">ITEM INFO</a></li>
                                <li class="nav-item"><a class="nav-link py-2" id="booking-tab" data-toggle="tab" href="#bookingInfo" role="tab">GUEST INFO</a></li>
                            </ul>
                            <div class="tab-content border-0 p-0 pt-2" id="modalTabContent">
                                <div class="tab-pane fade show active" id="foundInfo">
                                    <table class="table table-sm table-borderless bg-white rounded">
                                        <tr><th width="40%" class="text-muted font-weight-bold">FOUND IN:</th><td id="manage_modal_room" class="font-weight-bold text-primary"></td></tr>
                                        <tr><th class="text-muted font-weight-bold">FOUND AT:</th><td id="manage_modal_date"></td></tr>
                                        <tr><th class="text-muted font-weight-bold">GUEST NAME:</th><td id="manage_modal_guest"></td></tr>
                                        <tr><th class="text-muted font-weight-bold">GUEST PHONE:</th><td id="manage_modal_phone"></td></tr>
                                    </table>
                                    <div class="p-2 bg-light rounded border text-muted small">
                                        <i class="fa fa-info-circle"></i> <strong>Description:</strong> <span id="manage_modal_desc"></span>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="bookingInfo">
                                    <table class="table table-sm table-bordered">
                                        <tr><th class="bg-light" width="40%">BOOKING REF:</th><td id="modal_booking_ref" class="text-info font-weight-bold"></td></tr>
                                        <tr><th class="bg-light">STAY PERIOD:</th><td id="modal_booking_stay"></td></tr>
                                    </table>
                                </div>
                            </div>

                            <div id="claimed_info_display" style="display:none" class="alert alert-success mt-3 py-2 border-success">
                                <h6 class="mb-1 small font-weight-bold"><i class="fa fa-check-circle"></i> Item Processed</h6>
                                <p class="mb-0 small"><strong>Date:</strong> <span id="claimed_at"></span></p>
                                <p class="mb-0 small"><strong>Received By:</strong> <span id="claimed_by"></span></p>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3 text-secondary"><i class="fa fa-cog"></i> Update Item Control</h5>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold mb-1">Status <span class="text-danger">*</span></label>
                                <select name="status" id="modal_status_select" class="form-control" required>
                                    <option value="found">Found / Reported</option>
                                    <option value="claimed">Claimed</option>
                                    <option value="disposed">Disposed</option>
                                    <option value="donated">Donated</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold mb-1">Storage Location</label>
                                <input type="text" name="storage_location" id="modal_storage_input" class="form-control" placeholder="Locker or shelf #">
                            </div>
                        </div>
                    </div>
                    <div id="claim_group" class="form-group mb-3 mt-2" style="display:none">
                        <label class="small font-weight-bold mb-1 text-success">Receiver Full Name</label>
                        <input type="text" name="claimed_by_name" id="modal_claimed_by_input" class="form-control border-success" placeholder="Identify who claims the item">
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold mb-1">Reception Notes (Internal)</label>
                        <textarea name="reception_notes" id="modal_notes_input" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" id="update_btn"><i class="fa fa-save mr-1"></i> SAVE CHANGES</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .table-warning-light { background-color: #fff9e6; }
    .badge { padding: 5px 10px; font-weight: 700; font-size: 10px; text-transform: uppercase; }
    .nav-tabs .nav-link { font-weight: bold; padding: 10px 15px; color: #666; }
    .nav-tabs .nav-link.active { color: #009688; border-bottom: 2px solid #009688; }
</style>
@endsection

@section('scripts')
<script>
    function openManageModal(id, name, desc, room, guest, phone, date, status, storage, notes, image, claimedAt, claimedBy, processedBy, bookingRef, bookingStay) {
        const formAction = "{{ url('reception/lost-found') }}/" + id + "/update";
        $('#lfUpdateForm').attr('action', formAction);
        
        $('#modal_lf_id').text('LF-' + id.toString().padStart(5, '0'));
        $('#manage_modal_item_name').text(name);
        $('#manage_modal_desc').text(desc);
        $('#manage_modal_room').text('Room ' + room);
        if(room == 'N/A') $('#manage_modal_room').text('Public Area');
        
        $('#manage_modal_date').text(date);
        $('#manage_modal_guest').text(guest);
        $('#manage_modal_phone').text(phone);
        $('#modal_status_select').val(status);
        $('#modal_storage_input').val(storage);
        $('#modal_notes_input').val(notes);
        $('#modal_claimed_by_input').val(claimedBy || guest);
        
        $('#modal_booking_ref').text(bookingRef);
        $('#modal_booking_stay').text(bookingStay);

        const statusBadge = $('#manage_modal_status_badge');
        statusBadge.text(status.toUpperCase());
        statusBadge.removeClass('badge-warning badge-success badge-secondary');
        if (status === 'found') {
            statusBadge.addClass('badge-warning');
            $('#update_btn').show();
        } else if (status === 'claimed') {
            statusBadge.addClass('badge-success');
            $('#update_btn').hide(); 
        } else {
            statusBadge.addClass('badge-secondary');
            $('#update_btn').show();
        }

        if (image) {
            $('#manage_modal_image').attr('src', image).show();
            $('#manage_no_image').hide();
        } else {
            $('#manage_modal_image').hide();
            $('#manage_no_image').show();
        }

        if (status === 'claimed') {
            $('#claimed_at').text(claimedAt);
            $('#claimed_by').text(claimedBy);
            $('#claimed_info_display').fadeIn();
            $('#claim_group').hide();
            $('#modal_status_select').prop('disabled', true);
        } else {
            $('#claimed_info_display').hide();
            if (status === 'found') $('#claim_group').hide();
            $('#modal_status_select').prop('disabled', false);
        }

        $('#manageLFModal').modal('show');
        $('#found-tab').tab('show');
    }

    $(document).ready(function() {
        $("#realTimeSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".search-row").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('#modal_status_select').on('change', function() {
            if ($(this).val() === 'claimed') {
                $('#claim_group').fadeIn();
            } else {
                $('#claim_group').fadeOut();
            }
        });
    });
</script>
@endsection
