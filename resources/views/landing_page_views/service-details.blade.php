@extends('layouts.new_landing')

@section('title', 'Hotel Amenities Moshi | Swimming Pool & Airport Shuttle at Primeland Hotel')

@section('content')
<!-- Breadcrumb Section Start -->
<div class="gt-breadcrumb-wrapper bg-cover"
    style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('assets/img/new_images/coffee_.jpg') }}');">
    <div class="container">
        <div class="gt-page-heading">
            <div class="gt-breadcrumb-sub-title">
                <h1 class=" text-white wow fadeInUp" data-wow-delay=".3s">Hotel Services</h1>
            </div>
            <ul class="gt-breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
                <li>
                    <a href="{{ url('/') }}">
                        Home
                    </a>
                </li>
                <li>
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ url('/services') }}">
                        Services
                    </a>
                </li>
                <li>
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li>
                    Service Details
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- GT Service Section Start -->
<section class="gt-service-details-section section-padding">
    <div class="container">
        <div class="gt-service-details-wrapper">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="gt-service-details-items">
                        <div class="details-image">
                            <img src="{{ asset('assets/img/new_images/our-services.jpg') }}" alt="Primeland Hotel services and facilities in Moshi"
                                style="width: 100%; border-radius: 10px;">
                        </div>
                        <div class="details-content">
                            <h2>Family Fun Package</h2>
                            <p>
                                Planning a trip for the whole family? Our Family Fun Package is designed to offer
                                comfort, entertainment, and unforgettable moments for guests of all ages. Book now
                                for a stay filled with laughter, relaxation, and family bonding.
                            </p>
                            <span class="gt-box">
                                Our well-trained team is dedicated to providing an exceptional experience, from
                                local excursions to on-site comforts.
                            </span>
                            <div class="details-list-item">
                                <h3>Package Inclusions:</h3>
                                <ul>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Family-Size Room or Suite
                                        </span>
                                        Spacious accommodations with extra beds or connecting rooms, ideal for
                                        families.
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Daily Breakfast Buffet for the Family
                                        </span>
                                        A variety of kid-friendly and healthy options included each morning.
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Welcome Gifts for Children
                                        </span>
                                        Fun surprises like toys, coloring books, or snack packs upon arrival.
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Complimentary Kids Club Access
                                        </span>
                                        Supervised activities, games, and crafts for different age groups.
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Family Movie Night Experience
                                        </span>
                                        Enjoy in-room movie setup or join our weekly family movie screenings with
                                        popcorn.
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Kids Eat Free (Under Age 6)
                                        </span>
                                        Special dining benefits when ordering from the children menu with a paying
                                        adult.
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Access to Family Pool & Kids Play Area
                                        </span>
                                        Safe, clean recreational areas for fun and relaxation.
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Free Late Check-Out (Based on Availability)
                                        </span>
                                        Enjoy a little more time before departure.
                                    </li>
                                </ul>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="details-image-2">
                                        <img src="{{ asset('assets/img/new_images/swimming floating tray_.jpg') }}" alt="Swimming pool at Primeland Hotel Moshi Kilimanjaro"
                                            style="width: 100%; height: 300px; object-fit: cover; border-radius: 10px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="details-image-2">
                                        <img src="{{ asset('assets/img/new_images/PRIMELAND HOTEL BAR.jpg') }}" alt="Bar and restaurant at Primeland Hotel Moshi"
                                            style="width: 100%; height: 300px; object-fit: cover; border-radius: 10px;">
                                    </div>
                                </div>
                            </div>
                            <div class="details-list-item">
                                <h3>Booking Info:</h3>
                                <ul>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Availability:
                                        </span>
                                        Year-round (subject to blackout dates)
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Minimum Stay:
                                        </span>
                                        2 nights
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            How to Book:
                                        </span>
                                        Select the Family Fun Package when reserving your room online or contact our
                                        reservations team.
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fa-solid fa-circle-check"></i>
                                            Cancellation Policy:
                                        </span>
                                        Flexible options available (check specific rates)
                                    </li>
                                </ul>
                            </div>
                            <div class="faq-content">
                                <h3>Frequently Asked Question</h3>
                                <div class="faq-accordion">
                                    <div class="accordion" id="accordion">
                                        <div class="accordion-item mb-4 wow fadeInUp" data-wow-delay=".5s">
                                            <h5 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#faq1"
                                                    aria-expanded="false" aria-controls="faq1">
                                                    Is your restaurant and pool bar open to non-residents?
                                                </button>
                                            </h5>
                                            <div id="faq1" class="accordion-collapse collapse"
                                                data-bs-parent="#accordion">
                                                <div class="accordion-body">
                                                    Absolutely! Our facilities, including our pool bar and
                                                    restaurant, are open to all guests visiting Moshi Town.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item mb-4 wow fadeInUp" data-wow-delay=".3s">
                                            <h5 class="accordion-header">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#faq2"
                                                    aria-expanded="true" aria-controls="faq2">
                                                    Do you offer services or activities for families and children?
                                                </button>
                                            </h5>
                                            <div id="faq2" class="accordion-collapse show"
                                                data-bs-parent="#accordion">
                                                <div class="accordion-body">
                                                    Of course! We offer family-sized rooms and special activities
                                                    suitable for children of all ages.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item mb-4 wow fadeInUp" data-wow-delay=".7s">
                                            <h5 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#faq3"
                                                    aria-expanded="false" aria-controls="faq3">
                                                    Do you provide airport shuttle services to JRO?
                                                </button>
                                            </h5>
                                            <div id="faq3" class="accordion-collapse collapse"
                                                data-bs-parent="#accordion">
                                                <div class="accordion-body">
                                                    Yes, we offer reliable shuttle services to and from JRO Airport.
                                                    Please book at least 48 hours in advance.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item wow fadeInUp" data-wow-delay=".8s">
                                            <h5 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#faq4"
                                                    aria-expanded="false" aria-controls="faq4">
                                                    Is the front desk and security available 24 hours?
                                                </button>
                                            </h5>
                                            <div id="faq4" class="accordion-collapse collapse"
                                                data-bs-parent="#accordion">
                                                <div class="accordion-body">
                                                    Our front desk is available 24/7. However, our main gates and
                                                    restaurant follow standard operating hours for security.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="gt-main-sideber sticky-style">
                        <div class="gt-single-sideber-widget">
                            <div class="gt-widget-title">
                                <h3>All Categories</h3>
                            </div>
                            <ul class="gt-category-list">
                                <li><a href="{{ url('/service-details') }}">Airport Shuttle</a><span><i
                                            class="fa-solid fa-circle-chevron-right"></i></span></li>
                                <li><a href="{{ url('/service-details') }}">Free Car Parking</a><span><i
                                            class="fa-solid fa-circle-chevron-right"></i></span></li>
                                <li><a href="{{ url('/service-details') }}">Fast Wifi Internet</a><span><i
                                            class="fa-solid fa-circle-chevron-right"></i></span></li>
                                <li><a href="{{ url('/service-details') }}">Room Service</a><span><i
                                            class="fa-solid fa-circle-chevron-right"></i></span></li>
                                <li><a href="{{ url('/service-details') }}">Food & Drink</a><span><i
                                            class="fa-solid fa-circle-chevron-right"></i></span></li>
                                <li><a href="{{ url('/service-details') }}">Swimming Pool</a><span><i
                                            class="fa-solid fa-circle-chevron-right"></i></span></li>
                            </ul>
                        </div>
                        <div class="gt-single-sideber-widget">
                            <div class="gt-widget-title">
                                <h3>Hours</h3>
                            </div>
                            <ul class="hours-list">
                                <li>
                                    <span>Breakfast - <b>6:30 AM to 10:00 AM</b>
                                </li>
                                <li>
                                    <span>Lunch - <b>12:30 PM to 3:00 PM</b>
                                </li>
                                <li>
                                    <span>Dinner - <b>6:30 PM to 10:00 PM</b>
                                </li>
                                <li>
                                    <span>Pool Bar - <b>10:00 AM to 10:00 PM</b>
                                </li>
                                <li>
                                    <span>Office - <b>8:00 AM to 6:00 PM</b>
                                </li>
                            </ul>
                        </div>
                        <div class="service-details-contact-bg text-center bg-cover"
                            style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('assets/img/new_images/coffee_.jpg') }}');">
                            <h3>
                                Please Contact Us By Phone or Email to Make a Reservation.
                            </h3>
                            <div class="icon">
                                <img src="{{ asset('assets/img/inner/service-details/call.png') }}" alt="img">
                            </div>
                            <p>Need Reservation? Call us directly.</p>
                            <h3><a href="tel:+255677155156">+255 677-155-156</a></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
