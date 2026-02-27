# Configuración del Sistema de Clientes

## Resumen

Se ha creado un sistema completo para gestionar clientes con diseño **mobile-first** que se comunica con una API externa.

## Componentes Creados

### 1. Servicio API (`app/Services/ClienteApiService.php`)
Gestiona todas las llamadas HTTP al servidor externo:
- `getAll()` - Listar clientes
- `getById()` - Ver detalle de cliente
- `create()` - Crear cliente
- `update()` - Actualizar cliente
- `delete()` - Eliminar cliente

### 2. Componentes Livewire
- **Index** (`/clientes`) - Listado con búsqueda en tiempo real
- **Create** (`/clientes/create`) - Formulario de registro
- **Show** (`/clientes/{id}`) - Detalles de cliente

### 3. Vistas Mobile-First
Diseño completamente responsive con:
- Color corporativo #017C80
- Loading states
- Error handling
- Animaciones suaves

## Configuración de la API Externa

Necesitas configurar las variables de entorno para conectar con tu API:

```bash
# En tu archivo .env (NO COMMITEAR):
CLIENTE_API_URL=https://tu-api.com/api/v1
CLIENTE_API_KEY=tu-api-key-secreta
CLIENTE_API_TIMEOUT=30
```

## Formato Esperado de la API

### GET /clientes
```json
{
  "success": true,
  "data": [
    {
      "id": "1",
      "nombre": "Cliente Demo",
      "email": "cliente@ejemplo.com",
      "telefono": "912345678",
      "direccion": "Calle Mayor, 1",
      "ciudad": "Madrid",
      "provincia": "Madrid",
      "codigo_postal": "28001",
      "cif": "A12345678",
      "titular": "Juan Pérez",
      "estado": "activo",
      "observaciones": "Cliente principal"
    }
  ],
  "meta": {
    "total": 1,
    "per_page": 15,
    "current_page": 1
  }
}
```

### POST /clientes
**Request:**
```json
{
  "nombre": "Cliente Nuevo",
  "email": "nuevo@cliente.com",
  "telefono": "987654321",
  "direccion": "Calle Nueva, 10",
  "ciudad": "Barcelona",
  "provincia": "Barcelona",
  "codigo_postal": "08001",
  "cif": "B87654321",
  "titular": "María García",
  "observaciones": "Nuevo cliente"
}
```

**Response (éxito):**
```json
{
  "success": true,
  "message": "Cliente registrado correctamente",
  "data": {
    "id": "2",
    ...datos del cliente
  }
}
```

**Response (error):**
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "email": ["El email ya existe"],
    "telefono": ["El teléfono es requerido"]
  }
}
```

## Rutas Disponibles

- `GET /clientes` - Listado de clientes
- `GET /clientes/create` - Formulario de registro
- `GET /clientes/{id}` - Ver detalles

## Características Implementadas

✅ Diseño mobile-first responsive
✅ Búsqueda en tiempo real
✅ Loading states elegantes
✅ Manejo de errores completo
✅ Validación de formularios
✅ Colores corporativos (#017C80)
✅ Dark mode completo
✅ Navegación con Livewire (sin recargas)
✅ Mensajes de éxito/error
✅ Traducción completa al español

## Configuración Rápida

1. Añade a tu `.env`:
```bash
CLIENTE_API_URL=https://tu-api.com/api/v1
CLIENTE_API_KEY=tu-api-key-secreta
```

2. Visita: `http://localhost/clientes`

## Notas Técnicas

- **HTTP Client**: Laravel HTTP (basado en Guzzle)
- **Autenticación API**: Bearer Token (configurable)
- **Timeout**: 30 segundos por defecto
- **Logs**: Todos los errores se registran en `storage/logs/laravel.log`
