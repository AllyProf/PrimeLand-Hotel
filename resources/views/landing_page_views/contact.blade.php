@extends('layouts.new_landing')

@section('title', 'Contact Us | Primeland Hotel - Moshi, Kilimanjaro')

@section('content')
<!-- Breadcrumb Section Start -->
<div class="gt-breadcrumb-wrapper bg-cover"
    style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('assets/img/new_images/room_(5).jpg') }}');">
    <div class="container">
        <div class="gt-page-heading">
            <div class="gt-breadcrumb-sub-title">
                <h1 class=" text-white wow fadeInUp" data-wow-delay=".3s">Contact Us</h1>
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
                    Contact Us
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- GT Contacts Section Start -->
<section class="gt-contacts-section section-padding fix">
    <div class="container">
        <div class="gt-contact-wrapper">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="gt-contact-left-items">
                        <div class="gt-section-title">
                            <h6 class="wow fadeInUp"> CONTACT US </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s"> Ready to Contact Us </h2>
                            <div class="prl-divider wow fadeInUp" data-wow-delay=".3s">
                                <div class="prl-line"></div>
                                <div class="prl-dot"></div>
                                <div class="prl-line"></div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <!-- Location Card -->
                            <div class="contact-info-card p-4 rounded shadow-sm mb-4 d-flex align-items-center wow fadeInUp"
                                data-wow-delay=".3s"
                                style="background: #ffffff; border: 1px solid #f0f0f0; border-left: 4px solid var(--prl-brand); transition: all 0.3s ease;">
                                <div class="icon-wrapper d-flex justify-content-center align-items-center rounded-circle me-4"
                                    style="min-width: 60px; width: 60px; height: 60px; background: rgba(231, 122, 58, 0.1); color: var(--prl-brand); font-size: 24px;">
                                    <i class="fa-solid fa-map-marker-alt"></i>
                                </div>
                                <div class="content">
                                    <h5 class="mb-1" style="font-weight: 600; color: #333; font-size: 18px;">Our Location</h5>
                                    <p class="mb-0 text-muted" style="font-size: 15px; line-height: 1.5;">Sokoine Road, Moshi, Kilimanjaro, Tanzania</p>
                                </div>
                            </div>

                            <!-- Email Card -->
                            <div class="contact-info-card p-4 rounded shadow-sm mb-4 d-flex align-items-center wow fadeInUp"
                                data-wow-delay=".5s"
                                style="background: #ffffff; border: 1px solid #f0f0f0; border-left: 4px solid var(--prl-brand); transition: all 0.3s ease;">
                                <div class="icon-wrapper d-flex justify-content-center align-items-center rounded-circle me-4"
                                    style="min-width: 60px; width: 60px; height: 60px; background: rgba(231, 122, 58, 0.1); color: var(--prl-brand); font-size: 24px;">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="content">
                                    <h5 class="mb-1" style="font-weight: 600; color: #333; font-size: 18px;">Email Address</h5>
                                    <p class="mb-0" style="font-size: 15px; line-height: 1.5;"><a
                                            href="mailto:info@primelandhotel.com"
                                            class="text-muted text-decoration-none">info@primelandhotel.com</a></p>
                                </div>
                            </div>

                            <!-- Phone Card -->
                            <div class="contact-info-card p-4 rounded shadow-sm mb-4 d-flex align-items-center wow fadeInUp"
                                data-wow-delay=".7s"
                                style="background: #ffffff; border: 1px solid #f0f0f0; border-left: 4px solid var(--prl-brand); transition: all 0.3s ease;">
                                <div class="icon-wrapper d-flex justify-content-center align-items-center rounded-circle me-4"
                                    style="min-width: 60px; width: 60px; height: 60px; background: rgba(231, 122, 58, 0.1); color: var(--prl-brand); font-size: 24px;">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="content">
                                    <h5 class="mb-1" style="font-weight: 600; color: #333; font-size: 18px;">Phone Number</h5>
                                    <p class="mb-0" style="font-size: 15px; line-height: 1.5;"><a
                                            href="tel:+255677155156" class="text-muted text-decoration-none">+255
                                            677-155-156</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="gt-contact-right-items h-100 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/img/new_images/reception_.jpg') }}" alt="Primeland Hotel Reception"
                            class="rounded shadow-lg w-100 h-100 object-fit-cover wow fadeInUp" data-wow-delay=".4s"
                            style="object-fit: cover; border-radius: 10px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section Start -->
<div class="googpemap-2">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.723049103!2d37.3331034!3d-3.3290456!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1839d9c289b52703%3A0x4cb0911bcf50efb!2sPrimeland%20Hotel!5e0!3m2!1sen!2stz!4v1710000000000!5m2!1sen!2stz"
        style="border:0;" allowfullscreen="" loading="lazy"></iframe>
</div>
@endsection
