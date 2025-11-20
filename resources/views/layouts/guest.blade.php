<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Aisello') }} - {{ __('auth.page_title') }}</title>

        <!-- Fonts / Czcionki -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts / Skrypty -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex">
            <!-- Lewa strona - Formularz / Left side - Form -->
            <div class="w-full lg:w-1/2 flex flex-col min-h-screen bg-white dark:bg-gray-900">
                <!-- Header z logo i przełącznikiem języka / Header with logo and language switcher -->
                <div class="flex items-center justify-between p-6 sm:p-8">
                    <!-- Logo -->
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Aisello</span>
                    </a>

                    <!-- Przełącznik języka / Language switcher -->
                    <x-language-switcher />
                </div>

                <!-- Kontener formularza / Form container -->
                <div class="flex-1 flex items-center justify-center px-6 sm:px-8 pb-8">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Stopka / Footer -->
                <div class="p-6 sm:p-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        © {{ date('Y') }} Aisello. {{ __('auth.all_rights_reserved') }}
                    </p>
                </div>
            </div>

            <!-- Prawa strona - Grafika / Right side - Image -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 p-12 flex-col justify-between relative overflow-hidden">
                <!-- Tło z wzorem / Background pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);"></div>
                </div>

                <!-- Główna grafika / Main graphic -->
                <div class="relative z-10 flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <!-- Ilustracja API / API Illustration -->
                        <div class="mb-8">
                            <svg class="w-48 h-48 mx-auto text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <h2 class="text-3xl font-bold text-white mb-4">
                            {{ __('auth.hero_title') }}
                        </h2>
                        <p class="text-indigo-100 text-lg max-w-md mx-auto">
                            {{ __('auth.hero_subtitle') }}
                        </p>
                    </div>
                </div>

                <!-- Features na dole / Features at bottom -->
                <div class="relative z-10 grid grid-cols-3 gap-4">
                    <!-- Feature 1 -->
                    <div class="text-center">
                        <div class="w-10 h-10 mx-auto bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <p class="text-white text-xs font-medium">{{ __('auth.feature_ai_title') }}</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="text-center">
                        <div class="w-10 h-10 mx-auto bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-white text-xs font-medium">{{ __('auth.feature_integration_title') }}</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="text-center">
                        <div class="w-10 h-10 mx-auto bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <p class="text-white text-xs font-medium">{{ __('auth.feature_security_title') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
