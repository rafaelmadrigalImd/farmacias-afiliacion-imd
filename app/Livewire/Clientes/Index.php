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

    private function getCentros()
    {
        return [
            '2' => 'Mostoles',
            '4' => 'Principe',
            '5' => 'M. Urquijo',
            '6' => 'Alcalá',
            '7' => 'Valencia',
            '8' => 'Oviedo',
            '9' => 'Sevilla',
            '10' => 'Raimundo',
            '11' => 'Murcia',
            '58' => 'Casanova',
            '76' => 'Doctor Esquerdo',
            '97' => 'Castellana',
            '98' => 'Claris',
            '316' => 'Bilbao',
            '377' => 'Alicante',
            '411' => 'Málaga',
            '418' => 'Lesseps',
            '443' => 'Palma de Mallorca',
            '489' => 'Zaragoza',
            '505' => 'Valladolid',
            '544' => 'Córdoba',
            '579' => 'Vigo',
        ];
    }

    private function procesarClientes($clientes)
    {
        $centros = $this->getCentros();

        return array_map(function($cliente) use ($centros) {
            // Transformar centro de ID a nombre
            if (!empty($cliente['centro'])) {
                $centroId = $cliente['centro'];
                $cliente['centro_nombre'] = $centros[$centroId] ?? "Centro #{$centroId}";
            }

            // Transformar contratos "0" a "sin contratos"
            if (isset($cliente['contratos']) && ($cliente['contratos'] === '0' || $cliente['contratos'] === 0)) {
                $cliente['contratos'] = 'sin contratos';
            }

            return $cliente;
        }, $clientes);
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

            // Procesar clientes (transformar centro y contratos)
            $allClientes = $this->procesarClientes($allClientes);

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
