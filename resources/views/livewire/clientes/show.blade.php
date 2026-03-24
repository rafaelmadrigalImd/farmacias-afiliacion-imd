<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Mobile-First -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1 min-w-0">
                    <a href="/clientes" wire:navigate class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">
                            @if($cliente)
                                {{ $cliente['nombre'] ?? 'Cliente' }}
                            @else
                                Cliente
                            @endif
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Información detallada</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido -->
    <div class="px-4 sm:px-6 lg:px-8 py-6 max-w-3xl mx-auto">
        @if($errorMessage)
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-red-800 dark:text-red-300">{{ $errorMessage }}</p>
                </div>
            </div>
        @endif

        @if($loading)
            <!-- Loading State -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 animate-pulse">
                <div class="space-y-4">
                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>
            </div>
        @elseif($cliente)
            <!-- Información Principal -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <!-- Header con icono -->
                <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-8">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h2 class="text-2xl font-bold text-white">{{ $cliente['nombre'] ?? 'Sin nombre' }}</h2>
                            @if(isset($cliente['estado']))
                                <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                                    {{ ucfirst($cliente['estado']) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Datos -->
                <div class="p-6 space-y-6">
                    <!-- Farmacia que dio de alta -->
                    @if(!empty($cliente['farmacia_alta']))
                        <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4 border border-primary-200 dark:border-primary-800">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-xs font-medium text-primary-800 dark:text-primary-300 uppercase tracking-wider">Farmacia que dio de alta</p>
                                    <p class="mt-1 text-base font-semibold text-primary-900 dark:text-primary-100">
                                        {{ $cliente['farmacia_alta'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- ID del Paciente -->
                    @if(!empty($cliente['id']))
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Identificación</h3>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">ID Paciente:</span>
                                    <span class="text-sm text-gray-900 dark:text-white font-mono">{{ $cliente['id'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700"></div>
                    @endif

                    <!-- Información Básica -->
                    @if(isset($cliente['cif']) || isset($cliente['titular']))
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Información Básica</h3>
                            <div class="space-y-3">
                                @if(isset($cliente['cif']))
                                    <div class="flex items-start">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">CIF:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $cliente['cif'] }}</span>
                                    </div>
                                @endif
                                @if(isset($cliente['titular']))
                                    <div class="flex items-start">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Titular:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $cliente['titular'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700"></div>
                    @endif

                    <!-- Contacto -->
                    @if(isset($cliente['email']) || isset($cliente['telefono']) || isset($cliente['centro']))
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Contacto</h3>
                            <div class="space-y-3">
                                @if(isset($cliente['email']))
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <a href="mailto:{{ $cliente['email'] }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                                            {{ $cliente['email'] }}
                                        </a>
                                    </div>
                                @endif
                                @if(isset($cliente['telefono']))
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <a href="tel:{{ $cliente['telefono'] }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                                            {{ $cliente['telefono'] }}
                                        </a>
                                    </div>
                                @endif
                                @if(!empty($cliente['centro_nombre']))
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $cliente['centro_nombre'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700"></div>
                    @endif

                    <!-- Ubicación -->
                    @if(isset($cliente['direccion']))
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Ubicación</h3>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <p>{{ $cliente['direccion'] }}</p>
                                    <p class="mt-1">{{ $cliente['codigo_postal'] ?? '' }} {{ $cliente['ciudad'] ?? '' }}</p>
                                    @if(isset($cliente['provincia']))
                                        <p class="text-gray-600 dark:text-gray-400">{{ $cliente['provincia'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Estado y Contratos -->
                    @if(!empty($cliente['estado']) || !empty($cliente['contratos']))
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Estado</h3>
                            <div class="flex flex-wrap gap-2">
                                @if(!empty($cliente['estado']))
                                    @php
                                        $estadoClasses = match($cliente['estado']) {
                                            'presentado' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                            'no_presentado' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                            'no_se_sabe' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600'
                                        };

                                        $estadoTexto = match($cliente['estado']) {
                                            'presentado' => 'Presentado',
                                            'no_presentado' => 'No Presentado',
                                            'no_se_sabe' => 'No se sabe',
                                            default => ucfirst(str_replace('_', ' ', $cliente['estado']))
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border {{ $estadoClasses }}">
                                        {{ $estadoTexto }}
                                    </span>
                                @endif

                                @if(!empty($cliente['contratos']))
                                    @php
                                        $contratoClasses = match($cliente['contratos']) {
                                            'con_contratos' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                            'sin_contratos' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600',
                                            default => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400 border-orange-200 dark:border-orange-800'
                                        };

                                        $contratoTexto = match($cliente['contratos']) {
                                            'con_contratos' => 'Con Contratos',
                                            'sin_contratos' => 'Sin Contratos',
                                            default => ucfirst(str_replace('_', ' ', $cliente['contratos']))
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border {{ $contratoClasses }}">
                                        {{ $contratoTexto }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700"></div>
                    @endif

                    <!-- Observaciones -->
                    @if(isset($cliente['observaciones']) && $cliente['observaciones'])
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Observaciones</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $cliente['observaciones'] }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <button class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </button>
                <a href="/clientes" wire:navigate class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 transition-colors">
                    Volver
                </a>
            </div>
        @endif
    </div>
</div>
