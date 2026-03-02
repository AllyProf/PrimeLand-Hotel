<!-- Preloader Start -->
<div id="preloader" class="preloader">
    <div class="animation-preloader">
        <div class="spinner">                
        </div>
        <div class="txt-loading">
            <span data-text-preloader="P" class="letters-loading">P</span>
            <span data-text-preloader="R" class="letters-loading">R</span>
            <span data-text-preloader="I" class="letters-loading">I</span>
            <span data-text-preloader="M" class="letters-loading">M</span>
            <span data-text-preloader="E" class="letters-loading">E</span>
            <span data-text-preloader="L" class="letters-loading">L</span>
            <span data-text-preloader="A" class="letters-loading">A</span>
            <span data-text-preloader="N" class="letters-loading">N</span>
            <span data-text-preloader="D" class="letters-loading">D</span>
        </div>
        <p class="text-center">Loading</p>
    </div>
    <div class="loader">
        <div class="row">
            <div class="col-3 loader-section section-left">
                <div class="bg"></div>
            </div>
            <div class="col-3 loader-section section-left">
                <div class="bg"></div>
            </div>
            <div class="col-3 loader-section section-right">
                <div class="bg"></div>
            </div>
            <div class="col-3 loader-section section-right">
                <div class="bg"></div>
            </div>
        </div>
    </div>
</div>  

<!-- GT Back To Top Start -->
<button id="gt-back-top" class="gt-back-to-top">
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
                        <a href="{{ url('/') }}" style="text-decoration: none;">
                            <span style="font-size: 24px; font-weight: 800; color: #000; letter-spacing: 1px;">PrimeLand Hotel</span>
                        </a>
                    </div>
                    <div class="offcanvas__close">
                        <button>
                        <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <p class="text d-none d-xl-block">
                    Experience luxury and comfort at PrimeLand Hotel. Your home away from home.
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
                                <a target="_blank" href="#">Tanzania</a>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="offcanvas__contact-icon mr-15">
                                <i class="fal fa-envelope"></i>
                            </div>
                            <div class="offcanvas__contact-text">
                                <a href="mailto:info@primeland.com"><span class="mailto:info@primeland.com">info@primeland.com</span></a>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="offcanvas__contact-icon mr-15">
                                <i class="fal fa-clock"></i>
                            </div>
                            <div class="offcanvas__contact-text">
                                <a target="_blank" href="#">Mon-Sun, 24/7</a>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="offcanvas__contact-icon mr-15">
                                <i class="far fa-phone"></i>
                            </div>
                            <div class="offcanvas__contact-text">
                                <a href="tel:+255000000000">+255 000 000 000</a>
                            </div>
                        </li>
                    </ul>
                    <a href="{{ route('booking.index') }}" class="gt-theme-btn">BOOK NOW</a>
                    <div class="social-icon d-flex align-items-center">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas__overlay"></div>

<!-- Header Section Start -->
<header id="header-sticky" class="header-1 header-2">
    <div class="container-fluid">
        <div class="mega-menu-wrapper">
            <div class="header-main">
                <div class="logo">
                    <a href="{{ url('/') }}" class="header-logo d-none d-md-block" style="text-decoration: none;">
                        <span style="font-size: 24px; font-weight: 800; color: #fff; letter-spacing: 1px;">PrimeLand Hotel</span>
                    </a>
                    <a href="{{ url('/') }}" class="header-logo-2 d-none d-md-block" style="text-decoration: none;">
                        <span style="font-size: 24px; font-weight: 800; color: #e77a3a; letter-spacing: 1px;">PrimeLand Hotel</span>
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
                                <li class="{{ Request::is('services') ? 'active' : '' }}">
                                    <a href="{{ url('/services') }}">Services</a>
                                </li>
                                <li class="{{ Request::is('rooms') ? 'active' : '' }}">
                                    <a href="{{ url('/rooms') }}">Rooms</a>
                                </li>
                                <li class="{{ Request::is('gallery') ? 'active' : '' }}">
                                    <a href="{{ route('gallery.index') }}">Gallery</a>
                                </li>
                                <li class="{{ Request::is('contact') ? 'active' : '' }}">
                                    <a href="{{ url('/contact') }}">Contact Us</a>
                                </li>
                                <li>
                                    <a href="{{ route('login') }}">Login</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                   <a href="#" class="main-header__search search-toggler">
                        <i class="fa-regular fa-magnifying-glass"></i>
                    </a>
                    <div class="hero-button">
                        <a href="{{ route('booking.index') }}" class="gt-theme-btn">Book now</a>
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
