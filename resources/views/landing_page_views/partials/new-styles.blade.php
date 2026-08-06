<!--<< Favcion >>-->
<link rel="shortcut icon" href="{{ asset('assets/img/new_images/primeland_logo.png') }}">
<!--<< Bootstrap min.css >>-->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<!--<< All Min Css >>-->
<link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
<!--<< Animate.css >>-->
<link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
<!--<< Magnific Popup.css >>-->
<link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
<!--<< MeanMenu.css >>-->
<link rel="stylesheet" href="{{ asset('assets/css/meanmenu.css') }}">
<!--<< Swiper Bundle.css >>-->
<link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
<!--<< Nice Select.css >>-->
<link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">
<!--<< Main.css >>-->
<link rel="stylesheet" href="{{ asset('assets/css/flaticon.css') }}">
<!--<< Main.css >>-->
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

<style>
    /* ===== PRIMELAND BRAND COLOR ===== */
    :root {
        --prl-brand: #e77a3a;
    }

    /* Make logo background transparent visually */
    .logo img,
    .header-logo img,
    .header-logo-2 img,
    .offcanvas__logo img {
        mix-blend-mode: multiply;
    }

    /* ===== SECTION DIVIDER ===== */
    .prl-divider {
        display: flex;
        align-items: center;
        justify-content: center; /* Default to center (mobile) */
        gap: 10px;
        margin: 14px 0 22px;
    }

    @media (min-width: 992px) {
        .prl-divider {
            justify-content: flex-start;
        }
    }

    .prl-divider .prl-line {
        height: 2px;
        width: 45px;
        background: #e77a3a;
        border-radius: 2px;
        flex-shrink: 0;
        animation: prlLineAnim 4s infinite ease-in-out;
    }

    .prl-divider .prl-dot {
        width: 8px;
        height: 8px;
        background: #e77a3a;
        border-radius: 50%;
        flex-shrink: 0;
        animation: prlDotPulse 4s infinite ease-in-out;
    }

    @keyframes prlLineAnim {
        0%, 100% { width: 30px; opacity: 0.6; }
        50% { width: 65px; opacity: 1; }
    }

    @keyframes prlDotPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.4); }
    }

    /* ===== Offer Section: Override main.css large margins ===== */
    .gt-offer-content-left-3 h2 {
        margin-top: 8px !important;
        font-size: 44px !important;
    }
    .gt-offer-content-left-3 h4 {
        font-size: 20px !important;
        margin-bottom: 8px !important;
    }
    .gt-offer-content-left-3 h5 {
        margin-top: 0 !important;
        margin-bottom: 6px !important;
    }
    .gt-offer-section-3 .offer-btn {
        margin-top: 0 !important;
    }

    /* Offer Section Enhancement */
    .prl-offer-card {
        background: rgba(0, 0, 0, 0.35);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 28px 36px;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        max-width: 600px;
    }

    .prl-offer-card h2 {
        font-size: 44px;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 10px;
        color: #ffffff !important;
    }

    .prl-offer-card h5 {
        color: #e77a3a !important;
        letter-spacing: 4px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .prl-offer-card h4 {
        color: #ffffff !important;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .prl-offer-card p {
        color: rgba(255, 255, 255, 0.95) !important;
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    /* Footer Social Grid Styling */
    .prl-social-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        max-width: 240px; /* Increased from 220px */
        margin-left: auto;
        margin-right: auto;
    }

    @media (min-width: 992px) {
        .prl-social-grid {
            margin-left: 0;
            margin-right: 0;
        }
    }

    .prl-social-grid a {
        width: 70px; /* Increased from 60px */
        height: 70px; /* Increased from 60px */
        background: #222222;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 30px; /* Increased from 24px */
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .prl-social-grid a:hover {
        transform: translateY(-3px);
        background: #333333;
    }

    .prl-social-grid a.fb {
        color: #1877F2;
    }

    .prl-social-grid a.ig {
        color: #E4405F;
    }

    .prl-social-grid a.tw {
        color: #1DA1F2;
    }

    .prl-social-grid a.wa {
        color: #25D366;
    }

    .prl-social-grid a.yt {
        color: #FF0000;
    }

    .prl-social-grid a.li {
        color: #0077B5;
    }

    /* ===== REDUCE WHITESPACE: Facilities title ===== */
    .gt-service-section .gt-section-title.text-center {
        margin-bottom: 8px !important;
    }

    /* ===== REDUCE WHITESPACE: FAQ section ===== */
    .faq-section .gt-faq-content .gt-section-title {
        margin-bottom: 8px !important;
    }

    .faq-section .gt-faq-wrapper {
        padding-top: 20px;
    }

    /* ===== FIX: Room card images always visible (override theme hover-only opacity) ===== */
    .gt-room-box-items .gt-thumb img:first-child {
        position: relative !important;
        opacity: 1 !important;
        transform: none !important;
        filter: none !important;
        z-index: auto !important;
        left: auto !important;
        top: auto !important;
        right: auto !important;
        bottom: auto !important;
    }

    /* ===== Booking strip (homepage) ===== */
    .prl-booking-strip {
        padding: 36px 44px !important;
    }

    .prl-booking-strip-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 32px;
        width: 100%;
    }

    .prl-booking-strip-copy {
        flex: 1;
        min-width: 0;
        text-align: left;
    }

    .prl-booking-strip-copy h3 {
        color: #e77a3a !important;
        font-size: 28px !important;
        font-weight: 600;
        line-height: 1.3;
        margin: 0 0 8px !important;
    }

    .prl-booking-strip-copy p {
        color: rgba(255, 255, 255, 0.88) !important;
        font-size: 16px;
        line-height: 1.6;
        margin: 0 !important;
    }

    .prl-booking-strip .prl-booking-strip-btn {
        flex-shrink: 0;
        background-color: #ffffff !important;
        color: #e77a3a !important;
        min-width: 170px;
        text-align: center;
        white-space: nowrap;
    }

    .prl-booking-strip .prl-booking-strip-btn::before {
        background-color: #e77a3a !important;
    }

    .prl-booking-strip .prl-booking-strip-btn:hover {
        color: #ffffff !important;
    }

    .prl-booking-strip-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-start;
        gap: 8px 12px;
        margin-top: 22px !important;
        padding-top: 0 !important;
        border-top: none !important;
        text-align: left;
    }

    .prl-booking-strip-meta span {
        color: rgba(255, 255, 255, 0.78) !important;
        font-size: 14px;
        line-height: 1.4;
    }

    .prl-booking-strip-meta .prl-booking-dot {
        opacity: 0.45;
    }

    @media (max-width: 991px) {
        .prl-booking-strip {
            padding: 28px 22px !important;
        }

        .prl-booking-strip-inner {
            flex-direction: column;
            text-align: center;
        }

        .prl-booking-strip-copy {
            text-align: center;
        }

        .prl-booking-strip-copy h3 {
            font-size: 24px !important;
        }

        .prl-booking-strip .prl-booking-strip-btn {
            width: 100%;
        }

        .prl-booking-strip-meta {
            justify-content: center;
            flex-direction: column;
            gap: 6px;
        }

        .prl-booking-strip-meta .prl-booking-dot {
            display: none;
        }
    }

    /* ===== Room Slider Actions (Homepage Room Cards) ===== */
    .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
        margin-left: auto;
    }

    .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns .gt-theme-btn {
        padding: 8px 13px !important;
        font-size: 11px !important;
        font-weight: 700;
        letter-spacing: 0.4px;
        line-height: 1.2;
        border-radius: 6px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns .prl-btn-book {
        background-color: #e77a3a !important;
        color: #ffffff !important;
        border: 1px solid #e77a3a !important;
    }

    .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns .prl-btn-book::before,
    .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns .prl-btn-book::after {
        background-color: #1a2e2b !important;
    }

    .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns .prl-btn-book:hover {
        color: #ffffff !important;
        border-color: #1a2e2b !important;
    }

    .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns .prl-btn-details {
        background-color: transparent !important;
        color: #1a2e2b !important;
        border: 1px solid rgba(231, 122, 58, 0.45) !important;
    }

    .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns .prl-btn-details::before,
    .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns .prl-btn-details::after {
        background-color: #e77a3a !important;
    }

    .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns .prl-btn-details:hover {
        color: #ffffff !important;
        border-color: #e77a3a !important;
    }

    @media (max-width: 1399px) {
        .room-slider-image-3 .room-thumb .room-content .content .prl-room-slider-btns {
            width: 100%;
            justify-content: flex-start;
            margin-top: 10px;
        }
    }

    /* ===== Services Section: 1 Row Layout & Uniform Card Dimensions ===== */
    .gt-service-box-section .service-box-items {
        height: 100% !important;
        min-height: 190px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 30px 12px 25px !important;
        margin-top: 0 !important;
        background-color: #f2f9f5 !important;
        border: 1px solid rgba(231, 122, 58, 0.12) !important;
        border-radius: 12px !important;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
    }

    .gt-service-box-section .service-box-items .icon {
        font-size: 42px !important;
        color: #e77a3a !important;
        margin-bottom: 16px !important;
        line-height: 1 !important;
        transition: transform 0.3s ease !important;
    }

    .gt-service-box-section .service-box-items h4 {
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #1a2e2b !important;
        margin: 0 !important;
        line-height: 1.4 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        text-align: center !important;
    }

    .gt-service-box-section .service-box-items:hover {
        background-color: #ffffff !important;
        border-color: rgba(231, 122, 58, 0.45) !important;
        transform: translateY(-6px) !important;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08) !important;
    }

    .gt-service-box-section .service-box-items:hover .icon {
        transform: scale(1.12) !important;
    }

    @media (max-width: 1199px) {
        .gt-service-box-section .service-box-items {
            min-height: 175px !important;
            padding: 24px 10px 20px !important;
        }

        .gt-service-box-section .service-box-items h4 {
            font-size: 11px !important;
        }
    }

    /* ===== FIX: Mobile Room Slider Image Aspect & Zoom ===== */
    @media (max-width: 767px) {
        .room-slider-image-3 .room-thumb {
            height: 400px !important;
            border-radius: 16px !important;
            overflow: hidden !important;
        }

        .room-slider-image-3 .room-thumb img {
            height: 100% !important;
            width: 100% !important;
            object-fit: cover !important;
            object-position: center center !important;
        }

        .room-slider-image-3 .room-thumb .room-content {
            bottom: 15px !important;
            left: 15px !important;
            right: 15px !important;
        }

        .room-slider-image-3 .room-thumb .room-content .content {
            padding: 14px 16px !important;
        }
    }

    @media (max-width: 575px) {
        .room-slider-image-3 .room-thumb {
            height: 360px !important;
        }

        .room-slider-image-3 .room-thumb .room-content {
            bottom: 12px !important;
            left: 12px !important;
            right: 12px !important;
        }

        .room-slider-image-3 .room-thumb .room-content span {
            font-size: 13px !important;
            padding: 6px 14px !important;
        }

        .room-slider-image-3 .room-thumb .room-content .content {
            padding: 12px 12px !important;
        }

        .room-slider-image-3 .room-thumb .room-content .content ul {
            gap: 8px 12px !important;
        }
    }
</style>
