<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Mobile-First -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Clientes</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        @if($clientes->total() > 0)
                            Mostrando {{ $clientes->firstItem() }}-{{ $clientes->lastItem() }} de {{ $clientes->total() }} clientes
                        @else
                            No hay clientes registrados
                        @endif
                    </p>
                </div>
                <a href="/clientes/create" wire:navigate class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="hidden sm:inline">Nueva</span>
                </a>
            </div>

            <!-- Búsqueda -->
            <div class="mt-4">
                <div class="relative">
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar cliente..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido -->
    <div class="px-4 sm:px-6 lg:px-8 py-6">
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

        <!-- Loading State -->
        <div wire:loading class="space-y-3">
            @for($i = 0; $i < 5; $i++)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 animate-pulse">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                        <div class="ml-4 flex-1">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Content -->
        <div wire:loading.remove>
            @if($clientes->isNotEmpty())
                <!-- Lista de Clientes - Mobile First -->
                <div class="space-y-3">
                    @foreach($clientes as $cliente)
                        {{-- TODO: TEMPORAL - Deshabilitado hasta implementar funcionalidad de detalle --}}
                        {{-- <a href="/clientes/{{ $cliente['id'] }}" wire:navigate
                           class="block bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700"> --}}
                        <div class="block bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 cursor-default">
                            <div class="p-4">
                                <div class="flex items-start">
                                    <!-- Icono -->
                                    <div class="flex-shrink-0 w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>

                                    <!-- Información -->
                                    <div class="ml-4 flex-1 min-w-0">
                                        <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $cliente['nombre'] ?? 'Sin nombre' }}
                                        </h3>

                                        <div class="mt-2 space-y-1">
                                            @if(!empty($cliente['email']))
                                                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                                                    <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $cliente['email'] }}</span>
                                                </p>
                                            @endif

                                            @if(!empty($cliente['telefono']))
                                                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                                                    <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    {{ $cliente['telefono'] }}
                                                </p>
                                            @endif

                                            @if(!empty($cliente['centro_nombre']))
                                                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                                                    <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                    {{ $cliente['centro_nombre'] }}
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Status Badges -->
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @if(!empty($cliente['estado']))
                                                @php
                                                    $estadoClasses = match($cliente['estado']) {
                                                        'estado_presentado' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                                        'activo' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                        'inactivo' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                                    };
                                                    $estadoTexto = str_replace('estado_', '', $cliente['estado']);
                                                    $estadoTexto = ucfirst(str_replace('_', ' ', $estadoTexto));
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadoClasses }}">
                                                    {{ $estadoTexto }}
                                                </span>
                                            @endif

                                            @if(!empty($cliente['contratos']))
                                                @php
                                                    $contratoClasses = match($cliente['contratos']) {
                                                        'activo' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                                        'sin contratos' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                        default => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400'
                                                    };

                                                    $contratoTexto = $cliente['contratos'] === 'sin contratos'
                                                        ? 'Sin contratos'
                                                        : 'Contrato ' . ucfirst($cliente['contratos']);
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $contratoClasses }}">
                                                    {{ $contratoTexto }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Chevron -->
                                    {{-- TODO: TEMPORAL - Ocultar chevron hasta implementar funcionalidad de detalle --}}
                                    {{-- <svg class="w-5 h-5 text-gray-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg> --}}
                                </div>
                            </div>
                        </div>
                        {{-- </a> --}}
                    @endforeach
                </div>

                <!-- Paginación -->
                @if($clientes->hasPages())
                    <div class="mt-6">
                        {{ $clientes->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No hay clientes registradas</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Comienza agregando tu primera cliente</p>
                    <a href="/clientes/create" wire:navigate class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Registrar Cliente
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
