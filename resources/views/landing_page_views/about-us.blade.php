@extends('layouts.new_landing')

@section('title', 'About Us | Primeland Hotel - Moshi, Kilimanjaro')

@section('content')
    <!-- Breadcrumb Section Start -->
    <div class="gt-breadcrumb-wrapper bg-cover"
        style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('assets/img/new_images/hotel view_.jpg') }}');">
        <div class="container">
            <div class="gt-page-heading">
                <div class="gt-breadcrumb-sub-title">
                    <h1 class=" text-white wow fadeInUp" data-wow-delay=".3s">About Us</h1>
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
                        About Us
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- GT About Section Start -->
    <section class="gt-about-section-2 section-padding fix section-bg-3">
        <div class="gt-about-shape">
            <img src="{{ asset('assets/img/home-2/about/shape-01.png') }}" alt="img">
        </div>
        <div class="container">
            <div class="gt-about-wrapper-2">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="gt-about-left-content">
                            <div class="gt-section-title">
                                <h6 class="wow fadeInUp">
                                    About Us
                                </h6>
                                <h2 class="wow fadeInUp" data-wow-delay=".2s">
                                    Welcome to Primeland Hotel Comfort in Every Stay
                                </h2>
                                <div class="prl-divider mt-3 mb-4 wow fadeInUp" data-wow-delay=".3s">
                                    <div class="prl-divider-line"></div>
                                    <div class="prl-divider-dot"></div>
                                    <div class="prl-divider-line"></div>
                                </div>
                            </div>
                            <div class="gt-about-box-items">
                                <div class="row align-items-center">
                                    <div class="col-lg-5 wow fadeInUp" data-wow-delay=".4s">
                                        <div class="gt-about-images">
                                            <img src="{{ asset('assets/img/new_images/restaurant outside_.jpg') }}"
                                                alt="Restaurant Outside"
                                                style="border-radius: 10px; width: 100%; object-fit: cover;">
                                            <span class="title-box">
                                                <img src="{{ asset('assets/img/home-2/about/tir-vector.png') }}" alt="img">
                                                SINCE 2007
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-7 wow fadeInUp" data-wow-delay=".6s">
                                        <div class="about-content-icon">
                                            <div class="gt-icon-box">
                                                <div class="icon">
                                                    <i class="flaticon-target"></i>
                                                </div>
                                                <div class="content">
                                                    <h3>Our Mission</h3>
                                                    <p>We are a small boutique-style hotel in the heart of Moshi Town,
                                                        Kilimanjaro &ndash; Tanzania.</p>
                                                </div>
                                            </div>
                                            <div class="gt-icon-box style-2">
                                                <div class="icon">
                                                    <i class="flaticon-leadership"></i>
                                                </div>
                                                <div class="content">
                                                    <h3>Our Vision</h3>
                                                    <p>Conveniently located near the town center and 45 minutes from JRO
                                                        Airport. The perfect base for your trip.</p>
                                                </div>
                                            </div>
                                            <a href="{{ url('/about-us') }}" class="gt-theme-btn">DISCOVER MORE</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 wow fadeInUp" data-wow-delay=".5s">
                        <div class="gt-about-right-image">
                            <div class="gt-about-image">
                                <img src="{{ asset('assets/img/new_images/reception_.jpg') }}" alt="Reception"
                                    style="border-radius: 10px; width: 100%; object-fit: cover;">
                                <div class="gt-counter-box">
                                    <h2><span class="gt-count">100</span>+</h2>
                                    <h4>Happy Guests</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GT Marquee Section -->
    <div class="marquee-section fix" style="background-color: #e77a3a !important;">
        <style>
            .marquee-section, .marquee-section .marquee, .marquee-section .marquee-group { background-color: #e77a3a !important; }
            .marquee-section .marquee-group .text { color: white !important; }
            .marquee-section .marquee-group .text img { filter: brightness(0) invert(1) !important; }
        </style>
        <div class="marquee">
            <div class="marquee-group">
                <div class="text">Primeland Hotel</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Moshi, Kilimanjaro</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Boutique Hotel Tanzania</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Kilimanjaro Trekking Base</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Safari & Adventure</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
            </div>
            <div class="marquee-group">
                <div class="text">Primeland Hotel</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Moshi, Kilimanjaro</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Boutique Hotel Tanzania</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Kilimanjaro Trekking Base</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Safari & Adventure</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
            </div>
            <div class="marquee-group">
                <div class="text">Primeland Hotel</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Moshi, Kilimanjaro</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Boutique Hotel Tanzania</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Kilimanjaro Trekking Base</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
                <div class="text">Safari & Adventure</div>
                <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
            </div>
        </div>
    </div>

    <!-- GT Hotel Facilities Section Start -->
    <section class="gt-hotel-facilities-section-2 section-padding fix pt-5 mt-5">
        <div class="gt-hotel-facilities-shape">
            <img src="{{ asset('assets/img/home-2/hotel-facilites/Vector-01.png') }}" alt="img">
        </div>
        <div class="container">
            <div class="gt-hotel-facilities-wrapper-2">
                <div class="row g-4">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                        <div class="gt-hotel-left-images">
                            <img src="{{ asset('assets/img/new_images/hotel view_.jpg') }}" alt="Hotel View"
                                style="border-radius: 10px; width: 100%; object-fit: cover;">
                            <a href="https://www.instagram.com/primeland_hotel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                                target="_blank" class="video-btn ripple">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <div class="gt-counter">
                                <h2><span class="gt-count">21</span>+</h2>
                                <p>
                                    Years Of <br> Experience
                                </p>
                            </div>
                            <div class="gt-hotel-img wow fadeInUp" data-wow-delay=".5s">
                                <img src="{{ asset('assets/img/new_images/swimming view_(1).jpg') }}" alt="Swimming View"
                                    style="border-radius: 10px; object-fit: cover; max-width: 250px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="gt-hotel-right-content">
                            <div class="gt-section-title mb-0">
                                <h6 class="wow fadeInUp">
                                    Hotel Facilities
                                </h6>
                                <h2 class="wow fadeInUp" data-wow-delay=".2s">
                                    The Primeland Standard
                                </h2>
                                <div class="prl-divider mt-3 mb-4 wow fadeInUp" data-wow-delay=".3s">
                                    <div class="prl-divider-line"></div>
                                    <div class="prl-divider-dot"></div>
                                    <div class="prl-divider-line"></div>
                                </div>
                            </div>
                            <p class="gt-hotel-text wow fadeInUp" data-wow-delay=".4s">
                                Primeland Hotel blends modern comfort with timeless design, offering sophisticated
                                spaces, elegant details, and a serene atmosphere crafted for unforgettable experiences
                                in Moshi.
                            </p>
                            <div class="gt-skill-feature-items">
                                <div class="gt-skill-feature wow fadeInUp" data-wow-delay=".3s">
                                    <h3 class="gt-box-title">Room Service</h3>
                                    <div class="gt-progress">
                                        <div class="gt-progress-bar" style="width: 90%;">
                                            <div class="gt-progress-value"><span class="counter-number2">90</span>%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gt-skill-feature wow fadeInUp" data-wow-delay=".4s">
                                    <h3 class="gt-box-title">Breakfast Included</h3>
                                    <div class="gt-progress">
                                        <div class="gt-progress-bar" style="width: 55%;">
                                            <div class="gt-progress-value"><span class="counter-number2">55</span>%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gt-skill-feature wow fadeInUp" data-wow-delay=".4s">
                                    <h3 class="gt-box-title">Laundry & Ironing</h3>
                                    <div class="gt-progress">
                                        <div class="gt-progress-bar" style="width: 79%;">
                                            <div class="gt-progress-value"><span class="counter-number2">79</span>%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ url('/rooms') }}" class="gt-theme-btn wow fadeInUp" data-wow-delay=".6s">VIEW All DETAILS</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GT Hotel Feature Section Start -->
    <section class="gt-hotel-feature-section-2 section-padding fix pt-0">
        <div class="container">
            <div class="gt-hotel-feature-area">
                <div class="gt-hotel-feature-items wow fadeInUp" data-wow-delay=".2s">
                    <div class="icon"> <i class="flaticon-fitness-center"></i> </div>
                    <div class="content"> <h3>Fitness Center</h3> <p>Fully equipped daily</p> </div>
                </div>
                <div class="gt-hotel-feature-items wow fadeInUp" data-wow-delay=".4s">
                    <div class="icon"> <i class="flaticon-disinfect"></i> </div>
                    <div class="content"> <h3>Impeccable Cleanliness</h3> <p>Daily housekeeping</p> </div>
                </div>
                <div class="gt-hotel-feature-items wow fadeInUp" data-wow-delay=".6s">
                    <div class="icon"> <i class="flaticon-suite"></i> </div>
                    <div class="content"> <h3>Rooms and Suites</h3> <p>Relaxation guaranteed</p> </div>
                </div>
                <div class="gt-hotel-feature-items wow fadeInUp" data-wow-delay=".8s">
                    <div class="icon"> <i class="flaticon-luggage"></i> </div>
                    <div class="content"> <h3>Store Luggage</h3> <p>Safe and secure space</p> </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GT Instagram Section Start -->
    <section class="gt-instagram-section section-padding pb-0 fix section-bg">
        <div class="container">
            <div class="gt-section-title text-center">
                <h6 class="justify-content-center wow fadeInUp"> OUR INSTAGRAM </h6>
                <h2 class="wow fadeInUp" data-wow-delay=".2s"> Primeland Hotel Moshi </h2>
                <div class="prl-divider justify-content-center mt-3 mb-4">
                    <div class="prl-divider-line"></div> <div class="prl-divider-dot"></div> <div class="prl-divider-line"></div>
                </div>
            </div>
        </div>
        <div class="swiper gt-instagram-slider">
            <div class="swiper-wrapper">
                 <div class="swiper-slide">
                    <div class="gt-instagram-image">
                        <img src="{{ asset('assets/img/new_images/PRIMELAND HOTEL BAR.jpg') }}" alt="img" style="width: 305px; height: 297px; object-fit: cover;">
                        <a href="https://www.instagram.com/primeland_hotel/" class="gt-icon" target="_blank"> <i class="fa-brands fa-instagram"></i> </a>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gt-instagram-image">
                        <img src="{{ asset('assets/img/new_images/restaurant_.jpg') }}" alt="img" style="width: 305px; height: 297px; object-fit: cover;">
                        <a href="https://www.instagram.com/primeland_hotel/" class="gt-icon" target="_blank"> <i class="fa-brands fa-instagram"></i> </a>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gt-instagram-image">
                        <img src="{{ asset('assets/img/new_images/swimming view_.jpg') }}" alt="img" style="width: 305px; height: 297px; object-fit: cover;">
                        <a href="https://www.instagram.com/primeland_hotel/" class="gt-icon" target="_blank"> <i class="fa-brands fa-instagram"></i> </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
