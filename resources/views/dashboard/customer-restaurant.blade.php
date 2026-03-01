@extends('dashboard.layouts.app')

@section('content')
<style>
    :root {
        --brand:       #e77a30;
        --brand-dark:  #c45e18;
        --brand-light: #fff5ef;
        --text-dark:   #1a1a2e;
        --text-mid:    #555;
        --text-light:  #999;
        --radius:      20px;
    }

    /* ── PAGE HEADER ── */
    .rest-hero {
        background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
        border-radius: 24px;
        padding: 32px 36px;
        margin-bottom: 28px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        overflow: hidden;
    }
    .rest-hero::before {
        content: '🍽️';
        position: absolute; right: 30px; top: 50%;
        transform: translateY(-50%);
        font-size: 100px; opacity: 0.12;
    }
    .rest-hero h1 { font-size: 1.9rem; font-weight: 800; margin: 0; }
    .rest-hero p  { font-size: 1rem; opacity: 0.85; margin: 6px 0 0; }

    /* ── NOTICE BANNER ── */
    .order-notice {
        background: var(--brand-light);
        border-left: 5px solid var(--brand);
        border-radius: 12px;
        padding: 14px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 0.95rem;
        color: var(--text-dark);
    }
    .order-notice i { color: var(--brand); font-size: 1.4rem; }

    /* ── TABS ── */
    .main-nav-tabs {
        border-bottom: 2px solid #eee;
        margin-bottom: 24px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    .main-nav-tabs .nav-link {
        border: none; background: none;
        color: var(--text-mid);
        font-size: 1.1rem;
        font-weight: 800;
        padding: 12px 40px;
        position: relative;
        transition: color 0.3s;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .main-nav-tabs .nav-link.active { color: var(--brand); }
    .main-nav-tabs .nav-link::after {
        content: ''; position: absolute;
        bottom: -2px; left: 0;
        width: 0; height: 4px;
        background: var(--brand);
        border-radius: 10px;
        transition: width 0.3s;
    }
    .main-nav-tabs .nav-link.active::after { width: 100%; }

    /* ── SEARCH ── */
    .search-box { position: relative; margin-bottom: 26px; }
    .search-box i {
        position: absolute; left: 22px; top: 50%;
        transform: translateY(-50%);
        color: var(--brand); font-size: 1.1rem;
    }
    .search-box input {
        width: 100%;
        padding: 16px 20px 16px 58px;
        border-radius: 16px;
        border: 2px solid #eee;
        font-size: 1rem;
        transition: border 0.3s, box-shadow 0.3s;
    }
    .search-box input:focus {
        border-color: var(--brand); outline: none;
        box-shadow: 0 8px 20px rgba(231,122,48,0.12);
    }

    /* ── CATEGORY PILLS ── */
    .sub-cat-wrapper {
        overflow-x: auto; white-space: nowrap;
        padding: 6px 2px 22px;
        scrollbar-width: none;
    }
    .sub-cat-wrapper::-webkit-scrollbar { display: none; }
    .sub-cat-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 20px;
        background: white;
        border: 2px solid #eee;
        border-radius: 50px;
        color: var(--text-mid);
        font-weight: 700; font-size: 0.88rem;
        cursor: pointer; margin-right: 10px;
        transition: all 0.25s;
    }
    .sub-cat-pill:hover   { border-color: var(--brand); color: var(--brand); }
    .sub-cat-pill.active  {
        background: var(--brand); color: #fff;
        border-color: var(--brand);
        box-shadow: 0 8px 18px rgba(231,122,48,0.3);
    }

    /* ── MENU CARDS ── */
    .menu-card {
        border: none; border-radius: var(--radius);
        background: white;
        transition: transform 0.35s cubic-bezier(0.175,0.885,0.32,1.275), box-shadow 0.3s;
        box-shadow: 0 6px 22px rgba(0,0,0,0.05);
        height: 100%; display: flex; flex-direction: column;
        overflow: hidden;
    }
    .menu-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 38px rgba(0,0,0,0.11);
    }
    .card-image-holder {
        height: 155px; position: relative;
        background: #f8f8f8;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
    }
    .card-image-holder img {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform 0.55s ease;
    }
    .menu-card:hover .card-image-holder img { transform: scale(1.08); }

    .cat-badge {
        position: absolute; top: 12px; left: 12px;
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(6px);
        padding: 4px 12px; border-radius: 20px;
        font-size: 0.72rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.4px;
        color: var(--text-dark);
    }
    .oos-overlay {
        position: absolute; inset: 0;
        background: rgba(255,255,255,0.72);
        display: flex; align-items: center; justify-content: center;
        z-index: 2;
    }

    .card-body { padding: 14px; flex-grow: 1; display: flex; flex-direction: column; }
    .item-title { font-size: 1rem; font-weight: 800; color: var(--text-dark); margin-bottom: 8px; }
    .item-desc  { font-size: 0.82rem; color: var(--text-light); margin-bottom: auto; }

    /* ── PRICE + ORDER AREA ── */
    .price-order-row {
        display: flex; align-items: center;
        justify-content: space-between;
        margin-top: 14px;
        padding: 10px 12px;
        background: var(--brand-light);
        border: 1.5px solid rgba(231,122,48,0.25);
        border-radius: 14px;
    }
    .item-price { font-size: 1.05rem; font-weight: 900; color: var(--brand); }
    .item-price small { font-weight: 600; font-size: 0.75rem; color: var(--text-mid); }

    .btn-order-now {
        background: var(--brand); color: #fff;
        border: none; border-radius: 10px;
        padding: 7px 14px; font-size: 0.82rem;
        font-weight: 700; cursor: pointer;
        transition: background 0.2s, transform 0.2s;
        white-space: nowrap;
    }
    .btn-order-now:hover { background: var(--brand-dark); transform: scale(1.05); }

    /* option rows for drinks */
    .option-rows { display: flex; flex-direction: column; gap: 8px; margin-top: 12px; }
    .option-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 8px 12px;
        background: var(--brand-light);
        border: 1.5px solid rgba(231,122,48,0.25);
        border-radius: 12px;
    }
    .option-label { font-size: 0.78rem; font-weight: 800; color: var(--text-mid); text-transform: uppercase; }
    .option-price { font-size: 0.95rem; font-weight: 900; color: var(--brand-dark); }

    /* ── CONTACT MODAL ── */
    #orderModal .modal-content { border-radius: 28px; border: none; }
    .contact-card {
        display: flex; align-items: center; gap: 16px;
        padding: 18px 20px;
        background: var(--brand-light);
        border: 2px solid rgba(231,122,48,0.25);
        border-radius: 16px;
        margin-bottom: 14px;
        transition: border-color 0.2s;
    }
    .contact-card:hover { border-color: var(--brand); }
    .contact-card .icon-wrap {
        width: 50px; height: 50px;
        background: var(--brand);
        color: #fff; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .contact-card .contact-label { font-size: 0.78rem; color: var(--text-light); font-weight: 600; text-transform: uppercase; }
    .contact-card .contact-value { font-size: 1.05rem; font-weight: 800; color: var(--text-dark); }

    /* responsive */
    @media (max-width: 768px) {
        .rest-hero { padding: 22px 20px; }
        .rest-hero h1 { font-size: 1.3rem; }
        .main-nav-tabs .nav-link { padding: 10px 18px; font-size: 0.95rem; }
        .card-image-holder { height: 120px; }
    }
</style>

{{-- HERO --}}
<div class="rest-hero">
    <div>
        <h1>🍽️ Gourmet Dining</h1>
        <p>Expertly crafted food &amp; drinks — browse our menu below</p>
    </div>
</div>

{{-- ORDER NOTICE --}}
<div class="order-notice">
    <i class="fa fa-info-circle"></i>
    <div>
        <strong>Browsing Mode:</strong> You can explore our full menu here.
        To place an order, simply click <strong>"Order Now"</strong> on any item and our team's contact details will appear for you.
    </div>
</div>

{{-- MAIN TABS --}}
<ul class="nav main-nav-tabs" id="mainTabs">
    <li class="nav-item">
        <a class="nav-link active" id="drinks-tab" data-toggle="tab" href="#drinks-section">🍹 Drinks &amp; Bar</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="food-tab" data-toggle="tab" href="#food-section">🍲 Food &amp; Kitchen</a>
    </li>
</ul>

{{-- SEARCH --}}
<div class="search-box">
    <i class="fa fa-search"></i>
    <input type="text" id="globalSearch" placeholder="Search food or drinks…">
</div>

<div class="tab-content" id="mainContent">

    {{-- ── DRINKS SECTION ── --}}
    <div class="tab-pane fade show active" id="drinks-section">

        {{-- Real category pills --}}
        <div class="sub-cat-wrapper" id="drinkCatWrapper">
            <div class="sub-cat-pill active" onclick="filterDrinks('all', this)">
                <i class="fa fa-th-large"></i> All Drinks
            </div>
            @foreach($realDrinkCategories as $cat)
                <div class="sub-cat-pill" onclick="filterDrinks('{{ $cat }}', this)">
                    @php
                        $catIcons = [
                            'spirits'               => 'fa-fire',
                            'alcoholic_beverage'    => 'fa-beer',
                            'wines'                 => 'fa-glass',
                            'non_alcoholic_beverage'=> 'fa-coffee',
                            'water'                 => 'fa-tint',
                            'juices'                => 'fa-lemon-o',
                            'cocktails'             => 'fa-magic',
                            'energy_drinks'         => 'fa-bolt',
                            'hot_beverages'         => 'fa-thermometer',
                            'drinks'                => 'fa-glass',
                        ];
                        $icon = $catIcons[$cat] ?? 'fa-star';
                    @endphp
                    <i class="fa {{ $icon }}"></i>
                    {{ ucwords(str_replace('_', ' ', $cat)) }}
                </div>
            @endforeach
        </div>

        <div class="row px-2" id="drinksGrid">
            @forelse($drinks as $drink)
                <div class="col-6 col-md-4 col-lg-3 mb-4 px-2 drink-item-card"
                     data-category="{{ $drink->category }}"
                     data-name="{{ strtolower($drink->name) }}">
                    <div class="card menu-card">
                        <div class="card-image-holder">
                            <span class="cat-badge">{{ ucwords(str_replace('_', ' ', $drink->category)) }}</span>
                            @if(!($drink->in_stock ?? true))
                                <div class="oos-overlay">
                                    <span class="badge badge-danger" style="font-size:13px;padding:7px 14px;border-radius:18px;">Out of Stock</span>
                                </div>
                            @endif
                            @if(isset($drink->image) && $drink->image)
                                <img src="{{ asset('storage/' . $drink->image) }}" alt="{{ $drink->name }}"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($drink->name) }}&background=fff5ef&color=e77a30&size=200'"
                                     style="{{ !($drink->in_stock ?? true) ? 'filter:grayscale(1)blur(2px);' : '' }}">
                            @else
                                <div style="opacity:0.2;"><i class="fa fa-glass fa-4x"></i></div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="item-title">{{ $drink->name }}</h5>
                            <div class="option-rows">
                                @foreach($drink->options as $opt)
                                    <div class="option-row">
                                        <div>
                                            <div class="option-label">{{ $opt['type'] }}</div>
                                            <div class="option-price">{{ number_format($opt['price']) }} <small>TZS</small></div>
                                        </div>
                                        @if($drink->in_stock ?? true)
                                            <button class="btn-order-now" onclick="showOrderModal('{{ addslashes($drink->name) }}', '{{ $opt['type'] }}', {{ $opt['price'] }})">
                                                Order Now
                                            </button>
                                        @else
                                            <span class="badge badge-secondary" style="border-radius:8px;padding:6px 10px;">N/A</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="fa fa-glass fa-3x mb-3"></i><br>No drinks available at the moment.
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── FOOD SECTION ── --}}
    <div class="tab-pane fade" id="food-section">

        {{-- Real food category pills --}}
        <div class="sub-cat-wrapper" id="foodCatWrapper">
            <div class="sub-cat-pill active" onclick="filterFood('all', this)">
                <i class="fa fa-th-large"></i> All Food
            </div>
            @foreach($realFoodCategories as $cat)
                <div class="sub-cat-pill" onclick="filterFood('{{ $cat }}', this)">
                    <i class="fa fa-cutlery"></i>
                    {{ ucwords(str_replace('_', ' ', $cat)) }}
                </div>
            @endforeach
        </div>

        <div class="row px-2" id="foodGrid">
            @forelse($foodItems as $food)
                <div class="col-6 col-md-4 col-lg-3 mb-4 px-2 food-item-card"
                     data-name="{{ strtolower($food['name']) }}"
                     data-category="{{ strtolower($food['category'] ?? '') }}">
                    <div class="card menu-card">
                        <div class="card-image-holder">
                            <span class="cat-badge">{{ $food['category'] ?? 'Food' }}</span>
                            @if(isset($food['image']) && $food['image'])
                                <img src="{{ asset('storage/' . $food['image']) }}" alt="{{ $food['name'] }}"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($food['name']) }}&background=fff5ef&color=e77a30&size=200'">
                            @else
                                <div style="opacity:0.2;"><i class="fa fa-cutlery fa-4x"></i></div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="item-title">{{ $food['name'] }}</h5>
                            <p class="item-desc">{{ Str::limit($food['description'] ?? '', 65) }}</p>
                            <div class="price-order-row">
                                <div class="item-price">
                                    {{ number_format($food['price']) }}
                                    <small>TZS</small>
                                </div>
                                <button class="btn-order-now" onclick="showOrderModal('{{ addslashes($food['name']) }}', 'Portion', {{ $food['price'] }})">
                                    Order Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="fa fa-cutlery fa-3x mb-3"></i><br>No food items available at the moment.
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── CONTACT / ORDER MODAL ── --}}
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div>
                    <h4 class="font-weight-bold mb-1" style="color: var(--brand);">📞 Ready to Order?</h4>
                    <p class="text-muted small mb-0" id="modalItemSummary"></p>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <p class="text-muted mb-4" style="font-size:0.9rem;">
                    To place your order, please contact our team using any of the options below:
                </p>

                <div class="contact-card">
                    <div class="icon-wrap"><i class="fa fa-phone"></i></div>
                    <div>
                        <div class="contact-label">Reception Desk</div>
                        <div class="contact-value">+255 677 155 156</div>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="icon-wrap"><i class="fa fa-phone-square"></i></div>
                    <div>
                        <div class="contact-label">Room Extension</div>
                        <div class="contact-value">Ext: 601</div>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="icon-wrap"><i class="fa fa-whatsapp"></i></div>
                    <div>
                        <div class="contact-label">WhatsApp</div>
                        <div class="contact-value">+255 677 155 156</div>
                    </div>
                </div>

                <div class="contact-card" style="border-color:rgba(231,122,48,0.1);">
                    <div class="icon-wrap" style="background:#f0f0f0;color:#555;"><i class="fa fa-clock-o"></i></div>
                    <div>
                        <div class="contact-label">Service Hours</div>
                        <div class="contact-value" style="font-size:0.95rem;">24 Hours · 7 Days a Week</div>
                    </div>
                </div>

                <div class="alert alert-warning mt-3 mb-0" style="border-radius:12px;font-size:0.85rem;">
                    <i class="fa fa-info-circle mr-2"></i>
                    Orders are charged to your room account. Please have your <strong>room number</strong> ready.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentDrinkCat = 'all';
    let currentFoodCat  = 'all';

    // ── Search ──────────────────────────────────────────
    document.getElementById('globalSearch').addEventListener('input', function () {
        const term = this.value.toLowerCase();

        document.querySelectorAll('.drink-item-card').forEach(card => {
            const name = card.dataset.name;
            const cat  = card.dataset.category;
            const matchSearch = name.includes(term);
            const matchCat    = currentDrinkCat === 'all' || cat === currentDrinkCat;
            card.style.display = (matchSearch && matchCat) ? '' : 'none';
        });

        document.querySelectorAll('.food-item-card').forEach(card => {
            const name = card.dataset.name;
            const cat  = card.dataset.category;
            const matchSearch = name.includes(term);
            const matchCat    = currentFoodCat === 'all' || cat === currentFoodCat;
            card.style.display = (matchSearch && matchCat) ? '' : 'none';
        });
    });

    // ── Category Filter — Drinks ─────────────────────────
    function filterDrinks(cat, el) {
        currentDrinkCat = cat;
        document.querySelectorAll('#drinkCatWrapper .sub-cat-pill').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        const term = document.getElementById('globalSearch').value.toLowerCase();
        document.querySelectorAll('.drink-item-card').forEach(card => {
            const matchCat    = cat === 'all' || card.dataset.category === cat;
            const matchSearch = card.dataset.name.includes(term);
            card.style.display = (matchCat && matchSearch) ? '' : 'none';
        });
    }

    // ── Category Filter — Food ───────────────────────────
    function filterFood(cat, el) {
        currentFoodCat = cat;
        document.querySelectorAll('#foodCatWrapper .sub-cat-pill').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        const term = document.getElementById('globalSearch').value.toLowerCase();
        document.querySelectorAll('.food-item-card').forEach(card => {
            const matchCat    = cat === 'all' || card.dataset.category === cat;
            const matchSearch = card.dataset.name.includes(term);
            card.style.display = (matchCat && matchSearch) ? '' : 'none';
        });
    }

    // ── Show Contact Modal ───────────────────────────────
    function showOrderModal(itemName, type, price) {
        const formatted = price.toLocaleString();
        document.getElementById('modalItemSummary').innerText =
            `${itemName} · ${type} — TZS ${formatted}`;
        $('#orderModal').modal('show');
    }
</script>
@endsection
