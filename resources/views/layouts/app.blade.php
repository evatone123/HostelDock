<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <title>HostelDock - @yield('title', 'Home')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="HostelDock">
    <meta name="description" content="HostelDock helps students find, compare and book verified student hostels and rooms in Ghana.">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="{{ asset('assets/js/theme.js') }}"></script>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Sofia+Sans:wght@300;400;450;500;600;700&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600;700&amp;family=Fira+Code:wght@400;500&amp;display=swap">

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/font-awesome/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/apexcharts/css/apexcharts.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/flatpickr/css/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/choices/css/choices.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/glightbox/css/glightbox.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/tiny-slider/tiny-slider.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/nouislider/nouislider.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/docuforge-design.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/mastercard-design.css') }}">

    @livewireStyles
    @stack('styles')
</head>

<body class="@yield('body_class')">
    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <div class="back-top"></div>

    <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/clear-modal-overlay.js') }}"></script>
    <script src="{{ asset('assets/vendor/apexcharts/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/flatpickr/js/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/choices/js/choices.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/tiny-slider/tiny-slider.js') }}"></script>
    <script src="{{ asset('assets/vendor/purecounterjs/dist/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('assets/vendor/nouislider/nouislider.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/glightbox/js/glightbox.js') }}"></script>
    <script src="{{ asset('assets/vendor/jarallax/jarallax.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sticky-js/sticky.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/functions.js') }}"></script>

    @livewireScripts
    @stack('scripts')
</body>

</html>