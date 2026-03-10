<?php

namespace App\Livewire\Clientes;

use App\Helpers\PaginationHelper;
use App\Services\ClienteApiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $search = '';

    public $perPage = 5;
    public $errorMessage = '';

    protected $clienteService;

    public function boot(ClienteApiService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function refreshList()
    {
        $this->dispatch('$refresh');
    }

    public function render()
    {
        $this->errorMessage = '';

        $filters = [];

        if ($this->search) {
            $filters['search'] = $this->search;
        }

        $response = $this->clienteService->getAll($filters);

        if ($response['success']) {
            $allClientes = $response['data'];
            $total = count($allClientes);

            // Filtrar por búsqueda si existe (búsqueda local)
            if ($this->search) {
                $searchTerm = strtolower($this->search);
                $allClientes = array_filter($allClientes, function($cliente) use ($searchTerm) {
                    return str_contains(strtolower($cliente['nombre'] ?? ''), $searchTerm) ||
                           str_contains(strtolower($cliente['email'] ?? ''), $searchTerm) ||
                           str_contains(strtolower($cliente['telefono'] ?? ''), $searchTerm);
                });
                $total = count($allClientes);
            }

            // Paginar localmente
            $currentPage = $this->getPage();
            $offset = ($currentPage - 1) * $this->perPage;
            $paginatedData = array_slice($allClientes, $offset, $this->perPage);

            $clientes = PaginationHelper::createFromApiResponse(
                $paginatedData,
                [
                    'current_page' => $currentPage,
                    'total' => $total,
                ],
                $this->perPage,
                $currentPage
            );
        } else {
            $this->errorMessage = $response['message'];
            $clientes = PaginationHelper::createFromApiResponse(
                [],
                ['total' => 0, 'current_page' => 1],
                $this->perPage,
                1
            );
        }

        return view('livewire.clientes.index', [
            'clientes' => $clientes,
        ]);
    }
}
