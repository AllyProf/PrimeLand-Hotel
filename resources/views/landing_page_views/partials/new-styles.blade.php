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
        gap: 10px;
        margin: 14px 0 22px;
    }

    .prl-divider .prl-line {
        height: 2px;
        width: 45px;
        background: #e77a3a;
        border-radius: 2px;
        flex-shrink: 0;
    }

    .prl-divider .prl-dot {
        width: 8px;
        height: 8px;
        background: #e77a3a;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Offer Section Enhancement */
    .prl-offer-card {
        background: rgba(0, 0, 0, 0.35);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 50px;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        max-width: 600px;
    }

    .prl-offer-card h2 {
        font-size: 68px;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 20px;
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
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 25px;
    }

    .prl-offer-card p {
        color: rgba(255, 255, 255, 0.95) !important;
        font-size: 18px;
        line-height: 1.7;
        margin-bottom: 35px;
    }

    /* Footer Social Grid Styling */
    .prl-social-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        max-width: 220px;
    }

    .prl-social-grid a {
        width: 60px;
        height: 60px;
        background: #222222;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 24px;
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
</style>
