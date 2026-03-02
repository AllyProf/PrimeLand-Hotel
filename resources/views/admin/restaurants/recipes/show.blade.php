@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-cutlery"></i> {{ $recipe->name }}</h1>
        <p>{{ $recipe->category ?? 'General' }} Recipe</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.recipes.index') }}">Recipes</a></li>
        <li class="breadcrumb-item">View</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="tile p-0">
            @if($recipe->image)
                <img src="{{ asset('storage/' . ltrim($recipe->image, '/')) }}" class="img-fluid w-100" style="object-fit: cover; max-height: 300px;" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($recipe->name) }}&background=fff3e0&color=e77a31&size=200'">
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
                    $style = $foodIcons[$recipe->category] ?? ['icon' => 'fa-cutlery', 'grad' => 'linear-gradient(135deg, #009688 0%, #00695c 100%)'];
                @endphp
                <div class="d-flex align-items-center justify-content-center" style="height: 250px; background: {!! $style['grad'] !!};">
                    <i class="fa {!! $style['icon'] !!} fa-5x text-white opacity-50"></i>
                </div>
            @endif
            <div class="p-3">
                <div class="row border-bottom pb-2 mb-2">
                    <div class="col-6"><strong><i class="fa fa-clock-o"></i> Prep:</strong></div>
                    <div class="col-6 text-right">{{ $recipe->prep_time ?? 0 }} mins</div>
                </div>
                <div class="row border-bottom pb-2 mb-2">
                    <div class="col-6"><strong><i class="fa fa-fire"></i> Cook:</strong></div>
                    <div class="col-6 text-right">{{ $recipe->cook_time ?? 0 }} mins</div>
                </div>
                <div class="row border-bottom pb-2 mb-2">
                    <div class="col-6"><strong><i class="fa fa-money"></i> Price:</strong></div>
                    <div class="col-6 text-right font-weight-bold text-success">{{ number_format($recipe->selling_price) }} TZS</div>
                </div>
                <div class="row border-bottom pb-2 mb-2">
                    <div class="col-6"><strong><i class="fa fa-info-circle"></i> Status:</strong></div>
                    <div class="col-6 text-right">
                        @if($recipe->is_available)
                            <span class="badge badge-success">Available</span>
                        @else
                            <span class="badge badge-danger">Unavailable</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="tile">
            <h4 class="tile-title border-bottom pb-2">Ingredients</h4>
            <div class="tile-body">
                <ul class="list-group list-group-flush">
                    @foreach($recipe->ingredients as $ingredient)
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-0 py-1">
                        <span>{{ $ingredient->name }}</span>
                        <span class="badge badge-primary badge-pill">{{ $ingredient->quantity }} {{ $ingredient->unit }}</span>
                    </li>
                    @if($ingredient->notes)
                        <li class="list-group-item px-0 border-0 pt-0 pb-2"><small class="text-info italic"> - {{ $ingredient->notes }}</small></li>
                    @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="tile">
            <h3 class="tile-title">Preparation Steps</h3>
            <div class="tile-body" style="font-size: 1.1rem; line-height: 1.6;">
                @if($recipe->description)
                    <div class="alert alert-light border-left border-info">
                        <strong>Overview:</strong><br>
                        {{ $recipe->description }}
                    </div>
                @endif
                
                <div class="instructions-content mt-4">
                    {!! nl2br(e($recipe->instructions)) !!}
                </div>
            </div>
            <div class="tile-footer border-top mt-4 pt-3">
                <a href="{{ route('admin.recipes.edit', $recipe->id) }}" class="btn btn-warning"><i class="fa fa-edit"></i> Edit Recipe</a>
                <a href="{{ route('admin.recipes.index') }}" class="btn btn-secondary ml-2">Back to Catalog</a>
            </div>
        </div>
    </div>
</div>
@endsection
