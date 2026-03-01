@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-exchange"></i> Transfer Items to Departments</h1>
        <p>Distribute purchased items to requesting departments</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a></li>
        <li class="breadcrumb-item active"><a href="#">Transfers</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="tile-title-w-btn">
                <h3 class="title">Items Ready for Transfer by Department</h3>
                <div class="btn-group">
                    <a class="btn btn-secondary icon-btn" href="{{ route('admin.restaurants.shopping-list.index') }}">
                        <i class="fa fa-shopping-basket"></i> Back to Shopping Lists
                    </a>
                </div>
            </div>
            <div class="tile-body">
                @if(count($itemsByDepartment) > 0)
                    @php
                        $grandTotalItems = 0;
                        $grandTotalCost = 0;
                        foreach ($itemsByDepartment as $items) {
                            foreach ($items as $item) {
                                $grandTotalItems++;
                                $grandTotalCost += $item->purchased_cost ?? 0;
                            }
                        }
                    @endphp
                    <div class="alert alert-success mb-4">
                        <h5><i class="fa fa-info-circle"></i> Transfer Summary</h5>
                        <p class="mb-0">
                            <strong>Total Items:</strong> {{ $grandTotalItems }} | 
                            <strong>Total Cost:</strong> {{ number_format($grandTotalCost, 0) }} TZS | 
                            <strong>Departments:</strong> {{ count($itemsByDepartment) }}
                        </p>
                    </div>
                    
                    @foreach($itemsByDepartment as $department => $items)
                        @php
                            $deptTotalCost = 0;
                            $deptItemCount = 0;
                            foreach ($items as $item) {
                                $deptTotalCost += $item->purchased_cost ?? 0;
                                $deptItemCount++;
                            }
                        @endphp
                        <form action="{{ route('admin.restaurants.shopping-list.bulk-transfer') }}" method="POST" class="department-transfer-form mb-4">
                            @csrf
                            <div class="department-group" style="border: 2px solid #e07632; border-radius: 8px; padding: 10px; background: #f8f9fa; margin-bottom: 25px;">
                                <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 2px solid #e07632; padding-bottom: 10px;">
                                    <h4 style="color: #e07632; font-size: 18px; margin: 0;">
                                        <i class="fa fa-building"></i> {{ $department }} Department
                                    </h4>
                                    <span class="badge badge-primary" style="font-size: 14px; padding: 8px 15px;">
                                        {{ $deptItemCount }} item(s) - {{ number_format($deptTotalCost, 0) }} TZS
                                    </span>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr style="background-color: #e07632; color: #fff;">
                                                <th class="vertical-align-middle">Item Name</th>
                                                <th class="vertical-align-middle">Category</th>
                                                <th class="text-center vertical-align-middle">Qty</th>
                                                <th class="text-center vertical-align-middle">Unit</th>
                                                <th class="text-right vertical-align-middle">Unit Price</th>
                                                <th class="text-right vertical-align-middle">Total Cost</th>
                                                <th class="vertical-align-middle" width="25%">Selling Configuration</th>
                                                <th class="text-center vertical-align-middle">Transfer Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $item)
                                            <tr class="item-row" data-department="{{ $department }}">
                                                <td class="vertical-align-middle">
                                                    @php
                                                        $displayName = $item->product_name;
                                                        $foundVariant = $item->productVariant;

                                                        // Priority 1: Synced Variant – show variant name if it's meaningful
                                                        if ($foundVariant) {
                                                            $vName = trim($foundVariant->variant_name ?? '');
                                                            $pName = trim($foundVariant->product->name ?? '');
                                                            $generics = ['small','large','medium','standard','regular','unit','pic','bottle','box','carton','packet','kg','ltr','liters','ml'];
                                                            if ($vName && !in_array(strtolower($vName), $generics) && strcasecmp($vName, $pName) !== 0) {
                                                                $displayName = $vName;
                                                            } else {
                                                                $displayName = $pName;
                                                            }
                                                        }
                                                        // Priority 2: Purchase Request Name (Manager/Staff Edit)
                                                        elseif ($item->purchaseRequest && $item->purchaseRequest->item_name) {
                                                            $displayName = $item->purchaseRequest->item_name;
                                                        }
                                                        // Add measurement if available
                                                        if ($foundVariant && $foundVariant->measurement) {
                                                            $displayName .= ' (' . $foundVariant->measurement . ')';
                                                        }
                                                    @endphp
                                                    <div class="d-flex flex-column">
                                                        <span style="font-weight: 700; color: #2d3436; font-size: 14px;">
                                                            {{ $displayName }}
                                                        </span>
                                                        @if($foundVariant)
                                                            <small class="text-success" style="font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                                                                <i class="fa fa-check-circle"></i> Verified
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="vertical-align-middle"><span class="badge badge-light border">{{ $item->category_name }}</span></td>
                                                 <td class="text-center vertical-align-middle">{{ number_format($item->purchased_quantity, 0) }}</td>
                                                <td class="text-center vertical-align-middle">{{ in_array(strtolower($item->unit), ['bottles', 'pic', 'pcs']) ? 'Bottle' : $item->unit }}</td>
                                                <td class="text-right vertical-align-middle">{{ number_format($item->unit_price ?? 0, 0) }} <small class="text-muted">TZS</small></td>
                                                <td class="text-right vertical-align-middle"><strong>{{ number_format($item->purchased_cost ?? 0, 0) }} <small class="text-muted">TZS</small></strong></td>
                                                <td class="p-1 vertical-align-middle">
                                                    @if(strtolower($department) == 'bar')
                                                    <div class="card border-0 m-0 shadow-sm {{ $foundVariant ? 'bg-light' : '' }}" style="border-radius: 8px; overflow: hidden;">
                                                        <div class="card-body p-2 {{ $foundVariant ? 'bg-light' : 'bg-white' }}">
                                                            <input type="hidden" class="unit-cost" value="{{ $item->unit_price ?? 0 }}">
                                                            
                                                            @if($foundVariant && ($foundVariant->selling_price_per_pic > 0 || $foundVariant->selling_price_per_serving > 0))
                                                                @php $currentBarStock = $variantStockMap[$foundVariant->id] ?? null; @endphp
                                                                <div class="mb-2 p-1 px-2 d-flex align-items-center shadow-sm" style="font-size: 10px; color: #1b5e20; background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 4px;">
                                                                    <i class="fa fa-check-circle mr-2" style="opacity: 0.8;"></i> 
                                                                    <span><strong>Inventory Linked</strong> &mdash;
                                                                        @php
                                                                            $unit = $foundVariant->selling_unit_name;
                                                                            $servingsCount = (int)($foundVariant->servings_per_pic ?? 1);
                                                                            if ($servingsCount > 1) {
                                                                                if (strtolower($unit) == 'glass') $unit = 'Glasses';
                                                                                elseif (strtolower($unit) == 'tot/shot' || strtolower($unit) == 'shot' || strtolower($unit) == 'tot') $unit = 'Tots/Shots';
                                                                                else $unit = Str::plural($unit);
                                                                            }
                                                                        @endphp
                                                                        @if($servingsCount > 1)
                                                                            Sells as {{ $servingsCount }} {{ $unit }}/Bottle
                                                                        @else
                                                                            Sells per Bottle
                                                                        @endif
                                                                        @if($currentBarStock !== null)
                                                                            &nbsp;<span class="badge badge-{{ $currentBarStock > 0 ? 'success' : 'danger' }}" style="font-size: 9px;">Bar Stock: {{ number_format($currentBarStock, 1) }}</span>
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            @else
                                                                <div class="mb-2 p-1 px-2 border border-warning rounded shadow-sm" style="font-size: 10px; background: #fff3cd; color: #856404;">
                                                                    <i class="fa fa-warning mr-1"></i> <strong>New:</strong> Not configured
                                                                </div>
                                                            @endif

                                                            <div class="mb-2">
                                                                <label class="small text-muted mb-0 font-weight-bold" style="font-size: 10px;">Selling Method</label>
                                                                <select class="form-control form-control-sm selling-method" name="transfers[{{ $item->id }}][selling_method]" style="border-radius: 4px; font-size: 11px; height: 30px; border-color: #d1d8e0;">
                                                                    <option value="pic" {{ ($foundVariant?->can_sell_as_pic && !$foundVariant?->can_sell_as_serving) ? 'selected' : '' }}>Bottle Only</option>
                                                                    <option value="serving" {{ (!$foundVariant?->can_sell_as_pic && $foundVariant?->can_sell_as_serving) ? 'selected' : '' }}>Glass/Tot Only</option>
                                                                    <option value="mixed" {{ ($foundVariant?->can_sell_as_pic && $foundVariant?->can_sell_as_serving) ? 'selected' : ($foundVariant ? '' : 'selected') }}>Bottle & Glass (Mixed)</option>
                                                                </select>
                                                            </div>

                                                            <div class="config-fields-serving" style="display: none;">
                                                                <div class="row no-gutters mb-1">
                                                                    <div class="col-6 pr-1">
                                                                        <label class="small text-muted mb-0">Qty/Bottle</label>
                                                                        <input type="number" class="form-control form-control-sm servings-per-pic" name="transfers[{{ $item->id }}][servings_per_pic]" value="{{ $foundVariant?->servings_per_pic ?? 12 }}" min="1">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label class="small text-muted mb-0">Unit</label>
                                                                        <select class="form-control form-control-sm selling-unit" name="transfers[{{ $item->id }}][selling_unit]">
                                                                            <option value="pic" {{ ($foundVariant?->selling_unit ?? '') == 'pic' ? 'selected' : '' }}>Bottle</option>
                                                                            <option value="glass" {{ ($foundVariant?->selling_unit ?? '') == 'glass' ? 'selected' : 'selected' }}>Glass</option>
                                                                            <option value="tot" {{ ($foundVariant?->selling_unit ?? '') == 'tot' ? 'selected' : '' }}>Tot</option>
                                                                            <option value="shot" {{ ($foundVariant?->selling_unit ?? '') == 'shot' ? 'selected' : '' }}>Shot</option>
                                                                            <option value="cocktail" {{ ($foundVariant?->selling_unit ?? '') == 'cocktail' ? 'selected' : '' }}>Cocktail</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row no-gutters">
                                                                <div class="col-6 pr-1 config-fields-pic">
                                                                    <label class="small text-muted mb-0" style="font-size: 10px;">Price/Bottle</label>
                                                                    <input type="number" step="1" class="form-control form-control-sm selling-price-pic" name="transfers[{{ $item->id }}][selling_price_per_pic]" value="{{ $foundVariant && $foundVariant->selling_price_per_pic > 0 ? round($foundVariant->selling_price_per_pic) : '' }}" placeholder="Price" style="border-radius: 4px;">
                                                                </div>
                                                                <div class="col-6 text-right config-fields-serving" style="display: none;">
                                                                    <label class="small text-muted mb-0" style="font-size: 10px;">Price/Glass</label>
                                                                    <input type="number" step="1" class="form-control form-control-sm selling-price-serving" name="transfers[{{ $item->id }}][selling_price_per_serving]" value="{{ $foundVariant && $foundVariant->selling_price_per_serving > 0 ? round($foundVariant->selling_price_per_serving) : '' }}" placeholder="Price" style="border-radius: 4px;">
                                                                </div>
                                                            </div>

                                                            <div class="profit-preview small p-1 mt-2 border rounded bg-white text-center" style="font-size: 10px; line-height: 1.2; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                                                                <span class="text-muted italic">Awaiting inputs...</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="text-center text-muted small mt-2">N/A</div>
                                                    @endif
                                                </td>
                                                <td class="vertical-align-middle">
                                                    <input type="hidden" name="transfers[{{ $item->id }}][item_id]" value="{{ $item->id }}">
                                                    <input type="hidden" name="transfers[{{ $item->id }}][department]" value="{{ $department }}">
                                                    <input type="number" step="1" class="form-control transfer-quantity text-center mx-auto" 
                                                           name="transfers[{{ $item->id }}][quantity]" 
                                                           value="{{ round($item->purchased_quantity) }}" 
                                                           max="{{ round($item->purchased_quantity) }}" 
                                                           min="1" required style="max-width: 100px;">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr style="background-color: #f8f9fa; font-weight: bold;">
                                                <td colspan="5" style="text-align: right; border-top: 2px solid #e07632;">Department Total:</td>
                                                <td colspan="3" style="color: #e07632; font-size: 16px; border-top: 2px solid #e07632;">
                                                    {{ number_format($deptTotalCost, 0) }} <small>TZS</small>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Action Button Centered Below Table -->
                                <div class="text-center mt-3 pt-3" style="border-top: 1px dashed #ddd;">
                                    <button class="btn btn-primary" type="submit" style="min-width: 250px; padding: 10px 25px; font-weight: bold; border-radius: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <i class="fa fa-exchange"></i> Transfer to {{ $department }} Department
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endforeach
                    
                    <div class="alert alert-info mt-4">
                        <h6><i class="fa fa-info-circle"></i> <strong>Instructions:</strong></h6>
                        <p class="mb-0">
                            Review all items grouped by department. You can adjust transfer quantities if needed. 
                            Click the <strong>"Transfer to [Department]"</strong> button for each department to transfer items to that specific department.
                        </p>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No items ready for transfer. Complete purchases first, then items will appear here grouped by department.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle fields based on selling method
    function toggleMethodFields() {
        $('.item-row').each(function() {
            var row = $(this);
            var method = row.find('.selling-method').val();
            
            if (method === 'pic') {
                row.find('.config-fields-pic').show();
                row.find('.config-fields-serving').hide();
            } else if (method === 'serving') {
                row.find('.config-fields-pic').hide();
                row.find('.config-fields-serving').show();
            } else {
                row.find('.config-fields-pic').show();
                row.find('.config-fields-serving').show();
            }
        });
    }

    // Real-time Profit Calculation
    function calculateProfits() {
        $('.table tbody tr').each(function() {
            var row = $(this);
            var transferQty = parseFloat(row.find('.transfer-quantity').val()) || 0;
            var unitCost = parseFloat(row.find('.unit-cost').val()) || 0;
            var totalTransferredCost = transferQty * unitCost;
            
            var method = row.find('.selling-method').val();
            var servingsPerPic = parseInt(row.find('.servings-per-pic').val()) || 1;
            var totalServings = transferQty * servingsPerPic;
            var sellUnit = row.find('.selling-unit option:selected').text();
            
            var pricePic = parseFloat(row.find('.selling-price-pic').val()) || 0;
            var priceServing = parseFloat(row.find('.selling-price-serving').val()) || 0;
            
            var html = '<div class="mb-2">';
            var hasOutput = false;

            // PIC Calculation
            if ((method === 'pic' || method === 'mixed') && pricePic > 0) {
                var profitPerPic = pricePic - unitCost;
                var totalProfitPic = profitPerPic * transferQty;
                
                html += '<div class="d-flex justify-content-between"><span><strong>Bottle Sales</strong> (Total):</span> <strong>' + Math.round(pricePic * transferQty).toLocaleString() + '</strong></div>';
                html += '<div class="d-flex justify-content-between text-muted" style="font-size: 9px;"><span>Profit per Bottle:</span> <span>' + Math.round(profitPerPic).toLocaleString() + '</span></div>';
                html += '<div class="d-flex justify-content-between text-info" style="font-size: 9px;"><span>Total Profit:</span> <span>' + Math.round(totalProfitPic).toLocaleString() + '</span></div>';
                hasOutput = true;
            }

            // Serving Calculation
            if ((method === 'serving' || method === 'mixed') && priceServing > 0) {
                if (hasOutput) html += '<div class="mt-2 border-top pt-1"></div>';
                
                var costPerServing = unitCost / servingsPerPic;
                var profitPerServing = priceServing - costPerServing;
                var totalProfitServing = profitPerServing * totalServings;
                
                var displayUnit = sellUnit;
                if (totalServings > 1) {
                    if (displayUnit.toLowerCase() === 'glass') displayUnit = 'Glasses';
                    else if (displayUnit.toLowerCase() === 'tot' || displayUnit.toLowerCase() === 'shot') displayUnit += 's';
                    else if (!displayUnit.endsWith('s')) displayUnit += 's';
                }
                
                html += '<div class="d-flex justify-content-between"><span><strong>' + displayUnit + ' Sales</strong> (Total):</span> <strong>' + Math.round(priceServing * totalServings).toLocaleString() + '</strong></div>';
                html += '<div class="d-flex justify-content-between text-muted" style="font-size: 9px;"><span>Pieces/Qty:</span> <span>' + (totalServings % 1 === 0 ? totalServings : totalServings.toFixed(1)) + ' ' + displayUnit + '</span></div>';
                html += '<div class="d-flex justify-content-between text-muted" style="font-size: 9px;"><span>Profit per ' + sellUnit + ':</span> <span>' + Math.round(profitPerServing).toLocaleString() + '</span></div>';
                html += '<div class="d-flex justify-content-between text-info" style="font-size: 9px;"><span>Total Profit:</span> <span>' + Math.round(totalProfitServing).toLocaleString() + '</span></div>';
                hasOutput = true;
            }
            
            html += '</div>';
            
            if (method === 'mixed' && pricePic > 0 && priceServing > 0) {
                var totalProfitPic = (pricePic - unitCost) * transferQty;
                var totalProfitServing = (priceServing - (unitCost / servingsPerPic)) * totalServings;
                var diff = totalProfitServing - totalProfitPic;
                
                var displayUnit = sellUnit;
                if (totalServings > 1) {
                    if (displayUnit.toLowerCase() === 'glass') displayUnit = 'Glasses';
                }

                if (diff > 0) {
                    html += '<div class="text-success font-weight-bold" style="font-size: 9px;">💡 Sell ' + displayUnit + ' to make +' + Math.round(diff).toLocaleString() + ' extra </div>';
                } else if (diff < 0) {
                     html += '<div class="text-primary font-weight-bold" style="font-size: 9px;">💡 Sell Bottle to make +' + Math.round(Math.abs(diff)).toLocaleString() + ' extra</div>';
                }
            }
            
            if (hasOutput) {
                row.find('.profit-preview').html(html);
            } else {
                row.find('.profit-preview').html('<span class="text-muted italic">Awaiting inputs...</span>');
            }
        });
    }

    // Bind events
    $(document).on('change', '.selling-method', function() {
        toggleMethodFields();
        calculateProfits();
    });
    
    $(document).on('input', '.transfer-quantity, .selling-price-pic, .selling-price-serving, .servings-per-pic', calculateProfits);
    $(document).on('change', '.selling-unit', calculateProfits);
    
    // Initial calls
    toggleMethodFields();
    calculateProfits();

    // Validate transfer quantities don't exceed purchased quantities for each department form
    $('.department-transfer-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var isValid = true;
        var departmentName = form.find('h4').text().replace('Department', '').trim();
        
        form.find('.transfer-quantity').each(function() {
            var max = parseFloat($(this).attr('max'));
            var value = parseFloat($(this).val());
            if (value > max) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Transfer quantity cannot exceed purchased quantity for ' + $(this).closest('tr').find('td:first').text(),
                    confirmButtonColor: '#e77a3a'
                });
                isValid = false;
                return false;
            }
            if (value <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Transfer quantity must be greater than 0 for ' + $(this).closest('tr').find('td:first').text(),
                    confirmButtonColor: '#e77a3a'
                });
                isValid = false;
                return false;
            }
        });
        if (!isValid) {
            return false;
        }
        
        // Confirm before submitting with SweetAlert2
        Swal.fire({
            title: 'Confirm Transfer',
            html: 'Are you sure you want to transfer all items to <strong>' + departmentName + ' Department</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e77a3a',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-check"></i> Yes, Transfer',
            cancelButtonText: '<i class="fa fa-times"></i> Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.off('submit'); // Remove the event handler to prevent loop
                form.submit(); // Submit the form
            }
        });
    });
});
</script>
@endsection
