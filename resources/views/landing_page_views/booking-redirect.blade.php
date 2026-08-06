@extends('layouts.new_landing')

@section('title', 'Book Online | Primeland Hotel Moshi')

@section('content')
<div class="prl-booking-redirect">
    <div class="prl-booking-redirect-card wow fadeInUp">
        <img src="{{ asset('assets/img/new_images/primeland_logo.png') }}" alt="Primeland Hotel" class="prl-booking-redirect-logo">
        <div class="prl-spinner" aria-hidden="true"></div>
        <h2>Opening Booking</h2>
        <p>Please wait while we redirect you to our secure reservation page.</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    setTimeout(function () {
        window.location.href = '{{ $bookingUrl }}';
    }, 1800);
</script>
@endsection
