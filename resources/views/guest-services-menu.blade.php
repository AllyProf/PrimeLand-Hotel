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

        /* ── Pulse Animation (Steam effect) ── */
        .pulse-hint {
            position: absolute; top: 15px; right: 15px;
            background: var(--primary); color: #fff;
            font-size: 9px; font-weight: 800; padding: 4px 8px;
            border-radius: 50px; text-transform: uppercase;
            animation: pulse-steam 2s infinite;
            pointer-events: none;
            z-index: 5;
        }
        @keyframes pulse-steam {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(231, 122, 58, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(231, 122, 58, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(231, 122, 58, 0); }
        }
        .btn-details {
            padding: 4px 10px; background: rgba(255,255,255,0.05); color: var(--text-dim);
            border: 1px solid var(--glass-border); border-radius: 7px; font-size: 10px; font-weight: 800;
            cursor: pointer; transition: 0.3s; margin-right: 5px;
        }
        .btn-details:hover { background: rgba(255,255,255,0.1); color: #fff; }

    </style>
</head>
<body>

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
            <div class="nav-link-btn active" onclick="navTo('food', this)" data-en="🍳 Food" data-sw="🍳 Chakula">🍳 Food</div>
            <div class="nav-link-btn" onclick="navTo('drinks', this)" data-en="🍷 Drinks" data-sw="🍷 Kinywaji">🍷 Drinks</div>
            <div class="nav-link-btn" onclick="navTo('discover', this)" data-en="⛰️ Discover" data-sw="⛰️ Tembelea">⛰️ Discover</div>
            <div class="nav-link-btn" onclick="navTo('services', this)" data-en="🛎️ Services" data-sw="🛎️ Huduma">🛎️ Services</div>
            <div class="nav-link-btn" onclick="navTo('info', this)" data-en="ℹ️ Info" data-sw="ℹ️ Taarifa">ℹ️ Info</div>
        </nav>

        <div class="search-panel">
            <div class="search-bar">
                <i class="fa fa-search text-muted"></i>
                <input type="text" id="masterSearch" placeholder="Find food or drinks..." data-en-p="Find food or drinks..." data-sw-p="Tafuta chakula au kinywaji..." oninput="triggerSearch()">
            </div>
        </div>
    </div>

    <!-- FOOD -->
    <div id="page-food" class="content-container active">
        <div class="category-tabs" id="foodCatTabs">
            <div class="cat-pill active" data-cat="all" onclick="tabFilter('food','all',this)" data-en="All" data-sw="Vyote">All</div>
            @foreach($foodCategories as $cat)
                <div class="cat-pill" data-cat="{{ $cat }}" onclick="tabFilter('food','{{ $cat }}',this)">{{ ucwords(str_replace('_',' ',$cat)) }}</div>
            @endforeach
        </div>

        <div class="list-view" id="grid-food">
            @forelse($recipes as $recipe)
                <div class="rich-card recipe-node clickable-card" 
                     data-cat="{{ $recipe->category }}" 
                     data-search="{{ strtolower($recipe->name) }}"
                     onclick="showFullDescription('{{ addslashes($recipe->name) }}', '{{ addslashes($recipe->description ?? '') }}')">
                    <div class="rich-thumb">
                        <span class="cat-chip">{{ $recipe->category ?? 'Food' }}</span>
                        @if($recipe->image)
                            <img src="{{ asset('storage/' . $recipe->image) }}" alt="{{ $recipe->name }}"
                                 onerror="this.outerHTML='<div class=&quot;no-img&quot;>🥘</div>'">
                        @else
                            @php
                                $foodIcons = [
                                    'appetizers' => ['icon' => 'fa-fire', 'grad' => 'linear-gradient(135deg, #FF9966 0%, #FF5E62 100%)'],
                                    'main_course' => ['icon' => 'fa-cutlery', 'grad' => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)'],
                                    'desserts' => ['icon' => 'fa-birthday-cake', 'grad' => 'linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%)'],
                                    'beverages' => ['icon' => 'fa-coffee', 'grad' => 'linear-gradient(135deg, #3D2B1F 0%, #964B00 100%)'],
                                    'breakfast' => ['icon' => 'fa-sun-o', 'grad' => 'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)'],
                                    'lunch' => ['icon' => 'fa-shopping-bag', 'grad' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'],
                                    'dinner' => ['icon' => 'fa-moon-o', 'grad' => 'linear-gradient(135deg, #243B55 0%, #141E30 100%)'],
                                    'snacks' => ['icon' => 'fa-lemon-o', 'grad' => 'linear-gradient(135deg, #f2994a 0%, #f2c94c 100%)'],
                                    'salads' => ['icon' => 'fa-leaf', 'grad' => 'linear-gradient(135deg, #134E5E 0%, #71B280 100%)'],
                                    'soups' => ['icon' => 'fa-spoon', 'grad' => 'linear-gradient(135deg, #EB3349 0%, #F45C43 100%)'],
                                ];
                                $catKey = strtolower(str_replace(' ', '_', $recipe->category ?? ''));
                                $style = $foodIcons[$catKey] ?? ['icon' => 'fa-cutlery', 'grad' => 'linear-gradient(135deg, #e77a31 0%, #eaafc8 100%)'];
                            @endphp
                            <div class="d-flex align-items-center justify-content-center w-100 h-100" style="background: {!! $style['grad'] !!};">
                                <i class="fa {!! $style['icon'] !!} fa-3x text-white opacity-40"></i>
                            </div>
                        @endif
                        <div class="price-badge">
                            <span class="price-tsh">Tsh {{ number_format($recipe->selling_price) }}</span>
                            @if(!empty($recipe->selling_price_usd) && $recipe->selling_price_usd > 0)
                                <span class="price-usd">(${{ rtrim(rtrim(number_format($recipe->selling_price_usd, 2), '0'), '.') }})</span>
                            @endif
                        </div>
                    </div>
                    <div class="rich-body">
                        <div class="rich-title">{{ $recipe->name }}</div>
                        @if($recipe->description)
                            <div class="pulse-hint">Details</div>
                            <div class="rich-sub">{{ Str::limit($recipe->description, 55) }}</div>
                        @endif
                        <div class="option-strip">
                            <div class="opt-row">
                                <div style="flex: 1;">
                                    <div class="opt-label">Portion</div>
                                    <div class="opt-price">
                                        {{ number_format($recipe->selling_price) }} TZS
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    @if($recipe->description)
                                        <button class="btn-details" onclick="event.stopPropagation(); showFullDescription('{{ addslashes($recipe->name) }}', '{{ addslashes($recipe->description ?? '') }}')">Details</button>
                                    @endif
                                    <button class="btn-order-mini" onclick="event.stopPropagation(); requestItem('{{ addslashes($recipe->name) }}')">Order</button>
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
    <div id="page-services" class="content-container">
        <div class="info-card">
            <div class="info-header" data-en="Premium Facilities" data-sw="Huduma Bora">Premium Facilities</div>
            
            <div class="item-grid">
                @foreach($services->filter(function($s) { return !in_array(strtolower($s->service_name), ['ceremony', 'ceremory']); }) as $svc)
                    @php
                        $icon = '⭐';
                        $lowName = strtolower($svc->service_name);
                        if(str_contains($lowName, 'swim')) $icon = '🏊';
                        elseif(str_contains($lowName, 'laundry')) $icon = '👕';
                        elseif(str_contains($lowName, 'wifi')) $icon = '📶';
                        elseif(str_contains($lowName, 'parking')) $icon = '🚗';
                        elseif(str_contains($lowName, 'gym')) $icon = '🏋️';
                        elseif(str_contains($lowName, 'spa')) $icon = '🧖';
                        elseif(str_contains($lowName, 'room')) $icon = '🛏️';

                        // Use standard pricing
                        $displayPrice = $svc->price_tanzanian;
                    @endphp
                    <div class="service-card" onclick="requestItem('{{ addslashes($svc->service_name) }}')">
                        <div class="service-icon-box">{{ $icon }}</div>
                        <div class="service-name">{{ $svc->service_name }}</div>
                        <div class="service-price">
                             @if($displayPrice > 0)
                                {!! formatPrice($displayPrice, $usdRate) !!}
                             @else 
                                <span data-en="Complimentary" data-sw="Bure">Complimentary</span>
                             @endif
                        </div>
                        @if($lowName === 'swimming')
                            <div style="font-size: 10px; color: #28a745; font-weight: bold; margin-top: 5px; background: rgba(40, 167, 69, 0.1); padding: 3px 8px; border-radius: 10px;" data-en="Free for Internal Guests" data-sw="Bure kwa wageni wa hotelini">Free for Internal Guests</div>
                        @endif
                    </div>
                @endforeach

                <!-- Static Laundry Service -->
                <div class="service-card" onclick="requestItem('Laundry Services')">
                    <div class="service-icon-box">👕</div>
                    <div class="service-name">Laundry Services</div>
                    <div class="service-price">
                        <span data-en="Provided upon request" data-sw="Inatolewa ukiomba">Provided upon request</span>
                    </div>
                </div>
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
                    <a href="tel:+255677155156" class="call-item"><div class="dept-info"><b data-en="Reception Mobile" data-sw="Simu ya Mapokezi">Reception Mobile</b><span data-en="Direct Line" data-sw="Namba ya Moja kwa Moja">Direct Line</span></div><div class="dept-phone">155-156</div></a>
                    <a href="tel:+255677155157" class="call-item"><div class="dept-info"><b data-en="Manager" data-sw="Meneja">Manager</b><span data-en="Supervisor" data-sw="Msimamizi">Supervisor</span></div><div class="dept-phone">155-157</div></a>
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
        let curPage = 'food', curLang = 'en';

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
    </script>
</body>
</html>
