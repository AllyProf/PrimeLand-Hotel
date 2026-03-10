@extends('layouts.new_landing')

@section('title', 'Twin Room | Primeland Hotel - Moshi, Kilimanjaro')

@section('content')
    <!-- Breadcrumb Section Start -->
    <div class="gt-breadcrumb-wrapper bg-cover"
        style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('assets/img/new_images/room_(6).jpg') }}');">
        <div class="container">
            <div class="gt-page-heading">
                <div class="gt-breadcrumb-sub-title">
                    <h1 class="text-white wow fadeInUp" data-wow-delay=".3s">Twin Room</h1>
                </div>
                <ul class="gt-breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><i class="fa-solid fa-chevron-right"></i></li>
                    <li><a href="{{ url('/rooms') }}">Rooms</a></li>
                    <li><i class="fa-solid fa-chevron-right"></i></li>
                    <li>Twin Room</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- GT Room Details Section Start -->
    <section class="gt-room-details section section-padding">
        <div class="container">
            <div class="gt-room-details-wrapper">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="gt-room-items">
                            <div class="room-image">
                                <img src="{{ asset('assets/img/new_images/room_(6).jpg') }}" alt="Twin Room" style="max-width: 100%; height: auto; border-radius: 8px;">
                            </div>
                            <div class="gt-room-content">
                                <h5>$95 / NIGHT</h5>
                                <h2>Twin Room</h2>
                                <p>Spacious and comfortable twin beds, perfect for friends or colleagues sharing a room. Our Twin Room offers two separate beds with all the premium amenities Primeland Hotel is known for.</p>
                                <div class="gt-list">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <ul class="chack-list">
                                                <li><i class="fa-solid fa-circle-check"></i> Check-In: 2:00pm</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Max Occupancy: 2 Persons</li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6">
                                            <ul class="chack-list">
                                                <li><i class="fa-solid fa-circle-check"></i> Check Out: 10:00am</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Bed: 2 Separate Twin Beds</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <h3>Room Amenities:</h3>
                                <div class="gt-list-content">
                                    <ul>
                                        <li><i class="fa-solid fa-circle-check"></i> Free Wi-Fi</li>
                                        <li><i class="fa-solid fa-circle-check"></i> Air Conditioning</li>
                                        <li><i class="fa-solid fa-circle-check"></i> Standard universal electric sockets</li>
                                        <li><i class="fa-solid fa-circle-check"></i> Free toiletries</li>
                                        <li><i class="fa-solid fa-circle-check"></i> Towels</li>
                                        <li><i class="fa-solid fa-circle-check"></i> Mineral water</li>
                                        <li><i class="fa-solid fa-circle-check"></i> Tea / coffee making amenities</li>
                                        <li><i class="fa-solid fa-circle-check"></i> Refrigerator</li>
                                        <li><i class="fa-solid fa-circle-check"></i> Smart TV</li>
                                    </ul>
                                </div>
                                <h3>Rates &amp; Policies</h3>
                                <ul class="chack-list">
                                    <li><i class="fa-solid fa-circle-check"></i> All above rates are on Bed and Breakfast basis</li>
                                    <li><i class="fa-solid fa-circle-check"></i> Late checkout depends on availability and may be subject to an extra fee of 50% of room rate.</li>
                                    <li><i class="fa-solid fa-circle-check"></i> Late checkout later than 5:00pm is subject to a full room rate.</li>
                                </ul>
                                <div class="mt-4">
                                    <a href="{{ url('/contact') }}" class="gt-theme-btn">Book This Room</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="gt-main-sideber sticky-style">
                            <div class="gt-single-sideber-widget">
                                <div class="gt-widget-title">
                                    <h3>Hotel Booking</h3>
                                </div>
                                <div class="booking-item">
                                    <p style="font-size: 15px; color: #555; margin-bottom: 16px;">Our online booking is currently unavailable. Please contact us directly to make a reservation.</p>
                                    <a href="tel:+255677155156" class="gt-theme-btn w-100 d-block text-center mb-3">
                                        <i class="fa-solid fa-phone me-2"></i> Call Us Now
                                    </a>
                                    <a href="{{ url('/contact') }}" class="gt-theme-btn gt-border-style w-100 d-block text-center">
                                        Send Enquiry
                                    </a>
                                </div>
                            </div>
                            <div class="service-details-contact-bg text-center bg-cover mt-4"
                                style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('assets/img/new_images/coffee_.jpg') }}');">
                                <h3>Please Contact Us By Phone or Email to Make a Reservation.</h3>
                                <div class="icon">
                                    <img src="{{ asset('assets/img/inner/service-details/call.png') }}" alt="img">
                                </div>
                                <p>Need Reservation? Call us now</p>
                                <h3><a href="tel:+255677155156">+255 677-155-156</a></h3>
                                <p>info@primelandhotel.com</p>
                            </div>
                            <!-- Other Rooms -->
                            <div class="gt-single-sideber-widget mt-4">
                                <div class="gt-widget-title"><h3>Other Rooms</h3></div>
                                <ul class="gt-list-area">
                                    <li><a href="{{ url('/rooms/single-room') }}"><i class="fa-solid fa-bed me-2"></i> Single Room – $70/night</a></li>
                                    <li><a href="{{ url('/rooms/double-room') }}"><i class="fa-solid fa-bed me-2"></i> Double Room – $85/night</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
