@extends('layouts.new_landing')

@section('title', 'Best Boutique Hotel in Moshi for Tourists | Kilimanjaro Safari & Trekking Accommodation')

@section('content')
    <!-- Breadcrumb Section Start -->
    <div class="gt-breadcrumb-wrapper bg-cover"
        style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('assets/img/new_images/room_.jpg') }}');">
        <div class="container">
            <div class="gt-page-heading">
                <div class="gt-breadcrumb-sub-title">
                    <h1 class=" text-white wow fadeInUp" data-wow-delay=".3s">Rooms & Suites</h1>
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
                        Rooms & Suites
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- GT Room Section Start -->
    <section class="gt-room-section fix section-padding section-bg">
        <div class="gt-room-wrapper">
            <div class="container">
                <div class="gt-section-title text-center">
                    <h6 class="wow fadeInUp justify-content-center">
                        Explore
                    </h6>
                    <h2 class="wow fadeInUp" data-wow-delay=".2s">
                        Affordable Hotel Rooms for Kilimanjaro Climbers
                    </h2>
                </div>
                <div class="row">
                    <!-- Twin Room -->
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="gt-room-box-items">
                            <div class="gt-thumb">
                                <img src="{{ asset('assets/img/new_images/room_(6).jpg') }}" alt="Twin room in Moshi hotel - Primeland Hotel"
                                    style="width: 100%; aspect-ratio: 4/3; object-fit: cover;">
                                <img src="{{ asset('assets/img/new_images/room_(6).jpg') }}" alt="Twin Room"
                                    style="width: 100%; aspect-ratio: 4/3; object-fit: cover;">
                                <span class="gt-post-box">
                                    $95 / NIGHT
                                </span>
                            </div>
                            <div class="gt-content">
                                <a href="{{ url('/rooms/twin-room') }}" class="gt-post-cat">Twin Room</a>
                                <h3><a href="{{ url('/rooms/twin-room') }}">Twin Room</a></h3>
                                <ul class="gt-list">
                                    <li>
                                        <i class="flaticon-bed-1"></i>
                                        2 Separate Twin Beds
                                    </li>
                                    <li>
                                        <i class="flaticon-user"></i>
                                        02 Guests
                                    </li>
                                </ul>
                                <a href="{{ url('/rooms/twin-room') }}" class="gt-link-btn">DISCOVER MORE</a>
                            </div>
                        </div>
                    </div>
                    <!-- Double Room -->
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="gt-room-box-items">
                            <div class="gt-thumb">
                                <img src="{{ asset('assets/img/new_images/room_(2).jpg') }}" alt="Double room in Moshi - Primeland Hotel"
                                    style="width: 100%; aspect-ratio: 4/3; object-fit: cover;">
                                <img src="{{ asset('assets/img/new_images/room_(2).jpg') }}" alt="Double Room"
                                    style="width: 100%; aspect-ratio: 4/3; object-fit: cover;">
                                <span class="gt-post-box">
                                    $85 / NIGHT
                                </span>
                            </div>
                            <div class="gt-content">
                                <a href="{{ url('/rooms/double-room') }}" class="gt-post-cat">Double Room</a>
                                <h3><a href="{{ url('/rooms/double-room') }}">Double Room</a></h3>
                                <ul class="gt-list">
                                    <li>
                                        <i class="flaticon-bed-1"></i>
                                        1 Queen Bed
                                    </li>
                                    <li>
                                        <i class="flaticon-user"></i>
                                        02 Guests
                                    </li>
                                </ul>
                                <a href="{{ url('/rooms/double-room') }}" class="gt-link-btn">DISCOVER MORE</a>
                            </div>
                        </div>
                    </div>
                    <!-- Single Room -->
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="gt-room-box-items">
                            <div class="gt-thumb">
                                <img src="{{ asset('assets/img/new_images/room_(4).jpg') }}" alt="Single room in Moshi hotel - Primeland Hotel"
                                    style="width: 100%; aspect-ratio: 4/3; object-fit: cover;">
                                <img src="{{ asset('assets/img/new_images/room_(4).jpg') }}" alt="Single Room"
                                    style="width: 100%; aspect-ratio: 4/3; object-fit: cover;">
                                <span class="gt-post-box">
                                    $70 / NIGHT
                                </span>
                            </div>
                            <div class="gt-content">
                                <a href="{{ url('/rooms/single-room') }}" class="gt-post-cat">Single Room</a>
                                <h3><a href="{{ url('/rooms/single-room') }}">Single Room</a></h3>
                                <ul class="gt-list">
                                    <li>
                                        <i class="flaticon-bed-1"></i>
                                        1 Queen Bed
                                    </li>
                                    <li>
                                        <i class="flaticon-user"></i>
                                        01 Guest
                                    </li>
                                </ul>
                                <a href="{{ url('/rooms/single-room') }}" class="gt-link-btn">DISCOVER MORE</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GT Hotel Facilities Section Start -->
    <section class="gt-hotel-facilities-section-2 section-padding fix">
        <div class="gt-hotel-facilities-shape">
            <img src="{{ asset('assets/img/home-2/hotel-facilites/Vector-01.png') }}" alt="img">
        </div>
        <div class="container">
            <div class="gt-hotel-facilities-wrapper-2">
                <div class="row g-4">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                        <div class="gt-hotel-left-images">
                            <img src="{{ asset('assets/img/new_images/hotel view_.jpg') }}" alt="Primeland Hotel View"
                                style="border-radius: 10px; object-fit: cover; width: 100%;">
                            <a href="https://www.instagram.com/primeland_hotel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                                target="_blank" class="video-btn ripple">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <div class="gt-counter">
                                <h2><span class="gt-count">19</span>+</h2>
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
                                    Primeland Standards
                                </h2>
                                <div class="prl-divider wow fadeInUp" data-wow-delay=".3s">
                                    <div class="prl-line"></div>
                                    <div class="prl-dot"></div>
                                    <div class="prl-line"></div>
                                </div>
                            </div>
                            <p class="gt-hotel-text wow fadeInUp" data-wow-delay=".4s">
                                Primeland Hotel blends local Tanzanian hospitality with modern comfort, offering
                                sophisticated spaces, elegant details, and a serene atmosphere in the heart of Moshi
                                Town.
                            </p>
                            <div class="gt-skill-feature-items">
                                <div class="gt-skill-feature wow fadeInUp" data-wow-delay=".3s">
                                    <h3 class="gt-box-title">Room Service</h3>
                                    <div class="gt-progress">
                                        <div class="gt-progress-bar"
                                            style="width: 90%; animation: 2.6s ease 0s 1 normal none running animate-positive; opacity: 1;">
                                            <div class="gt-progress-value"><span class="counter-number2">90</span>%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gt-skill-feature wow fadeInUp" data-wow-delay=".4s">
                                    <h3 class="gt-box-title">Breakfast Included</h3>
                                    <div class="gt-progress">
                                        <div class="gt-progress-bar"
                                            style="width: 55%; animation: 2.6s ease 0s 1 normal none running animate-positive; opacity: 1;">
                                            <div class="gt-progress-value"><span class="counter-number2">55</span>%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gt-skill-feature wow fadeInUp" data-wow-delay=".4s">
                                    <h3 class="gt-box-title">Laundry & Ironing</h3>
                                    <div class="gt-progress">
                                        <div class="gt-progress-bar"
                                            style="width: 79%; animation: 2.6s ease 0s 1 normal none running animate-positive; opacity: 1;">
                                            <div class="gt-progress-value"><span class="counter-number2">79</span>%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ url('/about-us') }}" class="gt-theme-btn wow fadeInUp" data-wow-delay=".6s">VIEW All
                                DETAILS</a>
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
                    <div class="icon">
                        <i class="flaticon-fitness-center"></i>
                    </div>
                    <div class="content">
                        <h3>Fitness Center</h3>
                        <p>Stay active in our gym.</p>
                    </div>
                </div>
                <div class="gt-hotel-feature-items wow fadeInUp" data-wow-delay=".4s">
                    <div class="icon">
                        <i class="flaticon-disinfect"></i>
                    </div>
                    <div class="content">
                        <h3>Disinfection</h3>
                        <p>Highest hygiene standards.</p>
                    </div>
                </div>
                <div class="gt-hotel-feature-items wow fadeInUp" data-wow-delay=".6s">
                    <div class="icon">
                        <i class="flaticon-suite"></i>
                    </div>
                    <div class="content">
                        <h3>Rooms and Suites</h3>
                        <p>Comfortable stay guaranteed.</p>
                    </div>
                </div>
                <div class="gt-hotel-feature-items wow fadeInUp" data-wow-delay=".8s">
                    <div class="icon">
                        <i class="flaticon-luggage"></i>
                    </div>
                    <div class="content">
                        <h3>Store Luggage</h3>
                        <p>Secure storage for items.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GT Instagram Section Start -->
    <div class="gt-instagram-section section-padding pb-0 fix section-bg">
        <div class="container">
            <div class="gt-section-title text-center">
                <h6 class="justify-content-center wow fadeInUp">
                    OUR INSTAGRAM
                </h6>
                <h2 class="wow fadeInUp" data-wow-delay=".2s">
                    Follow Us @Primeland Hotel
                </h2>
            </div>
        </div>
        <div class="swiper gt-instagram-slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="gt-instagram-image">
                        <img src="{{ asset('assets/img/new_images/PRIMELAND HOTEL BAR.jpg') }}" alt="img"
                            style="width: 305px; height: 297px; object-fit: cover;">
                        <a href="https://www.instagram.com/primeland_hotel/" class="gt-icon" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gt-instagram-image">
                        <img src="{{ asset('assets/img/new_images/restaurant_.jpg') }}" alt="img"
                            style="width: 305px; height: 297px; object-fit: cover;">
                        <a href="https://www.instagram.com/primeland_hotel/" class="gt-icon" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gt-instagram-image">
                        <img src="{{ asset('assets/img/new_images/swimming view_.jpg') }}" alt="img"
                            style="width: 305px; height: 297px; object-fit: cover;">
                        <a href="https://www.instagram.com/primeland_hotel/" class="gt-icon" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gt-instagram-image">
                        <img src="{{ asset('assets/img/new_images/room_(1).jpg') }}" alt="img"
                            style="width: 305px; height: 297px; object-fit: cover;">
                        <a href="https://www.instagram.com/primeland_hotel/" class="gt-icon" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gt-instagram-image">
                        <img src="{{ asset('assets/img/new_images/reception_.jpg') }}" alt="img"
                            style="width: 305px; height: 297px; object-fit: cover;">
                        <a href="https://www.instagram.com/primeland_hotel/" class="gt-icon" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gt-instagram-image">
                        <img src="{{ asset('assets/img/new_images/coffee_.jpg') }}" alt="img"
                            style="width: 305px; height: 297px; object-fit: cover;">
                        <a href="https://www.instagram.com/primeland_hotel/" class="gt-icon" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
