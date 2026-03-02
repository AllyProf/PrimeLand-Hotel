@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-edit"></i> Edit Menu Item</h1>
        <p>Update menu item details</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.recipes.index') }}">Menu</a></li>
        <li class="breadcrumb-item active">Edit {{ $recipe->name }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="tile">
            <h3 class="tile-title"><i class="fa fa-utensils"></i> Edit: {{ $recipe->name }}</h3>
            <div class="tile-body">
                <form action="{{ route('admin.recipes.update', $recipe) }}" method="POST" enctype="multipart/form-data" id="menuForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Item Name -->
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Item Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $recipe->name) }}" 
                                       placeholder="e.g., Grilled Chicken, Caesar Salad" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category">Category <span class="text-danger">*</span></label>
                                <select class="form-control @error('category') is-invalid @enderror" 
                                        id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category', $recipe->category) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Brief description of the dish, ingredients, or special features...">{{ old('description', $recipe->description) }}</textarea>
                        <small class="form-text text-muted">This will be shown to customers when browsing the menu.</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Selling Price -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="selling_price">Price (TSH) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">TSH</span>
                                    </div>
                                    <input type="number" class="form-control @error('selling_price') is-invalid @enderror" 
                                           id="selling_price" name="selling_price" value="{{ old('selling_price', $recipe->selling_price) }}" 
                                           min="0" step="100" placeholder="15000" required>
                                </div>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Prep Time -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prep_time">Preparation Time (minutes)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('prep_time') is-invalid @enderror" 
                                           id="prep_time" name="prep_time" value="{{ old('prep_time', $recipe->prep_time) }}" 
                                           min="0" placeholder="30">
                                    <div class="input-group-append">
                                        <span class="input-group-text">min</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Estimated time to prepare this dish.</small>
                                @error('prep_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Image Section -->
                    <div class="form-group">
                        <label for="image">Food Image</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <label class="custom-file-label" for="image">Change image...</label>
                        </div>
                        <small class="form-text text-muted">Upload a quality image (Max 2MB). If none, a category default is used.</small>
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        
                        <!-- Image Preview -->
                        <div id="imagePreviewWrapper" class="mt-3">
                            @if(!$recipe->image)
                                <div id="defaultPreview" class="rounded d-flex align-items-center justify-content-center shadow-sm border" style="height: 150px; width: 250px;">
                                    <i id="defaultPreviewIcon" class="fa fa-utensils fa-4x text-white opacity-50"></i>
                                </div>
                            @endif
                            <div id="filePreview" class="{{ $recipe->image ? '' : 'd-none' }}">
                                <img id="previewImg" src="{{ $recipe->image ? asset('storage/' . ltrim($recipe->image, '/')) : '' }}" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                            @if($recipe->image)
                                <small class="text-muted d-block mt-1">Currently displaying uploaded image</small>
                            @endif
                        </div>
                    </div>

                    <!-- Availability -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_available" 
                                   name="is_available" {{ old('is_available', $recipe->is_available) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_available">
                                <strong>Available for Customers</strong>
                                <small class="d-block text-muted">Uncheck if this item is temporarily unavailable</small>
                            </label>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.recipes.index') }}" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update Menu Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const foodStyles = {
        'appetizers': {icon: 'fa-fire', grad: 'linear-gradient(135deg, #FF9966 0%, #FF5E62 100%)'},
        'main_course': {icon: 'fa-cutlery', grad: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)'},
        'desserts': {icon: 'fa-birthday-cake', grad: 'linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%)'},
        'beverages': {icon: 'fa-coffee', grad: 'linear-gradient(135deg, #3D2B1F 0%, #964B00 100%)'},
        'breakfast': {icon: 'fa-sun-o', grad: 'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)'},
        'lunch': {icon: 'fa-shopping-bag', grad: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'},
        'dinner': {icon: 'fa-moon-o', grad: 'linear-gradient(135deg, #243B55 0%, #141E30 100%)'},
        'snacks': {icon: 'fa-lemon-o', grad: 'linear-gradient(135deg, #f2994a 0%, #f2c94c 100%)'},
        'salads': {icon: 'fa-leaf', grad: 'linear-gradient(135deg, #134E5E 0%, #71B280 100%)'},
        'soups': {icon: 'fa-spoon', grad: 'linear-gradient(135deg, #EB3349 0%, #F45C43 100%)'},
    };

    function updateDefaultPreview() {
        const cat = $('#category').val();
        const style = foodStyles[cat] || {icon: 'fa-utensils', grad: 'linear-gradient(135deg, #009688 0%, #00695c 100%)'};
        
        const $def = $('#defaultPreview');
        if ($def.length) {
            $def.css('background', style.grad);
            $('#defaultPreviewIcon').attr('class', 'fa ' + style.icon + ' fa-4x text-white opacity-50');
        }
    }

    // Category change listener
    $('#category').on('change', updateDefaultPreview);
    updateDefaultPreview(); // init

    // Update file input label
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
        
        // Show image preview
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#filePreview').removeClass('d-none');
                $('#defaultPreview').addClass('d-none');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Form validation
    $('#menuForm').on('submit', function(e) {
        var price = $('#selling_price').val();
        if (price <= 0) {
            e.preventDefault();
            alert('Please enter a valid price greater than 0.');
            $('#selling_price').focus();
            return false;
        }
    });
});
</script>
@endsection
