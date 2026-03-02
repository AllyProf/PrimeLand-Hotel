@php
    $stockStatus = $item['current_stock_pics'] <= 0 ? 'critical' : ($item['current_stock_pics'] <= ($item['minimum_stock'] ?? 5) ? 'low' : 'normal');
    $statusColor = $stockStatus === 'critical' ? 'danger' : ($stockStatus === 'low' ? 'warning' : 'success');
    $borderClass = $stockStatus === 'critical' ? 'border-danger' : ($stockStatus === 'low' ? 'border-warning' : 'border-success');
    $headerClass = $stockStatus === 'critical' ? 'bg-danger text-white' : ($stockStatus === 'low' ? 'bg-warning text-dark' : 'bg-success text-white');
    
    $soldPics = $item['total_sold_pics'];
    $currentPics = $item['current_stock_pics'];
    $receivedPics = $item['total_received_pics'];
    // Expiry Logic
    $expiryDate = $item['nearest_expiry'] ?? null;
    $daysToExpiry = null;
    $isExpiringSoon = false;
    
    if ($expiryDate) {
        $daysToExpiry = now()->startOfDay()->diffInDays($expiryDate, false);
        $isExpiringSoon = $daysToExpiry <= 10;
        
        // Override styling if expiring soon and not already critical
        if ($isExpiringSoon && $stockStatus !== 'critical') {
            $statusColor = 'warning';
            $borderClass = 'border-warning shadow';
            $headerClass = 'bg-warning text-dark';
            // Use a specific orange flash if very close
            if ($daysToExpiry <= 3) {
                $headerClass = 'bg-danger text-white animated pulse infinite';
                $borderClass = 'border-danger shadow-lg';
            }
        }
    }
@endphp

<div class="col-xl-3 col-md-4 col-sm-6 mb-3 inventory-card" 
     data-name="{{ strtolower($item['product_name']) }}" 
     data-variant="{{ strtolower($item['variant_name']) }}"
     data-brand="{{ strtolower($item['brand_name'] ?? '') }}"
     data-category="{{ strtolower($item['product_category']) }}">
    
    <div class="card h-100 shadow-sm {{ $borderClass }}" style="border-width: 1px !important; {{ $isExpiringSoon && $daysToExpiry > 0 ? 'background-color: #fffaf5;' : '' }}">
        <!-- Compact Header -->
        <div class="card-header {{ $headerClass }} p-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center overflow-hidden">
                    <div class="custom-control custom-checkbox mr-1">
                        <input type="checkbox" class="custom-control-input stock-checkbox" id="check-{{ $item['variant_id'] }}" value="{{ $item['variant_id'] }}">
                        <label class="custom-control-label" for="check-{{ $item['variant_id'] }}"></label>
                    </div>
                    <div class="text-truncate mr-2">
                        <h6 class="card-title mb-0 font-weight-bold text-truncate" title="{{ $item['product_name'] }}" style="font-size: 11.5px;">
                            {{ $item['product_name'] }}
                        </h6>
                    </div>
                </div>
                <!-- Action Buttons (Smaller) -->
                <div class="d-flex" style="gap: 2px;">
                    <button class="btn btn-xs btn-info view-track-btn p-1" 
                            data-variant-id="{{ $item['variant_id'] }}" 
                            data-item-name="{{ $item['product_name'] }}"
                            style="width: 22px; height: 22px; font-size: 10px;">
                        <i class="fa fa-history"></i>
                    </button>
                    <button class="btn btn-xs btn-light settings-stock-btn p-1 border" 
                            data-variant-id="{{ $item['variant_id'] }}" 
                            data-item-name="{{ $item['product_name'] }}" 
                            data-minimum-stock="{{ $item['minimum_stock'] ?? 0 }}"
                            data-price-pic="{{ $item['selling_price_per_pic'] }}"
                            data-price-glass="{{ $item['selling_price_per_serving'] }}"
                            data-price-pic-usd="{{ (float)($item['selling_price_per_pic_usd'] ?? 0) }}"
                            data-price-glass-usd="{{ (float)($item['selling_price_per_serving_usd'] ?? 0) }}"
                            style="width: 22px; height: 22px; font-size: 10px;">
                        <i class="fa fa-cog"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <!-- Very Compact Image/Info Row -->
            <div class="d-flex" style="height: 70px; background: #fdfdfd; border-bottom: 1px solid #f0f0f0;">
                <div style="width: 70px; height: 70px; flex-shrink: 0; background: #eee;">
                    @if(!empty($item['product_image']))
                        <img src="{{ asset('storage/' . ltrim($item['product_image'], '/')) }}" class="w-100 h-100" style="object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('dashboard_assets/images/room-placeholder.jpg') }}'">
                    @else
                        @php
                            $cat = strtolower($item['product_category'] ?? '');
                            $icon = 'fa-glass';
                            $grad = 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)';
                            if (str_contains($cat, 'beer') || str_contains($cat, 'alcoholic_beverage')) { 
                                $icon='fa-beer'; $grad='linear-gradient(135deg, #fceabb 0%, #f8b500 100%)'; 
                            }
                            elseif (str_contains($cat, 'spirit') || str_contains($cat, 'whiskey') || str_contains($cat, 'liquor')) { 
                                $icon='fa-glass'; $grad='linear-gradient(135deg, #243B55 0%, #141E30 100%)'; 
                            }
                            elseif (str_contains($cat, 'water')) { 
                                $icon='fa-tint'; $grad='linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'; 
                            }
                            elseif (str_contains($cat, 'wine')) { 
                                $icon='fa-vine'; $grad='linear-gradient(135deg, #8E24AA 0%, #D81B60 100%)'; 
                            }
                            elseif (str_contains($cat, 'soft') || str_contains($cat, 'soda') || str_contains($cat, 'juice')) { 
                                $icon='fa-flask'; $grad='linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'; 
                            }
                            elseif (str_contains($cat, 'coffee') || str_contains($cat, 'hot')) { 
                                $icon='fa-coffee'; $grad='linear-gradient(135deg, #3D2B1F 0%, #964B00 100%)'; 
                            }
                            elseif (str_contains($cat, 'cocktail')) { 
                                $icon='fa-magic'; $grad='linear-gradient(135deg, #F093FB 0%, #F5576C 100%)'; 
                            }
                        @endphp
                        <div class="d-flex align-items-center justify-content-center h-100" style="background: {!! $grad !!};">
                            <i class="fa {!! $icon !!} fa-2x text-white opacity-50"></i>
                        </div>
                    @endif
                </div>
                <div class="p-2 flex-grow-1 overflow-hidden">
                    <div style="font-size: 9px; text-transform: uppercase; color: #888; font-weight: 700;" class="text-truncate">{{ $item['category_name'] }}</div>
                    <div class="font-weight-bold text-primary mt-1" style="font-size: 11px;">{{ $item['variant_name'] }}</div>
                    @if($daysToExpiry !== null)
                        <div class="mt-1 {{ $daysToExpiry <= 10 ? 'text-danger font-weight-bold' : 'text-muted' }}" style="font-size: 9px;">
                            Exp: {{ \Carbon\Carbon::parse($expiryDate)->format('d M y') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Condensed Stock Stats -->
            <div class="p-2">
                <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                    <div class="text-center bg-light border-right flex-fill rounded-left p-1">
                        <div class="text-muted small">Total</div>
                        <div class="font-weight-bold">{{ number_format($receivedPics, 1) }}</div>
                    </div>
                    <div class="text-center bg-light border-right flex-fill p-1">
                        <div class="text-muted small">Sold</div>
                        <div class="font-weight-bold text-danger" style="font-size: 11px;">
                            @if($item['servings_per_pic'] > 1)
                                {{ $item['sold_full_bottles'] }}B {{ $item['sold_servings'] }}G
                            @else
                                {{ number_format($item['total_sold_pics'], 1) }}
                            @endif
                        </div>
                    </div>
                    <div class="text-center bg-{{ $statusColor }} text-{{ ($statusColor === 'warning' ? 'dark' : 'white') }} flex-fill rounded-right p-1 shadow-sm">
                        <div class="small opacity-75">In Stock</div>
                        <div class="font-weight-bold" style="font-size: 12px;">
                            @if($item['servings_per_pic'] > 1)
                                {{ $item['full_bottles'] }}B {{ $item['open_servings'] }}G
                            @else
                                {{ number_format($currentPics, 1) }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Glass Breakdown (If ratio > 1) -->
                @if($item['servings_per_pic'] > 1)
                <div class="d-flex justify-content-between p-1 px-2 mb-1 bg-white border border-primary rounded" style="font-size: 10px; border-style: dashed !important;">
                    <span class="text-muted"><i class="fa fa-glass"></i> Tot: <strong class="text-primary">{{ number_format($item['total_servings_available']) }} gls</strong></span>
                    <span class="text-muted"><i class="fa fa-money-bill-wave"></i> {{ number_format($item['profit_per_serving']) }} Profit</span>
                </div>
                @endif

                <!-- Compact Finance Row -->
                <div class="bg-light p-1 rounded border overflow-hidden" style="font-size: 10px;">
                    <div class="d-flex justify-content-between mb-1">
                        <span title="Total revenue collected from sales so far">Collected:</span>
                        <strong class="text-primary">{{ number_format($item['revenue_generated'], 0) }} TSH</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span title="Expected revenue if all remaining stock is sold as servings">Potent. Sales:</span>
                        <strong class="text-{{ $statusColor === 'success' ? 'dark' : $statusColor }}">{{ number_format($item['revenue_serving'], 0) }} TSH</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Pricing (Smallest possible) -->
        <div class="card-footer bg-white p-1 px-2 border-top" style="font-size: 9px;">
            <div class="d-flex justify-content-between">
                <span>Pic: <strong>{{ number_format($item['selling_price_per_pic'], 0) }}</strong></span>
                @if($item['selling_price_per_serving'] > 0)
                <span>Glass: <strong>{{ number_format($item['selling_price_per_serving'], 0) }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>
