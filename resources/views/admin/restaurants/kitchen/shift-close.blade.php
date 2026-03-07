@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-sign-out"></i> Close Your Shift</h1>
    <p>Review and confirm before closing</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('chef-master.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Close Shift</li>
  </ul>
</div>

<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="tile shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">

      {{-- Header Banner --}}
      <div style="background: linear-gradient(135deg, #1a6e3c 0%, #145a32 100%); padding: 30px; color: #fff; text-align: center;">
        <i class="fa fa-cutlery fa-3x" style="opacity: 0.7; margin-bottom: 10px;"></i>
        <h3 class="mb-0" style="font-weight: 700; letter-spacing: 1px;">End Kitchen Shift</h3>
        <p class="mb-0" style="opacity: 0.85;">{{ $userName }}</p>
        <small style="opacity: 0.7;">Shift started: {{ $shift->opened_at->format('D, d M Y — H:i A') }}</small>
      </div>

      <div class="tile-body p-4">

        {{-- Shift Summary Stats --}}
        <div class="row text-center mb-4">
          <div class="col-12">
            <div class="p-3 rounded" style="background: #f8f9fa; border-left: 4px solid #28a745;">
              <h4 class="mb-1 text-success font-weight-bold">{{ $movementCount }}</h4>
              <small class="text-muted text-uppercase" style="letter-spacing: 1px;">Stock Movements This Shift</small>
            </div>
          </div>
        </div>

        <div class="alert alert-info py-3" style="border-radius: 10px; border: none; background: #e8f4fd;">
          <i class="fa fa-info-circle mr-2"></i>
          Closing this shift will <strong>generate your individual stock sheet</strong> — a printable document showing all inventory used, received, and remaining during your session. This will be submitted for manager sign-off.
        </div>

        {{-- Notes Input --}}
        <form action="{{ route('chef-master.shift.finalize') }}" method="POST" id="closeShiftForm">
          @csrf
          <div class="form-group mb-4">
            <label class="font-weight-bold text-muted small text-uppercase">Shift Notes / Handover Remarks <span class="text-muted">(Optional)</span></label>
            <textarea name="notes" class="form-control" rows="3"
              placeholder="E.g. Used extra flour for lunch rush, cold storage compressor issue reported. Passed duties to evening chef."
              style="border-radius: 8px; resize: none;"></textarea>
          </div>

          <div class="row">
            <div class="col-8">
              <button type="submit" class="btn btn-success btn-lg btn-block" style="border-radius: 10px; font-weight: 700; letter-spacing: 0.5px;" id="closeBtn">
                <i class="fa fa-print mr-2"></i> Close Shift & Print Stock Sheet
              </button>
            </div>
            <div class="col-4">
              <a href="{{ route('chef-master.dashboard') }}" class="btn btn-light btn-lg btn-block" style="border-radius: 10px; font-weight: 600; color: #666;">
                Cancel
              </a>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('closeShiftForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('closeBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Generating Stock Sheet...';
});
</script>
@endsection
