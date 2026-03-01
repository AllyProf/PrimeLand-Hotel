@extends('layouts.new_landing')

@section('title', 'PrimeLand Hotel - Luxury Stays in Moshi')

@section('content')
<!-- GT Hero Section Start -->
<section class="gt-hero-section fix hero-3">
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="{{ asset('landing-assets/img/home-3/hero/hero-video.mp4') }}" type="video/mp4">
    </video>

    <div class="container">
        <div class="hero-content">
            <div class="star wow fadeInUp">
                <img src="{{ asset('landing-assets/img/home-1/hero/star.svg') }}" alt="img">
                <img src="{{ asset('landing-assets/img/home-1/hero/star.svg') }}" alt="img">
                <img src="{{ asset('landing-assets/img/home-1/hero/star.svg') }}" alt="img">
                <img src="{{ asset('landing-assets/img/home-1/hero/star.svg') }}" alt="img">
            </div>
            <h1 class="wow fadeInUp" data-wow-delay=".3s">
                Primeland Hotel <br>
                Best Hotel in Kilimanjaro
            </h1>
            <p class="wow fadeInUp" data-wow-delay=".5s">
                Comfort in Every Stay
            </p>
            <div class="hero-button wow fadeInUp" data-wow-delay=".7s">
                <a href="{{ url('/contact') }}" class="gt-theme-btn">contact us</a>
                <a href="{{ url('/rooms') }}" class="gt-theme-btn style-2">our rooms</a>
            </div>
        </div>
    </div>
</section>

<!-- GT Booking Section Start -->
<section class="gt-booking section-bg-3">
    <div class="container">
        <div class="gt-booking-reserve-wrapper style-2">
            <form action="{{ route('booking.index') }}" method="GET">
                <div class="row g-4 row-cols-xxl-5 align-items-center row-cols-xl-4 row-cols-lg-3 row-cols-md-2 row-cols-1">
                    <div class="col wow fadeInUp">
                        <div class="form-clt mt-0">
                            <span>Check In</span>
                            <input type="date" name="check_in" required>
                        </div>
                    </div>
                    <div class="col wow fadeInUp" data-wow-delay=".2s">
                        <div class="form-clt mt-0">
                            <span>Check Out</span>
                            <input type="date" name="check_out" required>
                        </div>
                    </div>
                    <div class="col wow fadeInUp" data-wow-delay=".4s">
                        <div class="form-clt mt-0">
                            <span>Adults</span>
                            <div class="form">
                                <select class="single-select w-100">
                                    <option>01</option>
                                    <option>02</option>
                                    <option>03</option>
                                    <option>04</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col wow fadeInUp" data-wow-delay=".6s">
                        <div class="form-clt mt-0">
                            <span>Children</span>
                            <div class="form">
                                <select class="single-select w-100">
                                    <option>01</option>
                                    <option>02</option>
                                    <option>03</option>
                                    <option>04</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col wow fadeInUp" data-wow-delay=".8s">
                        <div class="form-clt mt-0">
                            <button class="gt-theme-btn w-100" type="submit">SEARCH NOW</button>
                        </div>
                    </div>
                </div>
            </form>
            <h4 class="text-white">Check-out time: before 11:00 am; check-in time: after 2:00 pm</h4>
        </div>
    </div>
</section>

<!-- GT Service Section Start -->
<section class="gt-service-section fix section-padding section-bg-3">
    <div class="left-shape">
        <img src="{{ asset('landing-assets/img/home-3/service/left-shape.png') }}" alt="img">
    </div>
    <div class="container">
        <div class="gt-service-wrapper-3">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="service-content">
                        <div class="gt-section-title mb-0">
                            <h6 class="wow fadeInUp">
                                SERVICES
                            </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s">
                            Our Offerings
                            </h2>
                        </div>
                        <p class="service-text wow fadeInUp" data-wow-delay=".4s">
                            At PrimeLand Hotel, luxury is more than just a word — it's a tradition. From exquisite design to personalized service, every detail is thoughtfully curated.
                        </p>
                        <div class="faq-item wow fadeInUp" data-wow-delay=".5s">
                            <h3>
                                <i class="fa-solid fa-circle-chevron-right"></i>
                                Spa Retreat
                            </h3>
                            <p class="faq-text">
                                At our hotel, luxury is more than just a word — it's a tradition. From exquisite design to personalized service, every detail is thoughtfully 
                            </p>
                        </div>
                         <div class="faq-item active wow fadeInUp" data-wow-delay=".6s">
                            <h3>
                                <i class="fa-solid fa-circle-chevron-right"></i>
                                Family Fun Package
                            </h3>
                            <p class="faq-text">
                                At our hotel, luxury is more than just a word — it's a tradition. From exquisite design to personalized service, every detail is thoughtfully 
                            </p>
                        </div>
                         <div class="faq-item wow fadeInUp" data-wow-delay=".7s">
                            <h3>
                                <i class="fa-solid fa-circle-chevron-right"></i>
                                Traveler Special
                            </h3>
                            <p class="faq-text">
                                At our hotel, luxury is more than just a word — it's a tradition. From exquisite design to personalized service, every detail is thoughtfully 
                            </p>
                        </div>
                         <div class="faq-item wow fadeInUp" data-wow-delay=".8s">
                            <h3>
                                <i class="fa-solid fa-circle-chevron-right"></i>
                                Romantic Getaway
                            </h3>
                            <p class="faq-text">
                                At our hotel, luxury is more than just a word — it's a tradition. From exquisite design to personalized service, every detail is thoughtfully 
                            </p>
                        </div>
                        <a href="{{ url('/services') }}" class="gt-theme-btn wow fadeInUp" data-wow-delay=".9s">VIEW ALL SERVICE</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="swiper service-image-slider">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="service-image">
                                    <img src="{{ asset('landing-assets/img/home-3/service/service-01.jpg') }}" alt="img">
                                    <span class="offer-text">From $120 / NIGHT</span>
                                </div>
                            </div>
                             <div class="swiper-slide">
                                <div class="service-image">
                                    <img src="{{ asset('landing-assets/img/home-3/service/service-01.jpg') }}" alt="img">
                                    <span class="offer-text">From $120 / NIGHT</span>
                                </div>
                            </div>
                             <div class="swiper-slide">
                                <div class="service-image">
                                    <img src="{{ asset('landing-assets/img/home-3/service/service-01.jpg') }}" alt="img">
                                    <span class="offer-text">From $120 / NIGHT</span>
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

<!-- GT About Section Start -->
<section class="gt-about-section-3 section-padding fix">
    <div class="right-shape">
        <img src="{{ asset('landing-assets/img/home-3/about/right-shape.png') }}" alt="img">
    </div>
    <div class="container">
        <div class="gt-about-wrapper-3">
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="gt-about-images-item">
                        <div class="gt-about-image">
                            <img src="{{ asset('landing-assets/img/home-3/about/about-01.jpg') }}" alt="img">
                            <div class="gt-about-image-2">
                                <img src="{{ asset('landing-assets/img/home-3/about/about-02.jpg') }}" alt="img">
                            </div>
                            <div class="about-video">
                                <a href="https://www.youtube.com/watch?v=Cn4G2lZ_g2I" class="video-btn ripple video-popup">
                                    <i class="fa-solid fa-play"></i>
                                </a>
                                <div class="text-circle">
                                    <img src="{{ asset('landing-assets/img/home-3/about/text-circle.png') }}" alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="gt-about-content">
                        <div class="gt-section-title mb-0">
                            <h6 class="wow fadeInUp">
                                    ABOUT US
                            </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s">
                                Welcome to PrimeLand Hotel & Resort
                            </h2>
                        </div>
                        <p class="gt-about-text wow fadeInUp" data-wow-delay=".4s">
                            At PrimeLand Hotel, luxury is more than just a word — it's a tradition. From exquisite design to personalized service, every detail is thoughtfully curated to create unforgettable experiences. Whether you're here for relaxation or celebration.
                        </p>
                        <ul class="gt-icon-items wow fadeInUp" data-wow-delay=".6s">
                            <li>
                                <div class="icon">
                                    <i class="flaticon-24-hour-service"></i>
                                </div>
                                <div class="content">
                                    <h4>24/7 Front Desk & Concierge</h4>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <i class="flaticon-all-day"></i>
                                </div>
                                <div class="content">
                                    <h4>In-Room <br> Dining</h4>
                                </div>
                            </li>
                        </ul>
                        <ul class="gt-about-list wow fadeInUp" data-wow-delay=".8s">
                            <li>
                                <i class="flaticon-arrow-right"></i>
                                Modern & Comfortable Rooms
                            </li>
                            <li>
                                <i class="flaticon-arrow-right"></i>
                                Business Lounge & Meeting Rooms
                            </li>
                            <li>
                                <i class="flaticon-arrow-right"></i>
                                Laundry & Dry Cleaning Services
                            </li>
                        </ul>
                        <a href="{{ url('/about-us') }}" class="gt-theme-btn mt-5 wow fadeInUp" data-wow-delay=".9s">LEARN MORE</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GT Room Section Start -->
<section class="gt-room-section fix section-padding bg-cover" style="background-image: url('{{ asset('landing-assets/img/home-3/room/room-bg.png') }}');">
    <div class="container">
        <div class="gt-room-wrapper-3">
            <div class="gt-section-title-area">
                <div class="gt-section-title">
                    <h6 class="wow fadeInUp">
                         ROOMS
                    </h6>
                    <h2 class="text-white wow fadeInUp" data-wow-delay=".2s">
                       Choose Your Room
                    </h2>
                </div>
                <a href="{{ url('/rooms') }}" class="gt-theme-btn wow fadeInUp" data-wow-delay=".4s">VIEW All DETAILS</a>
            </div>
            <div class="gt-room-top-items">
                <div class="row justify-content-center">
                    <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                        <div class="top-title">
                            <a href="{{ url('/rooms') }}">
                                Executive Room <img src="{{ asset('landing-assets/img/home-3/room/sm-01.jpg') }}" alt="img">
                            </a>
                        </div>
                    </div>
                     <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                        <div class="top-title">
                            <a href="{{ url('/rooms') }}">
                                Deluxe Room <img src="{{ asset('landing-assets/img/home-3/room/sm-02.jpg') }}" alt="img">
                            </a>
                        </div>
                    </div>
                     <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".6s">
                        <div class="top-title">
                            <a href="{{ url('/rooms') }}">
                                Queen Room <img src="{{ asset('landing-assets/img/home-3/room/sm-03.jpg') }}" alt="img">
                            </a>
                        </div>
                    </div>
                     <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".8s">
                        <div class="top-title">
                            <a href="{{ url('/rooms') }}">
                                Superior Room <img src="{{ asset('landing-assets/img/home-3/room/sm-04.jpg') }}" alt="img">
                            </a>
                        </div>
                    </div>
                     <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".9s">
                        <div class="top-title">
                            <a href="{{ url('/rooms') }}">
                                Executive Suite <img src="{{ asset('landing-assets/img/home-3/room/sm-05.jpg') }}" alt="img">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="room-slider-image-3">
        <div class="swiper room-slider-3">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="room-thumb">
                        <img src="{{ asset('landing-assets/img/home-3/room/room-01.jpg') }}" alt="img">
                        <div class="room-content">
                            <span>Rates From  $120</span>
                            <div class="content">
                                <h3><a href="{{ url('/rooms') }}">Deluxe Room</a></h3>
                                <ul>
                                    <li>
                                        <img src="{{ asset('landing-assets/img/home-3/room/bed.png') }}" alt="img">
                                        02 Beds
                                    </li>
                                    <li>
                                        <img src="{{ asset('landing-assets/img/home-3/room/room.png') }}" alt="img">
                                        80 sqr
                                    </li>
                                    <li>
                                        <img src="{{ asset('landing-assets/img/home-3/room/man.png') }}" alt="img">
                                    02 Guests
                                    </li>
                                </ul>
                                <a href="{{ url('/rooms') }}" class="gt-theme-btn">ROOM DETAILS</a>
                            </div>
                        </div>
                    </div>     
                </div>
                <div class="swiper-slide">
                    <div class="room-thumb">
                        <img src="{{ asset('landing-assets/img/home-3/room/room-02.jpg') }}" alt="img">
                        <div class="room-content">
                            <span>Rates From  $120</span>
                            <div class="content">
                                <h3><a href="{{ url('/rooms') }}">Deluxe Room</a></h3>
                                <ul>
                                    <li>
                                        <img src="{{ asset('landing-assets/img/home-3/room/bed.png') }}" alt="img">
                                        02 Beds
                                    </li>
                                    <li>
                                        <img src="{{ asset('landing-assets/img/home-3/room/room.png') }}" alt="img">
                                        80 sqr
                                    </li>
                                    <li>
                                        <img src="{{ asset('landing-assets/img/home-3/room/man.png') }}" alt="img">
                                    02 Guests
                                    </li>
                                </ul>
                                <a href="{{ url('/rooms') }}" class="gt-theme-btn">ROOM DETAILS</a>
                            </div>
                        </div>
                    </div>     
                </div>
                <div class="swiper-slide">
                    <div class="room-thumb">
                        <img src="{{ asset('landing-assets/img/home-3/room/room-03.jpg') }}" alt="img">
                        <div class="room-content">
                            <span>Rates From  $120</span>
                            <div class="content">
                                <h3><a href="{{ url('/rooms') }}">Deluxe Room</a></h3>
                                <ul>
                                    <li>
                                        <img src="{{ asset('landing-assets/img/home-3/room/bed.png') }}" alt="img">
                                        02 Beds
                                    </li>
                                    <li>
                                        <img src="{{ asset('landing-assets/img/home-3/room/room.png') }}" alt="img">
                                        80 sqr
                                    </li>
                                    <li>
                                        <img src="{{ asset('landing-assets/img/home-3/room/man.png') }}" alt="img">
                                    02 Guests
                                    </li>
                                </ul>
                                <a href="{{ url('/rooms') }}" class="gt-theme-btn">ROOM DETAILS</a>
                            </div>
                        </div>
                    </div>     
                </div>
            </div>
        </div>  
    </div>
</section>

<!-- GT Enjoy Hotel Section Start -->
<section class="gt-enjoy-hotel-section-3 section-padding">
    <div class="right-shape">
        <img src="{{ asset('landing-assets/img/home-3/right-shape.png') }}" alt="img">
    </div>
    <div class="container">
        <div class="gt-enjoy-hotel-wrapper-3">
            <div class="row g-4 align-items-end">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="gt-hotel-images-items">
                        <div class="gt-hotel-image">
                            <img src="{{ asset('landing-assets/img/home-3/enjoy-hotel/02.jpg') }}" alt="img">
                            <div class="gt-counter">
                                <h2><span class="gt-count">46</span>+</h2>
                                <p>
                                    Experience <br> Hoteler
                                </p>
                            </div>
                            <div class="gt-hotel-image-2">
                                <img src="{{ asset('landing-assets/img/home-3/enjoy-hotel/01.jpg') }}" alt="img">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay=".5s">
                    <div class="gt-enjoy-hotel-content">
                        <div class="gt-section-title mb-0">
                            <h6 class="wow fadeInUp">
                                    ENJOY YOUR DAY
                            </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s">
                                Locate the Greatest Luxury City Hotel
                            </h2>
                        </div>
                        <p class="gt-hotel-text wow fadeInUp" data-wow-delay=".4s">
                            Discover the perfect stay in the heart of the city. Our hotel is strategically located near major business districts, top attractions, and transport hubs.
                        </p>
                        <ul class="nav">
                            <li class="nav-item wow fadeInUp" data-wow-delay=".2s">
                                <a href="#Italian" data-bs-toggle="tab" class="nav-link active">
                                   Italian Restaurant
                                </a>
                            </li>
                            <li class="nav-item wow fadeInUp" data-wow-delay=".4s">
                                <a href="#Spa" data-bs-toggle="tab" class="nav-link">
                                   Spa Complex
                                </a>
                            </li>
                            <li class="nav-item wow fadeInUp" data-wow-delay=".6s">
                                <a href="#Children" data-bs-toggle="tab" class="nav-link">
                                   Children's Animators
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="Italian" class="tab-pane fade show active">
                                <div class="menu-list">
                                    <p>Working Hours:</p>
                                    <ul>    
                                        <li>
                                            <span>Breakfast     :</span>
                                            7:00am - 11:30am
                                        </li>
                                        <li>
                                            <span>Lunch     :</span>
                                        0:30pm - 4:30pm
                                        </li>
                                        <li>
                                            <span>Dinner     :</span>
                                        7:30pm - 10:30pm
                                        </li>
                                        <li>
                                            <span>Evening Bar      :</span>
                                        11:00pm - 02:00pm
                                        </li>
                                    </ul>
                                    <a href="{{ url('/services') }}" class="gt-theme-btn">READ MORE</a>
                                </div>
                            </div>
                            <div id="Spa" class="tab-pane fade">
                                 <div class="menu-list">
                                    <p>Working Hours:</p>
                                    <ul>    
                                        <li>
                                            <span>Breakfast     :</span>
                                            7:00am - 11:30am
                                        </li>
                                        <li>
                                            <span>Lunch     :</span>
                                        0:30pm - 4:30pm
                                        </li>
                                        <li>
                                            <span>Dinner     :</span>
                                        7:30pm - 10:30pm
                                        </li>
                                        <li>
                                            <span>Evening Bar      :</span>
                                        11:00pm - 02:00pm
                                        </li>
                                    </ul>
                                    <a href="{{ url('/services') }}" class="gt-theme-btn">READ MORE</a>
                                </div>
                            </div>
                            <div id="Children" class="tab-pane fade">
                                <div class="menu-list">
                                    <p>Working Hours:</p>
                                    <ul>    
                                        <li>
                                            <span>Breakfast     :</span>
                                            7:00am - 11:30am
                                        </li>
                                        <li>
                                            <span>Lunch     :</span>
                                        0:30pm - 4:30pm
                                        </li>
                                        <li>
                                            <span>Dinner     :</span>
                                        7:30pm - 10:30pm
                                        </li>
                                        <li>
                                            <span>Evening Bar      :</span>
                                        11:00pm - 02:00pm
                                        </li>
                                    </ul>
                                    <a href="{{ url('/services') }}" class="gt-theme-btn">READ MORE</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--GT Team Section Start -->
<section class="team-section-3 fix section-padding section-bg"> 
    <div class="container">
        <div class="gt-section-title text-center">
            <h6 class="wow fadeInUp justify-content-center">
                   OUR TEAM
            </h6>
            <h2 class="wow fadeInUp" data-wow-delay=".2s">
                Expert Team Persons
            </h2>
        </div>
        <div class="row">
            <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                <div class="team-card-item-3">
                    <div class="team-image">
                        <img src="{{ asset('landing-assets/img/home-3/team/team-01.jpg') }}" alt="img">
                        <div class="team-content">
                            <h4>
                                <a href="#">Jessica Brown</a>
                            </h4>
                            <p>
                                Guest Relations Officer
                            </p>
                        </div>
                        <div class="social-icon d-flex align-items-center">
                             <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                             <span>Follow On:</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                <div class="team-card-item-3">
                    <div class="team-image">
                        <img src="{{ asset('landing-assets/img/home-3/team/team-02.jpg') }}" alt="img">
                        <div class="team-content">
                            <h4>
                                <a href="#">Shikhon Islam</a>
                            </h4>
                            <p>
                                Guest Relations Officer
                            </p>
                        </div>
                        <div class="social-icon d-flex align-items-center">
                             <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                             <span>Follow On:</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".7s">
                <div class="team-card-item-3">
                    <div class="team-image">
                        <img src="{{ asset('landing-assets/img/home-3/team/team-03.jpg') }}" alt="img">
                        <div class="team-content">
                            <h4>
                                <a href="#">Nguyen Ralph</a>
                            </h4>
                            <p>
                                Guest Relations Officer
                            </p>
                        </div>
                        <div class="social-icon d-flex align-items-center">
                             <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                             <span>Follow On:</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--  GT Offer Section Start -->
<section class="gt-offer-section-3 parallaxie fix section-padding bg-cover" style="background-image: url('{{ asset('landing-assets/img/home-3/hotel-offer.jpg') }}');">
    <div class="left-shape">
        <img src="{{ asset('landing-assets/img/home-3/left-shape.png') }}" alt="img">
        <a href="https://www.youtube.com/watch?v=Cn4G2lZ_g2I" class="video-btn ripple video-popup">
            <i class="fa-solid fa-play"></i>
        </a>
    </div>
    <span class="book-text">BOOKING NOW</span>
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="gt-offer-content-left-3">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="img" style="max-height: 80px; filter: brightness(0) invert(1);">
                    </a>
                    <h2 class="wow fadeInUp" data-wow-delay=".5s">
                        Summer Big <br>
                        Offer
                    </h2>
                    <h5 class="wow fadeInUp" data-wow-delay=".7s">STARTS FROM</h5>
                    <h4 class="wow fadeInUp" data-wow-delay=".7s">$599.00</h4>
                    <p class="wow fadeInUp" data-wow-delay=".9">
                        Experience the ultimate summer getaway at PrimeLand Hotel. Enjoy exclusive discounts and world-class amenities during your stay with us.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!--  GT Testimonial Section Start -->
<section class="testimonial-section-3 section-padding fix section-bg-3">
    <div class="shape">
        <img src="{{ asset('landing-assets/img/home-3/testimonial/testimonial-bg.png') }}" alt="img">
    </div>
    <div class="container-fluid">
        <div class="row g-4 justify-content-center">
            <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                <div class="testimonial-box-item-3">
                    <p>
                        From the moment we arrived, every detail was flawless. The staff anticipated our every need,
                    </p>
                    <div class="client-info-item">
                        <div class="client-item">
                            <div class="client-image">
                                <img src="{{ asset('landing-assets/img/home-3/testimonial/client-01.png') }}" alt="img">
                            </div>
                            <div class="info-content">
                                <h4>John Doe</h4>
                                <span>Product Manager</span>
                            </div>
                        </div>
                        <div class="quate-icon">
                            <img src="{{ asset('landing-assets/img/home-3/testimonial/quate.svg') }}" alt="img">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                <div class="testimonial-box-item-3">
                    <p>
                        From the moment we arrived, every detail was flawless. The staff anticipated our every need,
                    </p>
                    <div class="client-info-item">
                        <div class="client-item">
                            <div class="client-image">
                                <img src="{{ asset('landing-assets/img/home-3/testimonial/client-02.png') }}" alt="img">
                            </div>
                            <div class="info-content">
                                <h4>Hannah Nicollet</h4>
                                <span>Product Manager</span>
                            </div>
                        </div>
                        <div class="quate-icon">
                            <img src="{{ asset('landing-assets/img/home-3/testimonial/quate.svg') }}" alt="img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 align-items-center">
            <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                <div class="testimonial-box-item-3 style-2">
                    <p>
                        From the moment we arrived, every detail was flawless. The staff anticipated our every need,
                    </p>
                    <div class="client-info-item">
                        <div class="client-item">
                            <div class="client-image">
                                <img src="{{ asset('landing-assets/img/home-3/testimonial/client-03.png') }}" alt="img">
                            </div>
                            <div class="info-content">
                                <h4>Danny Ocean</h4>
                                <span>Product Manager</span>
                            </div>
                        </div>
                        <div class="quate-icon">
                            <img src="{{ asset('landing-assets/img/home-3/testimonial/quate.svg') }}" alt="img">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                <div class="gt-section-title text-center">
                    <h6 class="justify-content-center"> TESTIMONIAL</h6>
                    <h2>What Our Clients Say</h2>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".7s">
                <div class="testimonial-box-item-3 style-2">
                    <p>
                        From the moment we arrived, every detail was flawless. The staff anticipated our every need,
                    </p>
                    <div class="client-info-item">
                        <div class="client-item">
                            <div class="client-image">
                                <img src="{{ asset('landing-assets/img/home-3/testimonial/client-04.png') }}" alt="img">
                            </div>
                            <div class="info-content">
                                <h4>John Doe</h4>
                                <span>Product Manager</span>
                            </div>
                        </div>
                        <div class="quate-icon">
                            <img src="{{ asset('landing-assets/img/home-3/testimonial/quate.svg') }}" alt="img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                <div class="testimonial-box-item-3">
                    <p>
                        From the moment we arrived, every detail was flawless. The staff anticipated our every need,
                    </p>
                    <div class="client-info-item">
                        <div class="client-item">
                            <div class="client-image">
                                <img src="{{ asset('landing-assets/img/home-3/testimonial/client-05.png') }}" alt="img">
                            </div>
                            <div class="info-content">
                                <h4>Brock Weqner</h4>
                                <span>Product Manager</span>
                            </div>
                        </div>
                        <div class="quate-icon">
                            <img src="{{ asset('landing-assets/img/home-3/testimonial/quate.svg') }}" alt="img">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                <div class="testimonial-box-item-3">
                    <p>
                        From the moment we arrived, every detail was flawless. The staff anticipated our every need,
                    </p>
                    <div class="client-info-item">
                        <div class="client-item">
                            <div class="client-image">
                                <img src="{{ asset('landing-assets/img/home-3/testimonial/client-06.png') }}" alt="img">
                            </div>
                            <div class="info-content">
                                <h4>Christina Roy</h4>
                                <span>Product Manager</span>
                            </div>
                        </div>
                        <div class="quate-icon">
                            <img src="{{ asset('landing-assets/img/home-3/testimonial/quate.svg') }}" alt="img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- News Section Start -->
<section class="news-section section-padding fix">
    <div class="container">
        <div class="gt-section-title-area">
          <div class="gt-section-title">
                <h6 class="wow fadeInUp"> LASTEST ARTICLES</h6>
                <h2 class="wow fadeInUp" data-wow-delay=".3s">Latest News & Articles</h2>
          </div>
          <a href="{{ url('/blog') }}" class="gt-theme-btn wow fadeInUp" data-wow-delay=".4s">VIEW ALL ARTICLES</a>
        </div>
        <div class="row">
            <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                <div class="news-card-items-3">
                    <div class="news-image">
                        <img src="{{ asset('landing-assets/img/home-3/news/news-1.jpg') }}" alt="img">
                        <div class="post-box">
                            <h4>17<br><span>April</span></h4>
                        </div>
                    </div>
                    <div class="news-content">
                        <ul class="news-meta">
                            <li>
                                <img src="{{ asset('landing-assets/img/home-3/arroow.png') }}" alt="img">
                                By Admin
                            </li>
                            <li class="style-2">
                                Business 
                            </li>
                        </ul>
                        <h3>
                            <a href="#">
                                Why Our Hotel Is Ideal for Remote Work Travelers
                            </a>
                        </h3>
                        <a href="#" class="link-btn">READ MORE</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                <div class="news-card-items-3">
                    <div class="news-image">
                        <img src="{{ asset('landing-assets/img/home-3/news/news-2.jpg') }}" alt="img">
                        <div class="post-box">
                            <h4>17<br><span>April</span></h4>
                        </div>
                    </div>
                    <div class="news-content">
                        <ul class="news-meta">
                            <li>
                                <img src="{{ asset('landing-assets/img/home-3/arroow.png') }}" alt="img">
                                By Admin
                            </li>
                            <li class="style-2">
                                City Hotel
                            </li>
                        </ul>
                        <h3>
                            <a href="#">
                                The Best Coffee Shops Within Walking Distance
                            </a>
                        </h3>
                        <a href="#" class="link-btn">READ MORE</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".7s">
                <div class="news-card-items-3">
                    <div class="news-image">
                        <img src="{{ asset('landing-assets/img/home-3/news/news-3.jpg') }}" alt="img">
                        <div class="post-box">
                            <h4>17<br><span>April</span></h4>
                        </div>
                    </div>
                    <div class="news-content">
                        <ul class="news-meta">
                            <li>
                               <img src="{{ asset('landing-assets/img/home-3/arroow.png') }}" alt="img">
                                By Admin
                            </li>
                            <li class="style-2">
                                Business 
                            </li>
                        </ul>
                        <h3>
                            <a href="#">
                                5 Relaxing Things to Do After a Busy Day in the City
                            </a>
                        </h3>
                        <a href="#" class="link-btn">READ MORE</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
