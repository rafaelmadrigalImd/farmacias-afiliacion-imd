<?php

namespace App\Livewire\Clientes;

use App\Services\ClienteApiService;
use Livewire\Attributes\Layout;
use Livewire\Component;

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

            // Procesar centro para mostrar nombre legible
            if (! empty($this->cliente['centro'])) {
                $centros = $this->getCentros();
                $centroId = $this->cliente['centro'];
                $this->cliente['centro_nombre'] = $centros[$centroId] ?? "Centro #{$centroId}";
            }
        } else {
            $this->errorMessage = $response['message'];
        }

        $this->loading = false;
    }

    private function getCentros(): array
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

    public function render()
    {
        return view('livewire.clientes.show');
    }
}
