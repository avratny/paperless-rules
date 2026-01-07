@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-bold text-2xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1 text-xs font-medium bg-green-500/10 text-green-400 rounded-full border border-green-500/20">
                {{ __('Online') }}
            </span>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Welcome Card -->
            <div class="bg-linear-to-br from-indigo-500/10 to-purple-500/10 border border-indigo-500/20 overflow-hidden shadow-xl shadow-indigo-500/5 rounded-2xl backdrop-blur-sm">
                <div class="p-8">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-2">{{__('Welcome to Paperless Rules')}} 👋</h3>
                            <p class="text-gray-400 text-lg">
                                {{__('The little tool to automatically process your documents.')}}
                            </p>
                        </div>
                        <div class="hidden sm:block">
                            <div class="w-16 h-16 bg-linear-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/50">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    use App\Models\RuleExecutionLog;
                    use App\Models\Rule;
                    use App\Models\LockedDocument;
                    use Carbon\Carbon;

                    // Dokumente in der letzten Stunde verarbeitet
                    $docsLastHour = RuleExecutionLog::where('executed_at', '>', Carbon::now()->subHour())
                        ->distinct('document_id')
                        ->count('document_id');

                    // Anzahl aktive und inaktive Regeln
                    $activeRules = Rule::where('enabled', true)->count();
                    $inactiveRules = Rule::where('enabled', false)->count();

                    // Gesperrte Dokumente
                    $lockedDocs = LockedDocument::where('locked_at', '>', Carbon::now()->subSeconds(60))->count();
                @endphp

                <div class="bg-gray-900/50 border border-gray-800/50 overflow-hidden shadow-xl rounded-2xl backdrop-blur-sm hover:border-gray-700/50 transition duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="font-semibold text-white text-lg mb-1">{{ __('Documents processed') }}</h4>
                        <p class="text-3xl font-bold text-white mb-2">{{ number_format($docsLastHour) }}</p>
                        <p class="text-gray-400 text-sm">{{ __('Last hour') }}</p>
                    </div>
                </div>

                <div class="bg-gray-900/50 border border-gray-800/50 overflow-hidden shadow-xl rounded-2xl backdrop-blur-sm hover:border-gray-700/50 transition duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="font-semibold text-white text-lg mb-1">{{ __('Rules') }}</h4>
                        <p class="text-3xl font-bold text-white mb-2">
                            <span class="text-green-400">{{ number_format($activeRules) }}</span>
                            <span class="text-gray-500 text-xl mx-1">/</span>
                            <span class="text-gray-500 text-xl">{{ number_format($inactiveRules) }}</span>
                        </p>
                        <p class="text-gray-400 text-sm">{{ __('Active / Inactive') }}</p>
                    </div>
                </div>

                <div class="bg-gray-900/50 border border-gray-800/50 overflow-hidden shadow-xl rounded-2xl backdrop-blur-sm hover:border-gray-700/50 transition duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-orange-500/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="font-semibold text-white text-lg mb-1">{{ __('Locked documents') }}</h4>
                        <p class="text-3xl font-bold text-white mb-2">{{ number_format($lockedDocs) }}</p>
                        <p class="text-gray-400 text-sm">{{ __('Currently locked') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <a href="{{ url('/docs') }}" class="bg-gray-900/50 border border-gray-800/50 overflow-hidden shadow-xl rounded-2xl backdrop-blur-sm hover:border-indigo-500/50 transition duration-300 group">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-indigo-500/10 rounded-lg flex items-center justify-center mr-3 group-hover:bg-indigo-500/20 transition duration-300">
                                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-white text-lg">{{ __('Documentation') }}</h4>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-400 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </div>
                        <p class="text-gray-400">
                            {{ __('Learn how to create powerful rules and automate your document workflow.') }}
                        </p>
                    </div>
                </a>

                <a href="https://github.com/avratny/paperless-rules" target="_blank" class="bg-gray-900/50 border border-gray-800/50 overflow-hidden shadow-xl rounded-2xl backdrop-blur-sm hover:border-purple-500/50 transition duration-300 group">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-500/20 transition duration-300">
                                    <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-white text-lg">{{ __('GitHub Repository') }}</h4>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </div>
                        <p class="text-gray-400">
                            {{ __('Contribute to the project, report issues, or check out the source code.') }}
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

