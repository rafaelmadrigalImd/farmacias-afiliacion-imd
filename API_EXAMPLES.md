# API REST - Ejemplos de Uso

## Autenticación con Sanctum

Base URL: `http://localhost/api`

### 1. Registro de Usuario

**Endpoint:** `POST /api/register`

**Body (JSON):**
```json
{
  "name": "Juan Pérez",
  "email": "juan@ejemplo.com",
  "password": "password123",
  "password_confirmation": "password123",
  "device_name": "mi-dispositivo"
}
```

**Respuesta exitosa (201):**
```json
{
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "created_at": "2024-02-27T16:00:00.000000Z",
    "updated_at": "2024-02-27T16:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz123456",
  "token_type": "Bearer"
}
```

**cURL:**
```bash
curl -X POST http://localhost/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

---

### 2. Login

**Endpoint:** `POST /api/login`

**Body (JSON):**
```json
{
  "email": "juan@ejemplo.com",
  "password": "password123",
  "device_name": "mi-dispositivo"
}
```

**Respuesta exitosa (200):**
```json
{
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "created_at": "2024-02-27T16:00:00.000000Z",
    "updated_at": "2024-02-27T16:00:00.000000Z"
  },
  "token": "2|zyxwvutsrqponmlkjihgfedcba654321",
  "token_type": "Bearer"
}
```

**cURL:**
```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "juan@ejemplo.com",
    "password": "password123"
  }'
```

---

### 3. Obtener Usuario Autenticado

**Endpoint:** `GET /api/me`

**Headers:**
```
Authorization: Bearer {tu-token-aquí}
Accept: application/json
```

**Respuesta exitosa (200):**
```json
{
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "created_at": "2024-02-27T16:00:00.000000Z",
    "updated_at": "2024-02-27T16:00:00.000000Z"
  }
}
```

**cURL:**
```bash
curl -X GET http://localhost/api/me \
  -H "Authorization: Bearer 2|zyxwvutsrqponmlkjihgfedcba654321" \
  -H "Accept: application/json"
```

---

### 4. Logout (Revocar Token Actual)

**Endpoint:** `POST /api/logout`

**Headers:**
```
Authorization: Bearer {tu-token-aquí}
Accept: application/json
```

**Respuesta exitosa (200):**
```json
{
  "message": "Sesión cerrada exitosamente"
}
```

**cURL:**
```bash
curl -X POST http://localhost/api/logout \
  -H "Authorization: Bearer 2|zyxwvutsrqponmlkjihgfedcba654321" \
  -H "Accept: application/json"
```

---

### 5. Logout All (Revocar Todos los Tokens)

**Endpoint:** `POST /api/logout-all`

**Headers:**
```
Authorization: Bearer {tu-token-aquí}
Accept: application/json
```

**Respuesta exitosa (200):**
```json
{
  "message": "Todas las sesiones cerradas exitosamente"
}
```

**cURL:**
```bash
curl -X POST http://localhost/api/logout-all \
  -H "Authorization: Bearer 2|zyxwvutsrqponmlkjihgfedcba654321" \
  -H "Accept: application/json"
```

---

## Errores Comunes

### Error 401 - No Autenticado
```json
{
  "message": "Unauthenticated."
}
```
**Solución:** Asegúrate de incluir el header `Authorization: Bearer {token}` en las peticiones protegidas.

### Error 422 - Validación Fallida
```json
{
  "message": "Las credenciales proporcionadas son incorrectas.",
  "errors": {
    "email": [
      "Las credenciales proporcionadas son incorrectas."
    ]
  }
}
```

### Error 419 - CSRF Token Mismatch (Solo para SPA)
Si estás construyendo un SPA, primero debes obtener un cookie CSRF:
```bash
curl -X GET http://localhost/sanctum/csrf-cookie \
  --cookie-jar cookies.txt
```

Luego usa ese cookie en tus peticiones subsecuentes.

---

## Testing con Postman

1. **Crear Colección**
   - Nombre: "Farmacias IMD API"
   - Base URL: `http://localhost/api`

2. **Variables de Entorno**
   - `base_url`: `http://localhost/api`
   - `token`: (se actualizará después del login)

3. **Configurar Auto-Token**
   En la pestaña "Tests" de la petición de login, agrega:
   ```javascript
   pm.environment.set("token", pm.response.json().token);
   ```

4. **Header Global**
   En las peticiones protegidas, usa:
   ```
   Authorization: Bearer {{token}}
   ```

---

## Testing con JavaScript (Fetch API)

```javascript
// Login
const loginResponse = await fetch('http://localhost/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: JSON.stringify({
    email: 'juan@ejemplo.com',
    password: 'password123'
  })
});

const { token } = await loginResponse.json();

// Usar el token en peticiones subsecuentes
const userResponse = await fetch('http://localhost/api/me', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
  }
});

const { user } = await userResponse.json();
console.log(user);
```

---

## CORS

Si vas a consumir esta API desde un frontend en otro dominio, necesitas configurar CORS en `config/cors.php`.
