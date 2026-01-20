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
        @vite(['resources/css/app.css', 'resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="shell">
            <div class="min-h-screen">
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="main">
                    {{ $slot }}
                </main>
            </div>

            <div class="toast-stack" data-toast-stack>
                @if (session('success'))
                    <div class="toast toast--success" role="status" data-timeout="3500">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="toast toast--error" role="alert" data-timeout="3500">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </body>
</html>
