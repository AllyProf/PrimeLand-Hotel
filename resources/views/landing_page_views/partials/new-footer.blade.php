<!-- GT Footer Section Start -->
<footer class="gt-footer-section fix bg-cover" style="background-image: url('{{ asset('landing-assets/img/home-1/footer/footer-img.jpg') }}');">
    <div class="container">
        <div class="footer-newsletter-items">
            <h2>Join Our Newsletter</h2>
            <form action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <div class="form-clt">
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="gt-theme-btn">Subscribe</button>
            </form>
        </div>
        <div class="footer-widget-wrapper">
            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-7">
                    <div class="footer-single-widget">
                        <div class="widget-head">
                            <a href="{{ url('/') }}" class="footer-logo">
                                <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="img" style="max-height: 80px; filter: brightness(0) invert(1);">
                            </a>
                        </div>
                        <div class="footer-content">
                            <p>
                                Experience unparalleled hospitality at PrimeLand Hotel. We provide world-class amenities and personalized service in the heart of Moshi.
                            </p>
                            <div class="social-icon d-flex align-items-center">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-vimeo-v"></i></a>
                                <a href="#"><i class="fab fa-pinterest-p"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-md-5 col-sm-6 col-6">
                     <div class="footer-single-widget">
                        <div class="widget-head">
                            <h3>Quick Links</h3>
                        </div>
                        <ul class="gt-list-area">
                            <li><a href="{{ url('/') }}">Home</a></li>
                            <li><a href="{{ url('/about-us') }}">About us</a></li>
                            <li><a href="{{ url('/rooms') }}">Hotel</a></li>
                            <li><a href="{{ url('/contact') }}">Contact</a></li>
                            <li><a href="{{ url('/services') }}">Services</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 ps-xl-5 col-lg-4 col-md-6 col-sm-6 col-6">
                     <div class="footer-single-widget">
                        <div class="widget-head">
                            <h3>Guest Services</h3>
                        </div>
                        <ul class="gt-list-area">
                            <li><a href="#">24/7 Front Desk</a></li>
                            <li><a href="#">Parking</a></li>
                            <li><a href="#">Room Service</a></li>
                            <li><a href="#">Free Wi-Fi</a></li>
                            <li><a href="#">Concierge Service</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6">
                     <div class="footer-single-widget">
                        <div class="widget-head">
                            <h3>Contact Us</h3>
                        </div>
                        <ul class="contact-list">
                            <li>
                                PrimeLand Hotel — <br>
                                Moshi, Tanzania
                            </li>
                            <li>
                                <a href="tel:+255677155156">0677-155-156</a>
                            </li>
                            <li>
                                Mon-Sun 24/7 Hours Open
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-bottom-wrapper">
                <p class="wow fadeInUp" data-wow-delay=".3s">
                    © {{ date('Y') }} <b>PrimeLand.</b> All rights reserved.
                </p>
                <ul class="footer-list wow fadeInUp" data-wow-delay=".7s">
                    <li>
                        <a href="{{ url('/contact') }}">Privacy policy</a>
                    </li>
                    <li>।</li>
                    <li>
                        <a href="{{ url('/contact') }}">Terms &amp; conditions</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
