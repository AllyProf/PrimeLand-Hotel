@extends('layouts.new_landing')

@section('title', 'Our Services | Primeland Hotel - Moshi, Kilimanjaro')

@section('content')
<!-- Breadcrumb Section Start -->
<div class="gt-breadcrumb-wrapper bg-cover"
    style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('assets/img/new_images/swimming view_(1).jpg') }}');">
    <div class="container">
        <div class="gt-page-heading">
            <div class="gt-breadcrumb-sub-title">
                <h1 class=" text-white wow fadeInUp" data-wow-delay=".3s">Services</h1>
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
                    Services
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- GT Service Section Start (Facilities Icons) -->
<section class="gt-service-section fix section-padding">
    <div class="container">
        <div class="gt-section-title text-center">
            <h6 class="justify-content-center wow fadeInUp">
                FACILITIES
            </h6>
            <h2 class="wow fadeInUp" data-wow-delay=".2s">
                Primeland Facilities
            </h2>
            <div class="prl-divider justify-content-center mt-3 mb-4 wow fadeInUp" data-wow-delay=".3s">
                <div class="prl-divider-line"></div>
                <div class="prl-divider-dot"></div>
                <div class="prl-divider-line"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp">
                <div class="service-box-items">
                    <div class="icon"> <i class="flaticon-key-card"></i> </div>
                    <h4>Smart Key</h4>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".2s">
                <div class="service-box-items">
                    <div class="icon"> <i class="flaticon-free-parking"></i> </div>
                    <h4>Free Car Parking</h4>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".4s">
                <div class="service-box-items">
                    <div class="icon"> <i class="flaticon-wifi-router"></i> </div>
                    <h4>Fast Wifi Internet</h4>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".6s">
                <div class="service-box-items">
                    <div class="icon"> <i class="flaticon-hotel-service"></i> </div>
                    <h4>Room Service</h4>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".8s">
                <div class="service-box-items">
                    <div class="icon"> <i class="flaticon-fast-food"></i> </div>
                    <h4>Food & Drink</h4>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="1s">
                <div class="service-box-items">
                    <div class="icon"> <i class="flaticon-swimming"></i> </div>
                    <h4>Swimming Pool</h4>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GT Service Section Start (Standard) -->
<section class="gt-service-section fix section-padding section-bg-3">
    <div class="left-shape">
        <img src="{{ asset('assets/img/home-3/service/left-shape.png') }}" alt="img">
    </div>
    <div class="container">
        <div class="gt-service-wrapper-3">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="service-content">
                        <div class="gt-section-title mb-0">
                            <h6 class="wow fadeInUp"> SERVICES </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s"> The Primeland Standard </h2>
                            <div class="prl-divider mt-3 mb-4 wow fadeInUp" data-wow-delay=".3s">
                                <div class="prl-divider-line"></div>
                                <div class="prl-divider-dot"></div>
                                <div class="prl-divider-line"></div>
                            </div>
                        </div>
                        <p class="service-text wow fadeInUp" data-wow-delay=".4s">
                            Primeland Hotel blends modern comfort with timeless design, offering sophisticated
                            spaces, elegant details, and a serene atmosphere crafted for unforgettable experiences
                            in Moshi.
                        </p>
                        <div class="faq-item wow fadeInUp" data-wow-delay=".5s">
                            <h3> <i class="fa-solid fa-circle-chevron-right"></i> Restaurant & Pool Bar </h3>
                            <p class="faq-text"> Enjoy fresh local and international meals at our restaurant and relax at our pool bar. </p>
                        </div>
                         <div class="faq-item active wow fadeInUp" data-wow-delay=".6s">
                            <h3> <i class="fa-solid fa-circle-chevron-right"></i> Swimming Pool </h3>
                            <p class="faq-text"> Our beautiful swimming pool is the perfect place to cool off after a safari or hike. </p>
                        </div>
                        <div class="faq-item wow fadeInUp" data-wow-delay=".7s">
                            <h3> <i class="fa-solid fa-circle-chevron-right"></i> Laundry Services </h3>
                            <p class="faq-text"> Full laundry and dry cleaning services are provided upon request. </p>
                        </div>
                        <div class="faq-item wow fadeInUp" data-wow-delay=".8s">
                            <h3> <i class="fa-solid fa-circle-chevron-right"></i> Airport Shuttle </h3>
                            <p class="faq-text"> We offer convenient airport pick-up and drop-off at Kilimanjaro International Airport (JRO). </p>
                        </div>
                        <a href="{{ url('/rooms') }}" class="gt-theme-btn wow fadeInUp" data-wow-delay=".9s">VIEW OUR ROOMS</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="swiper service-image-slider">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="service-image">
                                    <img src="{{ asset('assets/img/home-3/service/service-01.jpg') }}" alt="img">
                                    <span class="offer-text">From $70 / NIGHT</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="service-image">
                                    <img src="{{ asset('assets/img/home-3/service/service-01.jpg') }}" alt="img">
                                    <span class="offer-text">From $85 / NIGHT</span>
                                </div>
                            </div>
                        </div>
                        <div class="array-button-2 justify-content-center">
                            <button class="array-next"><i class="fa-solid fa-chevron-left"></i></button>
                            <button class="array-prev"><i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GT Enjoy Hotel Section Start -->
<section class="gt-enjoy-hotel-section-3 section-padding">
    <div class="right-shape"> <img src="{{ asset('assets/img/home-3/right-shape.png') }}" alt="img"> </div>
    <div class="container">
        <div class="gt-enjoy-hotel-wrapper-3">
            <div class="row g-4 align-items-end">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="gt-hotel-images-items">
                        <div class="gt-hotel-image">
                            <img src="{{ asset('assets/img/home-3/enjoy-hotel/02.jpg') }}" alt="img">
                            <div class="gt-counter">
                                <h2><span class="gt-count">24</span>/7</h2>
                                <p> Front Desk <br> Service </p>
                            </div>
                            <div class="gt-hotel-image-2"> <img src="{{ asset('assets/img/home-3/enjoy-hotel/01.jpg') }}" alt="img"> </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay=".5s">
                    <div class="gt-enjoy-hotel-content">
                        <div class="gt-section-title mb-0">
                            <h6 class="wow fadeInUp"> ENJOY YOUR DAY </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s"> Primeland Hotel Experience </h2>
                            <div class="prl-divider mt-3 mb-4 wow fadeInUp" data-wow-delay=".3s">
                                <div class="prl-divider-line"></div>
                                <div class="prl-divider-dot"></div>
                                <div class="prl-divider-line"></div>
                            </div>
                        </div>
                        <p class="gt-hotel-text wow fadeInUp" data-wow-delay=".4s">
                            Discover the perfect stay in the heart of Moshi town. Our hotel is strategically located
                            perfectly for Kilimanjaro trips and safaris.
                        </p>
                        <ul class="nav">
                            <li class="nav-item"> <a href="#restaurant" data-bs-toggle="tab" class="nav-link active"> Restaurant </a> </li>
                            <li class="nav-item"> <a href="#poolbar" data-bs-toggle="tab" class="nav-link"> Pool Bar </a> </li>
                            <li class="nav-item"> <a href="#airport" data-bs-toggle="tab" class="nav-link"> Airport Shuttle </a> </li>
                        </ul>
                        <div class="tab-content">
                            <div id="restaurant" class="tab-pane fade show active">
                                <div class="menu-list">
                                    <p>Working Hours:</p>
                                    <ul>
                                        <li> <span>Breakfast :</span> 06:30am - 10:00am </li>
                                        <li> <span>Lunch :</span> 12:30pm - 03:00pm </li>
                                        <li> <span>Dinner :</span> 06:30pm - 10:00pm </li>
                                        <li> <span>Evening Bar :</span> 10:00pm - 11:30pm </li>
                                    </ul>
                                    <a href="{{ url('/contact') }}" class="gt-theme-btn">BOOK A TABLE</a>
                                </div>
                            </div>
                            <div id="poolbar" class="tab-pane fade">
                                <div class="menu-list">
                                    <p>Working Hours:</p>
                                    <ul>
                                        <li> <span>Pool Access :</span> 07:00am - 07:00pm </li>
                                        <li> <span>Bar Service :</span> 10:00am - 10:00pm </li>
                                        <li> <span>Light Tapas :</span> 12:00pm - 08:00pm </li>
                                        <li> <span>Happy Hour :</span> 05:00pm - 07:00pm </li>
                                    </ul>
                                    <a href="{{ url('/contact') }}" class="gt-theme-btn">READ MORE</a>
                                </div>
                            </div>
                            <div id="airport" class="tab-pane fade">
                                <div class="menu-list">
                                    <p>Working Hours:</p>
                                    <ul>
                                        <li> <span>Office Hours :</span> 08:00am - 06:00pm </li>
                                        <li> <span>Consultations :</span> 09:00am - 05:00pm </li>
                                        <li> <span>Custom Bookings :</span> 08:00am - 08:00pm </li>
                                        <li> <span>Guest Support :</span> 24/7 Available </li>
                                    </ul>
                                    <a href="{{ url('/contact') }}" class="gt-theme-btn">READ MORE</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GT Counter Section Start -->
<div class="gt-counter-section fix">
    <div class="container">
        <div class="gt-counter-wrapper">
             <div class="gt-counter">
                <h2> <span class="gt-count">3</span> </h2>
                <p>Room Types</p>
            </div>
            <div class="gt-counter">
                <h2> <span class="gt-count">8</span>+ </h2>
                <p>Countries Visited</p>
            </div>
            <div class="gt-counter">
                <h2> <span class="gt-count">43</span>min </h2>
                <p>To JRO Airport</p>
            </div>
             <div class="gt-counter border-none">
                <h2 style="letter-spacing:1px;"> 5&#9733; </h2>
                <p>Guest Ratings</p>
            </div>
        </div>
    </div>
</div>
@endsection
