<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Services\ClienteApiService;

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
    #[Validate('required|email')]
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

    protected $clienteService;

    public function boot(ClienteApiService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function mount()
    {
        // Cargar lista de centros al inicializar el componente
        $response = $this->clienteService->getCentros();
        if ($response['success']) {
            $this->centros = $response['data'];
        }
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
            session()->flash('success', $response['message']);
            return $this->redirect('/clientes', navigate: true);
        } else {
            $this->errorMessage = $response['message'];

            if (isset($response['errors'])) {
                foreach ($response['errors'] as $field => $messages) {
                    $this->addError($field, is_array($messages) ? $messages[0] : $messages);
                }
            }
        }

        $this->saving = false;
    }

    public function render()
    {
        return view('livewire.clientes.create');
    }
}
