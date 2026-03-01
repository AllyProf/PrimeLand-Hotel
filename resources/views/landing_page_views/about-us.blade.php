@extends('layouts.new_landing')

@section('title', 'About Us - PrimeLand Hotel')

@section('content')
<!-- Breadcrumb Section Start -->
<div class="gt-breadcrumb-wrapper bg-cover" style="background-image: url('{{ asset('hotel_gallery/swimming view_(1).jpg') }}');">
    <div class="container">
        <div class="gt-page-heading">
            <div class="gt-breadcrumb-sub-title">
                <h1 class="text-white wow fadeInUp" data-wow-delay=".3s">About Us</h1>
            </div>
            <ul class="gt-breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
                <li>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li>
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li>
                    About
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- GT About Section Start -->
<section class="gt-about-section-2 section-padding fix section-bg-3">
    <div class="gt-about-shape">
        <img src="{{ asset('landing-assets/img/home-2/about/shape-01.png') }}" alt="img">
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
                            Welcome to PrimeLand Hotel & Resort
                            </h2>
                        </div>
                        <div class="gt-about-box-items">
                            <div class="row align-items-center">
                                <div class="col-lg-5 wow fadeInUp" data-wow-delay=".4s">
                                    <div class="gt-about-images">
                                        <img src="{{ asset('hotel_gallery/hotel view_.jpg') }}" alt="img">
                                        <span class="title-box">
                                            <img src="{{ asset('landing-assets/img/home-2/about/tir-vector.png') }}" alt="img">
                                            ESTABLISHED 2014
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
                                                <p>To provide a sanctuary of comfort and elegance for every traveler.</p>
                                            </div>
                                        </div>
                                        <div class="gt-icon-box style-2">
                                            <div class="icon">
                                                <i class="flaticon-leadership"></i>
                                            </div>
                                            <div class="content">
                                                <h3>Our Vision</h3>
                                                <p>To be the leading luxury hotel choice in Moshi, known for exceptional hospitality.</p>
                                            </div>
                                        </div>
                                        <a href="{{ url('/rooms') }}" class="gt-theme-btn">EXPLORE ROOMS</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 wow fadeInUp" data-wow-delay=".5s">
                    <div class="gt-about-right-image">
                        <div class="gt-about-image">
                            <img src="{{ asset('hotel_gallery/room_(5).jpg') }}" alt="img">
                            <div class="gt-counter-box">
                                <h2><span class="gt-count">50</span>+</h2>
                                <h4>LUXURY ROOMS</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GT Marquee Section Start -->
<div class="marquee-section fix section-padding pt-0">
    <div class="marquee">
        <div class="marquee-group">
            <div class="text">Luxury Accommodation</div>
            <div class="text">
                <img src="{{ asset('landing-assets/img/home-1/star.png') }}" alt="img">
            </div>
            <div class="text">Premium Dining</div>
            <div class="text">
                <img src="{{ asset('landing-assets/img/home-1/star.png') }}" alt="img">
            </div>
            <div class="text">Refreshing Pool</div>
            <div class="text">
                <img src="{{ asset('landing-assets/img/home-1/star.png') }}" alt="img">
            </div>
            <div class="text">Exceptional Service</div>
            <div class="text">
                <img src="{{ asset('landing-assets/img/home-1/star.png') }}" alt="img">
            </div>
        </div>
    </div>
</div>

<!-- GT Hotel Facilities Section Start -->
<section class="gt-hotel-facilities-section-2 section-padding pt-0 fix">
    <div class="gt-hotel-facilities-shape">
        <img src="{{ asset('landing-assets/img/home-2/hotel-facilites/Vector-01.png') }}" alt="img">
    </div>
    <div class="container">
        <div class="gt-hotel-facilities-wrapper-2">
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="gt-hotel-left-images">
                          <img src="{{ asset('hotel_gallery/restaurant_.jpg') }}" alt="img">
                        <div class="gt-counter">
                            <h2><span class="gt-count">10</span>+</h2>
                            <p>
                                Years Of <br> Experience
                            </p>
                        </div>
                        <div class="gt-hotel-img wow fadeInUp" data-wow-delay=".5s">
                            <img src="{{ asset('hotel_gallery/coffee_.jpg') }}" alt="img">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="gt-hotel-right-content">
                        <div class="gt-section-title mb-0">
                            <h6 class="wow fadeInUp">
                                   Hotel Experience
                            </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s">
                                Modern Comfort & Style
                            </h2>
                        </div>
                        <p class="gt-hotel-text wow fadeInUp" data-wow-delay=".4s">
                            PrimeLand Hotel blends modern luxury with timeless design, offering sophisticated spaces, elegant details, and a serene atmosphere crafted for unforgettable experiences in Moshi.
                        </p>
                        <div class="gt-skill-feature-items">
                            <div class="gt-skill-feature wow fadeInUp" data-wow-delay=".3s">
                                <h3 class="gt-box-title">Room Service Satisfaction</h3>
                                <div class="gt-progress">
                                    <div class="gt-progress-bar" style="width: 95%;">
                                        <div class="gt-progress-value"><span>95</span>%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="gt-skill-feature wow fadeInUp" data-wow-delay=".4s">
                                <h3 class="gt-box-title">Guest Return Rate</h3>
                                <div class="gt-progress">
                                    <div class="gt-progress-bar" style="width: 85%;">
                                        <div class="gt-progress-value"><span>85</span>%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="gt-skill-feature wow fadeInUp" data-wow-delay=".4s">
                                <h3 class="gt-box-title">Facility Cleanliness</h3>
                                <div class="gt-progress">
                                    <div class="gt-progress-bar" style="width: 100%;">
                                        <div class="gt-progress-value"><span>100</span>%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ url('/services') }}" class="gt-theme-btn wow fadeInUp" data-wow-delay=".6s">VIEW SERVICES</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
