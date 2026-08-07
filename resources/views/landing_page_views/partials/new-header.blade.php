<!-- Preloader Start -->
<div id="preloader" class="preloader">
    <div class="preloader-inner">
        <div class="preloader-spinner"></div>
    </div>
</div>

<script>
    (function() {
        var preloader = document.getElementById('preloader');
        if (preloader) {
            var hidePreloader = function() {
                if (!preloader.classList.contains('loaded')) {
                    preloader.classList.add('loaded');
                    setTimeout(function() {
                        preloader.style.display = 'none';
                    }, 500);
                }
            };
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    // Visible for 0.8s so mobile users clearly see the smooth spinner
                    setTimeout(hidePreloader, 800);
                });
            } else {
                setTimeout(hidePreloader, 800);
            }

            // Safety timeout: force hide after 1.8 seconds max
            setTimeout(hidePreloader, 1800);
        }
    })();
</script>


<!-- GT Back To Top Start -->
<button id="gt-back-top" class="gt-back-to-top show">
    <i class="fa-solid fa-chevrons-up"></i>
</button>

<!-- GT MouseCursor Start -->
<div class="mouseCursor cursor-outer"></div>
<div class="mouseCursor cursor-inner"></div>

<!-- Offcanvas Area Start -->
<div class="fix-area">
    <div class="offcanvas__info">
        <div class="offcanvas__wrapper">
            <div class="offcanvas__content">
                <div class="offcanvas__top mb-5 d-flex justify-content-between align-items-center">
                    <div class="offcanvas__logo">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('assets/img/new_images/primeland_logo.png') }}" alt="Primeland Hotel Moshi - Boutique Hotel near Kilimanjaro"
                                style="max-width: 150px; height: auto;">
                        </a>
                    </div>
                    <div class="offcanvas__close">
                        <button>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <p class="text d-none d-xl-block">
                    A boutique hotel in the heart of Moshi Town, Kilimanjaro &ndash; Tanzania. Conveniently located
                    near the town center and 45 minutes from JRO Airport.
                </p>
                <div class="mobile-menu fix mb-3"></div>
                <div class="offcanvas__contact">
                    <h4>Contact Info</h4>
                    <ul>
                        <li class="d-flex align-items-center">
                            <div class="offcanvas__contact-icon">
                                <i class="fal fa-map-marker-alt"></i>
                            </div>
                            <div class="offcanvas__contact-text">
                                <a target="_blank" href="#">Sokoine Road, Moshi, Kilimanjaro &ndash; Tanzania</a>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="offcanvas__contact-icon mr-15">
                                <i class="fal fa-envelope"></i>
                            </div>
                            <div class="offcanvas__contact-text">
                                <a href="mailto:info@primelandhotel.com"><span>info@primelandhotel.com</span></a>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="offcanvas__contact-icon mr-15">
                                <i class="fal fa-clock"></i>
                            </div>
                            <div class="offcanvas__contact-text">
                                <a target="_blank" href="#">Check-in: 2:00pm | Check-out: 10:00am</a>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="offcanvas__contact-icon mr-15">
                                <i class="far fa-phone"></i>
                            </div>
                            <div class="offcanvas__contact-text">
                                <a href="tel:+255677155156">+255 677-155-156</a>
                            </div>
                        </li>
                    </ul>
                    <a href="{{ config('services.aiosell.booking_url') }}" class="gt-theme-btn">BOOK NOW</a>
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
</div>
<div class="offcanvas__overlay"></div>

<!-- Header Section Start -->
<header id="header-sticky" class="header-1 header-2">
    <div class="container">
        <div class="mega-menu-wrapper">
            <div class="header-main">
                <div class="logo">
                    <a href="{{ url('/') }}" class="header-logo">
                        <img src="{{ asset('assets/img/new_images/primeland_logo.png') }}" alt="Primeland Hotel Moshi Logo"
                            style="max-width: 110px; height: auto;">
                    </a>
                    <a href="{{ url('/') }}" class="header-logo-2">
                        <img src="{{ asset('assets/img/new_images/primeland_logo.png') }}" alt="Primeland Hotel Moshi Logo"
                            style="max-width: 110px; height: auto;">
                    </a>
                </div>
                <div class="header-right d-flex justify-content-end align-items-center">
                    <div class="mean__menu-wrapper">
                        <div class="main-menu">
                            <nav id="mobile-menu">
                                <ul>
                                    <li class="{{ Request::is('/') || Request::is('home') ? 'active' : '' }}">
                                        <a href="{{ url('/') }}">Home</a>
                                    </li>
                                    <li class="{{ Request::is('about-us') ? 'active' : '' }}">
                                        <a href="{{ url('/about-us') }}">About Us</a>
                                    </li>
                                    <li class="has-dropdown {{ Request::is('services') || Request::is('service-details') ? 'active' : '' }}">
                                        <a href="{{ url('/services') }}">Services</a>
                                        <ul class="submenu">
                                            <li><a href="{{ url('/service-details') }}">Service Details</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-dropdown {{ Request::is('rooms*') ? 'active' : '' }}">
                                        <a href="#">Rooms</a>
                                        <ul class="submenu">
                                            <li><a href="{{ url('/rooms/single-room') }}">Single Room</a></li>
                                            <li><a href="{{ url('/rooms/double-room') }}">Double Room</a></li>
                                            <li><a href="{{ url('/rooms/twin-room') }}">Twin Room</a></li>
                                        </ul>
                                    </li>
                                    <li class="{{ Request::is('contact') ? 'active' : '' }}">
                                        <a href="{{ url('/contact') }}">Contact Us</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <a href="#" class="main-header__search search-toggler">
                        <i class="fa-regular fa-magnifying-glass"></i>
                    </a>
                    <div class="hero-button">
                        <a href="{{ config('services.aiosell.booking_url') }}" class="gt-theme-btn">Book now</a>
                    </div>
                    <div class="header__hamburger my-auto d-xl-none">
                        <div class="sidebar__toggle">
                            <i class="fa-regular fa-bars"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- GT Search Start -->
<div class="search-popup">
    <div class="search-popup__overlay search-toggler"></div>
    <div class="search-popup__content">
        <form role="search" method="get" class="search-popup__form" action="#">
            <input type="text" id="search" name="search" placeholder="Search Here...">
            <button type="submit" aria-label="search submit" class="search-btn">
                <span><i class="fa-regular fa-magnifying-glass"></i></span>
            </button>
        </form>
    </div>
</div>
