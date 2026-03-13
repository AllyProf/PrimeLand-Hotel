<!-- GT Footer Section Start -->
<footer class="gt-footer-section fix bg-cover"
    style="background-image: url('{{ asset('assets/img/home-1/footer/footer-img.jpg') }}');">
    <div class="container">

        <div class="footer-widget-wrapper">
            <div class="row">
                <!-- Prime Location Column -->
                <div class="col-xl-3 col-lg-3 col-md-6">
                    <div class="footer-single-widget">
                        <div class="widget-head">
                            <h3 style="color: #e77a3a !important;">Prime Location</h3>
                            <div class="prl-divider">
                                <div class="prl-line"></div>
                                <div class="prl-dot"></div>
                                <div class="prl-line"></div>
                            </div>
                        </div>
                        <div class="footer-content">
                            <ul class="gt-list-area">
                                <li style="color: #ffffff !important;">Moshi Town Center – 5 Mins</li>
                                <li style="color: #ffffff !important;">Kilimanjaro Int. Airport – 45 Mins</li>
                                <li style="color: #ffffff !important;">Marangu Gate (Kili) – 30 Mins</li>
                                <li style="color: #ffffff !important;">Moshi Airport – 10 Mins</li>
                                <li style="color: #ffffff !important;">Machame Gate – 40 Mins</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Quick Links Column -->
                <div class="col-xl-3 col-lg-3 col-md-6">
                    <div class="footer-single-widget">
                        <div class="widget-head">
                            <h3 style="color: #e77a3a !important;">Quick Links</h3>
                            <div class="prl-divider">
                                <div class="prl-line"></div>
                                <div class="prl-dot"></div>
                                <div class="prl-line"></div>
                            </div>
                        </div>
                        <ul class="gt-list-area">
                            <li><a href="{{ url('/') }}">Home</a></li>
                            <li><a href="{{ url('/about-us') }}">About us</a></li>
                            <li><a href="{{ url('/rooms') }}">Hotel</a></li>
                            <li><a href="{{ url('/contact') }}">Contact</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Contact Us Column -->
                <div class="col-xl-3 col-lg-3 col-md-6">
                    <div class="footer-single-widget">
                        <div class="widget-head">
                            <h3 style="color: #e77a3a !important;">Contact Us</h3>
                            <div class="prl-divider">
                                <div class="prl-line"></div>
                                <div class="prl-dot"></div>
                                <div class="prl-line"></div>
                            </div>
                        </div>
                        <ul class="contact-list">
                            <li>
                                Tanzania <br>
                                Sokoine Road, Moshi, Kilimanjaro
                            </li>
                            <li><a href="tel:+255677155156">+255 677-155-156</a></li>
                            <li><a href="mailto:info@primelandhotel.com">info@primelandhotel.com</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Digital Platforms Column -->
                <div class="col-xl-3 col-lg-3 col-md-6 text-center text-lg-start">
                    <div class="footer-single-widget">
                        <div class="widget-head">
                            <h3 style="color: #e77a3a !important;">Digital Platforms</h3>
                            <div class="prl-divider">
                                <div class="prl-line"></div>
                                <div class="prl-dot"></div>
                                <div class="prl-line"></div>
                            </div>
                        </div>
                        <div class="prl-social-grid mt-4">
                            <a href="https://www.instagram.com/primeland_hotel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                                target="_blank" class="ig" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://wa.me/255677155156" target="_blank" class="wa" title="WhatsApp"><i
                                    class="fab fa-whatsapp"></i></a>
                            <a href="#" class="fb" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="tw" title="X / Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="yt" title="YouTube"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="li" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-bottom-wrapper">
                <p class="wow fadeInUp" data-wow-delay=".3s">&copy; {{ date('Y') }} <b>Primeland Hotel.</b> All rights
                    reserved. | Sokoine Road, Moshi, Kilimanjaro &ndash; Tanzania</p>
                <p class="wow fadeInUp" data-wow-delay=".5s">Powered By <a href="http://www.emca.tech"
                        target="_blank" style="color: #940000; font-weight: bold;">EmCa Techonologies LTD</a></p>
            </div>
        </div>
    </div>

    <div class="floating-social-buttons">
        <a href="https://wa.me/255677155156" target="_blank" class="float-btn whatsapp-btn" title="Chat with us">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
        <a href="https://www.google.com/maps/place/Primeland+Hotel/@-3.3290456,37.3352921,17z/data=!3m1!4b1!4m9!3m8!1s0x1839d9c289b52703:0x4cb0911bcf50efb!5m2!4m1!1i2!8m2!3d-3.3290456!4d37.3352921!16s%2Fg%2F11vqscl66t?entry=ttu&g_ep=EgoyMDI2MDMwNC4xIKXMDSoASAFQAw%3D%3D" target="_blank"
            class="float-btn maps-btn" title="Find us on Maps">
            <i class="fa-solid fa-map-marker-alt"></i>
        </a>
    </div>
</footer>

<!-- Review Modal -->
<div class="modal fade" id="gtReviewModal" tabindex="-1" aria-labelledby="gtReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: #e77a3a; color: white; border: none;">
                <h5 class="modal-title" id="gtReviewModalLabel" style="font-weight: 700;">Guest Review</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <img id="gtModalFlag" src="" alt="" style="width: 40px; height: auto; border-radius: 4px; margin-right: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <div>
                        <h4 id="gtModalAuthor" class="mb-0" style="font-weight: 700; color: #333;"></h4>
                        <span id="gtModalLocation" style="color: #666; font-size: 14px;"></span>
                    </div>
                </div>
                <div class="review-text-container" style="background: #f8f9fa; padding: 20px; border-radius: 15px; position: relative;">
                    <i class="fa-solid fa-quote-left" style="color: #e77a3a; font-size: 24px; opacity: 0.2; position: absolute; top: 15px; left: 15px;"></i>
                    <p id="gtModalText" style="font-style: italic; line-height: 1.8; color: #444; margin-bottom: 0; position: relative; z-index: 1;"></p>
                </div>
            </div>
            <div class="modal-footer" style="border: none; padding-top: 0;">
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #333; color: white; border-radius: 10px; padding: 10px 25px; font-weight: 600;">Close</button>
            </div>
        </div>
    </div>
</div>
