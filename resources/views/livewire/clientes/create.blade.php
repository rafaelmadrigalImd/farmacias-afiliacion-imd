<div x-data="{
         showSuccessModal: @entangle('appointmentSaved'),
         redirecting: false
     }"
     @cita-guardada.window="
         setTimeout(() => {
             redirecting = true;
             setTimeout(() => {
                 window.location.href = '/clientes';
             }, 1000);
         }, 2000);
     ">
    <!-- Header Mobile-First -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center">
                <a href="/clientes" wire:navigate class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="flex-1">
                    @if($creatingAppointment)
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Agendar Cita</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Selecciona el día y hora para la cita</p>
                    @else
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo Paciente</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Completa la información del paciente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="px-4 sm:px-6 lg:px-8 py-6 max-w-2xl mx-auto">
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

        @if($creatingAppointment)
            <!-- Overlay de Éxito (cuando la cita se ha guardado) -->
            <div x-show="showSuccessModal"
                 x-cloak
                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                 style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4 shadow-2xl"
                     x-show="showSuccessModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-90"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/30 mb-4"
                             x-show="!redirecting">
                            <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">¡Cita Guardada Correctamente!</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-1">
                            La cita para <span class="font-semibold">{{ $nombre }} {{ $apellido1 }}</span>
                        </p>
                        @if($diaSeleccionado && $horaSeleccionada)
                        <p class="text-gray-600 dark:text-gray-400">
                            ha sido registrada para el <span class="font-semibold">{{ \Carbon\Carbon::parse($diaSeleccionado)->locale('es')->isoFormat('D [de] MMMM') }}</span> a las <span class="font-semibold">{{ $horaSeleccionada }}</span>
                        </p>
                        @endif
                        <div class="mt-6">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2" x-text="redirecting ? 'Redirigiendo ahora...' : 'Redirigiendo en unos segundos...'"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flujo de Creación de Cita -->
            <div class="space-y-6">
                <!-- Mensaje de éxito -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-green-800 dark:text-green-300">
                            <span class="font-semibold">¡Paciente registrado correctamente!</span>
                            <br>Ahora puedes agendar una cita para {{ $nombre }} {{ $apellido1 }}
                        </p>
                    </div>
                </div>

                <!-- Selección de Día -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Selecciona un día
                        @if($diaSeleccionado)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Seleccionado
                            </span>
                        @endif
                    </h3>

                    <div class="grid grid-cols-1 gap-3">
                        @foreach($diasLibres as $dia)
                            <button
                                type="button"
                                wire:click="seleccionarDia('{{ $dia['fecha'] }}')"
                                class="text-left px-4 py-3 rounded-lg border transition-all {{ $diaSeleccionado === $dia['fecha']
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-500'
                                    : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:border-primary-400 dark:hover:border-primary-500'
                                }}"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900 dark:text-white capitalize">
                                        {{ $dia['formatted'] }}
                                    </span>
                                    @if($diaSeleccionado === $dia['fecha'])
                                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Selección de Hora (visible solo si hay un día seleccionado) -->
                @if($diaSeleccionado)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Selecciona una hora
                            @if($horaSeleccionada)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $horaSeleccionada }}
                                </span>
                            @endif
                        </h3>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($horasLibres as $hora)
                                <button
                                    type="button"
                                    wire:click="seleccionarHora('{{ $hora['id'] }}', '{{ $hora['hora'] }}')"
                                    @if(!$hora['disponible']) disabled @endif
                                    class="px-4 py-3 rounded-lg border font-medium text-center transition-all {{ !$hora['disponible']
                                        ? 'border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed'
                                        : ($horaSeleccionada === $hora['hora']
                                            ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 ring-2 ring-primary-500'
                                            : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white hover:border-primary-400 dark:hover:border-primary-500')
                                    }}"
                                >
                                    {{ $hora['hora'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Botones de Acción -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        type="button"
                        wire:click="guardarCita"
                        wire:loading.attr="disabled"
                        @if(!$horaSeleccionada || $savingAppointment) disabled @endif
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center px-6 py-3 bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-lg shadow-sm transition-colors"
                    >
                        <svg wire:loading.remove wire:target="guardarCita" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <svg wire:loading wire:target="guardarCita" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="guardarCita">Guardar Cita</span>
                        <span wire:loading wire:target="guardarCita">Guardando...</span>
                    </button>
                    <button
                        type="button"
                        wire:click="omitirCita"
                        @if($savingAppointment) disabled @endif
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Omitir por ahora
                    </button>
                </div>
            </div>
        @else
            <!-- Formulario de Cliente -->
            <form wire:submit="save" class="space-y-6">
            <!-- Información del Paciente -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información del Paciente</h3>

                <div class="space-y-4">
                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="nombre"
                            wire:model="nombre"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Ej: Juan"
                        >
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellidos -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="apellido1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Primer Apellido <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="apellido1"
                                wire:model="apellido1"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Ej: García"
                            >
                            @error('apellido1')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="apellido2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Segundo Apellido
                            </label>
                            <input
                                type="text"
                                id="apellido2"
                                wire:model="apellido2"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Ej: López"
                            >
                            @error('apellido2')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Centro Médico -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Centro Médico</h3>

                <div>
                    <label for="centro_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Centro <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="centro_id"
                        wire:model="centro_id"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                        <option value="">Selecciona un centro</option>
                        @foreach($centros as $centro)
                            <option value="{{ $centro['id'] ?? $centro['codigo'] }}">
                                {{ $centro['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('centro_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contacto -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contacto</h3>

                <div class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            wire:model="email"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="cliente@ejemplo.com"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="tel"
                            id="telefono"
                            wire:model="telefono"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="912 345 678"
                        >
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- TODO: TEMPORAL - Ubicación comentada --}}
            {{-- <!-- Ubicación (opcional) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ubicación <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(Opcional)</span></h3>

                <div class="space-y-4">
                    <!-- Dirección -->
                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Dirección
                        </label>
                        <input
                            type="text"
                            id="direccion"
                            wire:model="direccion"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Calle Principal, 123"
                        >
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ciudad y Código Postal -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="ciudad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Ciudad
                            </label>
                            <input
                                type="text"
                                id="ciudad"
                                wire:model="ciudad"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Madrid"
                            >
                            @error('ciudad')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="codigo_postal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Código Postal
                            </label>
                            <input
                                type="text"
                                id="codigo_postal"
                                wire:model="codigo_postal"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="28001"
                            >
                            @error('codigo_postal')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Provincia -->
                    <div>
                        <label for="provincia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Provincia
                        </label>
                        <input
                            type="text"
                            id="provincia"
                            wire:model="provincia"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Madrid"
                        >
                        @error('provincia')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div> --}}

            <!-- Observaciones -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Adicional</h3>

                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Observaciones
                    </label>
                    <textarea
                        id="observaciones"
                        wire:model="observaciones"
                        rows="4"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="Notas adicionales sobre la cliente..."
                    ></textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center px-6 py-3 bg-primary-600 hover:bg-primary-700 disabled:bg-primary-400 text-white font-semibold rounded-lg shadow-sm transition-colors"
                >
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="save">Registrar Paciente</span>
                    <span wire:loading wire:target="save">Registrando...</span>
                </button>
                <a
                    href="/clientes"
                    wire:navigate
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 transition-colors"
                >
                    Cancelar
                </a>
            </div>
        </form>
        @endif
    </div>
</div>
