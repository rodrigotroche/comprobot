<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light text-dark">
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center pt-5">
        <div>
            <a href="/">
                <!-- Aquí puedes poner el logo de tu aplicación en Bootstrap, por ejemplo con una imagen o SVG -->
                <img src="{{ asset('path-to-your-logo') }}" class="w-20 h-20" alt="Application Logo">
            </a>
        </div>

        <div class="w-100 sm:max-w-md mt-4 p-4">
            {{ $slot }}
        </div>
    </div>
</body>

</html>