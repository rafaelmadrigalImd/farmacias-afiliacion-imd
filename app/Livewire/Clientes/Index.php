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

    public $perPage = 15;
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

        $filters = [
            'page' => $this->getPage(),
            'per_page' => $this->perPage,
        ];

        if ($this->search) {
            $filters['search'] = $this->search;
        }

        $response = $this->clienteService->getAll($filters);

        if ($response['success']) {
            $clientes = PaginationHelper::createFromApiResponse(
                $response['data'],
                $response['meta'],
                $this->perPage,
                $this->getPage()
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
