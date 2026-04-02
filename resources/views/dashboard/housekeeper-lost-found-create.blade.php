@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-suitcase"></i> Report Lost Item</h1>
    <p>Report an item forgotten by a guest</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('housekeeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('housekeeper.lost-found.index') }}">Lost & Found</a></li>
    <li class="breadcrumb-item">Report Item</li>
  </ul>
</div>

<div class="row">
  <div class="col-md-10 mx-auto">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-plus-circle"></i> Item Details</h3>
      <div class="tile-body">
        <form action="{{ route('housekeeper.lost-found.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label font-weight-bold">Room (Optional)</label>
                <select name="room_id" class="form-control @error('room_id') is-invalid @enderror">
                  <option value="">-- Select Room if Found in Room --</option>
                  @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ (isset($selectedRoomId) && $selectedRoomId == $room->id) || old('room_id') == $room->id ? 'selected' : '' }}>
                      Room {{ $room->room_number }} ({{ $room->room_type }})
                    </option>
                  @endforeach
                </select>
                @error('room_id')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <small class="form-text text-muted">Guest info will be auto-detected based on room selection.</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label font-weight-bold">Item Name <span class="text-danger">*</span></label>
                <input type="text" name="item_name" class="form-control @error('item_name') is-invalid @enderror" value="{{ old('item_name') }}" placeholder="e.g. iPhone, Watch, Bag..." required>
                @error('item_name')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label font-weight-bold">Description <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Describe the item (color, brand, distinguishing marks)..." required>{{ old('description') }}</textarea>
            @error('description')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label font-weight-bold">Location Found</label>
                <input type="text" name="location_found" class="form-control" value="{{ old('location_found') }}" placeholder="e.g. Under the bed, Bathroom, Lobby...">
                <small class="form-text text-muted">Leave empty if it corresponds to the room selected above.</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label font-weight-bold">Date & Time Found <span class="text-danger">*</span></label>
                <input type="datetime-local" name="found_at" class="form-control @error('found_at') is-invalid @enderror" value="{{ old('found_at', now()->format('Y-m-d\TH:i')) }}" required>
                @error('found_at')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label font-weight-bold">Item Image</label>
                <div class="custom-file">
                  <input type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="customFile">
                  <label class="custom-file-label" for="customFile">Choose image...</label>
                </div>
                <small class="form-text text-muted">Upload a photo of the item (Max 5MB).</small>
                @error('image')
                  <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>

          <div class="tile-footer">
            <button class="btn btn-primary" type="submit"><i class="fa fa-paper-plane"></i> Report to Reception</button>
            <a href="{{ route('housekeeper.lost-found.index') }}" class="btn btn-secondary"><i class="fa fa-times"></i> Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $('.custom-file-input').on('change', function() {
    let fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
  });
</script>
@endsection
