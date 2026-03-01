@extends('layouts.new_landing')

@section('title', 'Blog - PrimeLand Hotel')

@section('content')
<!-- Breadcrumb Section Start -->
<div class="gt-breadcrumb-wrapper bg-cover" style="background-image: url('{{ asset('hotel_gallery/coffee_.jpg') }}');">
    <div class="container">
        <div class="gt-page-heading">
            <div class="gt-breadcrumb-sub-title">
                <h1 class="text-white wow fadeInUp" data-wow-delay=".3s">Latest Blog</h1>
            </div>
            <ul class="gt-breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
                <li>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li>
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li>
                    Blog
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- GT Blog Section Start -->
<section class="gt-blog-section section-padding fix">
    <div class="container">
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="row g-4">
                    @forelse($posts ?? [] as $post)
                    <div class="col-md-6">
                        <div class="gt-blog-box-items">
                            <div class="gt-thumb">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                                @else
                                    <img src="{{ asset('hotel_gallery/room_(1).jpg') }}" alt="{{ $post->title }}">
                                @endif
                                <div class="gt-post-date">
                                    <h3>{{ $post->created_at->format('d') }}</h3>
                                    <span>{{ $post->created_at->format('M') }}</span>
                                </div>
                            </div>
                            <div class="gt-content">
                                <ul class="gt-list">
                                    <li>
                                        <i class="fa-solid fa-user"></i>
                                        By Admin
                                    </li>
                                    <li>
                                        <i class="fa-solid fa-tag"></i>
                                        {{ $post->category ?: 'General' }}
                                    </li>
                                </ul>
                                <h3><a href="#">{{ $post->title }}</a></h3>
                                <p>{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 100) }}</p>
                                <a href="#" class="gt-link-btn">READ MORE</a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <i class="fa-solid fa-newspaper fa-4x text-muted mb-3"></i>
                        <h3>No blog posts available yet.</h3>
                    </div>
                    @endforelse
                </div>
            </div>
            <div class="col-xl-4">
                <div class="gt-sidebar-wrapper">
                    <div class="gt-sidebar-widget">
                        <h3>Search</h3>
                        <div class="gt-sidebar-search">
                            <form action="#">
                                <input type="text" placeholder="Search here...">
                                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="gt-sidebar-widget">
                        <h3>Popular Category</h3>
                        <ul class="gt-category-list">
                            <li><a href="#">Luxury Rooms <span>(0)</span></a></li>
                            <li><a href="#">Travel Tips <span>(0)</span></a></li>
                            <li><a href="#">Events <span>(0)</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
