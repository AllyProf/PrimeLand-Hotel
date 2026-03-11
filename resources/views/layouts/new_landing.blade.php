<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-F3PHSXZPK8"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-F3PHSXZPK8');
        </script>
        <!-- ========== Meta Tags ========== -->
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Primeland Hotel">
        <meta name="description" content="Discover the best hotel in Moshi for Kilimanjaro climbers and tourists. Primeland Hotel offers boutique accommodation near Mount Kilimanjaro trekking routes. Features include a swimming pool, restaurant, and airport shuttle from JRO. Book your safari adventure stay online!">
        <meta name="keywords" content="hotels in Moshi Tanzania for tourists, hotels near Kilimanjaro for safari, best hotel in Moshi for Kilimanjaro climbers, boutique hotels in Tanzania, luxury hotel near Kilimanjaro, Mount Kilimanjaro trekking routes hotel, hotels close to Kilimanjaro airport, book hotel online in Moshi, Moshi hotels with swimming pool, cozy boutique hotel in Moshi near Kilimanjaro, affordable hotel rooms for Kilimanjaro climbers">
        <!-- ======== Page title ============ -->
        <title>@yield('title', 'PrimeLand Hotel')</title>
        
        @include('landing_page_views.partials.new-styles')
        
        @yield('styles')
    </head>
    <body>

        @include('landing_page_views.partials.new-header')

        @yield('content')

        @include('landing_page_views.partials.new-footer')
        
        @include('landing_page_views.partials.new-scripts')
        
        @yield('scripts')
    </body>
</html>
