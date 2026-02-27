<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Services\ClienteApiService;

#[Layout('layouts.app')]
class Create extends Component
{
    #[Validate('required|min:3')]
    public $nombre = '';

    #[Validate('required|email')]
    public $email = '';

    #[Validate('required')]
    public $telefono = '';

    #[Validate('required')]
    public $direccion = '';

    #[Validate('required')]
    public $ciudad = '';

    #[Validate('required')]
    public $provincia = '';

    #[Validate('required|regex:/^\d{5}$/')]
    public $codigo_postal = '';

    public $cif = '';
    public $titular = '';
    public $observaciones = '';

    public $saving = false;
    public $errorMessage = '';

    protected $clienteService;

    public function boot(ClienteApiService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function save()
    {
        $this->validate();

        $this->saving = true;
        $this->errorMessage = '';

        $data = [
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'provincia' => $this->provincia,
            'codigo_postal' => $this->codigo_postal,
            'cif' => $this->cif,
            'titular' => $this->titular,
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
