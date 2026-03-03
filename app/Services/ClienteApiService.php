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
        $this->baseUrl = env('CRM_API_BASE_URL', 'https://api.ejemplo.com');
        $this->apiKey = env('CRM_API_KEY', 'https://api.ejemplo.com');
    }

    /**
     * Obtener listado de centros/clínicas
     */
    public function getCentros()
    {
        try {
            $body = [
                'function' => 'getCentros',
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
            ])->send('GET', $this->baseUrl, [
                'body' => json_encode($body),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Si es un array, tomar el primer elemento
                if (is_array($responseData) && isset($responseData[0])) {
                    $responseData = $responseData[0];
                }

                return [
                    'success' => true,
                    'data' => $responseData['data'] ?? [],
                ];
            }

            Log::error('Error al obtener centros', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al obtener los centros',
                'data' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Excepción al obtener centros', [
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
     * Obtener listado de clientes
     */
    public function getAll(array $filters = [])
    {
        try {
            $body = array_merge([
                'function' => 'getPacientes',
            ], $filters);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
            ])->send('GET', $this->baseUrl, [
                'body' => json_encode($body),
            ]);

            if ($response->successful()) {
                // La API devuelve un array con un objeto dentro
                $responseData = $response->json();

                // Si es un array, tomar el primer elemento
                if (is_array($responseData) && isset($responseData[0])) {
                    $responseData = $responseData[0];
                }

                return [
                    'success' => true,
                    'data' => $responseData['data'] ?? [],
                    'meta' => $responseData['meta'] ?? [],
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
            $body = [
                'function' => 'getPaciente',
                'id' => $id,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
            ])->send('GET', $this->baseUrl, [
                'body' => json_encode($body),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Si es un array, tomar el primer elemento
                if (is_array($responseData) && isset($responseData[0])) {
                    $responseData = $responseData[0];
                }

                return [
                    'success' => $responseData['success'] ?? true,
                    'data' => $responseData['data'] ?? null,
                ];
            }

            Log::error('Error al obtener cliente', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Cliente no encontrado',
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
            $body = array_merge([
                'function' => 'createPaciente',
            ], $data);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
            ])->send('POST', $this->baseUrl, [
                'body' => json_encode($body),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Si es un array, tomar el primer elemento
                if (is_array($responseData) && isset($responseData[0])) {
                    $responseData = $responseData[0];
                }

                return [
                    'success' => $responseData['success'] ?? true,
                    'message' => $responseData['message'] ?? 'Cliente registrado correctamente',
                    'data' => $responseData['data'] ?? null,
                ];
            }

            Log::error('Error al crear cliente', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al crear el cliente',
                'errors' => [],
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
            $body = array_merge([
                'function' => 'updatePaciente',
                'id' => $id,
            ], $data);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
            ])->send('PUT', $this->baseUrl, [
                'body' => json_encode($body),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Si es un array, tomar el primer elemento
                if (is_array($responseData) && isset($responseData[0])) {
                    $responseData = $responseData[0];
                }

                return [
                    'success' => $responseData['success'] ?? true,
                    'message' => $responseData['message'] ?? 'Cliente actualizado correctamente',
                    'data' => $responseData['data'] ?? null,
                ];
            }

            Log::error('Error al actualizar cliente', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al actualizar el cliente',
                'errors' => [],
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
            $body = [
                'function' => 'deletePaciente',
                'id' => $id,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
            ])->send('DELETE', $this->baseUrl, [
                'body' => json_encode($body),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Si es un array, tomar el primer elemento
                if (is_array($responseData) && isset($responseData[0])) {
                    $responseData = $responseData[0];
                }

                return [
                    'success' => $responseData['success'] ?? true,
                    'message' => $responseData['message'] ?? 'Cliente eliminado correctamente',
                ];
            }

            Log::error('Error al eliminar cliente', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al eliminar el cliente',
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
