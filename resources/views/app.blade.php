<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#4f46e5">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="VEHIX">

        <!-- Manifest -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- Icons -->
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('logo_vehix_final.ico') }}">
        <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('logo_vehix_final.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('logo_vehix_final.ico') }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="preload" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" as="style">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap">

        <!--  Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bitcount+Grid+Single:wght@100..900&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
        <!--<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.16"></script>-->
        <!--<script src="https://cdn.jsdelivr.net/npm/typed.js@2.1.0"></script>-->
        <!-- Scripts -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
