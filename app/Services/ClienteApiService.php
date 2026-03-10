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

            Log::info('📤 [getCentros] Petición al CRM', [
                'url' => $this->baseUrl,
                'method' => 'GET',
                'body' => $body,
                'body_json' => json_encode($body),
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
            ])->send('GET', $this->baseUrl, [
                'body' => json_encode($body),
            ]);

            Log::info('📥 [getCentros] Respuesta del CRM', [
                'status' => $response->status(),
                'body' => $response->body(),
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

            Log::error('❌ [getCentros] Error al obtener centros', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al obtener los centros',
                'data' => [],
            ];
        } catch (\Exception $e) {
            Log::error('💥 [getCentros] Excepción al obtener centros', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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

            Log::info('📤 [getPacientes] Petición al CRM', [
                'url' => $this->baseUrl,
                'body' => $body,
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
            ])->send('GET', $this->baseUrl, [
                'body' => json_encode($body),
            ]);

            Log::info('📥 [getPacientes] Respuesta del CRM', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // La API devuelve: [{ "id1": {...}, "id2": {...} }]
                // Extraer el primer elemento si es un array
                if (is_array($responseData) && isset($responseData[0]) && is_array($responseData[0])) {
                    $responseData = $responseData[0];
                }

                // Convertir el objeto con IDs como keys a un array indexado
                $pacientes = [];
                if (is_array($responseData)) {
                    foreach ($responseData as $id => $paciente) {
                        if (is_array($paciente)) {
                            $pacientes[] = $paciente;
                        }
                    }
                }

                Log::info('✅ [getPacientes] Pacientes procesados', [
                    'total' => count($pacientes),
                ]);

                return [
                    'success' => true,
                    'data' => $pacientes,
                    'meta' => [
                        'current_page' => $filters['page'] ?? 1,
                        'total' => count($pacientes),
                    ],
                ];
            }

            Log::error('❌ [getPacientes] Error al obtener clientes', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al obtener los clientes',
                'data' => [],
            ];
        } catch (\Exception $e) {
            Log::error('💥 [getPacientes] Excepción al obtener clientes', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
            // Preparar el body con el formato específico requerido por el API
            $body = [
                'function' => 'crearPaciente',
                'nombre' => $data['nombre'] ?? 'Rafa',
                'apellido1' => $data['apellido1'] ?? 'Test',
                'apellido2' => $data['apellido2'] ?? 'Test',
                'email' => $data['email'] ?? 'dfdsf@gmail.com',
                'movil' => $data['telefono'] ?? '678128386',
                'centro' => $data['centro_id'] ?? 9,
            ];

            Log::info('📤 [crearPaciente] Petición al CRM', [
                'url' => $this->baseUrl,
                'method' => 'POST',
                'data_recibida' => $data,
                'body_preparado' => $body,
                'body_json' => json_encode($body),
                'body_json_length' => strlen(json_encode($body)),
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
            ])->send('POST', $this->baseUrl, [
                'body' => json_encode($body),
            ]);

            Log::info('📥 [crearPaciente] Respuesta del CRM', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Si es un array, tomar el primer elemento
                if (is_array($responseData) && isset($responseData[0])) {
                    $responseData = $responseData[0];
                }

                Log::info('✅ [crearPaciente] Paciente creado exitosamente', ['response' => $responseData]);

                return [
                    'success' => $responseData['success'] ?? true,
                    'message' => $responseData['message'] ?? 'Cliente registrado correctamente',
                    'data' => $responseData['data'] ?? null,
                ];
            }

            Log::error('❌ [crearPaciente] Error al crear cliente', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al crear el cliente',
                'errors' => [],
            ];
        } catch (\Exception $e) {
            Log::error('💥 [crearPaciente] Excepción al crear cliente', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
