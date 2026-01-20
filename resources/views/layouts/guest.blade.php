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
            <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                <div>
                    <a href="/" class="flex items-center gap-3">
                        <x-application-logo class="w-12 h-12 fill-current text-gray-400" />
                        <span class="text-sm font-medium text-gray-200">Support Desk Lite</span>
                    </a>
                </div>

                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
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
