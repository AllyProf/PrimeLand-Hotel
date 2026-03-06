<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PrimeLand Hotel — Guest Menu</title>
    
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --primary: #e77a3a;
            --primary-glow: rgba(231, 122, 58, 0.3);
            --bg-dark: #0f0f12;
            --card-bg: rgba(255, 255, 255, 0.04);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-main: #ffffff;
            --text-dim: rgba(255, 255, 255, 0.5);
            --radius-xl: 30px;
            --radius-md: 18px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Header ── */
        .hero-section {
            padding: 40px 25px 30px;
            background: radial-gradient(circle at top right, rgba(231, 122, 58, 0.12), transparent 70%);
            text-align: center;
            position: relative;
        }

        .lang-toggle {
            position: absolute; top: 20px; right: 20px;
            background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
            border-radius: 50px; padding: 4px; display: flex; gap: 4px; z-index: 1100;
        }
        .lang-btn {
            font-size: 10px; font-weight: 800; padding: 6px 12px; border-radius: 50px;
            cursor: pointer; color: var(--text-dim); transition: 0.3s;
        }
        .lang-btn.active { background: var(--primary); color: #fff; }

        .hotel-brand { font-size: 14px; letter-spacing: 6px; text-transform: uppercase; color: var(--primary); font-weight: 900; margin-bottom: 12px; }
        .hero-title { font-size: 24px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 15px; background: linear-gradient(135deg, #fff 0%, #aaa 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-subtitle { color: var(--text-dim); font-size: 14px; font-weight: 500; margin-bottom: 20px; }

        .room-indicator { display: inline-flex; align-items: center; gap: 8px; background: var(--primary); color: #fff; padding: 10px 22px; border-radius: 50px; font-size: 14px; font-weight: 800; box-shadow: 0 10px 25px var(--primary-glow); }

        /* ── Integrated Lang Toggle ── */
        .info-lang-toggle {
            display: flex; background: rgba(255,255,255,0.05); 
            border: 1px solid var(--glass-border); border-radius: 12px;
            padding: 4px; gap: 4px; margin-top: 15px;
        }
        .lang-item { 
            flex: 1; text-align: center; padding: 12px; border-radius: 10px;
            font-size: 12px; font-weight: 800; color: var(--text-dim); cursor: pointer;
            transition: 0.3s;
        }
        .lang-item.active { background: var(--primary); color: #fff; }

        /* ── Navigation ── */
        .sticky-wrapper { position: sticky; top: -1px; z-index: 1000; background: rgba(15, 15, 18, 0.9); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-bottom: 1px solid var(--glass-border); }
        .main-nav { display: flex; overflow-x: auto; white-space: nowrap; scrollbar-width: none; padding: 10px 15px; }
        .main-nav::-webkit-scrollbar { display: none; }
        .nav-link-btn { padding: 12px 18px; font-size: 13px; font-weight: 700; color: var(--text-dim); cursor: pointer; position: relative; }
        .nav-link-btn.active { color: #fff; }
        .nav-link-btn.active::after { content: ''; position: absolute; bottom: 4px; left: 18px; right: 18px; height: 3px; background: var(--primary); border-radius: 10px; }
        .search-panel { padding: 0 15px 15px; }
        .search-bar { background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 50px; display: flex; align-items: center; padding: 10px 18px; gap: 12px; }
        .search-bar input { background: transparent; border: none; color: #fff; width: 100%; font-size: 14px; font-weight: 500; }

        .category-tabs { padding: 10px 15px 0; display: flex; overflow-x: auto; white-space: nowrap; scrollbar-width: none; gap: 10px; }
        .category-tabs::-webkit-scrollbar { display: none; }
        .cat-pill { padding: 8px 16px; border-radius: 50px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); color: var(--text-dim); font-size: 11px; font-weight: 700; cursor: pointer; }
        .cat-pill.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── Content Grid ── */
        .content-container { display: none; padding: 10px 15px 120px; }
        .content-container.active { display: block; animation: fadeInUp 0.4s ease; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        .item-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 15px; }

        /* ── Service Card (New) ── */
        .service-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 20px 15px;
            display: flex; flex-direction: column; align-items: center; text-align: center;
            transition: 0.3s; cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        .service-card:active { transform: scale(0.95); background: rgba(255,255,255,0.06); border-bottom-color: var(--primary); }
        .service-icon-box {
            width: 54px; height: 54px; 
            background: rgba(231, 122, 58, 0.1);
            border-radius: 50%; margin-bottom: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
        }
        .service-name { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .service-price { font-size: 11px; font-weight: 800; color: var(--primary); }

        /* ── Product/Menu Cards (Rich Design) ── */
        .rich-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--glass-border);
            border-radius: 22px;
            overflow: hidden;
            display: flex; flex-direction: column;
            transition: transform 0.3s, border-color 0.3s;
            cursor: default;
        }
        .rich-card:hover { transform: translateY(-4px); border-color: rgba(231,122,58,0.4); }
        .rich-thumb {
            aspect-ratio: 4/3;
            position: relative;
            background: #1a1a1e;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .rich-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
        .rich-card:hover .rich-thumb img { transform: scale(1.07); }
        .rich-thumb .no-img { font-size: 38px; opacity: 0.3; }
        .oos-overlay-dark {
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.55);
            display: flex; align-items: center; justify-content: center;
            z-index: 2;
        }
        .cat-chip {
            position: absolute; top: 8px; left: 8px;
            background: rgba(0,0,0,0.65); backdrop-filter: blur(6px);
            color: #fff; padding: 3px 10px; border-radius: 10px;
            font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.3px;
            z-index: 3;
        }
        .price-badge { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); backdrop-filter: blur(6px); padding: 4px 8px; border-radius: 10px; font-size: 10px; font-weight: 800; line-height: 1.2; text-align: right; z-index:3; }
        .price-tsh { color: var(--primary); display: block; }
        .price-usd { color: #fff; opacity: 0.6; font-size: 9px; font-weight: 700; }
        .rich-body { padding: 10px 12px 12px; flex: 1; display: flex; flex-direction: column; }
        .rich-title { font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 6px; line-height: 1.3; }
        .rich-sub { font-size: 13px; font-weight: 500; color: var(--text-dim); line-height: 1.4; margin-bottom: 12px; }
        .option-strip { display: flex; flex-direction: column; gap: 8px; margin-top: auto; }
        .opt-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 12px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
        }
        .opt-label { font-size: 11px; font-weight: 800; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.5px; }
        .opt-price { font-size: 15px; font-weight: 900; color: var(--primary); }
        .list-view { display: flex; flex-direction: column; gap: 15px; margin-top: 15px; }
        .list-view .rich-card { flex-direction: column; padding: 20px; border-radius: 24px; }
        .list-view .rich-thumb { display: none; }
        .list-view .rich-body { padding: 0; }
        .btn-order-mini {
            padding: 4px 10px; background: var(--primary); color: #fff;
            border: none; border-radius: 7px; font-size: 10px; font-weight: 800;
            cursor: pointer; white-space: nowrap;
            transition: background 0.2s;
        }
        .btn-order-mini:hover { background: #c45e18; }

        /* ── Discover Section ── */
        .discover-card {
            background: rgba(255,255,255,0.02);
            border-radius: 28px; overflow: hidden;
            margin-bottom: 25px; border: 1px solid var(--glass-border);
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }
        .discover-img { height: 180px; background-size: cover; background-position: center; position: relative; }
        .discover-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, transparent 30%, rgba(0,0,0,0.9)); padding: 20px; display: flex; align-items: flex-end; }
        .discover-tag { font-size: 10px; font-weight: 900; background: var(--primary); color: #fff; padding: 3px 10px; border-radius: 7px; text-transform: uppercase; margin-bottom: 6px; display: inline-block; }
        .discover-body { padding: 20px; border-top: 1px solid var(--glass-border); }
        .discover-title { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 0; }
        .discover-text { font-size: 13px; line-height: 1.5; color: var(--text-dim); margin-top: 10px; }

        /* ── Info & Services ── */
        .info-card { background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 20px; margin-bottom: 12px; }
        .info-header { font-size: 11px; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .info-header::after { content: ''; flex: 1; height: 1px; background: var(--glass-border); }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
        .label { color: var(--text-dim); }
        .value { font-weight: 700; color: #fff; }

        /* ── Floating Interface ── */
        .floats { position: fixed; bottom: 25px; left: 0; right: 0; z-index: 2000; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; pointer-events: none; }
        .floats > * { pointer-events: auto; }
        .whatsapp-bubble { width: 55px; height: 55px; background: #25d366; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3); text-decoration: none; }
        .order-action-btn { 
            padding: 0 25px; height: 55px; background: var(--primary); color: #fff; 
            border-radius: 50px; display: flex; align-items: center; justify-content: center; 
            font-weight: 800; font-size: 14px; gap: 10px; 
            box-shadow: 0 10px 25px var(--primary-glow); text-decoration: none; border: none; 
        }

        /* ── Modal ── */
        .modal-content { background: #111; border: 1px solid var(--glass-border); border-radius: 30px; }
        .modal-body { padding: 25px; }
        .dept-phone { color: var(--primary); font-weight: 800; font-size: 14px; }

        /* ── Description Modal ── */
        .desc-modal-body { color: var(--text-dim); font-size: 15px; line-height: 1.6; }
        .desc-modal-title { font-weight: 800; color: #fff; margin-bottom: 5px; }
        .clickable-card { cursor: pointer !important; position: relative; }
        .clickable-card:active { transform: scale(0.98); }

        @keyframes pulse-steam {
            0% { transform: scale(0.98); box-shadow: 0 0 0 0 rgba(231, 122, 58, 0.6); }
            70% { transform: scale(1.02); box-shadow: 0 0 0 8px rgba(231, 122, 58, 0); }
            100% { transform: scale(0.98); box-shadow: 0 0 0 0 rgba(231, 122, 58, 0); }
        }
        .btn-order-mini {
            padding: 6px 15px; background: var(--primary); color: #fff;
            border: none; border-radius: 50px; font-size: 11px; font-weight: 800;
            cursor: pointer; white-space: nowrap;
            transition: 0.3s;
            animation: pulse-steam 2.5s infinite;
        }
        .btn-order-mini:hover { background: #c45e18; transform: scale(1.05); }

        /* ════════════ SPLASH SCREEN ════════════ */
        #splashScreen {
            position: fixed; inset: 0; z-index: 99999;
            background: radial-gradient(ellipse at 50% 40%, #1a1209 0%, #0b0b0d 70%);
            display: flex; align-items: center; justify-content: center;
            transition: opacity 0.8s ease, transform 0.8s ease;
            overflow: hidden;
        }
        #splashScreen.hidden {
            opacity: 0; transform: translateY(-30px); pointer-events: none;
        }

        /* Ambient glow orb */
        #splashScreen::before {
            content: '';
            position: absolute;
            width: 340px; height: 340px;
            background: radial-gradient(circle, rgba(231,122,58,0.18) 0%, transparent 70%);
            border-radius: 50%;
            top: 50%; left: 50%; transform: translate(-50%, -60%);
            animation: orbPulse 3s ease-in-out infinite;
        }
        @keyframes orbPulse {
            0%,100% { opacity: 0.6; transform: translate(-50%,-60%) scale(1); }
            50%      { opacity: 1;   transform: translate(-50%,-60%) scale(1.15); }
        }

        .splash-inner {
            position: relative; z-index: 2;
            display: flex; flex-direction: column; align-items: center; text-align: center;
            padding: 30px 24px;
            animation: splashRise 0.7s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        @keyframes splashRise {
            from { opacity: 0; transform: translateY(40px) scale(0.9); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Logo wrapper with dual orbit rings */
        .splash-logo-wrap {
            position: relative; width: 100px; height: 100px; margin-bottom: 22px;
        }
        .orbit-ring {
            position: absolute; inset: 0; border-radius: 50%;
            border: 1.5px dashed rgba(231,122,58,0.3);
            animation: orbitSpin 6s linear infinite;
        }
        .orbit-ring:nth-child(2) {
            inset: 8px;
            border-color: rgba(231,122,58,0.18);
            animation-direction: reverse;
            animation-duration: 4s;
        }
        /* Dot on outer ring */
        .orbit-ring::after {
            content: '';
            position: absolute; top: -3px; left: 50%; margin-left: -3px;
            width: 6px; height: 6px;
            background: var(--primary); border-radius: 50%;
            box-shadow: 0 0 6px var(--primary);
        }
        @keyframes orbitSpin { to { transform: rotate(360deg); } }

        .splash-logo-img {
            position: absolute;
            inset: 14px;
            object-fit: contain;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            padding: 8px;
            animation: logoPulse 2s ease-in-out infinite alternate;
            width: calc(100% - 28px);
            height: calc(100% - 28px);
        }
        @keyframes logoPulse {
            from { transform: scale(0.96); filter: drop-shadow(0 0 0px rgba(231,122,58,0)); }
            to   { transform: scale(1.04); filter: drop-shadow(0 0 10px rgba(231,122,58,0.6)); }
        }

        /* Shimmer tagline */
        .splash-brand {
            font-size: 10px; letter-spacing: 6px; font-weight: 900;
            text-transform: uppercase; margin-bottom: 8px;
            background: linear-gradient(90deg, var(--primary) 0%, #ffd580 50%, var(--primary) 100%);
            background-size: 200%;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: shimmer 2.5s linear infinite;
        }
        @keyframes shimmer { to { background-position: 200% center; } }

        .splash-title {
            font-size: 26px; font-weight: 900; letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 0%, #cccccc 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 6px; line-height: 1.2;
        }
        .splash-sub {
            font-size: 13px; color: var(--text-dim); line-height: 1.6; margin-bottom: 20px;
        }

        /* Feature pills */
        .splash-pills {
            display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;
            margin-bottom: 24px;
        }
        .splash-pill {
            font-size: 11px; font-weight: 700; padding: 5px 13px;
            border-radius: 50px;
            background: rgba(231,122,58,0.12);
            border: 1px solid rgba(231,122,58,0.35);
            color: #e77a3a;
            animation: pillFade 0.5s ease both;
        }
        .splash-pill:nth-child(1) { animation-delay: 0.3s; }
        .splash-pill:nth-child(2) { animation-delay: 0.5s; }
        .splash-pill:nth-child(3) { animation-delay: 0.7s; }
        .splash-pill:nth-child(4) { animation-delay: 0.9s; }
        @keyframes pillFade {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Progress bar */
        .splash-progress-wrap {
            width: 160px; height: 3px;
            background: rgba(255,255,255,0.08);
            border-radius: 10px; overflow: hidden;
        }
        .splash-progress-bar {
            height: 100%; width: 0;
            background: linear-gradient(90deg, var(--primary), #ffd580);
            border-radius: 10px;
            animation: progressFill 2.5s ease forwards;
        }
        @keyframes progressFill { to { width: 100%; } }

    </style>
</head>
<body>

    <!-- ═══ Welcome Splash Screen ═══ -->
    <div id="splashScreen">
        <div class="splash-inner">

            <!-- Logo with dual orbit rings -->
            <div class="splash-logo-wrap">
                <div class="orbit-ring"></div>
                <div class="orbit-ring"></div>
                <img src="{{ asset('royal-master/image/logo/Logo.png') }}"
                     alt="PrimeLand Hotel"
                     class="splash-logo-img">
            </div>

            <div class="splash-brand">PrimeLand Hotel</div>
            <h2 class="splash-title">Welcome, Valued Guest</h2>
            <p class="splash-sub">Your comfort is our pride.<br>Everything you need — right here.</p>

            <!-- Feature pills -->
            <div class="splash-pills">
                <span class="splash-pill">🍳 Food Menu</span>
                <span class="splash-pill">🍷 Drinks</span>
                <span class="splash-pill">🛎️ Services</span>
                <span class="splash-pill">⛰️ Discover</span>
            </div>

            <!-- Progress bar -->
            <div class="splash-progress-wrap">
                <div class="splash-progress-bar"></div>
            </div>

        </div>
    </div>

    @php
        $usdRate = 2700;
        function formatPrice($tsh, $rate) {
            $formattedTsh = number_format($tsh);
            $usd = round($tsh / $rate, 2);
            return '<span class="price-tsh">Tsh ' . $formattedTsh . '</span><span class="price-usd">($' . number_format($usd, 2) . ')</span>';
        }
    @endphp

    <!-- Header -->
    <section class="hero-section">
        <div class="hotel-brand">PrimeLand Hotel</div>
        <h1 class="hero-title" data-en="Comfort in every stay" data-sw="Faraja katika kila kukaa">Comfort in every stay</h1>
        
        @if($roomNumber)
            <div class="room-indicator animate__animated animate__bounceIn">
                <i class="fa fa-key"></i> <span data-en="Room" data-sw="Chumba">Room</span> {{ $roomNumber }}
            </div>
        @endif
    </section>

    <!-- Navigation -->
    <div class="sticky-wrapper">
        <nav class="main-nav">
            <div class="nav-link-btn active" onclick="navTo('services', this)" data-en="🛎️ SERVICES" data-sw="🛎️ HUDUMA">🛎️ SERVICES</div>
            <div class="nav-link-btn" onclick="navTo('food', this)" data-en="🍳 FOOD MENU" data-sw="🍳 ORODHA YA CHAKULA">🍳 FOOD MENU</div>
            <div class="nav-link-btn" onclick="navTo('drinks', this)" data-en="🍷 DRINKS" data-sw="🍷 VINYWAJI">🍷 DRINKS</div>
            <div class="nav-link-btn" onclick="navTo('discover', this)" data-en="⛰️ DISCOVER KILIMANJARO" data-sw="⛰️ TEMBELEA KILIMANJARO">⛰️ DISCOVER KILIMANJARO</div>
            <div class="nav-link-btn" onclick="navTo('info', this)" data-en="ℹ️ INFO" data-sw="ℹ️ TAARIFA">ℹ️ INFO</div>
        </nav>

        <div class="search-panel">
            <div class="search-bar">
                <i class="fa fa-search text-muted"></i>
                <input type="text" id="masterSearch" placeholder="Find food or drinks..." data-en-p="Find food or drinks..." data-sw-p="Tafuta chakula au kinywaji..." oninput="triggerSearch()">
            </div>
        </div>
    </div>

    <!-- FOOD -->
    <div id="page-food" class="content-container">
        <div class="category-tabs" id="foodCatTabs">
            <div class="cat-pill active" data-cat="all" onclick="tabFilter('food','all',this)" data-en="All" data-sw="Vyote">All</div>
            @foreach($foodCategories as $cat)
                @php
                    $catName = match($cat) {
                        'salads' => 'Salads',
                        'soups' => 'Soup',
                        'snacks' => 'Snacks & Bites',
                        'main_course' => 'Main Course',
                        'pasta_noodles' => 'Pasta & Noodles',
                        'fish_dishes' => 'Fish Dishes',
                        'side_dishes' => 'Side Dishes',
                        'burgers' => 'Burgers',
                        'pizza_corner' => 'Pizza Corner',
                        'sandwiches' => 'Sandwiches',
                        'desserts' => 'Desserts',
                        default => ucwords(str_replace('_',' ',$cat))
                    };
                @endphp
                <div class="cat-pill" data-cat="{{ $cat }}" onclick="tabFilter('food','{{ $cat }}',this)">{{ $catName }}</div>
            @endforeach
        </div>

        <div class="list-view" id="grid-food">
            @forelse($recipes as $recipe)
                <div class="rich-card recipe-node clickable-card" 
                     data-cat="{{ $recipe->category }}" 
                     data-search="{{ strtolower($recipe->name) }}"
                     onclick="showFullDescription('{{ addslashes($recipe->name) }}', '{{ addslashes($recipe->description ?? '') }}')">
                    <div class="rich-thumb">
                        <span class="cat-chip">{{ $recipe->category_name }}</span>
                    </div>
                    <div class="rich-body">
                        <div class="rich-title">{{ $recipe->name }}</div>
                        @if($recipe->description)
                            <div class="rich-sub">{{ Str::limit($recipe->description, 55) }}</div>
                        @endif
                        <div class="option-strip">
                            <div class="opt-row">
                                <div style="flex: 1;">
                                    <div class="opt-label">Portion</div>
                                    <div class="opt-price">
                                        @if($recipe->selling_price > 0)
                                            {{ number_format($recipe->selling_price) }} TZS
                                            @if($recipe->selling_price_usd > 0)
                                                <span style="color:#fff;opacity:0.6;font-size:10px;">(${{ number_format($recipe->selling_price_usd) }})</span>
                                            @endif
                                        @else
                                            <span data-en="Subject to availability" data-sw="Kulingana na upatikanaji">Subject to availability</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button class="btn-order-mini" onclick="event.stopPropagation(); requestItem('{{ addslashes($recipe->name) }}')">Order Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column:span 2;text-align:center;padding:40px;color:var(--text-dim);">No food items available.</div>
            @endforelse
        </div>
    </div>

    <!-- DRINKS -->
    <div id="page-drinks" class="content-container">
        <div class="category-tabs" id="drinkCatTabs">
            <div class="cat-pill active" data-cat="all" onclick="tabFilter('drinks','all',this)" data-en="All" data-sw="Vyote">All</div>
            @foreach($drinkCategories as $cat)
                <div class="cat-pill" data-cat="{{ $cat }}" onclick="tabFilter('drinks','{{ $cat }}',this)">{{ ucwords(str_replace('_',' ',$cat)) }}</div>
            @endforeach
        </div>

        <div class="list-view" id="grid-drinks">
            @forelse($drinks as $drink)
                <div class="rich-card drink-node" data-cat="{{ $drink->category }}" data-search="{{ strtolower($drink->name) }}">
                    <div class="rich-thumb">
                        <span class="cat-chip">{{ ucwords(str_replace('_',' ',$drink->category)) }}</span>
                        @if(!($drink->in_stock ?? true))
                            <div class="oos-overlay-dark">
                                <span style="background:#e74c3c;color:#fff;padding:5px 12px;border-radius:12px;font-size:11px;font-weight:800;">Out of Stock</span>
                            </div>
                        @endif
                        @if($drink->image)
                            <img src="{{ asset('storage/' . $drink->image) }}" alt="{{ $drink->name }}"
                                 onerror="this.outerHTML='<div class=&quot;no-img&quot;>🍾</div>'"
                                 style="{{ !($drink->in_stock ?? true) ? 'filter:grayscale(1)brightness(0.6);' : '' }}">
                        @else
                            @php
                                $drinkDesign = [
                                    'spirits' => ['icon' => 'fa-glass', 'grad' => 'linear-gradient(135deg, #4b6cb7 0%, #182848 100%)'],
                                    'wines' => ['icon' => 'fa-glass', 'grad' => 'linear-gradient(135deg, #8e0e00 0%, #1f1c18 100%)'],
                                    'alcoholic_beverage' => ['icon' => 'fa-beer', 'grad' => 'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)'],
                                    'cocktails' => ['icon' => 'fa-glass', 'grad' => 'linear-gradient(135deg, #00f2fe 0%, #4facfe 100%)'],
                                    'non_alcoholic_beverage' => ['icon' => 'fa-tint', 'grad' => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)'],
                                    'energy_drinks' => ['icon' => 'fa-bolt', 'grad' => 'linear-gradient(135deg, #f12711 0%, #f5af19 100%)'],
                                    'water' => ['icon' => 'fa-tint', 'grad' => 'linear-gradient(135deg, #00c6ff 0%, #0072ff 100%)'],
                                    'juices' => ['icon' => 'fa-sun-o', 'grad' => 'linear-gradient(135deg, #ff9966 0%, #ff5e62 100%)'],
                                    'hot_beverages' => ['icon' => 'fa-coffee', 'grad' => 'linear-gradient(135deg, #3D2B1F 0%, #964B00 100%)'],
                                ];
                                $dCatKey = strtolower(str_replace([' ', '-', '/'], '_', $drink->category ?? ''));
                                $dStyle = $drinkDesign[$dCatKey] ?? ['icon' => 'fa-glass', 'grad' => 'linear-gradient(135deg, #e77a31 0%, #eaafc8 100%)'];
                            @endphp
                            <div class="d-flex align-items-center justify-content-center w-100 h-100" style="background: {!! $dStyle['grad'] !!};">
                                <i class="fa {!! $dStyle['icon'] !!} fa-3x text-white opacity-40"></i>
                            </div>
                        @endif
                    </div>
                    <div class="rich-body">
                        <div class="rich-title">{{ $drink->name }}</div>
                        <div class="option-strip">
                            @foreach($drink->options as $opt)
                                <div class="opt-row">
                                    <div>
                                        <div class="opt-label">{{ $opt['type'] }}</div>
                                        <div class="opt-price">
                                            {{ number_format($opt['price']) }} TZS
                                            @if(!empty($opt['price_usd']) && $opt['price_usd'] > 0)
                                                <span style="color:#fff;opacity:0.6;font-size:10px;">(${{ rtrim(rtrim(number_format($opt['price_usd'], 2), '0'), '.') }})</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($drink->in_stock ?? true)
                                        <button class="btn-order-mini" onclick="requestItem('{{ addslashes($drink->name) }} ({{ $opt['type'] }})')">Order</button>
                                    @else
                                        <span style="font-size:10px;color:var(--text-dim);">N/A</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column:span 2;text-align:center;padding:40px;color:var(--text-dim);">No drinks available.</div>
            @endforelse
        </div>
    </div>

    <!-- DISCOVER MOSHI -->
    <div id="page-discover" class="content-container">
        <div class="info-card">
            <div class="info-header" data-en="Travel Concierge" data-sw="Mahali pa Kutembelea">Travel Concierge</div>
            
            <div class="discover-card">
                <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/kilimanjaro copy.jpg') }}');">
                    <div class="discover-overlay">
                        <div>
                            <span class="discover-tag">Iconic View</span>
                            <h5 class="discover-title" data-en="Mt. Kilimanjaro" data-sw="Mlima Kilimanjaro">Mt. Kilimanjaro</h5>
                        </div>
                    </div>
                </div>
                <div class="discover-body">
                    <p class="discover-text" data-en="Africa's rooftop. Visible from our hotel terrace during clear mornings. Experience the majesty of the world's highest free-standing mountain." data-sw="Kilele cha Afrika. Huonekana kutoka kwenye terasi ya hoteli yetu asubuhi. Jionee utukufu wa mlima mrefu zaidi duniani unaojitegemea.">Africa's rooftop. Visible from our hotel terrace during clear mornings. Experience the majesty of the world's highest free-standing mountain.</p>
                </div>
            </div>

            <div class="discover-card">
                <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/materuni.jpg') }}');">
                    <div class="discover-overlay">
                        <div>
                            <span class="discover-tag">Adventure</span>
                            <h5 class="discover-title" data-en="Materuni Falls" data-sw="Maporomoko ya Materuni">Materuni Falls</h5>
                        </div>
                    </div>
                </div>
                <div class="discover-body">
                    <p class="discover-text" data-en="A stunning 70m waterfall hidden in the rainforest. Includes a traditional coffee tour and a swim in the pool below." data-sw="Maporomoko ya maji yenye mita 70 yaliyofichwa kwenye msitu. Inajumuisha ziara ya kahawa ya asili na kuogelea." >A stunning 70m waterfall hidden in the rainforest. Includes a traditional coffee tour and a swim in the pool below.</p>
                </div>
            </div>

            <div class="discover-card">
                <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/chemka.jpg') }}');">
                    <div class="discover-overlay">
                        <div>
                            <span class="discover-tag">Relaxation</span>
                            <h5 class="discover-title" data-en="Chemka Hot Springs" data-sw="Chem Chem ya Chemka">Chemka Hot Springs</h5>
                        </div>
                    </div>
                </div>
                <div class="discover-body">
                    <p class="discover-text" data-en="Crystal clear turquoise waters in a desert oasis. Warm mineral waters perfect for a relaxing day trip." data-sw="Maji ya rangi kama anga kwenye bustani ya jangwani. Maji ya joto ya madini ni mazuri kwa matembezi ya kupumzika.">Crystal clear turquoise waters in a desert oasis. Warm mineral waters perfect for a relaxing day trip.</p>
                </div>
            </div>

            <div class="discover-card p-4 text-center" style="background: var(--primary); border: none;">
                <h5 class="font-weight-bold mb-2" data-en="Private Tour Booking" data-sw="Weka Nafasi ya Ziara">Private Tour Booking</h5>
                <p class="small text-white opacity-8 mb-3" data-en="We provide reliable taxis and expert local guides for all destinations." data-sw="Tunatoa taksi za uhakika na waongoza watalii wataalamu kwa maeneo yote.">We provide reliable taxis and expert local guides for all destinations.</p>
                <a href="tel:601" class="btn btn-light btn-block font-weight-bold" style="border-radius: 12px; color: var(--primary);" data-en="Call Reception" data-sw="Piga Mapokezi">Call Reception</a>
            </div>
        </div>
    </div>

    <!-- SERVICES -->
    <div id="page-services" class="content-container active">
        <div class="info-card mb-4 text-center pb-4 border-0 bg-transparent shadow-none" style="padding-top: 10px;">
            <div class="splash-icon mb-2 position-relative d-inline-block text-primary" style="font-size:38px; animation: none;">🏨</div>
            <h4 class="font-weight-bold" data-en="Welcome to Primeland Hotel!" data-sw="Karibu Primeland Hotel!">Welcome to Primeland Hotel!</h4>
            <p class="text-muted small px-3 mt-2" data-en="We are delighted to have you onboard. Here is a quick information about our hotel to help you navigate smoothly on your stay with us:" data-sw="Tunafurahi kuwa nawe. Hapa kuna maelezo mafupi kuhusu hoteli yetu ili kukusaidia katika kukaa kwako:">We are delighted to have you onboard. Here is a quick information about our hotel to help you navigate smoothly on your stay with us:</p>
        </div>

        <div class="info-header mb-3" data-en="OUR SERVICES" data-sw="HUDUMA ZETU">OUR SERVICES</div>

        <div class="discover-card">
            <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/executive suite.jpg') }}'); height: 150px;">
                <div class="discover-overlay pb-2 pt-4" style="background: linear-gradient(to bottom, transparent 10%, rgba(0,0,0,0.9));">
                    <div>
                        <span class="discover-tag"><i class="fa fa-bed"></i> Comfort</span>
                        <h5 class="discover-title" data-en="Accommodation" data-sw="Malazi">Accommodation</h5>
                    </div>
                </div>
            </div>
            <div class="discover-body pt-3 pb-4">
                <p class="discover-text mt-0 text-white font-weight-bold" data-en="Welcome to our accommodation service." data-sw="Karibu kwenye huduma yetu ya malazi.">Welcome to our accommodation service.</p>
                <div class="text-muted small" style="line-height: 1.7;">
                    <ul class="pl-3 mb-0">
                        <li data-en="Check-in time is 1400hrs; check out time is 10:00am">Check-in time is 1400hrs; check out time is 10:00am</li>
                        <li class="mt-1" data-en="Late checkout depends on availability and may be subject to an extra fee of 50% of room rate. Kindly inquire with front office receptionist.">Late checkout depends on availability and may be subject to an extra fee of 50% of room rate. Kindly inquire with front office receptionist.</li>
                        <li class="mt-1" data-en="Late checkout later than 1600hrs is subject to a full room rate.">Late checkout later than 1600hrs is subject to a full room rate.</li>
                        <li class="mt-1" data-en="Maximum guests occupancy per room is 2 adults.">Maximum guests occupancy per room is 2 adults.</li>
                        <li class="mt-1" data-en="All our rooms are non-smoking. We kindly request you to use the public areas for smoking to ensure comfort of fellow guests.">All our rooms are non-smoking. We kindly request you to use the public areas for smoking to ensure comfort of fellow guests.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="discover-card">
            <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/service-02.jpg') }}'); height: 150px;">
                <div class="discover-overlay pb-2 pt-4" style="background: linear-gradient(to bottom, transparent 10%, rgba(0,0,0,0.9));">
                    <div>
                        <span class="discover-tag"><i class="fa fa-phone"></i> Support</span>
                        <h5 class="discover-title" data-en="24/7 Front Desk Service" data-sw="Huduma ya Mapokezi 24/7">24/7 Front Desk Service</h5>
                    </div>
                </div>
            </div>
            <div class="discover-body pt-3 pb-4">
                <p class="discover-text mt-0 text-white font-weight-bold" data-en="Our front office reception is open to check in/out guests through the day." data-sw="Mapokezi yetu yako wazi siku nzima kwa kuingiza/kutoa wageni.">Our front office reception is open to check in/out guests through the day.</p>
                <p class="text-muted small mt-2 mb-3" data-en="Please use the following telephone extension to make inquiry for specific service:" data-sw="Tafadhali tumia namba zifuatazo kuulizia huduma maalum:">Please use the following telephone extension to make inquiry for specific service:</p>
                
                <div class="row text-center mx-0 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="col-4 p-2 border-right" style="border-color: rgba(255,255,255,0.1) !important;">
                        <small class="d-block text-muted font-weight-bold mb-1" data-en="Reception" data-sw="Mapokezi">Reception</small>
                        <b class="text-primary" style="font-size: 16px;">601</b>
                    </div>
                    <div class="col-4 p-2 border-right" style="border-color: rgba(255,255,255,0.1) !important;">
                        <small class="d-block text-muted font-weight-bold mb-1" data-en="Kitchen" data-sw="Jikoni">Kitchen</small>
                        <b class="text-primary" style="font-size: 16px;">601</b>
                    </div>
                    <div class="col-4 p-2">
                        <small class="d-block text-muted font-weight-bold mb-1" data-en="Bar" data-sw="Baa">Bar</small>
                        <b class="text-primary" style="font-size: 16px;">619</b>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <small class="text-muted"><span data-en="Laundry:" data-sw="Dobi:">Laundry:</span> <b class="text-white">601</b></small>
                </div>
            </div>
        </div>

        <div class="discover-card">
            <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/enjoy-your-day-01.jpg') }}'); height: 130px;">
                <div class="discover-overlay pb-2 pt-4" style="background: linear-gradient(to bottom, transparent 10%, rgba(0,0,0,0.9));">
                    <div>
                        <span class="discover-tag"><i class="fa fa-life-ring"></i> Leisure</span>
                        <h5 class="discover-title" data-en="Swimming Pool" data-sw="Bwawa la Kuogelea">Swimming Pool</h5>
                    </div>
                </div>
            </div>
            <div class="discover-body pt-3 pb-3">
                <p class="discover-text my-0 text-white font-weight-bold" data-en="Open daily free of charge for in-house guests from 6:00am - 6:30pm" data-sw="Wazi kila siku bure kwa wageni wa ndani kuanzia 6:00 asubuhi hadi 6:30 jioni">Open daily free of charge for in-house guests from 6:00am - 6:30pm</p>
            </div>
        </div>

        <div class="discover-card">
            <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/service-o1.jpg') }}'); height: 130px;">
                <div class="discover-overlay pb-2 pt-4" style="background: linear-gradient(to bottom, transparent 10%, rgba(0,0,0,0.9));">
                    <div>
                        <span class="discover-tag"><i class="fa fa-cutlery"></i> Dining</span>
                        <h5 class="discover-title" data-en="Restaurant & Pool Bar" data-sw="Mkahawa na Baa">Restaurant & Pool Bar</h5>
                    </div>
                </div>
            </div>
            <div class="discover-body pt-3 pb-4">
                <p class="discover-text mt-0 mb-2 text-white font-weight-bold" data-en="Open for service from 07:00 to 2100hrs" data-sw="Wazi kuanzia saa 07:00 hadi 21:00">Open for service from 07:00 to 2100hrs</p>
                <p class="text-muted small my-0" data-en="Kindly navigate to our Food & Drinks section to explore available options to order." data-sw="Tafadhali nenda kwenye sehemu yetu ya Chakula na Vinywaji ili kuchunguza chaguzi zinazopatikana ili kuagiza.">Kindly navigate to our Food Menu section to explore available options to order.</p>
                <button class="btn btn-sm btn-outline-primary mt-3 font-weight-bold w-100" onclick="navTo('food', document.querySelectorAll('.nav-link-btn')[1])"><i class="fa fa-arrow-right mr-1"></i> View Menu</button>
            </div>
        </div>

        <div class="discover-card">
            <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/room-01.jpg') }}'); height: 130px;">
                <div class="discover-overlay pb-2 pt-4" style="background: linear-gradient(to bottom, transparent 10%, rgba(0,0,0,0.9));">
                    <div>
                        <span class="discover-tag"><i class="fa fa-wifi"></i> Connectivity</span>
                        <h5 class="discover-title" data-en="Free WI-FI" data-sw="WI-FI Bure">Free WI-FI</h5>
                    </div>
                </div>
            </div>
            <div class="discover-body pt-3 pb-3">
                <p class="discover-text my-0 text-white font-weight-bold" data-en="Enjoy high speed free WI-FI, the passkey is on your room card." data-sw="Furahia WI-FI ya kasi bila malipo, nenosiri liko kwenye kadi yako ya chumba.">Enjoy high speed free WI-FI, the passkey is on your room card.</p>
            </div>
        </div>

        <div class="discover-card">
            <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/room-03.jpg') }}'); height: 130px;">
                <div class="discover-overlay pb-2 pt-4" style="background: linear-gradient(to bottom, transparent 10%, rgba(0,0,0,0.9));">
                    <div>
                        <span class="discover-tag"><i class="fa fa-shirtsinbulk"></i> Care</span>
                        <h5 class="discover-title" data-en="Laundry Service" data-sw="Huduma ya Dobi">Laundry Service</h5>
                    </div>
                </div>
            </div>
            <div class="discover-body pt-3 pb-3">
                <p class="discover-text my-0 text-white font-weight-bold mb-2" data-en="Laundry service is available daily from 08:00hrs to 1600hrs" data-sw="Huduma ya dobi inapatikana kila siku kutoka 08:00 hadi 16:00">Laundry service is available daily from 08:00hrs to 1600hrs</p>
                <p class="text-muted small my-0" data-en="Please fill in the laundry list in your room." data-sw="Tafadhali jaza orodha ya dobi chumbani kwako.">Please fill in the laundry list in your room.</p>
            </div>
        </div>

        <div class="discover-card">
            <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/service-03.jpg') }}'); height: 130px;">
                <div class="discover-overlay pb-2 pt-4" style="background: linear-gradient(to bottom, transparent 10%, rgba(0,0,0,0.9));">
                    <div>
                        <span class="discover-tag"><i class="fa fa-car"></i> Transport</span>
                        <h5 class="discover-title" data-en="Airport Shuttle" data-sw="Usafiri wa Uwanja wa Ndege">Airport Shuttle</h5>
                    </div>
                </div>
            </div>
            <div class="discover-body pt-3 pb-4">
                <p class="discover-text mt-0 mb-2 text-white font-weight-bold" data-en="Available at an extra fee of 50USD / 125,000TSh per vehicle per trip (maximum passage 4 pax)." data-sw="Inapatikana kwa ada ya ziada ya USD 50 / 125,000TSh kwa kila gari kwa safari (kiwango cha juu abiria 4).">Available at an extra fee of 50USD / 125,000TSh per vehicle per trip (maximum passage 4 pax).</p>
                <p class="text-muted small my-0" data-en="Kindly communicate with receptionist for reservations." data-sw="Tafadhali wasiliana na mapokezi kwa ajili ya uwekaji nafasi.">Kindly communicate with receptionist for reservations.</p>
            </div>
        </div>

        <div class="discover-card mb-2">
            <div class="discover-img" style="background-image: url('{{ asset('landing-assets/new_images_assets/room-02.jpg') }}'); height: 130px;">
                <div class="discover-overlay pb-2 pt-4" style="background: linear-gradient(to bottom, transparent 10%, rgba(0,0,0,0.9));">
                    <div>
                        <span class="discover-tag"><i class="fa fa-shield"></i> Safety & Security</span>
                        <h5 class="discover-title" data-en="24/7 Security" data-sw="Ulinzi 24/7">24/7 Security</h5>
                    </div>
                </div>
            </div>
            <div class="discover-body pt-3 pb-4">
                <p class="discover-text my-0 text-white font-weight-bold" data-en="At Primeland hotel, we put high regard about safety and security of our guests." data-sw="Katika hoteli ya Primeland, tunazingatia sana usalama na ulinzi wa wageni wetu.">At Primeland hotel, we put high regard about safety and security of our guests.</p>
            </div>
        </div>
    </div>

    <!-- INFO -->
    <div id="page-info" class="content-container">
        <div class="info-card">
            <div class="info-header" data-en="Connect" data-sw="Muunganisho">Connect</div>
            <div class="info-row"><span class="label" data-en="WiFi Network" data-sw="Mtandao wa WiFi">WiFi Network</span><span class="value">PrimeLand_Guests</span></div>
            <div class="info-row"><span class="label" data-en="Password" data-sw="Neno la Siri">Password</span><span class="value">primeland2024</span></div>
            <div class="info-row mt-3">
                <span class="label" data-en="Website" data-sw="Tovuti">Website</span>
                <a href="https://primelandhotel.com" target="_blank" class="value text-primary">www.primelandhotel.com</a>
            </div>
        </div>

        <div class="info-card">
            <div class="info-header" data-en="Language Settings" data-sw="Marekebisho ya Lugha">Language Settings</div>
            <div class="info-lang-toggle">
                <div class="lang-item active" id="btn-en" onclick="setLang('en')">
                    <i class="fa fa-globe mr-1"></i> ENGLISH
                </div>
                <div class="lang-item" id="btn-sw" onclick="setLang('sw')">
                    <i class="fa fa-globe mr-1"></i> SWAHILI
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="info-header" data-en="Social Media" data-sw="Mitandao ya Jamii">Social Media</div>
            <div class="d-flex justify-content-around py-2">
                <a href="https://instagram.com/primelandhotel" target="_blank" class="text-white" style="font-size: 24px;"><i class="fa fa-instagram"></i></a>
                <a href="https://facebook.com/primelandhotel" target="_blank" class="text-white" style="font-size: 24px;"><i class="fa fa-facebook-square"></i></a>
                <a href="https://wa.me/255677155156" target="_blank" class="text-white" style="font-size: 24px;"><i class="fa fa-whatsapp"></i></a>
            </div>
        </div>

        <div class="info-card">
            <div class="info-header" data-en="Directory" data-sw="Namba za Simu">Directory</div>
            <div class="info-row"><span class="label" data-en="Reception" data-sw="Mapokezi">Reception</span><span class="value">Ext: 601</span></div>
            <div class="info-row"><span class="label" data-en="The Bar" data-sw="Baa">The Bar</span><span class="value">Ext: 619</span></div>
            <div class="info-row"><span class="label" data-en="Manager" data-sw="Meneja">Manager</span><span class="value">Ext: 618</span></div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 40px; margin-bottom: 30px; padding-bottom: 80px; font-size: 11px; color: rgba(255,255,255,0.6); font-family: 'Inter', sans-serif;">
        <div data-en="Powered By" data-sw="Imeundwa na">Powered By</div>
        <a href="https://www.emca.tech" target="_blank" style="color: #940000; font-weight: 800; margin-top: 3px; font-size: 14px; letter-spacing: 1px; text-decoration: none;">EmCa Techonologies</a>
    </div>

    <!-- Floats -->
    <div class="floats" style="justify-content: center;">
        <button class="order-action-btn" onclick="toggleCallModal(true)">
            <i class="fa fa-phone"></i> <span data-en="Order Now" data-sw="Agiza Sasa">Order Now</span>
        </button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="orderCallModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h5 class="font-weight-bold" id="orderTarget" data-en="Contact Department" data-sw="Wasiliana na Idara">Contact Department</h5>
                        <p class="text-muted small" data-en="Select your preferred line" data-sw="Chagua namba unayopendelea">Select your preferred line</p>
                    </div>
                    <a href="tel:601" class="call-item"><div class="dept-info"><b data-en="Reception" data-sw="Mapokezi">Reception</b><span data-en="Internal" data-sw="Ndani ya Hotel">Internal</span></div><div class="dept-phone">601</div></a>
                    <a href="tel:619" class="call-item"><div class="dept-info"><b data-en="Prime Bar" data-sw="Baa ya Prime">Prime Bar</b><span data-en="Drinks" data-sw="Vinywaji">Drinks</span></div><div class="dept-phone">619</div></a>
                    <a href="tel:+255677155156" class="call-item"><div class="dept-info"><b data-en="Reception Mobile" data-sw="Simu ya Mapokezi">Reception Mobile</b><span data-en="Direct Line" data-sw="Namba ya Moja kwa Moja">Direct Line</span></div><div class="dept-phone">0677155156</div></a>
                    <a href="tel:+255677155157" class="call-item"><div class="dept-info"><b data-en="Manager" data-sw="Meneja">Manager</b><span data-en="Supervisor" data-sw="Msimamizi">Supervisor</span></div><div class="dept-phone">0677155157</div></a>
                    <button class="btn btn-link btn-block text-muted mt-2" onclick="toggleCallModal(false)" data-en="Close" data-sw="Funga">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Food Description Modal -->
    <div class="modal fade" id="descModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 25px;">
                <div class="modal-body">
                    <h5 class="desc-modal-title" id="descTitle">Item Name</h5>
                    <hr style="border-color: var(--glass-border); margin: 15px 0;">
                    <p class="desc-modal-body" id="descText">Description goes here...</p>
                    <button class="btn btn-primary btn-block mt-4" style="border-radius: 12px; font-weight: 800;" onclick="closeDescModal()">Awesome</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let curPage = 'services', curLang = 'en';

        function setLang(lang) {
            curLang = lang;
            document.querySelectorAll('.lang-item').forEach(b => b.classList.remove('active'));
            document.getElementById('btn-' + lang).classList.add('active');

            $('[data-en]').each(function() {
                $(this).text($(this).attr(`data-${lang}`));
            });

            $('[data-en-p]').each(function() {
                $(this).attr('placeholder', $(this).attr(`data-${lang}-p`));
            });
        }

        function navTo(page, el) {
            curPage = page;
            $('.content-container').removeClass('active'); $('.nav-link-btn').removeClass('active');
            $(`#page-${page}`).addClass('active'); $(el).addClass('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            triggerSearch();
        }

        function tabFilter(scope, cat, el) {
            $(el).siblings().removeClass('active'); $(el).addClass('active');
            const cls = scope === 'food' ? '.recipe-node' : '.drink-node';
            $(cls).hide();
            if(cat === 'all') $(cls).show(); else $(`${cls}[data-cat="${cat}"]`).show();
            triggerSearch();
        }

        function triggerSearch() {
            const term = $('#masterSearch').val().toLowerCase(), cls = curPage === 'food' ? '.recipe-node' : '.drink-node';
            if(curPage === 'services' || curPage === 'info' || curPage === 'discover') return;
            const activeCat = $(`.content-container.active .cat-pill.active`).data('cat');
            $(cls).each(function() {
                const name = $(this).data('search'), cat = $(this).data('cat');
                $(this).toggle(name.includes(term) && (activeCat === 'all' || cat === activeCat));
            });
        }

        function requestItem(name) { $('#orderTarget').text(`Order: ${name}`); toggleCallModal(true); }
        function toggleCallModal(show) { $('#orderCallModal').modal(show ? 'show' : 'hide'); }

        function showFullDescription(name, desc) {
            if(!desc || desc.length < 5) return; // Don't show if empty
            $('#descTitle').text(name);
            $('#descText').text(desc);
            $('#descModal').modal('show');
        }
        function closeDescModal() { $('#descModal').modal('hide'); }

        // Dismiss splash after page load
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('splashScreen').classList.add('hidden');
            }, 2600);
        });
    </script>
</body>
</html>
