<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClienteApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.cliente_api.base_url', 'https://api.ejemplo.com');
        $this->apiKey = config('services.cliente_api.key', '');
    }

    /**
     * Obtener listado de clientes
     */
    public function getAll(array $filters = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/clientes', $filters);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json('data', []),
                    'meta' => $response->json('meta', []),
                ];
            }

            Log::error('Error al obtener clientes', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al obtener las clientes',
                'data' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Excepción al obtener clientes', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión con el servidor',
                'data' => [],
            ];
        }
    }

    /**
     * Obtener una cliente por ID
     */
    public function getById(string $id)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/clientes/' . $id);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json('data'),
                ];
            }

            return [
                'success' => false,
                'message' => 'Farmacia no encontrada',
                'data' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Excepción al obtener cliente', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión con el servidor',
                'data' => null,
            ];
        }
    }

    /**
     * Crear nueva cliente
     */
    public function create(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/clientes', $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Farmacia registrada correctamente',
                    'data' => $response->json('data'),
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message', 'Error al crear la cliente'),
                'errors' => $response->json('errors', []),
            ];
        } catch (\Exception $e) {
            Log::error('Excepción al crear cliente', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión con el servidor',
            ];
        }
    }

    /**
     * Actualizar cliente
     */
    public function update(string $id, array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->put($this->baseUrl . '/clientes/' . $id, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Farmacia actualizada correctamente',
                    'data' => $response->json('data'),
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message', 'Error al actualizar la cliente'),
                'errors' => $response->json('errors', []),
            ];
        } catch (\Exception $e) {
            Log::error('Excepción al actualizar cliente', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión con el servidor',
            ];
        }
    }

    /**
     * Eliminar cliente
     */
    public function delete(string $id)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->delete($this->baseUrl . '/clientes/' . $id);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Farmacia eliminada correctamente',
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message', 'Error al eliminar la cliente'),
            ];
        } catch (\Exception $e) {
            Log::error('Excepción al eliminar cliente', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión con el servidor',
            ];
        }
    }
}
