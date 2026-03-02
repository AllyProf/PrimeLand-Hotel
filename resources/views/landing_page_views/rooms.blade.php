@extends('layouts.new_landing')

@section('title', 'Accomodation - PrimeLand Hotel')

@section('content')
<!-- Breadcrumb Section Start -->
<div class="gt-breadcrumb-wrapper bg-cover" style="background-image: url('{{ asset('hotel_gallery/room_ (1).jpg') }}');">
    <div class="container">
        <div class="gt-page-heading">
            <div class="gt-breadcrumb-sub-title">
                <h1 class="text-white wow fadeInUp" data-wow-delay=".3s">Our Rooms</h1>
            </div>
            <ul class="gt-breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
                <li>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li>
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li>
                    Accomodation
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- GT Room Section Start -->
<section class="gt-room-section fix section-padding">
    <div class="container">
        <div class="gt-section-title text-center">
            <h6 class="justify-content-center wow fadeInUp">
                ROOMS & SUITES
            </h6>
            <h2 class="wow fadeInUp" data-wow-delay=".2s">
                Our Phenomenal Rooms
            </h2>
        </div>
        <div class="row g-4">
            <!-- Room 1 -->
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay=".2s">
                <div class="gt-room-items-3 item-2">
                    <div class="gt-room-image">
                        <img src="{{ asset('hotel_gallery/room_ (1).jpg') }}" alt="img">
                    </div>
                    <div class="gt-room-content">
                        <div class="gt-room-price">
                            <h6>
                                <span>$250</span> / Night
                            </h6>
                        </div>
                        <div class="gt-room-title">
                            <ul class="gt-room-info">
                                <li>
                                    <i class="flaticon-bed"></i> 02 Bed
                                </li>
                                <li>
                                    <i class="flaticon-square-shape"></i> 400 Sq.Ft
                                </li>
                            </ul>
                            <h3><a href="{{ route('booking.index') }}">Deluxe Room</a></h3>
                        </div>
                        <div class="gt-room-bottom">
                            <a href="{{ route('booking.index') }}" class="gt-theme-btn">ROOM DETAILS</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room 2 -->
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay=".4s">
                <div class="gt-room-items-3 item-3">
                    <div class="gt-room-image">
                        <img src="{{ asset('hotel_gallery/room_ (2).jpg') }}" alt="img">
                    </div>
                    <div class="gt-room-content">
                        <div class="gt-room-price">
                            <h6>
                                <span>$200</span> / Night
                            </h6>
                        </div>
                        <div class="gt-room-title">
                            <ul class="gt-room-info">
                                <li>
                                    <i class="flaticon-bed"></i> 01 Bed
                                </li>
                                <li>
                                    <i class="flaticon-square-shape"></i> 300 Sq.Ft
                                </li>
                            </ul>
                            <h3><a href="{{ route('booking.index') }}">Standard Room</a></h3>
                        </div>
                        <div class="gt-room-bottom">
                            <a href="{{ route('booking.index') }}" class="gt-theme-btn">ROOM DETAILS</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room 3 -->
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay=".6s">
                <div class="gt-room-items-3 item-4">
                    <div class="gt-room-image">
                        <img src="{{ asset('hotel_gallery/room_ (3).jpg') }}" alt="img">
                    </div>
                    <div class="gt-room-content">
                        <div class="gt-room-price">
                            <h6>
                                <span>$350</span> / Night
                            </h6>
                        </div>
                        <div class="gt-room-title">
                            <ul class="gt-room-info">
                                <li>
                                    <i class="flaticon-bed"></i> 03 Bed
                                </li>
                                <li>
                                    <i class="flaticon-square-shape"></i> 600 Sq.Ft
                                </li>
                            </ul>
                            <h3><a href="{{ route('booking.index') }}">Luxury Room</a></h3>
                        </div>
                        <div class="gt-room-bottom">
                            <a href="{{ route('booking.index') }}" class="gt-theme-btn">ROOM DETAILS</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room 4 -->
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay=".2s">
                <div class="gt-room-items-3">
                    <div class="gt-room-image">
                        <img src="{{ asset('hotel_gallery/room_ (4).jpg') }}" alt="img">
                    </div>
                    <div class="gt-room-content">
                        <div class="gt-room-price">
                            <h6>
                                <span>$450</span> / Night
                            </h6>
                        </div>
                        <div class="gt-room-title">
                            <ul class="gt-room-info">
                                <li>
                                    <i class="flaticon-bed"></i> 02 Bed
                                </li>
                                <li>
                                    <i class="flaticon-square-shape"></i> 500 Sq.Ft
                                </li>
                            </ul>
                            <h3><a href="{{ route('booking.index') }}">Honeymoon Suit</a></h3>
                        </div>
                        <div class="gt-room-bottom">
                            <a href="{{ route('booking.index') }}" class="gt-theme-btn">ROOM DETAILS</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room 5 -->
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay=".4s">
                <div class="gt-room-items-3 item-2">
                    <div class="gt-room-image">
                        <img src="{{ asset('hotel_gallery/room_ (5).jpg') }}" alt="img">
                    </div>
                    <div class="gt-room-content">
                        <div class="gt-room-price">
                            <h6>
                                <span>$150</span> / Night
                            </h6>
                        </div>
                        <div class="gt-room-title">
                            <ul class="gt-room-info">
                                <li>
                                    <i class="flaticon-bed"></i> 01 Bed
                                </li>
                                <li>
                                    <i class="flaticon-square-shape"></i> 250 Sq.Ft
                                </li>
                            </ul>
                            <h3><a href="{{ route('booking.index') }}">Economy Room</a></h3>
                        </div>
                        <div class="gt-room-bottom">
                            <a href="{{ route('booking.index') }}" class="gt-theme-btn">ROOM DETAILS</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room 6 -->
            <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay=".6s">
                <div class="gt-room-items-3 item-3">
                    <div class="gt-room-image">
                        <img src="{{ asset('hotel_gallery/room_ (6).jpg') }}" alt="img">
                    </div>
                    <div class="gt-room-content">
                        <div class="gt-room-price">
                            <h6>
                                <span>$280</span> / Night
                            </h6>
                        </div>
                        <div class="gt-room-title">
                            <ul class="gt-room-info">
                                <li>
                                    <i class="flaticon-bed"></i> 02 Bed
                                </li>
                                <li>
                                    <i class="flaticon-square-shape"></i> 450 Sq.Ft
                                </li>
                            </ul>
                            <h3><a href="{{ route('booking.index') }}">Executive Room</a></h3>
                        </div>
                        <div class="gt-room-bottom">
                            <a href="{{ route('booking.index') }}" class="gt-theme-btn">ROOM DETAILS</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GT Project Video Start -->
<div class="gt-project-video fix bg-cover" style="background-image: url('{{ asset('hotel_gallery/room_ (6).jpg') }}');">
    <div class="container">
        <div class="gt-video-wrapper text-center">
            <div class="video-btn wow fadeInUp" data-wow-delay=".3s">
                <a href="https://www.youtube.com/watch?v=S3U-13_c0Bw" class="video-link" data-lity><i class="fa-solid fa-play"></i></a>
            </div>
            <h2 class="text-white wow fadeInUp" data-wow-delay=".5s">
                Experience Luxury Like <br> Never Before
            </h2>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lity/2.4.1/lity.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lity/2.4.1/lity.min.js"></script>
@endsection
