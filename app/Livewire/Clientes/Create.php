<?php

namespace App\Livewire\Clientes;

use App\Services\ClienteApiService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    // Información del Paciente
    #[Validate('required|min:2')]
    public $nombre = '';

    #[Validate('required|min:2')]
    public $apellido1 = '';

    #[Validate('sometimes|min:2')]
    public $apellido2 = '';

    // Centro Médico
    #[Validate('required')]
    public $centro_id = '';

    // Contacto
    #[Validate('nullable|email')]
    public $email = '';

    #[Validate('required')]
    public $telefono = '';

    // Ubicación (opcional)
    #[Validate('sometimes|nullable')]
    public $direccion = '';

    #[Validate('sometimes|nullable')]
    public $ciudad = '';

    #[Validate('sometimes|nullable')]
    public $provincia = '';

    #[Validate('sometimes|nullable|regex:/^\d{5}$/')]
    public $codigo_postal = '';

    // Información adicional
    public $observaciones = '';

    // Lista de centros
    public $centros = [];

    public $saving = false;

    public $errorMessage = '';

    // Control de flujo para creación de cita
    public $clienteSaved = false;

    public $creatingAppointment = false;

    public $clienteId = ''; // ID del cliente creado

    public $savingAppointment = false; // Estado de guardando cita

    public $appointmentSaved = false; // Cita guardada exitosamente

    // Datos de la cita
    public $diasLibres = [];

    public $horasLibres = [];

    public $diaSeleccionado = '';

    public $horaSeleccionada = '';

    public $horaIdSeleccionada = ''; // ID de la hora para enviar a la API

    protected $clienteService;

    public function boot(ClienteApiService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function mount()
    {
        // TODO: Descomentar cuando getCentros esté funcionando
        // Cargar lista de centros al inicializar el componente
        // $response = $this->clienteService->getCentros();
        // if ($response['success']) {
        //     $this->centros = $response['data'];
        // }

        // Centros disponibles IMD
        $this->centros = [
            ['id' => '2', 'nombre' => 'Mostoles'],
            ['id' => '4', 'nombre' => 'Principe'],
            ['id' => '5', 'nombre' => 'M. Urquijo'],
            ['id' => '6', 'nombre' => 'Alcalá'],
            ['id' => '7', 'nombre' => 'Valencia'],
            ['id' => '8', 'nombre' => 'Oviedo'],
            ['id' => '9', 'nombre' => 'Sevilla'],
            ['id' => '10', 'nombre' => 'Raimundo'],
            ['id' => '11', 'nombre' => 'Murcia'],
            ['id' => '58', 'nombre' => 'Casanova'],
            ['id' => '76', 'nombre' => 'Doctor Esquerdo'],
            ['id' => '97', 'nombre' => 'Castellana'],
            ['id' => '98', 'nombre' => 'Claris'],
            ['id' => '316', 'nombre' => 'Bilbao'],
            ['id' => '377', 'nombre' => 'Alicante'],
            ['id' => '411', 'nombre' => 'Málaga'],
            ['id' => '418', 'nombre' => 'Lesseps'],
            ['id' => '443', 'nombre' => 'Palma de Mallorca'],
            ['id' => '489', 'nombre' => 'Zaragoza'],
            ['id' => '505', 'nombre' => 'Valladolid'],
            ['id' => '544', 'nombre' => 'Córdoba'],
            ['id' => '579', 'nombre' => 'Vigo'],
        ];

        // Días libres de prueba (próximos 7 días laborables)
        $this->diasLibres = $this->generarDiasLibresPrueba();
    }

    private function generarDiasLibresPrueba()
    {
        $dias = [];
        $fecha = now();
        $count = 0;

        while ($count < 7) {
            $fecha = $fecha->addDay();
            // Saltar solo los domingos (permitir lunes a sábado)
            if (! $fecha->isSunday()) {
                $dias[] = [
                    'fecha' => $fecha->format('Y-m-d'),
                    'formatted' => $fecha->locale('es')->isoFormat('dddd, D [de] MMMM'),
                ];
                $count++;
            }
        }

        return $dias;
    }

    public function save()
    {
        $this->validate();

        $this->saving = true;
        $this->errorMessage = '';

        $data = [
            'nombre' => $this->nombre,
            'apellido1' => $this->apellido1,
            'apellido2' => $this->apellido2,
            'centro_id' => $this->centro_id,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'provincia' => $this->provincia,
            'codigo_postal' => $this->codigo_postal,
            'observaciones' => $this->observaciones,
        ];

        $response = $this->clienteService->create($data);

        if ($response['success']) {
            // Guardar el ID del cliente creado
            $this->clienteId = $response['data']['cliente_id'] ?? '';

            Log::info('✅ [save] Cliente creado, iniciando flujo de cita', [
                'cliente_id' => $this->clienteId,
                'nombre' => $this->nombre,
                'apellido1' => $this->apellido1,
            ]);

            // En lugar de redirigir, activar el flujo de creación de cita
            $this->clienteSaved = true;
            $this->creatingAppointment = true;
            $this->saving = false;
        } else {
            $this->errorMessage = $response['message'];

            if (isset($response['errors'])) {
                foreach ($response['errors'] as $field => $messages) {
                    $this->addError($field, is_array($messages) ? $messages[0] : $messages);
                }
            }
            $this->saving = false;
        }
    }

    public function seleccionarDia($fecha)
    {
        $this->diaSeleccionado = $fecha;
        $this->horaSeleccionada = '';
        $this->horaIdSeleccionada = '';

        // Validar que se haya seleccionado un centro
        if (empty($this->centro_id)) {
            $this->errorMessage = 'No se puede obtener las horas disponibles sin un centro seleccionado.';
            $this->horasLibres = [];

            return;
        }

        // Obtener horas disponibles desde la API
        $response = $this->clienteService->getHorasDisponibles($this->centro_id, $fecha);

        if ($response['success']) {
            $this->horasLibres = $response['data'];
            $this->errorMessage = '';

            Log::info('✅ [seleccionarDia] Horas cargadas', [
                'fecha' => $fecha,
                'centro' => $this->centro_id,
                'total_horas' => count($this->horasLibres),
            ]);
        } else {
            $this->horasLibres = [];
            $this->errorMessage = $response['message'] ?? 'No se pudieron cargar las horas disponibles.';

            Log::error('❌ [seleccionarDia] Error al cargar horas', [
                'fecha' => $fecha,
                'centro' => $this->centro_id,
                'mensaje' => $this->errorMessage,
            ]);
        }
    }

    public function seleccionarHora($horaId, $hora)
    {
        $this->horaIdSeleccionada = $horaId;
        $this->horaSeleccionada = $hora;
    }

    public function guardarCita()
    {
        // Validaciones
        if (empty($this->diaSeleccionado) || empty($this->horaSeleccionada)) {
            $this->errorMessage = 'Debes seleccionar un día y una hora para la cita.';

            return;
        }

        if (empty($this->clienteId)) {
            $this->errorMessage = 'No se pudo obtener el ID del cliente.';
            Log::error('❌ [guardarCita] ClienteId vacío', [
                'cliente_saved' => $this->clienteSaved,
                'creating_appointment' => $this->creatingAppointment,
            ]);

            return;
        }

        $this->savingAppointment = true;
        $this->errorMessage = '';

        // Llamar al servicio para guardar la cita
        $response = $this->clienteService->guardarCita(
            $this->clienteId,
            $this->centro_id,
            $this->diaSeleccionado,
            $this->horaIdSeleccionada,
            $this->observaciones
        );

        if ($response['success']) {
            Log::info('✅ [guardarCita] Cita creada exitosamente para el cliente', [
                'cliente_id' => $this->clienteId,
                'fecha' => $this->diaSeleccionado,
                'hora' => $this->horaSeleccionada,
            ]);

            $this->savingAppointment = false;
            $this->appointmentSaved = true;

            session()->flash('success', 'Paciente registrado y cita creada correctamente.');

            // Despachar evento JavaScript para redirigir después de 3 segundos
            $this->dispatch('cita-guardada');
        } else {
            $this->savingAppointment = false;
            $this->errorMessage = $response['message'] ?? 'Error al crear la cita.';

            Log::error('❌ [guardarCita] Error al crear cita', [
                'cliente_id' => $this->clienteId,
                'mensaje' => $this->errorMessage,
            ]);
        }
    }

    public function omitirCita()
    {
        session()->flash('success', 'Paciente registrado correctamente.');

        return $this->redirect('/clientes', navigate: true);
    }

    public function render()
    {
        return view('livewire.clientes.create');
    }
}
