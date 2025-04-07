<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title', 'Fire-Refill System')</title>
    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('fontawesome-5.5/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('slick/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('magnific-popup/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/tooplate-infinite-loop.css') }}" />
    @stack('styles')
  </head>
  <body>
    <!-- Navigation Section -->
    @include('layouts.navigation')
    
    <!-- Main Content with top padding (adjust if your nav is fixed) -->
    <main style="padding-top: 80px;">
      @yield('content')
    </main>
    
    <!-- Footer Section -->
    <!-- @include('layouts.footer') -->
    
    <!-- JavaScript Files -->
    <script src="{{ asset('js/jquery-1.9.1.min.js') }}"></script>
    <script src="{{ asset('slick/slick.min.js') }}"></script>
    <script src="{{ asset('magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('js/easing.min.js') }}"></script>
    <script src="{{ asset('js/jquery.singlePageNav.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    @stack('scripts')
  </body>
</html>
