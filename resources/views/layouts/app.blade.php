<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="antialiased bg-gray-950 text-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-gray-900/50 backdrop-blur-xl border-b border-gray-800/50 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ url('/') }}" class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <span class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                                    Paperless Rules
                                </span>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-10 sm:flex sm:items-center sm:gap-1">
                            <!-- Main Navigation Items with Text -->
                            <a href="{{ url('/') }}"
                               class="group inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->is('/') ? 'text-white bg-gray-800 shadow-lg shadow-gray-900/50' : 'text-gray-300 hover:text-white hover:bg-gray-800/70' }}">
                                <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>{{ __('Dashboard') }}</span>
                            </a>

                            <a href="{{ url('/rules') }}"
                               class="group inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->is('rules') && !request()->is('rules/*') ? 'text-white bg-gray-800 shadow-lg shadow-gray-900/50' : 'text-gray-300 hover:text-white hover:bg-gray-800/70' }}">
                                <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>{{ __('Rules') }}</span>
                            </a>

                            <a href="{{ url('/rules/manual') }}"
                               class="group inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->is('rules/manual') ? 'text-white bg-gray-800 shadow-lg shadow-gray-900/50' : 'text-gray-300 hover:text-white hover:bg-gray-800/70' }}">
                                <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>{{ __('Manual Mode') }}</span>
                            </a>

                            <!-- Separator -->
                            <div class="w-px h-6 bg-gray-700/50 mx-2"></div>

                            <!-- Icon-Only Navigation Items -->
                            <a href="{{ url('/processing-history') }}"
                               title="{{ __('Processing History') }}"
                               class="group relative inline-flex items-center justify-center w-10 h-10 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->is('processing-history') ? 'text-white bg-gray-800 shadow-lg shadow-gray-900/50' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
                                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <!-- Tooltip -->
                                <span class="absolute -bottom-10 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                    {{ __('Processing History') }}
                                </span>
                            </a>

                            <a href="{{ url('/docs') }}"
                               title="{{ __('Documentation') }}"
                               class="group relative inline-flex items-center justify-center w-10 h-10 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->is('docs') ? 'text-white bg-gray-800 shadow-lg shadow-gray-900/50' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
                                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <!-- Tooltip -->
                                <span class="absolute -bottom-10 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                    {{ __('Documentation') }}
                                </span>
                            </a>

                            @if(!app(\App\Services\SettingsService::class)->isLoginEnabled() || (auth()->check() && auth()->user()->isAdministrator()))
                                <a href="{{ url('/settings') }}"
                                   title="{{ __('Settings') }}"
                                   class="group relative inline-flex items-center justify-center w-10 h-10 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->is('settings') ? 'text-white bg-gray-800 shadow-lg shadow-gray-900/50' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
                                    <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <!-- Tooltip -->
                                    <span class="absolute -bottom-10 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                        {{ __('Settings') }}
                                    </span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Right Side: System Status & Language Chooser & Logout -->
                    <div class="flex items-center space-x-4">
                        <livewire:system-status />
                        <livewire:language-chooser />

                        @if(app(\App\Services\SettingsService::class)->isLoginEnabled() && auth()->check())
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    title="{{ __('Logout') }}"
                                    class="group relative inline-flex items-center justify-center w-10 h-10 rounded-lg text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-gray-800/70"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <!-- Tooltip -->
                                    <span class="absolute -bottom-10 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                        {{ __('Logout') }}
                                    </span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @hasSection('header')
            <header class="bg-gradient-to-r from-gray-900/50 to-gray-800/50 backdrop-blur-sm border-b border-gray-800/50">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 min-h-[92px] flex items-center">
                    <div class="w-full">
                        @yield('header')
                    </div>
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="pb-12">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- CodeMirror Initialization (loaded separately to avoid Alpine.js parsing) -->
    <script src="{{ asset('js/codemirror-init.js') }}"></script>

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>

