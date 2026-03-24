<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginationHelper
{
    /**
     * Convierte una respuesta de API externa a un LengthAwarePaginator de Laravel
     *
     * @param  array  $items  Los datos a paginar
     * @param  array  $meta  Metadata de paginación de la API externa
     * @param  int  $perPage  Elementos por página
     * @param  int|null  $currentPage  Página actual (si es null, se toma de $meta)
     * @param  array  $options  Opciones adicionales para el paginador
     */
    public static function createFromApiResponse(
        array $items,
        array $meta,
        int $perPage,
        ?int $currentPage = null,
        array $options = []
    ): LengthAwarePaginator {
        // Extraer información de paginación del meta
        $currentPage = $currentPage ?: ($meta['current_page'] ?? 1);
        $total = $meta['total'] ?? count($items);

        // Opciones por defecto para el paginador
        $options = array_merge([
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ], $options);

        // Crear y retornar el paginador
        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            $options
        );
    }
}
