<div>
    <!-- Page Header -->
    <header class="bg-gradient-to-r from-gray-900/50 to-gray-800/50 backdrop-blur-sm border-b border-gray-800/50">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 min-h-[92px] flex items-center">
            <div class="w-full flex items-center justify-between">
                <h2 class="font-bold text-2xl text-white leading-tight">
                    {{ __('Settings') }}
                </h2>
            </div>
        </div>
    </header>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Settings Overview Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Paperless API Configuration Card -->
                <a href="{{ route('settings.paperless-api') }}" class="group block bg-gradient-to-br from-gray-900/50 to-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-800/50 shadow-xl overflow-hidden hover:border-indigo-500/50 transition-all duration-300 hover:shadow-indigo-500/10 cursor-pointer">
                    <div class="p-8">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mr-4 p-2 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-full h-full text-white" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                    <path d="M6.338 23.028c-0.117 -0.56 -0.353 -1.678 -0.382 -1.678 -4.977 -2.975 -4.388 -8.128 -2.739 -11.073 0.353 3.71 6.92 6.273 3.092 10.808 -0.03 0.059 0.177 0.765 0.353 1.413 0.766 -1.296 1.915 -2.856 1.856 -3.004C3.806 8.01 18.53 7.126 21.592 0c1.385 6.89 -0.706 17.55 -12.544 20.26 -0.06 0.03 -2.15 3.71 -2.238 3.74 0 -0.059 -0.884 -0.03 -0.766 -0.324 0.059 -0.177 0.177 -0.412 0.294 -0.648zm-0.147 -2.768c1.502 -1.737 -0.265 -4.712 -1.325 -5.683 1.796 3.092 1.679 4.888 1.325 5.683z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-white group-hover:text-indigo-400 transition-colors">{{ __('Paperless API') }}</h3>
                            </div>
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400">{{ __('Configure the connection to your Paperless NGX instance') }}</p>
                    </div>
                </a>

                <!-- Document Processing Configuration Card -->
                <a href="{{ route('settings.document-processing') }}" class="group block bg-gradient-to-br from-gray-900/50 to-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-800/50 shadow-xl overflow-hidden hover:border-blue-500/50 transition-all duration-300 hover:shadow-blue-500/10 cursor-pointer">
                    <div class="p-8">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mr-4 p-2 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-white group-hover:text-blue-400 transition-colors">{{ __('Document Processing') }}</h3>
                            </div>
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-blue-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400">{{ __('Configure how and when documents are processed') }}</p>
                    </div>
                </a>

                <!-- Ollama AI Configuration Card -->
                <a href="{{ route('settings.ollama') }}" class="group block bg-gradient-to-br from-gray-900/50 to-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-800/50 shadow-xl overflow-hidden hover:border-purple-500/50 transition-all duration-300 hover:shadow-purple-500/10 cursor-pointer">
                    <div class="p-8">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-4 p-2 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-full h-full text-white" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M168.64 23.253c4.608 1.814 8.768 4.8 12.544 8.747 6.293 6.528 11.605 15.872 15.659 26.944 4.074 11.136 6.72 23.467 7.722 35.84a107.824 107.824 0 0143.712-13.568l1.088-.085c18.56-1.494 36.907 1.856 52.907 10.112a103.091 103.091 0 016.336 3.626c1.067-12.138 3.669-24.192 7.68-35.072 4.053-11.093 9.365-20.416 15.637-26.965a35.628 35.628 0 0112.566-8.747c5.482-2.133 11.306-2.517 16.981-.896 8.555 2.432 15.893 7.851 21.675 15.723 5.29 7.19 9.258 16.405 11.968 27.456 4.906 19.925 5.76 46.144 2.453 77.76l1.131.853.554.406c16.15 12.288 27.392 29.802 33.344 50.133 9.28 31.723 4.608 67.307-11.392 87.211l-.384.448.043.064c8.896 16.256 14.293 33.429 15.445 51.2l.043.64c1.365 22.72-4.267 45.589-17.365 68.053l-.15.213.214.512c10.069 24.683 13.226 49.536 9.344 74.368l-.128.832a13.888 13.888 0 01-15.936 11.435 13.83 13.83 0 01-11.31-10.43 13.828 13.828 0 01-.21-5.399c3.562-22.038.213-44.139-10.24-66.624a13.713 13.713 0 01.853-13.163l.085-.128c12.886-19.712 18.219-39.04 17.067-58.027-.981-16.618-6.933-32.938-17.067-48.49a13.737 13.737 0 013.84-18.902l.192-.128c5.184-3.392 9.963-12.053 12.374-23.893a90.218 90.218 0 00-2.027-42.112c-4.373-14.933-12.373-27.392-23.573-35.904-12.694-9.685-29.504-14.357-50.774-13.013a13.93 13.93 0 01-13.482-7.915c-6.699-14.187-16.47-24.341-28.651-30.635a70.145 70.145 0 00-37.803-7.082c-26.56 2.112-49.984 17.088-56.96 35.968a13.91 13.91 0 01-13.013 9.066c-22.763.043-40.384 5.376-53.269 14.998-11.136 8.32-18.731 19.946-22.742 33.877a86.824 86.824 0 00-1.45 40.235c2.389 11.904 7.061 21.76 12.416 27.072l.17.149c4.523 4.416 5.483 11.307 2.326 16.747-7.68 13.269-13.419 33.045-14.358 52.053-1.066 21.717 3.968 40.576 15.339 54.101l.341.406a13.711 13.711 0 012.027 14.72c-12.288 26.368-16.064 48.042-11.989 65.109a13.91 13.91 0 01-27.072 6.357c-5.184-21.717-1.664-46.592 10.09-74.624l.299-.746-.17-.256a92.574 92.574 0 01-12.758-27.926l-.107-.405a122.965 122.965 0 01-3.776-38.08c.939-19.413 5.931-39.296 13.27-55.253l.256-.555-.043-.043c-6.25-8.917-10.88-20.33-13.44-32.96l-.107-.512a114.176 114.176 0 011.984-53.12c5.59-19.52 16.576-36.288 32.768-48.405 1.28-.96 2.624-1.92 3.968-2.816-3.392-31.851-2.538-58.24 2.39-78.293 2.709-11.051 6.698-20.267 11.989-27.456 5.76-7.851 13.099-13.27 21.653-15.723 5.675-1.621 11.52-1.259 17.003.896v.021zm87.808 193.92c19.968 0 38.4 6.678 52.181 18.24 13.44 11.243 21.44 26.347 21.44 41.387 0 18.944-8.661 33.707-24.17 43.136-13.227 8-30.955 11.883-51.264 11.883-21.526 0-39.915-5.526-53.184-15.659-13.163-10.027-20.544-24.107-20.544-39.36 0-15.083 8.49-30.229 22.528-41.515 14.25-11.456 33.066-18.112 53.013-18.112zm0 19.115a65.498 65.498 0 00-40.875 13.867c-9.834 7.893-15.402 17.813-15.402 26.666 0 9.131 4.48 17.686 13.013 24.192 9.707 7.403 23.979 11.691 41.451 11.691 17.045 0 31.424-3.136 41.216-9.088 9.877-5.973 14.933-14.635 14.933-26.816 0-9.024-5.248-18.987-14.571-26.795-10.325-8.64-24.32-13.717-39.765-13.717zm14.123 25.813l.085.086a7.431 7.431 0 01-1.195 10.453l-6.229 4.907v9.514a7.999 7.999 0 01-8.021 7.958 8.004 8.004 0 01-8.022-7.958v-9.813l-5.781-4.651a7.4 7.4 0 01-1.109-10.453 7.53 7.53 0 0110.538-1.088l4.587 3.669 4.693-3.712a7.533 7.533 0 0110.454 1.088zm-107.52-40.938c10.197 0 18.496 8.32 18.496 18.581a18.564 18.564 0 01-18.518 18.581 18.559 18.559 0 01-18.496-18.56 18.565 18.565 0 015.399-13.129 18.609 18.609 0 0113.119-5.473zm185.728 0c10.24 0 18.517 8.32 18.517 18.581a18.559 18.559 0 01-18.517 18.581 18.56 18.56 0 01-18.496-18.56 18.56 18.56 0 0118.496-18.602zM158.72 49.067l-.064.042a14.06 14.06 0 00-6.08 5.078l-.107.128c-2.944 4.032-5.504 9.962-7.424 17.749-3.626 14.763-4.608 34.795-2.645 59.349 9.173-2.73 19.179-4.437 29.952-5.056l.213-.021.406-.725a69.41 69.41 0 013.157-5.099c2.624-16.448.469-36.096-5.397-52.139-2.859-7.765-6.336-13.866-9.664-17.344a13.403 13.403 0 00-2.283-1.92l-.064-.042zm195.712.853l-.043.021a13.396 13.396 0 00-2.282 1.92c-3.328 3.478-6.827 9.6-9.664 17.366-6.187 16.938-8.256 37.888-4.907 54.869l1.237 2.069.171.299h.64a110.599 110.599 0 0131.275 4.523c1.834-23.979.81-43.584-2.731-58.07-1.92-7.786-4.48-13.717-7.445-17.749l-.086-.128a14.054 14.054 0 00-6.08-5.099h-.085v-.021z" fill="currentColor"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-white group-hover:text-purple-400 transition-colors">{{ __('Ollama AI') }}</h3>
                            </div>
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-purple-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400">{{ __('Configure the connection to your Ollama instance') }}</p>
                    </div>
                </a>

                <!-- DSL Settings Card -->
                <a href="{{ route('settings.dsl') }}" class="group block bg-gradient-to-br from-gray-900/50 to-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-800/50 shadow-xl overflow-hidden hover:border-orange-500/50 transition-all duration-300 hover:shadow-orange-500/10 cursor-pointer">
                    <div class="p-8">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-4 p-2 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-white group-hover:text-orange-400 transition-colors">{{ __('DSL Settings') }}</h3>
                            </div>
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-orange-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400">{{ __('Configure rule execution limits and DSL parser settings') }}</p>
                    </div>
                </a>

                <!-- Authentication Settings Card -->
                <a href="{{ route('settings.authentication') }}" class="group block bg-gradient-to-br from-gray-900/50 to-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-800/50 shadow-xl overflow-hidden hover:border-green-500/50 transition-all duration-300 hover:shadow-green-500/10 cursor-pointer">
                    <div class="p-8">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-4 p-2 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-white group-hover:text-green-400 transition-colors">{{ __('Authentication') }}</h3>
                            </div>
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-green-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400">{{ __('Manage users and authentication settings') }}</p>
                    </div>
                </a>

            </div>

            <!-- Setup Wizard Info Box -->
            <div class="mt-6 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl p-6 backdrop-blur-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-start flex-1">
                        <svg class="w-6 h-6 text-indigo-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-indigo-400 font-semibold mb-2">{{ __('Setup Wizard') }}</h4>
                            <p class="text-indigo-400/70 text-sm leading-relaxed">
                                {{ __('Need to reconfigure your initial settings? Run the setup wizard again to update your Paperless connection, document processing mode, and authentication settings.') }}
                            </p>
                        </div>
                    </div>
                    <button
                        wire:click="restartSetupWizard"
                        class="ml-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 whitespace-nowrap"
                    >
                        {{ __('Run Setup Wizard') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

