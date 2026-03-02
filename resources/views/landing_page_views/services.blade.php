@extends('layouts.new_landing')

@section('title', 'Services - PrimeLand Hotel')

@section('content')
<!-- Breadcrumb Section Start -->
<div class="gt-breadcrumb-wrapper bg-cover" style="background-image: url('{{ asset('hotel_gallery/swimming floating tray_.jpg') }}');">
    <div class="container">
        <div class="gt-page-heading">
            <div class="gt-breadcrumb-sub-title">
                <h1 class="text-white wow fadeInUp" data-wow-delay=".3s">Our Services</h1>
            </div>
            <ul class="gt-breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
                <li>
                    <a href="{{ url('/') }}">Home</a>
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

<!-- GT Service Section Start -->
<section class="gt-service-section fix section-padding">
    <div class="container">
        <div class="gt-section-title text-center">
            <h6 class="justify-content-center wow fadeInUp">
                FACILITIES
            </h6>
            <h2 class="wow fadeInUp" data-wow-delay=".2s">
                Hotel’s Facilities
            </h2>
        </div>
        <div class="row">
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp">
                <div class="service-box-items">
                    <div class="icon">
                       <i class="flaticon-key-card"></i>
                    </div>
                    <h4>Smart Key</h4>
                </div>    
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".2s">
                <div class="service-box-items">
                    <div class="icon">
                       <i class="flaticon-free-parking"></i>
                    </div>
                    <h4>Free Car Parking</h4>
                </div>    
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".4s">
                <div class="service-box-items">
                    <div class="icon">
                       <i class="flaticon-wifi-router"></i>
                    </div>
                    <h4>Fast Wifi Internet</h4>
                </div>    
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".6s">
                <div class="service-box-items">
                    <div class="icon">
                      <i class="flaticon-hotel-service"></i>
                    </div>
                    <h4>Room Service</h4>
                </div>    
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".8s">
                <div class="service-box-items">
                    <div class="icon">
                       <i class="flaticon-fast-food"></i>
                    </div>
                    <h4>Food & Drink</h4>
                </div>    
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="1s">
                <div class="service-box-items">
                    <div class="icon">
                       <i class="flaticon-swimming"></i>
                    </div>
                    <h4>Swimming Pool</h4>
                </div>    
            </div>
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
                            At PrimeLand Hotel, luxury is more than just a word — it's a tradition. From exquisite design to personalized service, every detail is thoughtfully crafted for your comfort.
                        </p>
                        <div class="faq-item active wow fadeInUp" data-wow-delay=".5s">
                            <h3>
                                <i class="fa-solid fa-circle-chevron-right"></i>
                                Hotel Accommodation
                            </h3>
                            <p class="faq-text">
                                Comfortable and well-appointed rooms designed for your relaxation. Each room features modern amenities and complimentary Wi-Fi.
                            </p>
                        </div>
                         <div class="faq-item wow fadeInUp" data-wow-delay=".6s">
                            <h3>
                                <i class="fa-solid fa-circle-chevron-right"></i>
                                Restaurant Delicious Meals
                            </h3>
                            <p class="faq-text">
                                Savor delicious meals prepared with fresh, local ingredients. Our restaurant offers a diverse menu featuring local and international food.
                            </p>
                        </div>
                         <div class="faq-item wow fadeInUp" data-wow-delay=".7s">
                            <h3>
                                <i class="fa-solid fa-circle-chevron-right"></i>
                                Bar & Lounge
                            </h3>
                            <p class="faq-text">
                                Unwind at our bar with a selection of premium beverages, cocktails, and light snacks. The perfect place to relax and socialize.
                            </p>
                        </div>
                         <div class="faq-item wow fadeInUp" data-wow-delay=".8s">
                            <h3>
                                <i class="fa-solid fa-circle-chevron-right"></i>
                                Swimming Pool
                            </h3>
                            <p class="faq-text">
                                Take a refreshing dip in our clean, well-maintained swimming pool. Perfect for relaxation and exercise throughout your stay.
                            </p>
                        </div>
                        <a href="{{ route('booking.index') }}" class="gt-theme-btn wow fadeInUp" data-wow-delay=".9s">BOOK NOW</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="swiper service-image-slider">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="service-image">
                                    <img src="{{ asset('hotel_gallery/room_(5).jpg') }}" alt="img">
                                    <span class="offer-text">Luxury Stays</span>
                                </div>
                            </div>
                             <div class="swiper-slide">
                                <div class="service-image">
                                    <img src="{{ asset('hotel_gallery/restaurant_.jpg') }}" alt="img">
                                    <span class="offer-text">Exquisite Dining</span>
                                </div>
                            </div>
                             <div class="swiper-slide">
                                <div class="service-image">
                                    <img src="{{ asset('hotel_gallery/swimming view_(1).jpg') }}" alt="img">
                                    <span class="offer-text">Relaxing Pool</span>
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
    <div class="right-shape">
        <img src="{{ asset('landing-assets/img/home-3/right-shape.png') }}" alt="img">
    </div>
    <div class="container">
        <div class="gt-enjoy-hotel-wrapper-3">
            <div class="row g-4 align-items-end">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="gt-hotel-images-items">
                        <div class="gt-hotel-image">
                            <img src="{{ asset('hotel_gallery/night view_.jpg') }}" alt="img">
                            <div class="gt-counter">
                                <h2><span class="gt-count">10</span>+</h2>
                                <p>
                                    Years Of <br> Experience
                                </p>
                            </div>
                            <div class="gt-hotel-image-2">
                                <img src="{{ asset('hotel_gallery/hotel view_.jpg') }}" alt="img">
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
                                Locate the Greatest Luxury Hotel Experience
                            </h2>
                        </div>
                        <p class="gt-hotel-text wow fadeInUp" data-wow-delay=".4s">
                            Experience the perfect stay in the heart of comfort. PrimeLand Hotel is strategically located to offer you both serenity and accessibility to major attractions.
                        </p>
                        <ul class="nav">
                            <li class="nav-item wow fadeInUp" data-wow-delay=".2s">
                                <a href="#Italian" data-bs-toggle="tab" class="nav-link active">
                                   PrimeLand Restaurant
                                </a>
                            </li>
                            <li class="nav-item wow fadeInUp" data-wow-delay=".4s">
                                <a href="#Bar" data-bs-toggle="tab" class="nav-link">
                                   Bar & Lounge
                                </a>
                            </li>
                            <li class="nav-item wow fadeInUp" data-wow-delay=".6s">
                                <a href="#Pool" data-bs-toggle="tab" class="nav-link">
                                   Swimming Area
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="Italian" class="tab-pane fade show active">
                                <div class="menu-list">
                                    <p>Working Hours:</p>
                                    <ul>    
                                        <li>
                                            <span>Breakfast:</span>
                                            7:00 am - 11:30 am
                                        </li>
                                        <li>
                                            <span>Lunch:</span>
                                            12:30 pm - 4:30 pm
                                        </li>
                                        <li>
                                            <span>Dinner:</span>
                                            7:30 pm - 10:30 pm
                                        </li>
                                    </ul>
                                    <a href="{{ url('/contact') }}" class="gt-theme-btn">CONTACT US</a>
                                </div>
                            </div>
                            <div id="Bar" class="tab-pane fade">
                                 <div class="menu-list">
                                    <p>Working Hours:</p>
                                    <ul>    
                                        <li>
                                            <span>Open Daily:</span>
                                            10:00 am - 12:00 am
                                        </li>
                                        <li>
                                            <span>Happy Hours:</span>
                                            5:00 pm - 7:00 pm
                                        </li>
                                    </ul>
                                    <a href="{{ url('/contact') }}" class="gt-theme-btn">CONTACT US</a>
                                </div>
                            </div>
                            <div id="Pool" class="tab-pane fade">
                                <div class="menu-list">
                                    <p>Working Hours:</p>
                                    <ul>    
                                        <li>
                                            <span>Morning Session:</span>
                                            6:00 am - 10:00 am
                                        </li>
                                        <li>
                                            <span>Afternoon Session:</span>
                                            11:00 am - 6:00 pm
                                        </li>
                                    </ul>
                                    <a href="{{ url('/contact') }}" class="gt-theme-btn">CONTACT US</a>
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
                <h2>
                    <span class="gt-count">50</span>+
                </h2>
                <p>Luxury Rooms</p>
            </div>
             <div class="gt-counter">
                <h2>
                    <span class="gt-count">100</span>%
                </h2>
                <p>Satisfaction</p>
            </div>
             <div class="gt-counter">
                <h2>
                    <span class="gt-count">10</span>+
                </h2>
                <p>Years Experience</p>
            </div>
             <div class="gt-counter border-none">
                <h2>
                    <span class="gt-count">24</span>/7
                </h2>
                <p>Availability</p>
            </div>
        </div>
    </div>
</div>

@endsection
