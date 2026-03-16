@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-file-text-o"></i> Create Quick Invoice</h1>
    <p>Send a fast, professional quote to a guest or company</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active"><a href="#">Create Invoice</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-12">
    <form id="quickInvoiceForm" method="POST" action="{{ $role === 'reception' ? route('reception.invoices.store') : route('admin.invoices.store') }}">
      @csrf
      <input type="hidden" name="invoice_type" id="invoice_type" value="individual">
      <input type="hidden" name="company_id" id="company_id">
      <input type="hidden" id="room_id" name="room_id">

      <div class="row">
        <!-- LEFT COLUMN: INPUT FORM -->
        <div class="col-lg-7">
          <div class="tile shadow-sm pb-4" style="border-radius: 12px; border: 1px solid #ebedf2;">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
              <h5 class="mb-0 text-dark font-weight-bold">Guest Details</h5>
              <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-outline-primary active btn-sm px-3" onclick="toggleInvoiceType('individual')">
                  <input type="radio" name="invoice_type_toggle" checked> Individual
                </label>
                <label class="btn btn-outline-primary btn-sm px-3" onclick="toggleInvoiceType('corporate')">
                  <input type="radio" name="invoice_type_toggle"> Corporate
                </label>
              </div>
            </div>

            <!-- Individual Guest Section -->
            <div id="individual_section">
              <div class="form-group position-relative">
                <label class="font-weight-bold text-muted small text-uppercase">Search Returning Guest</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-search text-muted"></i></span>
                  </div>
                  <input type="text" id="guestSearchInput" class="form-control border-left-0 bg-light" placeholder="Phone, email or name..." autocomplete="off">
                </div>
                <div id="guestSearchResults" class="list-group position-absolute w-100 shadow-sm" style="display: none; z-index: 1050; max-height: 200px; overflow-y: auto;"></div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="guest_name" name="guest_name" placeholder="John Doe">
                  </div>
                </div>
              </div>
            </div>

            <!-- Corporate Section -->
            <div id="corporate_section" style="display: none;">
              <div class="form-group position-relative">
                <label class="font-weight-bold text-muted small text-uppercase">Search Existing Company</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-search text-muted"></i></span>
                  </div>
                  <input type="text" id="companySearchInput" class="form-control border-left-0 bg-light" placeholder="Company name or email..." autocomplete="off">
                </div>
                <div id="companySearchResults" class="list-group position-absolute w-100 shadow-sm" style="display: none; z-index: 1050; max-height: 200px; overflow-y: auto;"></div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="font-weight-bold">Company Name <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="company_name" name="company_name" placeholder="Acme Corp">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="font-weight-bold">Lead Guest (Optional)</label>
                    <input class="form-control" type="text" id="corporate_guest_name" name="corporate_guest_name" placeholder="Contact person">
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="font-weight-bold">Guest Nationality <span class="text-danger">*</span></label>
                  <div class="d-flex mt-1">
                    <div class="custom-control custom-radio mr-4">
                      <input type="radio" id="nat_international" name="guest_type" class="custom-control-input" value="international" checked onchange="handleNationalityChange()">
                      <label class="custom-control-label" for="nat_international">International</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" id="nat_tanzanian" name="guest_type" class="custom-control-input" value="tanzanian" onchange="handleNationalityChange()">
                      <label class="custom-control-label" for="nat_tanzanian">Tanzanian</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <!-- Empty or potentially other field -->
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="font-weight-bold">Email Address <span class="text-danger">*</span></label>
                  <input class="form-control" type="email" id="guest_email" name="guest_email" placeholder="example@email.com" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="font-weight-bold">Phone Number <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-light border-right-0">+</span>
                    </div>
                    <input class="form-control border-left-0" type="text" id="guest_phone" name="guest_phone" value="255" placeholder="255700000000" required>
                  </div>
                </div>
              </div>
            </div>

            <h5 class="mb-4 text-dark font-weight-bold mt-4 border-bottom pb-3">Stay Details</h5>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="font-weight-bold">Check-in <span class="text-danger">*</span></label>
                  <input class="form-control" type="date" id="check_in" name="check_in" value="{{ date('Y-m-d') }}" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="font-weight-bold">Check-out <span class="text-danger">*</span></label>
                  <input class="form-control" type="date" id="check_out" name="check_out" value="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-8">
                <div class="form-group">
                  <label class="font-weight-bold">Room Type <span class="text-danger">*</span></label>
                  <select class="form-control" id="room_type" name="room_type" required>
                    <option value="">-- Select Room Type --</option>
                    @foreach($roomTypes as $type)
                      <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="font-weight-bold">Rooms <span class="text-danger">*</span></label>
                  <input class="form-control" type="number" id="number_of_rooms" name="number_of_rooms" min="1" value="1" required>
                </div>
              </div>
            </div>
            
            <div id="rooms_loading" style="display: none;" class="text-center py-2"><i class="fa fa-spinner fa-spin text-primary"></i> <small>Finding rooms...</small></div>
            <div id="availability_status" class="mb-3 small font-weight-bold"></div>

            <div class="form-group mt-3">
              <label class="font-weight-bold">Invoice Notes (Optional)</label>
              <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Ex: Airport pickup included"></textarea>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: LIVE SUMMARY & PRICING -->
        <div class="col-lg-5">
          <div class="tile sticky-top shadow-sm" style="top: 80px; border-radius: 12px; background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%); border: 1px solid #dee2e6;">
            <h5 class="font-weight-bold border-bottom pb-3 mb-4">Invoice Summary</h5>
            
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted"><i class="fa fa-moon-o"></i> Duration:</span>
              <span class="font-weight-bold text-dark"><span id="summary_nights">1</span> Night(s)</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted"><i class="fa fa-bed"></i> Rooms:</span>
              <span class="font-weight-bold text-dark"><span id="summary_rooms">1</span> Room(s)</span>
            </div>
            <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
              <span class="text-muted"><i class="fa fa-tag"></i> Base Rate:</span>
              <span class="font-weight-bold text-dark"><span id="summary_currency_symbol">$</span><span id="summary_base_rate">0.00</span> /night</span>
            </div>

            <!-- HIDDEN USD FIELD FOR SERVER -->
            <input type="hidden" id="real_total_price_usd" name="total_price">

            <div class="form-group">
              <label class="font-weight-bold text-primary">Final Total (<span id="input_currency_label">USD</span>) <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text bg-primary text-white border-0" id="input_currency_symbol">$</span></div>
                <input class="form-control form-control-lg font-weight-bold" style="font-size: 1.5rem; color: #333; border-color: #007bff;" type="number" id="display_total_price" step="0.01" min="0" placeholder="0.00" required>
              </div>
            </div>

            <div class="card bg-light border-0 mb-4 p-3 rounded">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 font-weight-bold text-muted small text-uppercase" id="conversion_card_title">Local Value (TZS)</h6>
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="overrideExchangeRate">
                  <label class="custom-control-label small" for="overrideExchangeRate">Override</label>
                </div>
              </div>
              
              <div id="defaultRateDisplay" class="d-flex justify-content-between align-items-center">
                <span class="small text-muted">System Rate:</span>
                <span class="font-weight-bold text-dark">{{ number_format($exchangeRate, 2) }} TZS / USD</span>
              </div>

              <div id="customRateInputContainer" style="display:none;" class="mt-2">
                <div class="form-group mb-2">
                  <input type="number" step="0.01" class="form-control form-control-sm" id="custom_exchange_rate" name="custom_exchange_rate" placeholder="Custom Rate (e.g. 2600)">
                </div>
                <div class="form-group mb-0">
                  <input type="text" class="form-control form-control-sm" name="exchange_rate_note" placeholder="Reason for change (Required)">
                </div>
              </div>

              <div class="mt-3 pt-2 border-top text-right">
                <span class="text-muted small" id="alternative_value_label">Approx USD:</span>
                <h5 class="text-dark font-weight-bold mb-0"><span id="alternative_currency_symbol">$</span><span id="alternative_total_value">0</span></h5>
              </div>
            </div>

            <button class="btn btn-primary btn-lg btn-block shadow-sm font-weight-bold" type="submit" id="submitBtn" style="border-radius: 8px;">
              <i class="fa fa-paper-plane"></i> GENERATE & SEND PDF
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow" style="border-radius: 12px;">
      <div class="modal-body text-center p-5">
        <i class="fa fa-check-circle fa-5x text-success mb-3"></i>
        <h3 class="mb-3 font-weight-bold">Invoice Sent!</h3>
        <p class="text-muted mb-4" id="successMessage"></p>
        <div class="d-flex justify-content-center">
            <a href="{{ route('reception.invoices.index') }}" id="viewInvoicesBtn" class="btn btn-primary px-4 mr-2"><i class="fa fa-list"></i> View Sent Invoices</a>
            <button type="button" class="btn btn-light px-4" data-dismiss="modal"><i class="fa fa-plus"></i> Create Another</button>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
const SYSTEM_RATE = {{ $exchangeRate ?? 2500 }};
let currentPricePerNightUsd = 0;
let currentPricePerNightTzs = 0;
let currentSelectedNationality = 'international';

function handleNationalityChange() {
    currentSelectedNationality = $('input[name="guest_type"]:checked').val();
    updateLiveSummary();
}

function toggleInvoiceType(type) {
    $('#invoice_type').val(type);
    if (type === 'individual') {
        $('#individual_section').fadeIn(200);
        $('#corporate_section').hide();
        $('#guest_name').attr('required', true);
        $('#company_name').attr('required', false);
    } else {
        $('#individual_section').hide();
        $('#corporate_section').fadeIn(200);
        $('#guest_name').attr('required', false);
        $('#company_name').attr('required', true);
    }
}

function calculateNights() {
    const checkIn = new Date($('#check_in').val());
    const checkOut = new Date($('#check_out').val());
    if (checkOut > checkIn) {
        return Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
    }
    return 0;
}

function updateLiveSummary() {
    const nights = calculateNights();
    const rooms = parseInt($('#number_of_rooms').val()) || 1;
    
    // Choose the base rate based on nationality
    let baseRate = (currentSelectedNationality === 'tanzanian') 
        ? (currentPricePerNightTzs / SYSTEM_RATE) 
        : currentPricePerNightUsd;
    
    // Display purposes
    $('#summary_nights').text(nights);
    $('#summary_rooms').text(rooms);
    $('#summary_base_rate').text(baseRate.toFixed(2));

    if (nights > 0 && baseRate > 0) {
        const total = (nights * rooms * baseRate).toFixed(2);
        $('#total_price').val(total).trigger('input'); 
    }
}

function fetchAvailableRooms() {
    const type = $('#room_type').val();
    const start = $('#check_in').val();
    const end = $('#check_out').val();

    if(!type || !start || !end) return;

    $('#rooms_loading').show();
    $('#availability_status').empty();
    currentPricePerNightUsd = 0;
    currentPricePerNightTzs = 0;
    
    $.ajax({
        url: "{{ $role === 'reception' ? route('reception.bookings.available-rooms') : route('admin.bookings.available-rooms') }}",
        data: { room_type: type, check_in: start, check_out: end },
        success: function(data) {
            $('#rooms_loading').hide();
            if (data.success) {
                const rooms = Array.isArray(data.available_rooms) ? data.available_rooms : Object.values(data.available_rooms);
                const needed = parseInt($('#number_of_rooms').val()) || 1;
                if (rooms.length > 0) {
                    currentPricePerNightUsd = parseFloat(rooms[0].price_per_night);
                    currentPricePerNightTzs = parseFloat(rooms[0].price_per_night_tzs);
                    $('#room_id').val(rooms[0].id);
                    updateLiveSummary();
                    
                    if (rooms.length >= needed) {
                        $('#availability_status').html(`<span class="text-success"><i class="fa fa-check-circle"></i> Sufficient rooms available (${rooms.length} available). Rate: $${currentPricePerNightUsd}/night Or ${currentPricePerNightTzs.toLocaleString()} TZS/night</span>`);
                    } else {
                        $('#availability_status').html(`<span class="text-warning" style="font-size: 14px;"><i class="fa fa-exclamation-triangle"></i> Warning: Only <strong>${rooms.length}</strong> available, but <strong>${needed}</strong> requested!</span><br><span class="text-muted">Rate: $${currentPricePerNightUsd}/night Or ${currentPricePerNightTzs.toLocaleString()} TZS/night</span>`);
                    }
                } else {
                    $('#availability_status').html(`<span class="text-danger"><i class="fa fa-times-circle"></i> No rooms available for these dates!</span>`);
                    $('#room_id').val('');
                }
            }
        }
    });
}

function recalcAlternative() {
    const displayValue = parseFloat($('#display_total_price').val()) || 0;
    const isOverride = $('#overrideExchangeRate').is(':checked');
    const customRate = parseFloat($('#custom_exchange_rate').val()) || SYSTEM_RATE;
    const rateToUse = isOverride ? customRate : SYSTEM_RATE;
    
    let realUsd = 0;
    let alternativeValue = 0;

    if (currentSelectedNationality === 'tanzanian') {
        // Display is TZS, Alternative is USD
        realUsd = displayValue / rateToUse;
        alternativeValue = realUsd;
        $('#alternative_total_value').text(alternativeValue.toLocaleString(undefined, {minimumFractionDigits: 2}));
    } else {
        // Display is USD, Alternative is TZS
        realUsd = displayValue;
        alternativeValue = displayValue * rateToUse;
        $('#alternative_total_value').text(alternativeValue.toLocaleString(undefined, {minimumFractionDigits: 0}));
    }

    // Always update the hidden field for the server
    $('#real_total_price_usd').val(realUsd.toFixed(2));
}

$(document).ready(function() {
    toggleInvoiceType('individual');

    $('#check_in, #check_out, #number_of_rooms').on('change input', function() {
        updateLiveSummary();
        if($('#room_type').val()) fetchAvailableRooms();
    });

    $('#room_type').on('change', fetchAvailableRooms);

    $('#display_total_price').on('input', recalcAlternative);
    $('#custom_exchange_rate').on('input', recalcAlternative);

    $('#overrideExchangeRate').change(function() {
        if($(this).is(':checked')) {
            $('#defaultRateDisplay').hide();
            $('#customRateInputContainer').fadeIn(200);
            $('#custom_exchange_rate').attr('required', true);
            $('input[name="exchange_rate_note"]').attr('required', true);
        } else {
            $('#customRateInputContainer').hide();
            $('#defaultRateDisplay').fadeIn(200);
            $('#custom_exchange_rate').attr('required', false).val('');
            $('input[name="exchange_rate_note"]').attr('required', false).val('');
        }
        recalcTzs();
    });

    // Reuse Guest Search
    $('#guestSearchInput').on('keyup', function() {
        const query = $(this).val();
        if (query.length < 2) { $('#guestSearchResults').hide(); return; }
        $.ajax({
            url: "{{ route('admin.bookings.search.guests') }}",
            data: { q: query },
            success: function(data) {
                let html = '';
                if (data.length > 0) {
                    data.forEach(g => {
                        html += `<a href="#" class="list-group-item list-group-item-action guest-select-item" data-name="${g.name}" data-email="${g.email}" data-phone="${g.phone}" data-guest-type="${g.guest_type || 'international'}">
                                     <div class="d-flex justify-content-between">
                                         <strong>${g.name}</strong> <i class="fa fa-chevron-right text-muted small"></i>
                                     </div>
                                     <small class="text-muted"><i class="fa fa-envelope-o"></i> ${g.email} | <i class="fa fa-phone"></i> ${g.phone}</small>
                                  </a>`;
                    });
                    $('#guestSearchResults').html(html).show();
                } else { $('#guestSearchResults').hide(); }
            }
        });
    });

    $(document).on('click', '.guest-select-item', function(e) {
        e.preventDefault();
        $('#guest_name').val($(this).data('name'));
        $('#guest_email').val($(this).data('email'));
        $('#guest_phone').val($(this).data('phone'));
        
        // Auto-select nationality
        const gType = $(this).data('guest-type');
        if (gType === 'tanzanian') {
            $('#nat_tanzanian').prop('checked', true);
        } else {
            $('#nat_international').prop('checked', true);
        }
        handleNationalityChange();

        $('#guestSearchResults').hide();
        $('#guestSearchInput').val('');
    });

    // Reuse Company Search
    $('#companySearchInput').on('keyup', function() {
        const query = $(this).val();
        if (query.length < 2) { $('#companySearchResults').hide(); return; }
        $.ajax({
            url: "{{ route('admin.bookings.search.companies') }}",
            data: { q: query },
            success: function(data) {
                let html = '';
                if (data.length > 0) {
                    data.forEach(c => {
                        html += `<a href="#" class="list-group-item list-group-item-action company-select-item" data-id="${c.id}" data-name="${c.name}" data-email="${c.email}" data-phone="${c.phone}">
                                    <div class="d-flex justify-content-between">
                                        <strong>${c.name}</strong> <i class="fa fa-chevron-right text-muted small"></i>
                                    </div>
                                    <small class="text-muted">${c.email}</small>
                                 </a>`;
                    });
                    $('#companySearchResults').html(html).show();
                } else { $('#companySearchResults').hide(); }
            }
        });
    });

    $(document).on('click', '.company-select-item', function(e) {
        e.preventDefault();
        $('#company_id').val($(this).data('id'));
        $('#company_name').val($(this).data('name'));
        $('#guest_email').val($(this).data('email'));
        $('#guest_phone').val($(this).data('phone'));
        $('#companySearchResults').hide();
        $('#companySearchInput').val('');
    });

    // Click outside search
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#individual_section').length) $('#guestSearchResults').hide();
        if (!$(e.target).closest('#corporate_section').length) $('#companySearchResults').hide();
    });

    // Submit
    $('#quickInvoiceForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#submitBtn');
        const originalHtml = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> GENERATING INVOICE...').attr('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#successMessage').text(response.message);
                    $('#successModal').modal('show');
                    $('#quickInvoiceForm')[0].reset();
                    updateLiveSummary();
                }
 else {
                    swal("Error", response.message || "Failed to send invoice", "error");
                }
            },
            error: function(xhr) {
                let msg = "An error occurred";
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors)[0][0] || xhr.responseJSON.message;
                } else if (xhr.responseJSON) {
                    msg = xhr.responseJSON.message;
                }
                swal("Error", msg, "error");
            },
            complete: function() {
                btn.html(originalHtml).attr('disabled', false);
            }
        });
    });
});
</script>
@endsection
