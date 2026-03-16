@extends('layouts.new_landing')

@section('title', 'Primeland Hotel | Boutique Hotel in Moshi Near Mount Kilimanjaro')

@section('content')
<!-- GT Hero Section Start -->
<section class="gt-hero-section fix hero-3">
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="{{ asset('assets/img/home-3/hero/hero-video.mp4') }}" type="video/mp4">
    </video>
    <div class="container">
        <div class="hero-content">
            <!-- Hotel name eyebrow -->
            <p class="wow fadeInUp" data-wow-delay=".1s"
                style="font-size:13px;font-weight:700;letter-spacing:4px;text-transform:uppercase;color:#e77a3a;margin-bottom:10px;">
                PRIMELAND HOTEL
            </p>

            <!-- Main headline -->
            <h1 class="wow fadeInUp" data-wow-delay=".3s" style="margin-bottom:0;">
                Comfort In Every Stay
            </h1>

            <!-- Decorative divider -->
            <div class="wow fadeInUp" data-wow-delay=".4s"
                style="display:flex;align-items:center;gap:12px;margin:18px 0 16px;">
                <div style="height:2px;width:50px;background:#e77a3a;"></div>
                <span
                    style="color:#e77a3a;font-size:12px;letter-spacing:3px;text-transform:uppercase;font-weight:600;">Best Hotel In Moshi for your Stay</span>
                <div style="height:2px;width:50px;background:#e77a3a;"></div>
            </div>

            <!-- Sub-caption -->
            <p class="wow fadeInUp" data-wow-delay=".5s"
                style="font-size:16px;color:rgba(255,255,255,0.85);margin-bottom:30px;">
                STAY WITH US: Relax & Unwind
            </p>

            <div class="hero-button wow fadeInUp" data-wow-delay=".7s">
                <a href="{{ url('/contact') }}" class="gt-theme-btn">Book Now</a>
                <a href="{{ url('/rooms') }}" class="gt-theme-btn style-2">Our Rooms</a>
            </div>
        </div>
    </div>
</section>

<!-- GT Booking Section Start -->
<section class="gt-booking section-bg-3">
    <div class="container">
        <div class="gt-booking-reserve-wrapper style-2">
            <div class="row align-items-center justify-content-center text-center">
                <div class="col-12 wow fadeInUp">
                    <h3 class="text-white mb-2" style="font-size: 28px; font-weight: 600;">Online Booking System
                        Coming Soon</h3>
                    <p class="text-white mb-0" style="font-size: 16px;">In the meantime, please contact us directly
                        by phone or email to make a reservation.</p>
                    <a href="tel:+255677155156" class="gt-theme-btn mt-4"
                        style="background-color: white !important; color: var(--prl-brand) !important;">Call Us
                        Now</a>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- GT About Section Start -->
<section class="gt-about-section fix section-padding pt-0" style="margin-top:40px;">
    <div class="gt-right-shape">
        <img src="{{ asset('assets/img/home-1/about/right-shape.png') }}" alt="img">
    </div>
    <div class="container">
        <div class="gt-about-wrapper">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="gt-about-image-items text-center text-lg-start">
                        <div class="gt-about-image wow fadeInUp" data-wow-delay=".2s">
                            <img src="{{ asset('assets/img/home-1/about/about-01.jpg') }}" alt="img" class="wow fadeInUp"
                                data-wow-delay=".2s">
                            <div class="gt-about-image-2 wow fadeInUp" data-wow-delay=".4s">
                                <img src="{{ asset('assets/img/home-1/about/about-02.jpg') }}" alt="img">
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
                                Welcome to Primeland Hotel
                            </h2>
                            <div class="prl-divider wow fadeInUp" data-wow-delay=".3s">
                                <div class="prl-line"></div>
                                <div class="prl-dot"></div>
                                <div class="prl-line"></div>
                            </div>
                        </div>
                        <p class="gt-about-text wow fadeInUp" data-wow-delay=".4s">
                            We are a small boutique style hotel in the heart of Moshi Town, Kilimanjaro region, -Tanzania. Conveniently located near Moshi’s town center approximately 1.5km away (7-10 min car ride)/ and 45 minutes’ drive from JRO airport. Relax and unwind your day on a visit to Moshi, whether on your business trip, family visit or Kilimanjaro trek and safaris. Our hotel is managed by a team of well trained staff who are ready to take care of your stay. Enjoy our cozy and neat interiors for a calm and peaceful overnight.
                        </p>
                        <div class="gt-about-button wow fadeInUp" data-wow-delay="1s">
                            <a href="{{ url('/about-us') }}" class="gt-theme-btn">READ MORE</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GT Service Section Start -->
<section class="gt-service-section fix section-padding bg-cover" 
    style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('assets/img/new_images/hotel view_.jpg') }}'); padding: 100px 0;">
    <div class="container">
        <div class="gt-section-title text-center">
            <h6 class="justify-content-center wow fadeInUp text-white">
                OUR SERVICES
            </h6>
            <h2 class="wow fadeInUp text-white" data-wow-delay=".2s">
                Our Services at Primeland Hotel
            </h2>
            <div class="prl-divider justify-content-center wow fadeInUp" data-wow-delay=".3s">
                <div class="prl-line" style="background: white;"></div>
                <div class="prl-dot" style="background: white;"></div>
                <div class="prl-line" style="background: white;"></div>
            </div>
        </div>
    </div>
</section>

<section class="gt-service-box-section fix section-padding pt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".2s">
                <div class="service-box-items">
                    <div class="icon">
                        <i class="flaticon-hotel-service"></i>
                    </div>
                    <h4>ACCOMMODATION</h4>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".3s">
                <div class="service-box-items">
                    <div class="icon">
                        <i class="flaticon-24-hour-service"></i>
                    </div>
                    <h4>24/7 FRONT DESK SERVICE</h4>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".4s">
                <div class="service-box-items">
                    <div class="icon">
                        <i class="flaticon-swimming"></i>
                    </div>
                    <h4>SWIMMING POOL</h4>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".5s">
                <div class="service-box-items">
                    <div class="icon">
                        <i class="flaticon-fast-food"></i>
                    </div>
                    <h4>RESTAURANT AND POOL BAR</h4>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".6s">
                <div class="service-box-items">
                    <div class="icon">
                        <i class="flaticon-wifi-router"></i>
                    </div>
                    <h4>FREE WIFI</h4>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay=".7s">
                <div class="service-box-items">
                    <div class="icon">
                        <i class="flaticon-disinfect"></i>
                    </div>
                    <h4>24/7 SECURITY</h4>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- OUR ROOMS Text Section -->
<section class="gt-about-section-3 fix" style="padding-top: 40px; padding-bottom: 120px;">
    <div class="right-shape">
        <img src="{{ asset('assets/img/home-3/about/right-shape.png') }}" alt="img">
    </div>
    <div class="container">
        <div class="gt-about-wrapper-3">
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="gt-about-images-item">
                        <div class="gt-about-image">
                            <img src="{{ asset('assets/img/home-3/about/about-01.jpg') }}" alt="img">
                            <div class="gt-about-image-2">
                                <img src="{{ asset('assets/img/home-3/about/about-02.jpg') }}" alt="img">
                            </div>
                            <div class="about-video">
                                <a href="https://www.instagram.com/primeland_hotel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                                    target="_blank" class="video-btn ripple">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                                <div class="text-circle">
                                    <img src="{{ asset('assets/img/home-3/about/text-circle.png') }}" alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="gt-about-content">
                        <div class="gt-section-title mb-0">
                            <h6 class="wow fadeInUp"> OUR ROOMS </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s"> Cozy Rooms Designed for Your Comfort </h2>
                            <div class="prl-divider wow fadeInUp" data-wow-delay=".3s">
                                <div class="prl-line"></div>
                                <div class="prl-dot"></div>
                                <div class="prl-line"></div>
                            </div>
                        </div>
                        <p class="gt-about-text wow fadeInUp" data-wow-delay=".4s">
                            Every room features big windows for fresh air, queen-size beds, a walk-in shower with
                            hot &amp; cold water, air conditioning, Smart TV, mini refrigerator, tea/coffee
                            facilities, bottled mineral water, and free Wi-Fi.
                        </p>
                        <ul class="gt-icon-items wow fadeInUp" data-wow-delay=".6s">
                            <li>
                                <div class="icon"> <i class="flaticon-24-hour-service"></i> </div>
                                <div class="content"> <h4>24/7 Front Desk &amp; Security</h4> </div>
                            </li>
                            <li>
                                <div class="icon"> <i class="flaticon-all-day"></i> </div>
                                <div class="content"> <h4>Free Daily <br> Breakfast</h4> </div>
                            </li>
                        </ul>
                        <ul class="gt-about-list wow fadeInUp" data-wow-delay=".8s">
                            <li> <i class="flaticon-arrow-right"></i> Air Conditioning &amp; Free Wi-Fi </li>
                            <li> <i class="flaticon-arrow-right"></i> Smart TV &amp; Mini Refrigerator </li>
                            <li> <i class="flaticon-arrow-right"></i> Free Toiletries &amp; Mineral Water </li>
                        </ul>
                        <a href="{{ url('/rooms') }}" class="gt-theme-btn mt-5 wow fadeInUp" data-wow-delay=".9s">VIEW ROOMS</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Choose Your Room Slider Section -->
<section class="gt-room-section fix section-padding bg-cover"
    style="background-image: url('{{ asset('assets/img/home-3/room/room-bg.png') }}');">
    <div class="container">
        <div class="gt-room-wrapper-3">
            <div class="gt-section-title-area">
                <div class="gt-section-title">
                    <h6 class="wow fadeInUp"> ROOMS </h6>
                    <h2 class="text-white wow fadeInUp" data-wow-delay=".2s"> Choose Your Room </h2>
                    <div class="prl-divider wow fadeInUp" data-wow-delay=".3s">
                        <div class="prl-line"></div>
                        <div class="prl-dot"></div>
                        <div class="prl-line"></div>
                    </div>
                </div>
                <a href="{{ url('/rooms') }}" class="gt-theme-btn wow fadeInUp" data-wow-delay=".4s">VIEW All DETAILS</a>
            </div>

        </div>
    </div>
    <div class="room-slider-image-3">
        <div class="swiper room-slider-3">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="room-thumb">
                        <img src="{{ asset('assets/img/home-3/room/room-01.jpg') }}" alt="Single Room">
                        <div class="room-content">
                            <span>From $70 / Night</span>
                            <div class="content">
                                <h3><a href="{{ url('/rooms/single-room') }}">Single Room</a></h3>
                                <ul>
                                    <li> <img src="{{ asset('assets/img/home-3/room/bed.png') }}" alt="img"> 1 Queen Bed </li>
                                    <li> <img src="{{ asset('assets/img/home-3/room/man.png') }}" alt="img"> 1 Guest </li>
                                    <li> <img src="{{ asset('assets/img/home-3/room/room.png') }}" alt="img"> B&amp;B Basis </li>
                                </ul>
                                <a href="{{ url('/rooms/single-room') }}" class="gt-theme-btn">ROOM DETAILS</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="room-thumb">
                        <img src="{{ asset('assets/img/home-3/room/room-02.jpg') }}" alt="Double Room">
                        <div class="room-content">
                            <span>From $85 / Night</span>
                            <div class="content">
                                <h3><a href="{{ url('/rooms/double-room') }}">Double Room</a></h3>
                                <ul>
                                    <li> <img src="{{ asset('assets/img/home-3/room/bed.png') }}" alt="img"> 1 Queen Bed </li>
                                    <li> <img src="{{ asset('assets/img/home-3/room/man.png') }}" alt="img"> 2 Guests </li>
                                    <li> <img src="{{ asset('assets/img/home-3/room/room.png') }}" alt="img"> B&amp;B Basis </li>
                                </ul>
                                <a href="{{ url('/rooms/double-room') }}" class="gt-theme-btn">ROOM DETAILS</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="room-thumb">
                        <img src="{{ asset('assets/img/home-3/room/room-03.jpg') }}" alt="Twin Room">
                        <div class="room-content">
                            <span>From $95 / Night</span>
                            <div class="content">
                                <h3><a href="{{ url('/rooms/twin-room') }}">Twin Room</a></h3>
                                <ul>
                                    <li> <img src="{{ asset('assets/img/home-3/room/bed.png') }}" alt="img"> 2 Twin Beds </li>
                                    <li> <img src="{{ asset('assets/img/home-3/room/man.png') }}" alt="img"> 2 Guests </li>
                                    <li> <img src="{{ asset('assets/img/home-3/room/room.png') }}" alt="img"> B&amp;B Basis </li>
                                </ul>
                                <a href="{{ url('/rooms/twin-room') }}" class="gt-theme-btn">ROOM DETAILS</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- GT Testimonial Section Start -->
<section class="gt-testimonial-section section-padding fix section-bg-3">
    <div class="gt-shape">
        <img src="{{ asset('assets/img/home-1/testimonial/Vector-01.png') }}" alt="img">
    </div>
    <div class="container">
        <div class="gt-testimonial-wrapper">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="gt-testimonial-left-content">
                        <div class="gt-section-title mb-0">
                            <h6 class="wow fadeInUp"> TESTIMONIAL </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s"> What Our Visitors Are Saying </h2>
                            <div class="prl-divider wow fadeInUp" data-wow-delay=".3s">
                                <div class="prl-line"></div>
                                <div class="prl-dot"></div>
                                <div class="prl-line"></div>
                            </div>
                        </div>
                        <p class="gt-testimonial-text wow fadeInUp" data-wow-delay=".4s">
                            We pride ourselves on delivering unforgettable experiences but don't just take our
                            word for it. Our guests return time and again for the impeccable service, exquisite
                            surroundings, and the feeling of true indulgence.
                        </p>

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="swiper gt-testimonial-slider">
                        <div class="swiper-wrapper">
                            <!-- Review 1: Serah (Kenya) -->
                            <div class="swiper-slide">
                                <div class="gt-testimonial-box">
                                    <div class="quote-icon"> <img src="{{ asset('assets/img/home-1/testimonial/quote-01.png') }}" alt="img"> </div>
                                    <div class="star" style="display:flex;align-items:center;gap:6px;margin-bottom:10px;">
                                        <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i>
                                        <span style="margin-left:8px;font-size:11px;color:#999;font-weight:600;letter-spacing:1px;"><i class="fa-brands fa-google" style="color:#4285F4;margin-right:3px;"></i>Google Review</span>
                                    </div>
                                    <p class="gt-testi-text"> "The staff are helpful and understanding. The hotel is very clean. The space is beautiful, and it offers calmness and is very quiet." </p>
                                    <div class="gt-client-info">
                                        <div class="gt-client-image" style="overflow:hidden;border-radius:50%;width:55px;height:55px;flex-shrink:0;">
                                            <img src="https://flagcdn.com/w160/ke.png" alt="Kenya" style="width:100%;height:100%;object-fit:cover;">
                                        </div>
                                        <div class="gt-client-content"> <h4>Serah</h4> <p>Kenya</p> </div>
                                    </div>
                                    <button class="gt-testi-read-more-btn" data-author="Serah" data-flag="https://flagcdn.com/w160/ke.png" data-location="Kenya" data-full-text="The staff are helpful and understanding. The hotel is very clean. The space is beautiful, and it offers calmness and is very quiet."> Read Full Review <i class="fa-solid fa-arrow-right" style="margin-left:5px;font-size:11px;"></i> </button>
                                </div>
                            </div>
                            <!-- Review 2: Sharon Jeruto (Kenya) -->
                            <div class="swiper-slide">
                                <div class="gt-testimonial-box">
                                    <div class="quote-icon"> <img src="{{ asset('assets/img/home-1/testimonial/quote-01.png') }}" alt="img"> </div>
                                    <div class="star" style="display:flex;align-items:center;gap:6px;margin-bottom:10px;">
                                        <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i>
                                        <span style="margin-left:8px;font-size:11px;color:#999;font-weight:600;letter-spacing:1px;"><i class="fa-brands fa-google" style="color:#4285F4;margin-right:3px;"></i>Google Review</span>
                                    </div>
                                    <p class="gt-testi-text"> "The staff from the gate, to the reception to the kitchen and the servers are just but amaazzzing. Special mention to Elfas, Jackie, Godfrey, Nice, and Pamelina." </p>
                                    <div class="gt-client-info">
                                        <div class="gt-client-image" style="overflow:hidden;border-radius:50%;width:55px;height:55px;flex-shrink:0;">
                                            <img src="https://flagcdn.com/w160/ke.png" alt="Kenya" style="width:100%;height:100%;object-fit:cover;">
                                        </div>
                                        <div class="gt-client-content"> <h4>Sharon Jeruto</h4> <p>Kenya</p> </div>
                                    </div>
                                    <button class="gt-testi-read-more-btn" data-author="Sharon Jeruto" data-flag="https://flagcdn.com/w160/ke.png" data-location="Kenya" data-full-text="The staff from the gate, to the reception to the kitchen and the servers are just but amaazzzing. Special mention to Elfas, Jackie, Godfrey, Nice, and Pamelina. The food is to die for, and the attention to the guests is top notch. The rooms are very clean and the toiletries and beddings are very high standard. By the way, you can see Mt. Kilimanjaro from the upper deck. My family has such an amazing amazing time here."> Read Full Review <i class="fa-solid fa-arrow-right" style="margin-left:5px;font-size:11px;"></i> </button>
                                </div>
                            </div>
                            <!-- Review 3: Arina (Moldova) -->
                            <div class="swiper-slide">
                                <div class="gt-testimonial-box">
                                    <div class="quote-icon"> <img src="{{ asset('assets/img/home-1/testimonial/quote-01.png') }}" alt="img"> </div>
                                    <div class="star" style="display:flex;align-items:center;gap:6px;margin-bottom:10px;">
                                        <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i>
                                        <span style="margin-left:8px;font-size:11px;color:#999;font-weight:600;letter-spacing:1px;"><i class="fa-brands fa-google" style="color:#4285F4;margin-right:3px;"></i>Google Review</span>
                                    </div>
                                    <p class="gt-testi-text"> "The best food in Moshi. We liked it here. Very friendly staff, clean rooms, low prices for food and more than that, the food was fresh and very delicious." </p>
                                    <div class="gt-client-info">
                                        <div class="gt-client-image" style="overflow:hidden;border-radius:50%;width:55px;height:55px;flex-shrink:0;">
                                            <img src="https://flagcdn.com/w160/md.png" alt="Moldova" style="width:100%;height:100%;object-fit:cover;">
                                        </div>
                                        <div class="gt-client-content"> <h4>Arina</h4> <p>Moldova</p> </div>
                                    </div>
                                    <button class="gt-testi-read-more-btn" data-author="Arina" data-flag="https://flagcdn.com/w160/md.png" data-location="Moldova" data-full-text="The best food in Moshi. We liked it here. Very friendly staff, clean rooms, low prices for food and more than that, the food was fresh and very delicious. The lady at the reception was very nice and allowed us to leave our luggage at the hotel while we were away in the mountains, without paying anything extra. I mention the food once again, which is super delicious!"> Read Full Review <i class="fa-solid fa-arrow-right" style="margin-left:5px;font-size:11px;"></i> </button>
                                </div>
                            </div>
                            <!-- Review 4: Maria Goretti (Norway) -->
                            <div class="swiper-slide">
                                <div class="gt-testimonial-box">
                                    <div class="quote-icon"> <img src="{{ asset('assets/img/home-1/testimonial/quote-01.png') }}" alt="img"> </div>
                                    <div class="star" style="display:flex;align-items:center;gap:6px;margin-bottom:10px;">
                                        <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i>
                                        <span style="margin-left:8px;font-size:11px;color:#999;font-weight:600;letter-spacing:1px;"><i class="fa-brands fa-google" style="color:#4285F4;margin-right:3px;"></i>Google Review</span>
                                    </div>
                                    <p class="gt-testi-text"> "A home away from home! Location, room decor, service, tasty local food and staff's friendliness and kindness." </p>
                                    <div class="gt-client-info">
                                        <div class="gt-client-image" style="overflow:hidden;border-radius:50%;width:55px;height:55px;flex-shrink:0;">
                                            <img src="https://flagcdn.com/w160/no.png" alt="Norway" style="width:100%;height:100%;object-fit:cover;">
                                        </div>
                                        <div class="gt-client-content"> <h4>Maria Goretti</h4> <p>Norway</p> </div>
                                    </div>
                                    <button class="gt-testi-read-more-btn" data-author="Maria Goretti" data-flag="https://flagcdn.com/w160/no.png" data-location="Norway" data-full-text="A home away from home! Location, room decor, service, tasty local food and staff's friendliness and kindness. They take care of you as if you were family. Thanks to manager Jackie for taking me to the hospital, staying with me all the way through and ensuring that everything went well when I got sick during my stay. Blessings!"> Read Full Review <i class="fa-solid fa-arrow-right" style="margin-left:5px;font-size:11px;"></i> </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Navigation Buttons -->
                    <div class="array-button-2 justify-content-center">
                        <button class="array-prev"><i class="fa-solid fa-chevron-left"></i></button>
                        <div class="swiper-dot1"> <div class="dot"></div> </div>
                        <button class="array-next"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Marquee Section -->
<div class="marquee-section fix" style="background-color: #e77a3a !important;">
    <style>
        .marquee-section, .marquee-section .marquee, .marquee-section .marquee-group { background-color: #e77a3a !important; }
        .marquee-section .marquee-group .text { color: white !important; }
        .marquee-section .marquee-group .text img { filter: brightness(0) invert(1) !important; }
    </style>
    <div class="marquee">
        <div class="marquee-group">
            <div class="text">Luxury Accommodation</div>
            <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
            <div class="text">Premier Hospitality</div>
            <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
            <div class="text">Gateway to Kilimanjaro</div>
        </div>
        <!-- Repeat for animation -->
        <div class="marquee-group">
            <div class="text">Luxury Accommodation</div>
            <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
            <div class="text">Premier Hospitality</div>
            <div class="text"> <img src="{{ asset('assets/img/home-1/star.png') }}" alt="img"> </div>
            <div class="text">Gateway to Kilimanjaro</div>
        </div>
    </div>
</div>

<!-- GT Enjoy Hotel Section Start -->
<section class="gt-enjoy-hotel-section-3" style="padding-top: 40px; padding-bottom: 120px;">
    <div class="right-shape"> <img src="{{ asset('assets/img/home-3/right-shape.png') }}" alt="img"> </div>
    <div class="container">
        <div class="gt-enjoy-hotel-wrapper-3">
            <div class="row g-4 align-items-end">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="gt-hotel-images-items">
                        <div class="gt-hotel-image">
                            <img src="{{ asset('assets/img/home-3/enjoy-hotel/02.jpg') }}" alt="img">
                            <div class="gt-counter">
                                <h2><span class="gt-count">100</span>+</h2>
                                <p> Happy<br>Guests </p>
                            </div>
                            <div class="gt-hotel-image-2"> <img src="{{ asset('assets/img/home-3/enjoy-hotel/01.jpg') }}" alt="img"> </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay=".5s">
                    <div class="gt-enjoy-hotel-content">
                        <div class="gt-section-title mb-0">
                            <h6 class="wow fadeInUp"> DINE &amp; UNWIND </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s"> Restaurant, Pool Bar &amp; More </h2>
                            <div class="prl-divider wow fadeInUp" data-wow-delay=".3s">
                                <div class="prl-line"></div> <div class="prl-dot"></div> <div class="prl-line"></div>
                            </div>
                        </div>
                        <p class="gt-hotel-text wow fadeInUp" data-wow-delay=".4s">
                            Enjoy a delicious breakfast every morning, savor fresh local meals at our restaurant,
                            and unwind with cold drinks at the pool bar. We cater to all tastes with a varied,
                            international menu.
                        </p>
                        <ul class="nav">
                            <li class="nav-item"> <a href="#Italian" data-bs-toggle="tab" class="nav-link active"> Restaurant </a> </li>
                            <li class="nav-item"> <a href="#Spa" data-bs-toggle="tab" class="nav-link"> Pool Bar </a> </li>
                            <li class="nav-item"> <a href="#Children" data-bs-toggle="tab" class="nav-link"> Swimming Pool </a> </li>
                        </ul>
                        <div class="tab-content">
                            <div id="Italian" class="tab-pane fade show active">
                                <div class="menu-list">
                                    <p>Restaurant Hours:</p>
                                    <ul>
                                        <li> <span>Breakfast :</span> 7:00am &ndash; 10:30am </li>
                                        <li> <span>Lunch :</span> 12:00pm &ndash; 3:00pm </li>
                                        <li> <span>Dinner :</span> 7:00pm &ndash; 10:00pm </li>
                                        <li> <span>Open Daily</span> </li>
                                    </ul>
                                    <a href="{{ url('/contact') }}" class="gt-theme-btn">BOOK A TABLE</a>
                                </div>
                            </div>
                            <div id="Spa" class="tab-pane fade">
                                <div class="menu-list">
                                    <p>Pool Bar Hours:</p>
                                    <ul>
                                        <li> <span>Drinks &amp; Cocktails :</span> 10:00am &ndash; 10:30pm </li>
                                        <li> <span>Snacks :</span> 12:00pm &ndash; 8:00pm </li>
                                        <li> <span>Happy Hour :</span> 5:00pm &ndash; 7:00pm </li>
                                        <li> <span>Open Daily</span> </li>
                                    </ul>
                                    <a href="{{ url('/contact') }}" class="gt-theme-btn">VIEW MENU</a>
                                </div>
                            </div>
                            <div id="Children" class="tab-pane fade">
                                <div class="menu-list">
                                    <p>Swimming Pool Hours:</p>
                                    <ul>
                                        <li> <span>Morning Dip :</span> 7:00am &ndash; 11:00am </li>
                                        <li> <span>General Access :</span> 11:00am &ndash; 6:30pm </li>
                                        <li> <span>Poolside Service :</span> 10:00am &ndash; 6:30pm </li>
                                        <li> <span>Open Daily</span> </li>
                                    </ul>
                                    <a href="{{ url('/rooms') }}" class="gt-theme-btn">VIEW ROOMS</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- EXCLUSIVE DEALS Section -->
<section class="gt-offer-section-3 parallaxie fix bg-cover" 
    style="margin-top: -40px; padding: 80px 0; background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('assets/img/new_images/hotel view_.jpg') }}');">
    <div class="left-shape">
        <img src="{{ asset('assets/img/new_images/left-shape (2).png') }}" alt="img">
        <a href="https://www.instagram.com/primeland_hotel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
            target="_blank" class="video-btn ripple">
            <i class="fa-brands fa-instagram"></i>
        </a>
    </div>
    <span class="book-text">EXCLUSIVE DEALS</span>
    <div class="container">
        <div class="row">
            <div class="col-xl-7 col-lg-8">
                <div class="prl-offer-card wow fadeInLeft" data-wow-delay=".3s">
                    <div class="gt-offer-content-left-3">
                        <h5 class="wow fadeInUp" data-wow-delay=".5s">LIMITED TIME OFFER</h5>
                        <h2 class="wow fadeInUp" data-wow-delay=".6s"> Stay 5, <br> Pay 4! </h2>
                        <div class="prl-divider wow fadeInUp" data-wow-delay=".7s">
                            <div class="prl-line"></div> <div class="prl-dot"></div>
                        </div>
                        <h4 class="wow fadeInUp" data-wow-delay=".8s">Save 20% on Your Extended Stay</h4>
                        <p class="wow fadeInUp" data-wow-delay=".9s">
                            Experience more of Moshi with our exclusive extended stay package. Reserve 5 consecutive
                            nights and enjoy your 5th night on us.
                            Includes daily buffet breakfast, a welcome bottle of house champagne, and flexible
                            check-in options.
                        </p>
                        <div class="offer-btn wow fadeInUp" data-wow-delay="1s">
                            <a href="{{ url('/contact') }}" class="gt-theme-btn">Claim This Offer <i class="fa-solid fa-arrow-right-long ms-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section fix pb-120 section-bg" style="margin-top: 0;">
    <div class="container">
        <div class="gt-faq-wrapper">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="gt-faq-content">
                        <div class="gt-section-title mb-0">
                            <h6 class="wow fadeInUp"> Ask Question </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s"> Your Questions <br> Answered </h2>
                        </div>
                        <p class="gt-faq-text wow fadeInUp" data-wow-delay=".5s">
                            Have questions about your stay, booking process, or our services? We've compiled answers to the most common inquiries.
                        </p>
                        <div class="gt-faq-button wow fadeInUp" data-wow-delay=".7s">
                            <a href="{{ url('/contact') }}" class="gt-theme-btn"> CONTACT US </a>
                            <a href="{{ url('/about-us') }}" class="gt-theme-btn gt-border-style"> ABOUT US </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp">
                    <div class="faq-items mt-0 ms-0">
                        <div class="accordion" id="accordionExample">
                            <!-- Item 1 -->
                            <div class="accordion-item wow fadeInUp" data-wow-delay=".3s">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        Is breakfast included in the room rate?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <p> Yes! We offer a free daily breakfast buffet for all our guests staying at Primeland Hotel. </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Item 2 -->
                            <div class="accordion-item wow fadeInUp" data-wow-delay=".5s">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false"
                                        aria-controls="collapseTwo">
                                        What are your check-in and check-out times?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse"
                                    aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <p> Our standard check-in time is at 2:00 PM, and check-out is at 10:00 AM. Late check-outs may be available upon request, subject to availability and additional fees. </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Item 3 -->
                            <div class="accordion-item wow fadeInUp" data-wow-delay=".7s">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        Do you offer airport transfer services?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <p> Yes, we offer secure and comfortable airport pickups and drop-offs for Kilimanjaro International Airport (JRO). Please inform us of your flight details in advance to arrange the transfer. </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Instagram Section -->
<div class="gt-instagram-section pt-80 pb-0 fix section-bg">
    <div class="container">
        <div class="gt-section-title text-center">
            <h6 class="justify-content-center wow fadeInUp"> FOLLOW US </h6>
            <h2 class="wow fadeInUp" data-wow-delay=".2s"> @Primeland Hotel on Instagram </h2>
            <div class="prl-divider justify-content-center mt-3 mb-4">
                <div class="prl-line"> </div> <div class="prl-dot"> </div> <div class="prl-line"> </div>
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
</div>
@endsection
