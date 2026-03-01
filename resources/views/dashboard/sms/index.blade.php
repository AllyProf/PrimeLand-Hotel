@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-envelope-o"></i> Messaging Center</h1>
        <p>Manage hotel SMS notifications, view logs, and check balance</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item active">Messaging Center</li>
    </ul>
</div>

<div class="row">
    <!-- Detailed Stats Cards -->
    <div class="col-md-6 col-lg-3">
        <div class="widget-small primary coloured-icon shadow-sm">
            <i class="icon fa fa-credit-card fa-3x"></i>
            <div class="info">
                <h4>Balance</h4>
                <p><b>{{ $balance }}</b></p>
                <small class="text-muted">Est. {{ $smsCount ?? 0 }} units</small>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="widget-small info coloured-icon shadow-sm">
            <i class="icon fa fa-paper-plane-o fa-3x"></i>
            <div class="info">
                <h4>Today</h4>
                <p><b>{{ $todayCount }}</b></p>
                <small class="text-muted">Sent today</small>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="widget-small success coloured-icon shadow-sm">
            <i class="icon fa fa-check-circle fa-3x"></i>
            <div class="info">
                <h4>Lifetime Sent</h4>
                <p><b>{{ $totalSent }}</b></p>
                <small class="text-muted">All successful</small>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="widget-small danger coloured-icon shadow-sm">
            <i class="icon fa fa-exclamation-triangle fa-3x"></i>
            <div class="info">
                <h4>Failed</h4>
                <p><b>{{ $failedCount }}</b></p>
                <small class="text-muted">Requires retry</small>
            </div>
        </div>
    </div>
</div>

<!-- Main UI Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="tile shadow-sm">
            <div class="tile-title-w-btn mb-4">
                <div>
                    <h3 class="title"><i class="fa fa-history"></i> Transmission Log</h3>
                    <p class="text-muted small mb-0">Record of all manual and automated SMS communications</p>
                </div>
                <div class="btn-group border rounded shadow-sm overflow-hidden">
                    <button class="btn btn-success px-3" data-toggle="modal" data-target="#sendSmsModal" title="Quick SMS">
                        <i class="fa fa-envelope mr-1"></i> Quick SMS
                    </button>
                    <button class="btn btn-primary px-3" data-toggle="modal" data-target="#bulkSmsModal" title="Bulk Campaign">
                        <i class="fa fa-users mr-1"></i> Bulk Campaign
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th>DateTime</th>
                            <th>Sender</th>
                            <th>Recipient</th>
                            <th>Message Snippet</th>
                            <th>Units</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <span class="d-block font-weight-bold">{{ $log->created_at->format('M d, Y') }}</span>
                                <small class="text-muted">{{ $log->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                @if($log->sender)
                                    <span class="badge badge-light p-2 border"><i class="fa fa-user mr-1"></i> {{ $log->sender->name }}</span>
                                @else
                                    <span class="text-muted italic">System</span>
                                @endif
                            </td>
                            <td>
                                <strong class="d-block text-primary">{{ $log->recipient }}</strong>
                                @if($log->target_id)
                                    <small class="badge badge-secondary py-0">Stored Contact</small>
                                @endif
                            </td>
                            <td title="{{ $log->message }}">
                                {{ Str::limit($log->message, 45) }}
                            </td>
                            <td><span class="badge badge-pill badge-info px-2">{{ $log->sms_count }}</span></td>
                            <td class="text-center">
                                @if($log->status === 'sent')
                                    <span class="badge badge-success"><i class="fa fa-check mr-1"></i> Delivered</span>
                                @else
                                    <span class="badge badge-danger"><i class="fa fa-times mr-1"></i> {{ ucfirst($log->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                    <p>No communication logs found in the local database.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 d-flex justify-content-center">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Bulk SMS Modal -->
<div class="modal fade" id="bulkSmsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-lg" style="border-radius: 12px; border: none;">
            <div class="modal-header bg-primary text-white" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title"><i class="fa fa-bullhorn"></i> New Sales/Announcement Campaign</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route($role . '.sms.bulk-send') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold"><i class="fa fa-crosshairs"></i> Select Target Group</label>
                        <div class="row no-gutters text-center" style="border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden;">
                            <div class="col">
                                <label class="m-0 p-3 target-btn active" style="width:100%; cursor:pointer;">
                                    <input type="radio" name="target" value="active" class="d-none" checked onchange="handleTargetUpdate('active')">
                                    <div class="h5 m-0 fa fa-hotel"></div>
                                    <small class="d-block mt-1 font-weight-bold">In-House Guests ({{ $activeGuestsCount }})</small>
                                </label>
                            </div>
                            <div class="col">
                                <label class="m-0 p-3 target-btn" style="width:100%; cursor:pointer; border-left: 1px solid #dee2e6;">
                                    <input type="radio" name="target" value="all" class="d-none" onchange="handleTargetUpdate('all')">
                                    <div class="h5 m-0 fa fa-address-book text-warning"></div>
                                    <small class="d-block mt-1 font-weight-bold">All Guests ({{ $totalGuestsCount }})</small>
                                </label>
                            </div>
                            <div class="col">
                                <label class="m-0 p-3 target-btn" style="width:100%; cursor:pointer; border-left: 1px solid #dee2e6;">
                                    <input type="radio" name="target" value="all_staff" class="d-none" onchange="handleTargetUpdate('all_staff')">
                                    <div class="h5 m-0 fa fa-user-secret text-info"></div>
                                    <small class="d-block mt-1 font-weight-bold">Only Staff ({{ $staffCount }})</small>
                                </label>
                            </div>
                            <div class="col">
                                <label class="m-0 p-3 target-btn" style="width:100%; cursor:pointer; border-left: 1px solid #dee2e6;">
                                    <input type="radio" name="target" value="all_everyone" class="d-none" onchange="handleTargetUpdate('all_everyone')">
                                    <div class="h5 m-0 fa fa-globe text-primary"></div>
                                    <small class="d-block mt-1 font-weight-bold">Everyone ({{ $totalGuestsCount + $staffCount }})</small>
                                </label>
                            </div>
                            <div class="col">
                                <label class="m-0 p-3 target-btn" style="width:100%; cursor:pointer; border-left: 1px solid #dee2e6;">
                                    <input type="radio" name="target" value="specific" class="d-none" onchange="handleTargetUpdate('specific')">
                                    <div class="h5 m-0 fa fa-check-square-o text-success"></div>
                                    <small class="d-block mt-1 font-weight-bold">Specific People</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Recipient Selection Area (Hidden by default) -->
                    <div id="recipientSelectionArea" style="display: none; border: 1px solid #eee; padding: 20px; border-radius: 10px; margin-bottom: 25px; background: #fafafa;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0 font-weight-bold"><i class="fa fa-search"></i> Select Specific Recipients</h6>
                            <small class="badge badge-info shadow-sm" id="selectedBadge">0 Selected</small>
                        </div>
                        <div class="input-group mb-3 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fa fa-search text-muted"></i></span>
                            </div>
                            <input type="text" id="recipientSearch" class="form-control border-left-0" placeholder="Search by name, phone, or type (Guest/Staff)..." onkeyup="filterRecipients()" style="border-radius: 0 20px 20px 0;">
                        </div>
                        
                        <div style="max-height: 280px; overflow-y: auto; border: 1px solid #efefef; background: white; padding: 0; border-radius: 6px;">
                            <div class="p-2 border-bottom d-flex align-items-center justify-content-between sticky-top bg-light">
                                <div class="custom-control custom-checkbox ml-2">
                                    <input type="checkbox" class="custom-control-input" id="selectAllItems" onclick="toggleAllRecipients(this)">
                                    <label class="custom-control-label font-weight-bold" for="selectAllItems">Select Visible List</label>
                                </div>
                                <span class="badge badge-secondary mr-2" id="visibleCount">Show all: {{ count($allGuests) + count($allStaff) }}</span>
                            </div>
                            
                            <div id="recipientListItems">
                                <!-- Staff Group -->
                                @if(count($allStaff) > 0)
                                <div class="px-3 py-1 bg-light text-muted font-weight-bold" style="font-size: 11px; letter-spacing: 1px;">STAFF MEMBERS</div>
                                @foreach($allStaff as $staff)
                                <div class="recipient-item border-bottom px-3 py-2 d-flex align-items-center justify-content-between" style="transition: all 0.2s; cursor: pointer;" onclick="toggleCheckbox('staff_{{ $staff->id }}')">
                                    <div class="custom-control custom-checkbox pointer-events-none">
                                        <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}" class="custom-control-input recipient-checkbox" id="staff_{{ $staff->id }}" onclick="event.stopPropagation()">
                                        <label class="custom-control-label" for="staff_{{ $staff->id }}">
                                            <span class="font-weight-bold">{{ $staff->name }}</span> <br>
                                            <small class="text-muted"><i class="fa fa-phone"></i> {{ $staff->phone }}</small>
                                        </label>
                                    </div>
                                    <span class="badge badge-pill badge-outline-info" style="font-size: 9px; border: 1px solid #17a2b8; color: #17a2b8;">STAFF</span>
                                </div>
                                @endforeach
                                @endif

                                <!-- Guest Group -->
                                <div class="px-3 py-1 bg-light text-muted font-weight-bold" style="font-size: 11px; letter-spacing: 1px;">GUESTS</div>
                                @foreach($allGuests as $guest)
                                <div class="recipient-item border-bottom px-3 py-2 d-flex align-items-center justify-content-between" style="transition: all 0.2s; cursor: pointer;" onclick="toggleCheckbox('guest_{{ $guest->id }}')">
                                    <div class="custom-control custom-checkbox pointer-events-none">
                                        <input type="checkbox" name="guest_ids[]" value="{{ $guest->id }}" class="custom-control-input recipient-checkbox" id="guest_{{ $guest->id }}" onclick="event.stopPropagation()">
                                        <label class="custom-control-label" for="guest_{{ $guest->id }}">
                                            <span class="font-weight-bold">{{ $guest->name }}</span> <br>
                                            <small class="text-muted"><i class="fa fa-phone"></i> {{ $guest->phone }}</small>
                                        </label>
                                    </div>
                                    <span class="badge badge-pill badge-outline-secondary" style="font-size: 9px; border: 1px solid #6c757d; color: #6c757d;">GUEST</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold"><i class="fa fa-pencil"></i> Campaign Message</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Dear [Name], Welcome to PrimeLand... (Note: system auto-tags are not active yet)" style="border-radius: 8px; border: 1px solid #dee2e6;" required></textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted" id="charCount">0 characters</small>
                            <small class="text-info font-italic">SMS will be sent to all selected recipients immediately.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5" style="border-radius: 8px; font-weight: 700;" onclick="return confirm('Launch this SMS campaign?')"><i class="fa fa-send"></i> Launch Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send SMS Modal -->
<div class="modal fade" id="sendSmsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-lg" style="border-radius: 12px; border: none;">
            <div class="modal-header bg-success text-white" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title"><i class="fa fa-paper-plane"></i> Quick Direct Message</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route($role . '.sms.send') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Recipient Phone Number</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-phone"></i></span>
                            </div>
                            <input type="text" name="phone" class="form-control" placeholder="e.g. 255712345678" style="font-size: 1.1rem; letter-spacing: 1px;" required>
                        </div>
                        <small class="text-muted"><i class="fa fa-info-circle"></i> Use international format without + (e.g., 255... for Tanzania)</small>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Message Content</label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Type your personal message here..." style="border-radius: 8px;" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4" style="border-radius: 8px; font-weight: 600;">Send SMS Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.target-btn.active {
    background: #e7f1ff;
    border: 1px solid #007bff !important;
    color: #007bff;
}
.target-btn:hover:not(.active) {
    background: #f8f9fa;
}
.recipient-item:hover {
    background-color: #f0f7ff;
}
.badge-outline-info { border: 1px solid #17a2b8; color: #17a2b8; background: transparent; }
.badge-outline-secondary { border: 1px solid #6c757d; color: #6c757d; background: transparent; }
</style>

<script>
function handleTargetUpdate(val) {
    // UI Update
    document.querySelectorAll('.target-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.querySelector('input').value === val) {
            btn.classList.add('active');
        }
    });

    // Content Display
    const area = document.getElementById('recipientSelectionArea');
    area.style.display = (val === 'specific') ? 'block' : 'none';
}

function toggleCheckbox(id) {
    const cb = document.getElementById(id);
    if(cb) {
        cb.checked = !cb.checked;
        updateSelectedCount();
    }
}

function filterRecipients() {
    const search = document.getElementById('recipientSearch').value.toLowerCase();
    const items = document.querySelectorAll('.recipient-item');
    let visibleCount = 0;
    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        const matches = text.includes(search);
        item.style.display = matches ? 'flex' : 'none';
        if (matches) visibleCount++;
    });
    document.getElementById('visibleCount').innerText = `Matches: ${visibleCount}`;
}

function toggleAllRecipients(source) {
    const checkboxes = document.querySelectorAll('.recipient-checkbox');
    checkboxes.forEach(cb => {
        const item = cb.closest('.recipient-item');
        if (item.style.display !== 'none') {
            cb.checked = source.checked;
        }
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.recipient-checkbox:checked').length;
    document.getElementById('selectedBadge').innerText = `${count} Selected`;
}

// Character counter
document.querySelector('textarea[name="message"]').addEventListener('input', function() {
    document.getElementById('charCount').innerText = `${this.value.length} characters (${Math.ceil(this.value.length / 160)} SMS units)`;
});
</script>

@endsection

