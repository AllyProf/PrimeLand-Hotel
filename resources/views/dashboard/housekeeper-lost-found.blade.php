@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-list"></i> My Reported Items</h1>
    <p>List of items you found and reported to reception</p>
  </div>
  <div class="tile-footer d-none d-md-block">
    <a href="{{ route('housekeeper.lost-found.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> New Report</a>
  </div>
  <ul class="app-breadcrumb breadcrumb d-none d-md-flex">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('housekeeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">My Items</li>
  </ul>
</div>

<!-- Mobile Quick Action Button -->
<div class="d-block d-md-none mb-3">
    <a href="{{ route('housekeeper.lost-found.create') }}" class="btn btn-primary btn-block py-3 font-weight-bold shadow-sm" style="border-radius: 10px;">
        <i class="fa fa-plus-circle"></i> SUBMIT NEW REPORT
    </a>
</div>

<div class="row">
  <div class="col-md-12">
    <!-- Success Alert -->
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    <!-- Search Tile -->
    <div class="tile shadow-sm mb-3">
        <div class="form-group mb-0">
            <input type="text" id="housekeeperSearch" class="form-control" placeholder="Search my reports by item, room, or guest...">
        </div>
    </div>

    <!-- Desktop View (Template Responsive Table) -->
    <div class="tile shadow-sm d-none d-lg-block">
      <div class="tile-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-bordered mb-0">
            <thead class="bg-light">
              <tr>
                <th>Found At</th>
                <th>Room</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Guest</th>
                <th>Status</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody id="desktopTableBody">
              @forelse($items as $item)
              <tr class="search-row">
                <td>{{ $item->found_at->format('M d, Y H:i') }}</td>
                <td><strong>Room {{ $item->room->room_number ?? 'N/A' }}</strong></td>
                <td><strong>{{ $item->item_name }}</strong></td>
                <td>{{ Str::limit($item->description, 50) }}</td>
                <td>{{ $item->guest_name ?? ($item->booking->guest_name ?? 'Unknown') }}</td>
                <td>
                  <span class="badge badge-{{ $item->status === 'found' ? 'warning' : ($item->status === 'claimed' ? 'success' : 'secondary') }}">
                    {{ strtoupper($item->status === 'found' ? 'Reported' : $item->status) }}
                  </span>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-info view-item-btn" 
                          data-id="{{ $item->id }}" 
                          data-name="{{ $item->item_name }}"
                          data-desc="{{ $item->description }}"
                          data-room="{{ $item->room->room_number ?? 'N/A' }}"
                          data-guest="{{ $item->guest_name ?? ($item->booking->guest_name ?? 'Unknown') }}"
                          data-date="{{ $item->found_at->format('M d, Y H:i') }}"
                          data-status="{{ ucfirst($item->status === 'found' ? 'Reported' : $item->status) }}"
                          data-image="{{ $item->image_path ? asset($item->image_path) : '' }}">
                    <i class="fa fa-eye"></i> View Detail
                  </button>
                </td>
              </tr>
              @empty
              <tr><td colspan="7" class="text-center py-5">No items reported yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Mobile View (Built-in Design Tiles as Cards) -->
    <div class="d-block d-lg-none" id="mobileCardsContainer">
        @forelse($items as $item)
        <div class="tile shadow-sm mb-3 search-row p-3 border-left" style="border-left: 5px solid {{ $item->status === 'found' ? '#ffc107' : ($item->status === 'claimed' ? '#28a745' : '#6c757d') }}">
            <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                <div>
                    <h5 class="mb-0 font-weight-bold text-dark">{{ $item->item_name }}</h5>
                    <span class="text-primary font-weight-bold small">Room {{ $item->room->room_number ?? 'N/A' }}</span>
                </div>
                <span class="badge badge-{{ $item->status === 'found' ? 'warning' : ($item->status === 'claimed' ? 'success' : 'secondary') }}">
                   {{ strtoupper($item->status === 'found' ? 'REPORTED' : $item->status) }}
                </span>
            </div>
            
            <div class="row align-items-center mb-3">
                <div class="col-8">
                    <p class="mb-0 small text-muted"><i class="fa fa-calendar-o"></i> {{ $item->found_at->format('M d, H:i') }}</p>
                    <p class="mb-0 small text-dark"><strong>Guest:</strong> {{ Str::limit($item->guest_name ?? ($item->booking->guest_name ?? 'Unknown'), 20) }}</p>
                </div>
                <div class="col-4 text-right">
                    @if($item->image_path)
                        <img src="{{ asset($item->image_path) }}" class="rounded shadow-sm border" style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded border d-inline-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                            <i class="fa fa-camera"></i>
                        </div>
                    @endif
                </div>
            </div>

            <button type="button" class="btn btn-outline-info btn-block btn-sm py-2 font-weight-bold view-item-btn" 
                    data-id="{{ $item->id }}" 
                    data-name="{{ $item->item_name }}"
                    data-desc="{{ $item->description }}"
                    data-room="{{ $item->room->room_number ?? 'N/A' }}"
                    data-guest="{{ $item->guest_name ?? ($item->booking->guest_name ?? 'Unknown') }}"
                    data-date="{{ $item->found_at->format('M d, Y H:i') }}"
                    data-status="{{ ucfirst($item->status === 'found' ? 'Reported' : $item->status) }}"
                    data-image="{{ $item->image_path ? asset($item->image_path) : '' }}">
                <i class="fa fa-eye-slash mr-1"></i> VIEW REPORT DETAIL
            </button>
        </div>
        @empty
        <div class="tile text-center py-5">
            <i class="fa fa-search fa-3x text-muted mb-3"></i>
            <p class="text-muted">No items reported.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-3">
      {{ $items->links() }}
    </div>
  </div>
</div>

<!-- View Item Modal -->
<div class="modal fade" id="viewItemModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title font-weight-bold"><i class="fa fa-id-card-o"></i> My Submission Detail</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-dark font-weight-bold">
        <div class="row">
          <div class="col-lg-5 col-12 text-center mb-3">
            <div id="modal_image_container" class="mb-2" style="display:none">
              <img id="modal_image" src="" alt="Item Image" class="img-fluid rounded shadow-sm border" style="max-height: 250px;">
            </div>
            <div id="no_image_placeholder" class="py-5 text-muted border-dashed bg-light rounded">
              <i class="fa fa-camera-retro fa-4x mb-2 opacity-25"></i>
              <p class="small">No Photo</p>
            </div>
            <h4 id="modal_item_name" class="font-weight-bold mt-2"></h4>
            <span id="modal_status" class="badge"></span>
          </div>
          <div class="col-lg-7 col-12">
            <table class="table table-sm table-bordered bg-light">
              <tr><th width="40%" class="bg-white">FOUND IN</th><td id="modal_room" class="font-weight-bold text-info"></td></tr>
              <tr><th class="bg-white">DATE/TIME</th><td id="modal_date" class="small"></td></tr>
              <tr><th class="bg-white">GUEST</th><td id="modal_guest" class="small"></td></tr>
            </table>
            
            <div class="group-notes mt-3">
                <label class="text-muted small font-weight-bold uppercase">MY DESCRIPTION / NOTES</label>
                <p id="modal_desc" class="p-3 bg-white border rounded text-dark" style="min-height: 80px;"></p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#housekeeperSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".search-row").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        @if(session('success'))
            swal({
                title: "Success",
                text: "{{ session('success') }}",
                type: "success",
                timer: 4000,
                showConfirmButton: false
            });
        @endif

        $('.view-item-btn').on('click', function() {
            const data = $(this).data();
            $('#modal_item_name').text(data.name);
            $('#modal_desc').text(data.desc);
            $('#modal_room').text('Room ' + data.room);
            $('#modal_guest').text(data.guest);
            $('#modal_date').text(data.date);
            
            const statusBadge = $('#modal_status');
            statusBadge.text(data.status.toUpperCase());
            statusBadge.removeClass('badge-warning badge-success badge-secondary');
            if (data.status === 'Reported' || data.status === 'Found') {
                statusBadge.addClass('badge-warning');
            } else if (data.status === 'Claimed') {
                statusBadge.addClass('badge-success');
            } else {
                statusBadge.addClass('badge-secondary');
            }

            if (data.image) {
                $('#modal_image').attr('src', data.image);
                $('#modal_image_container').show();
                $('#no_image_placeholder').hide();
            } else {
                $('#modal_image_container').hide();
                $('#no_image_placeholder').show();
            }

            $('#viewItemModal').modal('show');
        });
    });
</script>
@endsection
