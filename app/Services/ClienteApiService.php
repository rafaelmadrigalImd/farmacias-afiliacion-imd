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
                'id_usuario' => auth()->id(),
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
     * Nota: Como la API no soporta getPaciente individual correctamente,
     * obtenemos el listado completo y filtramos por ID localmente
     */
    public function getById(string $id)
    {
        try {
            Log::info('📤 [getById] Obteniendo cliente', ['id' => $id]);

            // Obtener todos los pacientes
            $response = $this->getAll();

            if (!$response['success']) {
                Log::error('❌ [getById] Error al obtener listado de pacientes', [
                    'id' => $id,
                ]);

                return [
                    'success' => false,
                    'message' => 'Error al obtener el cliente',
                    'data' => null,
                ];
            }

            // Buscar el paciente por ID en el array
            $pacientes = $response['data'] ?? [];
            $pacienteEncontrado = null;

            foreach ($pacientes as $paciente) {
                if (isset($paciente['id']) && $paciente['id'] === $id) {
                    $pacienteEncontrado = $paciente;
                    break;
                }
            }

            if ($pacienteEncontrado) {
                Log::info('✅ [getById] Cliente encontrado', [
                    'id' => $id,
                    'nombre' => $pacienteEncontrado['nombre'] ?? 'N/A',
                ]);

                return [
                    'success' => true,
                    'data' => $pacienteEncontrado,
                ];
            }

            Log::warning('⚠️ [getById] Cliente no encontrado', ['id' => $id]);

            return [
                'success' => false,
                'message' => 'Cliente no encontrado',
                'data' => null,
            ];
        } catch (\Exception $e) {
            Log::error('💥 [getById] Excepción al obtener cliente', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
                'centro' => (int) ($data['centro_id'] ?? 4),
                'id_usuario' => auth()->id(),
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

    /**
     * Obtener horas disponibles para un día y centro específico
     */
    public function getHorasDisponibles(string $centroId, string $fecha)
    {
        try {
            // Usar credenciales específicas para este endpoint
            $baseUrl2 = env('CRM_API_BASE_URL2', 'https://api2.imdermatologico.es/web.php');
            $apiKey2 = env('CRM_API_KEY2', '');

            // Convertir fecha de Y-m-d a d-m-Y según espera la API
            $fechaFormateada = \Carbon\Carbon::parse($fecha)->format('d-m-Y');

            $body = [
                'function' => 'getHorasDiaDisponiblesClinica',
                'centro' => (int) $centroId,
                'dia' => $fechaFormateada,
                'key' => $apiKey2,
            ];

            Log::info('📤 [getHorasDisponibles] Petición al CRM', [
                'url' => $baseUrl2,
                'method' => 'GET',
                'body' => $body,
                'body_json' => json_encode($body),
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->send('GET', $baseUrl2, [
                'body' => json_encode($body),
            ]);

            Log::info('📥 [getHorasDisponibles] Respuesta del CRM', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Extraer el array de horas disponibles
                $horasDisponibles = $responseData['horas_disponibles'] ?? [];

                // Transformar el formato de la API al formato esperado por el componente
                // API: { "914": "08:00", "915": "08:30", ... }
                // Componente: [{ "id": "914", "hora": "08:00", "disponible": true }, ...]
                $horasTransformadas = [];
                foreach ($horasDisponibles as $id => $hora) {
                    $horasTransformadas[] = [
                        'id' => $id,
                        'hora' => $hora,
                        'disponible' => true,
                    ];
                }

                Log::info('✅ [getHorasDisponibles] Horas procesadas', [
                    'total' => count($horasTransformadas),
                    'centro' => $centroId,
                    'fecha' => $fechaFormateada,
                ]);

                return [
                    'success' => true,
                    'data' => $horasTransformadas,
                ];
            }

            Log::error('❌ [getHorasDisponibles] Error al obtener horas', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al obtener las horas disponibles',
                'data' => [],
            ];
        } catch (\Exception $e) {
            Log::error('💥 [getHorasDisponibles] Excepción al obtener horas', [
                'centro' => $centroId,
                'fecha' => $fecha,
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
     * Guardar cita online desde farmacia
     */
    public function guardarCita(string $clienteId, string $centroId, string $fecha, string $horaId, string $observaciones = '')
    {
        try {
            // Usar credenciales específicas para este endpoint
            $baseUrl2 = env('CRM_API_BASE_URL2', 'https://api2.imdermatologico.es/web.php');
            $apiKey2 = env('CRM_API_KEY2', '');

            // Convertir fecha de Y-m-d a d-m-Y según espera la API
            $fechaFormateada = \Carbon\Carbon::parse($fecha)->format('d-m-Y');

            $body = [
                'function' => 'guardarCitaOnlineDesdeFarmacia',
                'id_cliente' => $clienteId,
                'centro' => (int) $centroId,
                'dia' => $fechaFormateada,
                'hora_id' => (int) $horaId,
                'observaciones' => $observaciones,
                'key' => $apiKey2,
            ];

            Log::info('📤 [guardarCita] Petición al CRM', [
                'url' => $baseUrl2,
                'method' => 'POST',
                'body' => $body,
                'body_json' => json_encode($body),
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->send('POST', $baseUrl2, [
                'body' => json_encode($body),
            ]);

            Log::info('📥 [guardarCita] Respuesta del CRM', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                Log::info('✅ [guardarCita] Cita guardada exitosamente', [
                    'response' => $responseData,
                    'cliente_id' => $clienteId,
                    'centro' => $centroId,
                    'fecha' => $fechaFormateada,
                    'hora_id' => $horaId,
                ]);

                return [
                    'success' => true,
                    'message' => $responseData['message'] ?? 'Cita creada correctamente',
                    'data' => $responseData['data'] ?? $responseData,
                ];
            }

            Log::error('❌ [guardarCita] Error al guardar cita', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al crear la cita',
                'data' => null,
            ];
        } catch (\Exception $e) {
            Log::error('💥 [guardarCita] Excepción al guardar cita', [
                'cliente_id' => $clienteId,
                'centro' => $centroId,
                'fecha' => $fecha,
                'hora_id' => $horaId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión con el servidor',
                'data' => null,
            ];
        }
    }
}
