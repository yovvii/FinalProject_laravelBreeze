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
    <body class="font-sans antialiased focus:outline-none">

        @include('layouts.notification')

        @include('super_admin.layouts.partials.navbar_super_admin')

        <div class="lg:flex min-h-screen bg-gray-100 dark:bg-gray-900"> 

            @include('super_admin.layouts.partials.sidebar_super_admin')
            
            <!-- Page Content -->
            <main class="flex-1 oveflow-y-auto lg:ml-64">
                @yield('content')
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
