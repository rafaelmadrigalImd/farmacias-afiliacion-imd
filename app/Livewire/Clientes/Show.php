<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\ClienteApiService;

#[Layout('layouts.app')]
class Show extends Component
{
    public $clienteId;
    public $cliente = null;
    public $loading = true;
    public $errorMessage = '';

    protected $clienteService;

    public function boot(ClienteApiService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function mount($id)
    {
        $this->clienteId = $id;
        $this->loadFarmacia();
    }

    public function loadFarmacia()
    {
        $this->loading = true;
        $this->errorMessage = '';

        $response = $this->clienteService->getById($this->clienteId);

        if ($response['success']) {
            $this->cliente = $response['data'];
        } else {
            $this->errorMessage = $response['message'];
        }

        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.clientes.show');
    }
}
