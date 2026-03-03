<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClienteApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function __construct(
        protected ClienteApiService $clienteService
    ) {}

    /**
     * Listado de clientes con paginación
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $filters = [
            'page' => $request->input('page', 1),
            'per_page' => $request->input('per_page', 15),
        ];

        if ($request->filled('search')) {
            $filters['search'] = $request->input('search');
        }

        $response = $this->clienteService->getAll($filters);

        if (! $response['success']) {
            return response()->json([
                'message' => 'Error al obtener clientes del CRM',
                'errors' => [
                    'service' => [$response['message'] ?? 'No se pudo conectar con la API externa'],
                ],
            ], 500);
        }

        return response()->json([
            'data' => $response['data'],
            'meta' => [
                'pagination' => $response['meta'] ?? [],
            ],
        ]);
    }

    /**
     * Obtener un cliente específico por ID
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $response = $this->clienteService->getById($id);

        if (! $response['success']) {
            $statusCode = $response['message'] === 'Farmacia no encontrada' ? 404 : 500;

            return response()->json([
                'message' => $response['message'],
            ], $statusCode);
        }

        return response()->json([
            'data' => $response['data'],
        ]);
    }
}
