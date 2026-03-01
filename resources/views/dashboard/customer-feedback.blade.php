@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-star"></i> Feedback & Reviews</h1>
    <p>{{ $message ?? 'Share your experience with us' }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Feedback</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-comment"></i> Submit Your Feedback</h3>
      <div class="tile-body">
        <form id="feedbackForm">
          @csrf
          
          <div class="row">
            @if($role === 'public')
              <div class="col-md-6 mb-3">
                <label for="guest_name">Your Name (Optional)</label>
                <input type="text" class="form-control" id="guest_name" name="guest_name" placeholder="Enter your name">
              </div>
              <div class="col-md-6 mb-3">
                <label for="guest_email">Your Email (Optional)</label>
                <input type="email" class="form-control" id="guest_email" name="guest_email" placeholder="Enter your email">
              </div>
              <div class="col-md-6 mb-3">
                <label for="country">Country <span class="text-danger">*</span></label>
                <select class="form-control select2" id="country" name="country" required>
                  <option value="">Select Country...</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="guest_phone">Phone Number <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="phone_country_code" style="min-width: 65px; text-align: center;">+255</span>
                  </div>
                  <input class="form-control" type="text" id="guest_phone" name="guest_phone" placeholder="Phone number" required>
                </div>
              </div>
              <div class="col-md-12 mb-3">
                <label for="room_id">Room You Stayed In (Optional)</label>
                <select class="form-control select2" id="room_id" name="room_id">
                  <option value="">Select a room (Optional)...</option>
                  @foreach($allRooms as $room)
                    <option value="{{ $room->id }}">Room {{ $room->room_number }} - {{ $room->room_type }}</option>
                  @endforeach
                </select>
              </div>
            @else
              <div class="col-md-12 mb-3">
                <label for="booking_id">Select Your Booking *</label>
                <select class="form-control" id="booking_id" name="booking_id" required>
                  <option value="">Select a completed booking...</option>
                  @forelse($bookings as $booking)
                  <option value="{{ $booking->id }}">
                    {{ $booking->booking_reference }} - {{ $booking->room->room_type ?? ($booking->room->room_number ?? 'N/A') }} 
                    ({{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }})
                  </option>
                  @empty
                  <option value="" disabled>No completed bookings found</option>
                  @endforelse
                </select>
                @if(count($bookings) === 0)
                  <small class="form-text text-muted">You can still provide general feedback below.</small>
                @endif
              </div>
            @endif
          </div>

          <hr>

          <div class="form-group">
            <label class="d-block font-weight-bold">Overall Rating *</label>
            <div class="rating-input">
              @for($i = 5; $i >= 1; $i--)
              <input type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}">
              <label for="rating{{ $i }}" class="star-label">
                <i class="fa fa-star"></i>
              </label>
              @endfor
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-2">
              <label>Room Quality</label>
              <select class="form-control" name="categories[room_quality]">
                <option value="">Select rating...</option>
                @for($i=5; $i>=1; $i--) <option value="{{$i}}">{{$i}} Star{{$i>1?'s':''}}</option> @endfor
              </select>
            </div>
            <div class="col-md-6 mb-2">
              <label>Service</label>
              <select class="form-control" name="categories[service]">
                <option value="">Select rating...</option>
                @for($i=5; $i>=1; $i--) <option value="{{$i}}">{{$i}} Star{{$i>1?'s':''}}</option> @endfor
              </select>
            </div>
          </div>
          
          <div class="form-group mt-3">
            <label for="comment">Your Comments</label>
            <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="How was your stay? Any suggestions?"></textarea>
          </div>
          
          <div id="feedbackAlert"></div>
          
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="fa fa-paper-plane"></i> Submit Feedback
          </button>

          @if($role === 'public')
            <div class="mt-4 pt-3 border-top">
                <p class="text-muted small text-center">
                    Already have an account? <a href="{{ route('login', ['redirect' => url()->current()]) }}">Log in</a> to track your bookings.
                </p>
            </div>
          @endif
        </form>
      </div>
    </div>
  </div>
</div>

@if(count($submittedFeedback ?? []) > 0)
<div class="row mt-4">
  <div class="col-md-12">
    <div class="tile shadow-sm">
      <h3 class="tile-title text-success"><i class="fa fa-check-circle"></i> Your Previous Feedback</h3>
      <div class="tile-body">
        @foreach($submittedFeedback as $feedback)
        <div class="card mb-3 border-0 bg-light" style="border-radius: 10px;">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">
                    <i class="fa fa-calendar"></i> 
                    {{ $feedback->booking->booking_reference ?? 'General Feedback' }}
                </h6>
                <span class="badge badge-success">Submitted</span>
            </div>
            <div class="text-warning mb-2">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fa fa-star {{ $i <= $feedback->rating ? '' : 'text-muted' }}"></i>
                @endfor
            </div>
            @if($feedback->comment)
                <p class="mb-0 text-dark" style="font-style: italic;">"{{ $feedback->comment }}"</p>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/plugins/select2.min.js') }}"></script>
<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.rating-input input[type="radio"] {
    display: none;
}
.rating-input label {
    cursor: pointer;
    font-size: 35px;
    color: #cbd5e0;
    margin-right: 8px;
    transition: color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input[type="radio"]:checked ~ label {
    color: #ecc94b; /* Premium gold */
}
.tile { border-radius: 12px; border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
.btn-primary { background-color: #4a5568; border: none; }
.btn-primary:hover { background-color: #2d3748; }

/* Select2 Overrides to match Bootstrap */
.select2-container { width: 100% !important; }
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 0;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
    padding-right: 36px;
    color: #495057;
    font-size: 14px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
    right: 6px;
}
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #e77a3a;
    box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25);
    outline: 0;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #e77a3a;
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 6px 10px;
}
.select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
<script>
// Complete country list with flags and codes
const countries = [
    { name: 'Tanzania', flag: '🇹🇿', code: '+255' },
    { name: 'Afghanistan', flag: '🇦🇫', code: '+93' },
    { name: 'Albania', flag: '🇦🇱', code: '+355' },
    { name: 'Algeria', flag: '🇩🇿', code: '+213' },
    { name: 'Andorra', flag: '🇦🇩', code: '+376' },
    { name: 'Angola', flag: '🇦🇴', code: '+244' },
    { name: 'Antigua and Barbuda', flag: '🇦🇬', code: '+1' },
    { name: 'Argentina', flag: '🇦🇷', code: '+54' },
    { name: 'Armenia', flag: '🇦🇲', code: '+374' },
    { name: 'Australia', flag: '🇦🇺', code: '+61' },
    { name: 'Austria', flag: '🇦🇹', code: '+43' },
    { name: 'Azerbaijan', flag: '🇦🇿', code: '+994' },
    { name: 'Bahamas', flag: '🇧🇸', code: '+1' },
    { name: 'Bahrain', flag: '🇧🇭', code: '+973' },
    { name: 'Bangladesh', flag: '🇧🇩', code: '+880' },
    { name: 'Barbados', flag: '🇧🇧', code: '+1' },
    { name: 'Belarus', flag: '🇧🇾', code: '+375' },
    { name: 'Belgium', flag: '🇧🇪', code: '+32' },
    { name: 'Belize', flag: '🇧🇿', code: '+501' },
    { name: 'Benin', flag: '🇧🇯', code: '+229' },
    { name: 'Bhutan', flag: '🇧🇹', code: '+975' },
    { name: 'Bolivia', flag: '🇧🇴', code: '+591' },
    { name: 'Bosnia and Herzegovina', flag: '🇧🇦', code: '+387' },
    { name: 'Botswana', flag: '🇧🇼', code: '+267' },
    { name: 'Brazil', flag: '🇧🇷', code: '+55' },
    { name: 'Brunei', flag: '🇧🇳', code: '+673' },
    { name: 'Bulgaria', flag: '🇧🇬', code: '+359' },
    { name: 'Burkina Faso', flag: '🇧🇫', code: '+226' },
    { name: 'Burundi', flag: '🇧🇮', code: '+257' },
    { name: 'Cabo Verde', flag: '🇨🇻', code: '+238' },
    { name: 'Cambodia', flag: '🇰🇭', code: '+855' },
    { name: 'Cameroon', flag: '🇨🇲', code: '+237' },
    { name: 'Canada', flag: '🇨🇦', code: '+1' },
    { name: 'Central African Republic', flag: '🇨🇫', code: '+236' },
    { name: 'Chad', flag: '🇹🇩', code: '+235' },
    { name: 'Chile', flag: '🇨🇱', code: '+56' },
    { name: 'China', flag: '🇨🇳', code: '+86' },
    { name: 'Colombia', flag: '🇨🇴', code: '+57' },
    { name: 'Comoros', flag: '🇰🇲', code: '+269' },
    { name: 'Congo', flag: '🇨🇬', code: '+242' },
    { name: 'Costa Rica', flag: '🇨🇷', code: '+506' },
    { name: 'Croatia', flag: '🇭🇷', code: '+385' },
    { name: 'Cuba', flag: '🇨🇺', code: '+53' },
    { name: 'Cyprus', flag: '🇨🇾', code: '+357' },
    { name: 'Czech Republic', flag: '🇨🇿', code: '+420' },
    { name: 'Denmark', flag: '🇩🇰', code: '+45' },
    { name: 'Djibouti', flag: '🇩🇯', code: '+253' },
    { name: 'Dominica', flag: '🇩🇲', code: '+1' },
    { name: 'Dominican Republic', flag: '🇩🇴', code: '+1' },
    { name: 'Ecuador', flag: '🇪🇨', code: '+593' },
    { name: 'Egypt', flag: '🇪🇬', code: '+20' },
    { name: 'El Salvador', flag: '🇸🇻', code: '+503' },
    { name: 'Equatorial Guinea', flag: '🇬🇶', code: '+240' },
    { name: 'Eritrea', flag: '🇪🇷', code: '+291' },
    { name: 'Estonia', flag: '🇪🇪', code: '+372' },
    { name: 'Eswatini', flag: '🇸🇿', code: '+268' },
    { name: 'Ethiopia', flag: '🇪🇹', code: '+251' },
    { name: 'Fiji', flag: '🇫🇯', code: '+679' },
    { name: 'Finland', flag: '🇫🇮', code: '+358' },
    { name: 'France', flag: '🇫🇷', code: '+33' },
    { name: 'Gabon', flag: '🇬🇦', code: '+241' },
    { name: 'Gambia', flag: '🇬🇲', code: '+220' },
    { name: 'Georgia', flag: '🇬🇪', code: '+995' },
    { name: 'Germany', flag: '🇩🇪', code: '+49' },
    { name: 'Ghana', flag: '🇬🇭', code: '+233' },
    { name: 'Greece', flag: '🇬🇷', code: '+30' },
    { name: 'Grenada', flag: '🇬🇩', code: '+1' },
    { name: 'Guatemala', flag: '🇬🇹', code: '+502' },
    { name: 'Guinea', flag: '🇬🇳', code: '+224' },
    { name: 'Guinea-Bissau', flag: '🇬🇼', code: '+245' },
    { name: 'Guyana', flag: '🇬🇾', code: '+592' },
    { name: 'Haiti', flag: '🇭🇹', code: '+509' },
    { name: 'Honduras', flag: '🇭🇳', code: '+504' },
    { name: 'Hungary', flag: '🇭🇺', code: '+36' },
    { name: 'Iceland', flag: '🇮🇸', code: '+354' },
    { name: 'India', flag: '🇮🇳', code: '+91' },
    { name: 'Indonesia', flag: '🇮🇩', code: '+62' },
    { name: 'Iran', flag: '🇮🇷', code: '+98' },
    { name: 'Iraq', flag: '🇮🇶', code: '+964' },
    { name: 'Ireland', flag: '🇮🇪', code: '+353' },
    { name: 'Israel', flag: '🇮🇱', code: '+972' },
    { name: 'Italy', flag: '🇮🇹', code: '+39' },
    { name: 'Jamaica', flag: '🇯🇲', code: '+1' },
    { name: 'Japan', flag: '🇯🇵', code: '+81' },
    { name: 'Jordan', flag: '🇯🇴', code: '+962' },
    { name: 'Kazakhstan', flag: '🇰🇿', code: '+7' },
    { name: 'Kenya', flag: '🇰🇪', code: '+254' },
    { name: 'Kiribati', flag: '🇰🇮', code: '+686' },
    { name: 'Kuwait', flag: '🇰🇼', code: '+965' },
    { name: 'Kyrgyzstan', flag: '🇰🇬', code: '+996' },
    { name: 'Laos', flag: '🇱🇦', code: '+856' },
    { name: 'Latvia', flag: '🇱🇻', code: '+371' },
    { name: 'Lebanon', flag: '🇱🇧', code: '+961' },
    { name: 'Lesotho', flag: '🇱🇸', code: '+266' },
    { name: 'Liberia', flag: '🇱🇷', code: '+231' },
    { name: 'Libya', flag: '🇱🇾', code: '+218' },
    { name: 'Liechtenstein', flag: '🇱🇮', code: '+423' },
    { name: 'Lithuania', flag: '🇱🇹', code: '+370' },
    { name: 'Luxembourg', flag: '🇱🇺', code: '+352' },
    { name: 'Madagascar', flag: '🇲🇬', code: '+261' },
    { name: 'Malawi', flag: '🇲🇼', code: '+265' },
    { name: 'Malaysia', flag: '🇲🇾', code: '+60' },
    { name: 'Maldives', flag: '🇲🇻', code: '+960' },
    { name: 'Mali', flag: '🇲🇱', code: '+223' },
    { name: 'Malta', flag: '🇲🇹', code: '+356' },
    { name: 'Marshall Islands', flag: '🇲🇭', code: '+692' },
    { name: 'Mauritania', flag: '🇲🇷', code: '+222' },
    { name: 'Mauritius', flag: '🇲🇺', code: '+230' },
    { name: 'Mexico', flag: '🇲🇽', code: '+52' },
    { name: 'Micronesia', flag: '🇫🇲', code: '+691' },
    { name: 'Moldova', flag: '🇲🇩', code: '+373' },
    { name: 'Monaco', flag: '🇲🇨', code: '+377' },
    { name: 'Mongolia', flag: '🇲🇳', code: '+976' },
    { name: 'Montenegro', flag: '🇲🇪', code: '+382' },
    { name: 'Morocco', flag: '🇲🇦', code: '+212' },
    { name: 'Mozambique', flag: '🇲🇿', code: '+258' },
    { name: 'Myanmar', flag: '🇲🇲', code: '+95' },
    { name: 'Namibia', flag: '🇳🇦', code: '+264' },
    { name: 'Nauru', flag: '🇳🇷', code: '+674' },
    { name: 'Nepal', flag: '🇳🇵', code: '+977' },
    { name: 'Netherlands', flag: '🇳🇱', code: '+31' },
    { name: 'New Zealand', flag: '🇳🇿', code: '+64' },
    { name: 'Nicaragua', flag: '🇳🇮', code: '+505' },
    { name: 'Niger', flag: '🇳🇪', code: '+227' },
    { name: 'Nigeria', flag: '🇳🇬', code: '+234' },
    { name: 'North Korea', flag: '🇰🇵', code: '+850' },
    { name: 'North Macedonia', flag: '🇲🇰', code: '+389' },
    { name: 'Norway', flag: '🇳🇴', code: '+47' },
    { name: 'Oman', flag: '🇴🇲', code: '+968' },
    { name: 'Pakistan', flag: '🇵🇰', code: '+92' },
    { name: 'Palau', flag: '🇵🇼', code: '+680' },
    { name: 'Palestine', flag: '🇵🇸', code: '+970' },
    { name: 'Panama', flag: '🇵🇦', code: '+507' },
    { name: 'Papua New Guinea', flag: '🇵🇬', code: '+675' },
    { name: 'Paraguay', flag: '🇵🇾', code: '+595' },
    { name: 'Peru', flag: '🇵🇪', code: '+51' },
    { name: 'Philippines', flag: '🇵🇭', code: '+63' },
    { name: 'Poland', flag: '🇵🇱', code: '+48' },
    { name: 'Portugal', flag: '🇵🇹', code: '+351' },
    { name: 'Qatar', flag: '🇶🇦', code: '+974' },
    { name: 'Romania', flag: '🇷🇴', code: '+40' },
    { name: 'Russia', flag: '🇷🇺', code: '+7' },
    { name: 'Rwanda', flag: '🇷🇼', code: '+250' },
    { name: 'Saint Kitts and Nevis', flag: '🇰🇳', code: '+1' },
    { name: 'Saint Lucia', flag: '🇱🇨', code: '+1' },
    { name: 'Saint Vincent and the Grenadines', flag: '🇻🇨', code: '+1' },
    { name: 'Samoa', flag: '🇼🇸', code: '+685' },
    { name: 'San Marino', flag: '🇸🇲', code: '+378' },
    { name: 'Sao Tome and Principe', flag: '🇸🇹', code: '+239' },
    { name: 'Saudi Arabia', flag: '🇸🇦', code: '+966' },
    { name: 'Senegal', flag: '🇸🇳', code: '+221' },
    { name: 'Serbia', flag: '🇷🇸', code: '+381' },
    { name: 'Seychelles', flag: '🇸🇨', code: '+248' },
    { name: 'Sierra Leone', flag: '🇸🇱', code: '+232' },
    { name: 'Singapore', flag: '🇸🇬', code: '+65' },
    { name: 'Slovakia', flag: '🇸🇰', code: '+421' },
    { name: 'Slovenia', flag: '🇸🇮', code: '+386' },
    { name: 'Solomon Islands', flag: '🇸🇧', code: '+677' },
    { name: 'Somalia', flag: '🇸🇴', code: '+252' },
    { name: 'South Africa', flag: '🇿🇦', code: '+27' },
    { name: 'South Korea', flag: '🇰🇷', code: '+82' },
    { name: 'South Sudan', flag: '🇸🇸', code: '+211' },
    { name: 'Spain', flag: '🇪🇸', code: '+34' },
    { name: 'Sri Lanka', flag: '🇱🇰', code: '+94' },
    { name: 'Sudan', flag: '🇸🇩', code: '+249' },
    { name: 'Suriname', flag: '🇸🇷', code: '+597' },
    { name: 'Sweden', flag: '🇸🇪', code: '+46' },
    { name: 'Switzerland', flag: '🇨🇭', code: '+41' },
    { name: 'Syria', flag: '🇸🇾', code: '+963' },
    { name: 'Taiwan', flag: '🇹🇼', code: '+886' },
    { name: 'Tajikistan', flag: '🇹🇯', code: '+992' },
    { name: 'Tanzania', flag: '🇹🇿', code: '+255' },
    { name: 'Thailand', flag: '🇹🇭', code: '+66' },
    { name: 'Timor-Leste', flag: '🇹🇱', code: '+670' },
    { name: 'Togo', flag: '🇹🇬', code: '+228' },
    { name: 'Tonga', flag: '🇹🇴', code: '+676' },
    { name: 'Trinidad and Tobago', flag: '🇹🇹', code: '+1' },
    { name: 'Tunisia', flag: '🇹🇳', code: '+216' },
    { name: 'Turkey', flag: '🇹🇷', code: '+90' },
    { name: 'Turkmenistan', flag: '🇹🇲', code: '+993' },
    { name: 'Tuvalu', flag: '🇹🇻', code: '+688' },
    { name: 'Uganda', flag: '🇺🇬', code: '+256' },
    { name: 'Ukraine', flag: '🇺🇦', code: '+380' },
    { name: 'United Arab Emirates', flag: '🇦🇪', code: '+971' },
    { name: 'United Kingdom', flag: '🇬🇧', code: '+44' },
    { name: 'United States', flag: '🇺🇸', code: '+1' },
    { name: 'Uruguay', flag: '🇺🇾', code: '+598' },
    { name: 'Uzbekistan', flag: '🇺🇿', code: '+998' },
    { name: 'Vanuatu', flag: '🇻🇺', code: '+678' },
    { name: 'Vatican City', flag: '🇻🇦', code: '+39' },
    { name: 'Venezuela', flag: '🇻🇪', code: '+58' },
    { name: 'Vietnam', flag: '🇻🇳', code: '+84' },
    { name: 'Yemen', flag: '🇾🇪', code: '+967' },
    { name: 'Zambia', flag: '🇿🇲', code: '+260' },
    { name: 'Zimbabwe', flag: '🇿🇼', code: '+263' }
];

$(document).ready(function() {
    const countrySelect = document.getElementById('country');
    if (countrySelect) {
        countries.forEach(country => {
            const option = document.createElement('option');
            option.value = country.name;
            option.textContent = country.flag + ' ' + country.name;
            option.setAttribute('data-code', country.code);
            countrySelect.appendChild(option);
        });

        // Initialize Select2 for country
        $('#country').select2({
            placeholder: 'Select your country...',
            allowClear: true,
            width: '100%'
        });

        // Handle country selection: update phone country code prefix
        $('#country').on('select2:select', function(e) {
            const selectedOption = $(this).find('option:selected');
            const countryCode = selectedOption.attr('data-code');
            if (countryCode) {
                document.getElementById('phone_country_code').textContent = countryCode;
            }
        });

        // Handle country clear: reset to default
        $('#country').on('select2:unselect select2:clear', function() {
            document.getElementById('phone_country_code').textContent = '+255';
        });

        // Default to Tanzania
        $('#country').val('Tanzania').trigger('change');
        document.getElementById('phone_country_code').textContent = '+255';
    }

    // Initialize Room Select2
    @if($role === 'public')
    $('#room_id').select2({
        placeholder: 'Select a room (Optional)...',
        allowClear: true,
        width: '100%'
    });
    @endif

    document.getElementById('feedbackForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const alertContainer = document.getElementById('feedbackAlert');
        
        if (!formData.get('rating')) {
            alertContainer.innerHTML = '<div class="alert alert-danger shadow-sm">Please select a star rating.</div>';
            return;
        }

        @if($role === 'public')
        if (!formData.get('country')) {
            alertContainer.innerHTML = '<div class="alert alert-danger shadow-sm">Please select your country.</div>';
            return;
        }
        if (!formData.get('guest_phone')) {
            alertContainer.innerHTML = '<div class="alert alert-danger shadow-sm">Please enter your phone number.</div>';
            return;
        }
        
        // Merge country code with phone number
        const countryCode = document.getElementById('phone_country_code').textContent;
        const phone = formData.get('guest_phone');
        formData.set('guest_phone', countryCode + ' ' + phone);
        @endif
        
        alertContainer.innerHTML = '<div class="alert alert-info shadow-sm"><i class="fa fa-spinner fa-spin"></i> Submitting your feedback...</div>';
        
        fetch('{{ route("customer.feedback.submit") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                swal({
                    title: "Feedback Received!",
                    text: "Thank you for helping us improve our services.",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }, function() {
                    window.location.href = "{{ url('/') }}";
                });
            } else {
                alertContainer.innerHTML = '<div class="alert alert-danger shadow-sm">' + (data.message || 'Submission failed.') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alertContainer.innerHTML = '<div class="alert alert-danger shadow-sm">Connection error. Please try again.</div>';
        });
    });
});
</script>
@endsection
