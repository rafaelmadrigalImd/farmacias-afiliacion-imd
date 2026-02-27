<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\ClienteApiService;

#[Layout('layouts.app')]
class Index extends Component
{
    public $clientes = [];
    public $loading = true;
    public $search = '';
    public $errorMessage = '';

    protected $clienteService;

    public function boot(ClienteApiService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function mount()
    {
        $this->loadFarmacias();
    }

    public function loadFarmacias()
    {
        $this->loading = true;
        $this->errorMessage = '';

        $filters = [];
        if ($this->search) {
            $filters['search'] = $this->search;
        }

        $response = $this->clienteService->getAll($filters);

        if ($response['success']) {
            $this->clientes = $response['data'];
        } else {
            $this->errorMessage = $response['message'];
            $this->clientes = [];
        }

        $this->loading = false;
    }

    public function updatedSearch()
    {
        $this->loadFarmacias();
    }

    public function refreshList()
    {
        $this->loadFarmacias();
    }

    public function render()
    {
        return view('livewire.clientes.index');
    }
}
