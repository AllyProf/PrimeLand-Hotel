@extends('layouts.new_landing')

@section('title', 'Contact Us - PrimeLand Hotel')

@section('content')
<!-- Breadcrumb Section Start -->
<div class="gt-breadcrumb-wrapper bg-cover" style="background-image: url('{{ asset('hotel_gallery/swimming view_(1).jpg') }}');">
    <div class="container">
        <div class="gt-page-heading">
            <div class="gt-breadcrumb-sub-title">
                <h1 class="text-white wow fadeInUp" data-wow-delay=".3s">Contact Us</h1>
            </div>
            <ul class="gt-breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
                <li>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li>
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li>
                    Contact
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
                <div class="col-lg-4">
                    <div class="gt-contact-left-items">
                        <div class="gt-section-title">
                            <h6 class="wow fadeInUp">
                                CONTACT US
                            </h6>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s">
                                Ready to Contact Us
                            </h2>
                        </div>
                        <ul class="gt-contact-list">
                            <li>
                                <div class="icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="content">
                                    <p>Location</p>
                                    <h4>
                                        Sokoine Road, Moshi Kilimanjaro, Tanzania
                                    </h4>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="content">
                                    <p>Email Address</p>
                                    <h4><a href="mailto:info@primelandhotel.com">info@primelandhotel.com</a></h4>
                                    <h4><a href="mailto:infoprimelandhotel@gmail.com">infoprimelandhotel@gmail.com</a></h4>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="content">
                                    <p>Phone No</p>
                                    <h4><a href="tel:+255677155156">0677-155-156</a></h4>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="gt-contact-right-items">
                        <h2>
                            Send Us Message
                        </h2>
                        <p>There will be no publication of your email address. Required fields are indicated with a *.</p>
                        <form action="#" id="contact-form" class="contact-form-box">
                          <div class="row g-4 align-items-center">
                              <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                                <h4>Your Name</h4>
                                  <div class="form-clt">
                                      <input type="text" name="name" id="name" placeholder="Your Name" required>
                                  </div>
                              </div>
                              <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                                <h4>Your Email</h4>
                                  <div class="form-clt">
                                      <input type="email" name="email" id="email2" placeholder="Your Email" required>
                                  </div>
                              </div>
                              <div class="col-lg-12 wow fadeInUp" data-wow-delay=".3s">
                                <h4>Your Message</h4>
                                  <div class="form-clt">
                                      <textarea name="message" id="message" placeholder="Type your message" required></textarea>
                                  </div>
                              </div>
                              <div class="col-lg-12 wow fadeInUp" data-wow-delay=".5s">
                                  <button type="submit" class="gt-theme-btn">SEND MESSAGE</button>
                              </div>
                          </div>
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section Start -->
<div class="googpemap-2">
    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d4366.224696276086!2d37.33295041043378!3d-3.3290562106369452!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2stz!4v1763832924736" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
</div>

@endsection

@section('scripts')
<script src="{{ asset('royal-master/js/jquery.form.js') }}"></script>
<script src="{{ asset('royal-master/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('royal-master/js/contact.js') }}"></script>
@endsection
