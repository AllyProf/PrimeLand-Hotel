<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- ========== Meta Tags ========== -->
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="PrimeLand">
        <meta name="description" content="PrimeLand Hotel & Resort">
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
