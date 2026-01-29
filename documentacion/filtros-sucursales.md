# 🔍 Filtros y Búsqueda de Sucursales - API de Facturación Electrónica SUNAT

Guía completa de endpoints, filtros y búsqueda de sucursales empresariales.

## 📑 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Endpoints Disponibles](#endpoints-disponibles)
3. [Listar Sucursales con Filtros](#listar-sucursales-con-filtros)
4. [Búsqueda por Código](#búsqueda-por-código)
5. [Búsqueda por Ubigeo](#búsqueda-por-ubigeo)
6. [Ejemplos de Peticiones](#ejemplos-de-peticiones)
7. [Ejemplos con cURL](#ejemplos-con-curl)
8. [Ejemplos con JavaScript/Fetch](#ejemplos-con-javascriptfetch)
9. [Ejemplos con PHP](#ejemplos-con-php)
10. [Casos de Uso Comunes](#casos-de-uso-comunes)
11. [Respuestas y Códigos de Estado](#respuestas-y-códigos-de-estado)

---

## Introducción

El sistema de filtros de sucursales permite buscar y filtrar establecimientos empresariales mediante múltiples criterios. Los filtros están optimizados para búsquedas rápidas y eficientes.

### Características Principales

✅ Búsqueda por múltiples campos simultáneamente
✅ Búsqueda general (search) en todos los campos
✅ Filtros exactos (ubigeo) y parciales (nombre, distrito)
✅ Ordenamiento personalizable
✅ Paginación opcional
✅ Endpoints específicos para código y ubigeo

---

## Endpoints Disponibles

### Base URL
```
{{base_url}}/api/v1
```

### Autenticación
Todos los endpoints requieren autenticación mediante token Bearer:
```
Authorization: Bearer {your_access_token}
```

### Lista de Endpoints

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/companies/{company_id}/branches` | Listar sucursales con filtros avanzados |
| `GET` | `/companies/{company_id}/branches/search/codigo` | Buscar por código exacto |
| `GET` | `/companies/{company_id}/branches/search/ubigeo` | Buscar por ubigeo |

---

## Listar Sucursales con Filtros

**Endpoint:** `GET /api/v1/companies/{company_id}/branches`

Este es el endpoint principal para listar y filtrar sucursales de una empresa específica.

### Parámetros de URL

| Parámetro | Tipo | Descripción | Ejemplo |
|-----------|------|-------------|---------|
| `company_id` | integer | **Requerido.** ID de la empresa | `1` |

### Parámetros de Query (Todos Opcionales)

| Parámetro | Tipo | Descripción | Búsqueda | Ejemplo |
|-----------|------|-------------|----------|---------|
| `codigo` | string | Código de sucursal | Parcial (LIKE) | `?codigo=0001` |
| `ubigeo` | string | Código de ubigeo (6 dígitos) | Exacta | `?ubigeo=150101` |
| `nombre` | string | Nombre de la sucursal | Parcial (LIKE) | `?nombre=Principal` |
| `distrito` | string | Distrito | Parcial (LIKE) | `?distrito=Lima` |
| `provincia` | string | Provincia | Parcial (LIKE) | `?provincia=Lima` |
| `departamento` | string | Departamento | Parcial (LIKE) | `?departamento=Lima` |
| `activo` | boolean | Estado activo/inactivo | Exacta | `?activo=true` |
| `search` | string | Búsqueda general (todos los campos) | Parcial (LIKE) | `?search=miraflores` |
| `sort_by` | string | Campo para ordenar | - | `?sort_by=nombre` |
| `sort_order` | string | Orden: `asc` o `desc` | - | `?sort_order=asc` |
| `per_page` | integer | Resultados por página (máx 100) | - | `?per_page=10` |
| `page` | integer | Número de página | - | `?page=1` |

### Campos Válidos para `sort_by`

- `id`
- `codigo`
- `nombre`
- `distrito`
- `provincia`
- `departamento`
- `created_at`
- `updated_at`

**Default:** `nombre`

### Respuesta Exitosa (Sin Paginación)

**Status:** `200 OK`

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "company_id": 1,
      "codigo": "0001",
      "nombre": "Sucursal Principal",
      "direccion": "Av. Larco 1234, Piso 5",
      "ubigeo": "150122",
      "distrito": "Miraflores",
      "provincia": "Lima",
      "departamento": "Lima",
      "telefono": "01-2345678",
      "email": "miraflores@empresa.com",
      "series_factura": "F001,F002",
      "series_boleta": "B001,B002",
      "series_nota_credito": "FC01",
      "series_nota_debito": "FD01",
      "series_guia_remision": "T001",
      "activo": true,
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-20T14:25:00.000000Z"
    },
    {
      "id": 2,
      "company_id": 1,
      "codigo": "0002",
      "nombre": "Sucursal San Isidro",
      "direccion": "Av. Javier Prado 456",
      "ubigeo": "150131",
      "distrito": "San Isidro",
      "provincia": "Lima",
      "departamento": "Lima",
      "telefono": "01-3456789",
      "email": "sanisidro@empresa.com",
      "series_factura": "F001",
      "series_boleta": "B001",
      "series_nota_credito": null,
      "series_nota_debito": null,
      "series_guia_remision": null,
      "activo": true,
      "created_at": "2024-01-16T09:15:00.000000Z",
      "updated_at": "2024-01-16T09:15:00.000000Z"
    }
  ],
  "meta": {
    "company_id": 1,
    "company_name": "EMPRESA DEMO SAC",
    "total_branches": 2,
    "active_branches": 2
  }
}
```

### Respuesta Exitosa (Con Paginación)

**Status:** `200 OK`

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "codigo": "0001",
      "nombre": "Sucursal Principal",
      "...": "..."
    }
  ],
  "meta": {
    "company_id": 1,
    "company_name": "EMPRESA DEMO SAC",
    "current_page": 1,
    "per_page": 10,
    "total": 25,
    "last_page": 3,
    "from": 1,
    "to": 10
  }
}
```

### Ejemplo 1: Listar todas las sucursales

```http
GET /api/v1/companies/1/branches
Authorization: Bearer {token}
```

### Ejemplo 2: Buscar por código

```http
GET /api/v1/companies/1/branches?codigo=0001
Authorization: Bearer {token}
```

### Ejemplo 3: Filtrar sucursales activas en Lima

```http
GET /api/v1/companies/1/branches?departamento=Lima&activo=true
Authorization: Bearer {token}
```

### Ejemplo 4: Búsqueda general

```http
GET /api/v1/companies/1/branches?search=miraflores
Authorization: Bearer {token}
```

### Ejemplo 5: Con paginación y ordenamiento

```http
GET /api/v1/companies/1/branches?sort_by=codigo&sort_order=asc&per_page=10&page=1
Authorization: Bearer {token}
```

### Ejemplo 6: Múltiples filtros combinados

```http
GET /api/v1/companies/1/branches?departamento=Lima&distrito=Miraflores&activo=true&sort_by=nombre
Authorization: Bearer {token}
```

---

## Búsqueda por Código

**Endpoint:** `GET /api/v1/companies/{company_id}/branches/search/codigo`

Busca una sucursal específica por su código exacto. Retorna un solo resultado.

### Parámetros de URL

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `company_id` | integer | **Requerido.** ID de la empresa |

### Parámetros de Query

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `codigo` | string | Sí | Código exacto de la sucursal |

### Petición

```http
GET /api/v1/companies/1/branches/search/codigo?codigo=0001
Authorization: Bearer {token}
```

### Respuesta Exitosa

**Status:** `200 OK`

```json
{
  "success": true,
  "data": {
    "id": 1,
    "company_id": 1,
    "codigo": "0001",
    "nombre": "Sucursal Principal",
    "direccion": "Av. Larco 1234",
    "ubigeo": "150122",
    "distrito": "Miraflores",
    "provincia": "Lima",
    "departamento": "Lima",
    "telefono": "01-2345678",
    "email": "miraflores@empresa.com",
    "series_factura": "F001,F002",
    "series_boleta": "B001",
    "series_nota_credito": "FC01",
    "series_nota_debito": "FD01",
    "series_guia_remision": "T001",
    "activo": true,
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-20T14:25:00.000000Z"
  }
}
```

### Respuesta Error - No Encontrada

**Status:** `404 Not Found`

```json
{
  "success": false,
  "message": "No se encontró ninguna sucursal con el código proporcionado"
}
```

### Respuesta Error - Parámetro Faltante

**Status:** `400 Bad Request`

```json
{
  "success": false,
  "message": "El parámetro \"codigo\" es requerido"
}
```

---

## Búsqueda por Ubigeo

**Endpoint:** `GET /api/v1/companies/{company_id}/branches/search/ubigeo`

Busca todas las sucursales ubicadas en un ubigeo específico. Puede retornar múltiples resultados.

### Parámetros de URL

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `company_id` | integer | **Requerido.** ID de la empresa |

### Parámetros de Query

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `ubigeo` | string | Sí | Código de ubigeo (6 dígitos) |

### Petición

```http
GET /api/v1/companies/1/branches/search/ubigeo?ubigeo=150122
Authorization: Bearer {token}
```

### Respuesta Exitosa

**Status:** `200 OK`

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "company_id": 1,
      "codigo": "0001",
      "nombre": "Sucursal Principal",
      "direccion": "Av. Larco 1234",
      "ubigeo": "150122",
      "distrito": "Miraflores",
      "provincia": "Lima",
      "departamento": "Lima",
      "telefono": "01-2345678",
      "email": "miraflores@empresa.com",
      "series_factura": "F001,F002",
      "series_boleta": "B001",
      "activo": true,
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-20T14:25:00.000000Z"
    },
    {
      "id": 3,
      "company_id": 1,
      "codigo": "0003",
      "nombre": "Sucursal Miraflores 2",
      "direccion": "Calle Los Pinos 890",
      "ubigeo": "150122",
      "distrito": "Miraflores",
      "provincia": "Lima",
      "departamento": "Lima",
      "telefono": "01-4567890",
      "email": "miraflores2@empresa.com",
      "series_factura": "F003",
      "series_boleta": "B003",
      "activo": true,
      "created_at": "2024-01-18T11:00:00.000000Z",
      "updated_at": "2024-01-18T11:00:00.000000Z"
    }
  ],
  "meta": {
    "company_id": 1,
    "ubigeo": "150122",
    "total": 2
  }
}
```

### Respuesta Exitosa - Sin Resultados

**Status:** `200 OK`

```json
{
  "success": true,
  "data": [],
  "meta": {
    "company_id": 1,
    "ubigeo": "150122",
    "total": 0
  }
}
```

### Respuesta Error - Parámetro Faltante

**Status:** `400 Bad Request`

```json
{
  "success": false,
  "message": "El parámetro \"ubigeo\" es requerido"
}
```

---

## Ejemplos de Peticiones

### Postman Collection

#### 1. Listar Todas las Sucursales

```
GET {{base_url}}/api/v1/companies/{{company_id}}/branches
Authorization: Bearer {{token}}
```

#### 2. Filtrar por Departamento

```
GET {{base_url}}/api/v1/companies/{{company_id}}/branches?departamento=Lima
Authorization: Bearer {{token}}
```

#### 3. Filtrar por Distrito y Estado

```
GET {{base_url}}/api/v1/companies/{{company_id}}/branches?distrito=Miraflores&activo=true
Authorization: Bearer {{token}}
```

#### 4. Búsqueda General

```
GET {{base_url}}/api/v1/companies/{{company_id}}/branches?search=principal
Authorization: Bearer {{token}}
```

#### 5. Con Paginación

```
GET {{base_url}}/api/v1/companies/{{company_id}}/branches?per_page=5&page=1
Authorization: Bearer {{token}}
```

#### 6. Buscar por Código

```
GET {{base_url}}/api/v1/companies/{{company_id}}/branches/search/codigo?codigo=0001
Authorization: Bearer {{token}}
```

#### 7. Buscar por Ubigeo

```
GET {{base_url}}/api/v1/companies/{{company_id}}/branches/search/ubigeo?ubigeo=150122
Authorization: Bearer {{token}}
```

---

## Ejemplos con cURL

### Ejemplo 1: Listar Todas las Sucursales

```bash
curl -X GET "https://api.ejemplo.com/api/v1/companies/1/branches" \
  -H "Authorization: Bearer tu_token_aqui" \
  -H "Accept: application/json"
```

### Ejemplo 2: Filtrar por Código

```bash
curl -X GET "https://api.ejemplo.com/api/v1/companies/1/branches?codigo=0001" \
  -H "Authorization: Bearer tu_token_aqui" \
  -H "Accept: application/json"
```

### Ejemplo 3: Filtrar Sucursales Activas en Lima

```bash
curl -X GET "https://api.ejemplo.com/api/v1/companies/1/branches?departamento=Lima&activo=true" \
  -H "Authorization: Bearer tu_token_aqui" \
  -H "Accept: application/json"
```

### Ejemplo 4: Búsqueda General

```bash
curl -X GET "https://api.ejemplo.com/api/v1/companies/1/branches?search=miraflores" \
  -H "Authorization: Bearer tu_token_aqui" \
  -H "Accept: application/json"
```

### Ejemplo 5: Con Paginación

```bash
curl -X GET "https://api.ejemplo.com/api/v1/companies/1/branches?per_page=10&page=1&sort_by=nombre&sort_order=asc" \
  -H "Authorization: Bearer tu_token_aqui" \
  -H "Accept: application/json"
```

### Ejemplo 6: Buscar por Código Exacto

```bash
curl -X GET "https://api.ejemplo.com/api/v1/companies/1/branches/search/codigo?codigo=0001" \
  -H "Authorization: Bearer tu_token_aqui" \
  -H "Accept: application/json"
```

### Ejemplo 7: Buscar por Ubigeo

```bash
curl -X GET "https://api.ejemplo.com/api/v1/companies/1/branches/search/ubigeo?ubigeo=150122" \
  -H "Authorization: Bearer tu_token_aqui" \
  -H "Accept: application/json"
```

---

## Ejemplos con JavaScript/Fetch

### Configuración Base

```javascript
const API_BASE_URL = 'https://api.ejemplo.com/api/v1';
const TOKEN = 'tu_token_aqui';
const COMPANY_ID = 1;

const headers = {
  'Authorization': `Bearer ${TOKEN}`,
  'Accept': 'application/json',
  'Content-Type': 'application/json'
};
```

### Ejemplo 1: Listar Todas las Sucursales

```javascript
async function getAllBranches() {
  try {
    const response = await fetch(
      `${API_BASE_URL}/companies/${COMPANY_ID}/branches`,
      { headers }
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    console.log('Sucursales:', data.data);
    console.log('Total:', data.meta.total_branches);
    return data;
  } catch (error) {
    console.error('Error al obtener sucursales:', error);
    throw error;
  }
}

// Uso
getAllBranches();
```

### Ejemplo 2: Filtrar por Departamento

```javascript
async function getBranchesByDepartamento(departamento) {
  try {
    const params = new URLSearchParams({ departamento });
    const response = await fetch(
      `${API_BASE_URL}/companies/${COMPANY_ID}/branches?${params}`,
      { headers }
    );

    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}

// Uso
getBranchesByDepartamento('Lima');
```

### Ejemplo 3: Búsqueda General

```javascript
async function searchBranches(searchTerm) {
  try {
    const params = new URLSearchParams({ search: searchTerm });
    const response = await fetch(
      `${API_BASE_URL}/companies/${COMPANY_ID}/branches?${params}`,
      { headers }
    );

    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}

// Uso
searchBranches('miraflores');
```

### Ejemplo 4: Filtros Múltiples

```javascript
async function getBranchesWithFilters(filters) {
  try {
    const params = new URLSearchParams(filters);
    const response = await fetch(
      `${API_BASE_URL}/companies/${COMPANY_ID}/branches?${params}`,
      { headers }
    );

    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}

// Uso
getBranchesWithFilters({
  departamento: 'Lima',
  distrito: 'Miraflores',
  activo: true,
  sort_by: 'nombre',
  sort_order: 'asc'
});
```

### Ejemplo 5: Con Paginación

```javascript
async function getBranchesPaginated(page = 1, perPage = 10) {
  try {
    const params = new URLSearchParams({
      page,
      per_page: perPage,
      sort_by: 'nombre'
    });

    const response = await fetch(
      `${API_BASE_URL}/companies/${COMPANY_ID}/branches?${params}`,
      { headers }
    );

    const data = await response.json();

    console.log(`Página ${data.meta.current_page} de ${data.meta.last_page}`);
    console.log(`Mostrando ${data.meta.from}-${data.meta.to} de ${data.meta.total}`);

    return data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}

// Uso
getBranchesPaginated(1, 10);
```

### Ejemplo 6: Buscar por Código

```javascript
async function getBranchByCodigo(codigo) {
  try {
    const params = new URLSearchParams({ codigo });
    const response = await fetch(
      `${API_BASE_URL}/companies/${COMPANY_ID}/branches/search/codigo?${params}`,
      { headers }
    );

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message);
    }

    const data = await response.json();
    return data.data; // Retorna directamente la sucursal
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}

// Uso
getBranchByCodigo('0001')
  .then(branch => console.log('Sucursal:', branch))
  .catch(error => console.error('No encontrada:', error.message));
```

### Ejemplo 7: Buscar por Ubigeo

```javascript
async function getBranchesByUbigeo(ubigeo) {
  try {
    const params = new URLSearchParams({ ubigeo });
    const response = await fetch(
      `${API_BASE_URL}/companies/${COMPANY_ID}/branches/search/ubigeo?${params}`,
      { headers }
    );

    const data = await response.json();
    console.log(`Encontradas ${data.meta.total} sucursales en ubigeo ${ubigeo}`);
    return data.data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}

// Uso
getBranchesByUbigeo('150122');
```

### Ejemplo 8: Componente React de Búsqueda

```jsx
import React, { useState, useEffect } from 'react';

function BranchSearch({ companyId, token }) {
  const [branches, setBranches] = useState([]);
  const [loading, setLoading] = useState(false);
  const [filters, setFilters] = useState({
    search: '',
    departamento: '',
    activo: 'true'
  });

  useEffect(() => {
    searchBranches();
  }, [filters]);

  const searchBranches = async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams(
        Object.entries(filters).filter(([_, v]) => v !== '')
      );

      const response = await fetch(
        `https://api.ejemplo.com/api/v1/companies/${companyId}/branches?${params}`,
        {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        }
      );

      const data = await response.json();
      setBranches(data.data);
    } catch (error) {
      console.error('Error:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <input
        type="text"
        placeholder="Buscar..."
        value={filters.search}
        onChange={(e) => setFilters({ ...filters, search: e.target.value })}
      />

      <select
        value={filters.departamento}
        onChange={(e) => setFilters({ ...filters, departamento: e.target.value })}
      >
        <option value="">Todos los departamentos</option>
        <option value="Lima">Lima</option>
        <option value="Arequipa">Arequipa</option>
      </select>

      {loading ? (
        <p>Cargando...</p>
      ) : (
        <ul>
          {branches.map(branch => (
            <li key={branch.id}>
              {branch.codigo} - {branch.nombre} ({branch.distrito})
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
```

---

## Ejemplos con PHP

### Configuración Base

```php
<?php

class BranchAPI {
    private $baseUrl = 'https://api.ejemplo.com/api/v1';
    private $token;
    private $companyId;

    public function __construct($token, $companyId) {
        $this->token = $token;
        $this->companyId = $companyId;
    }

    private function request($endpoint, $params = []) {
        $url = $this->baseUrl . $endpoint;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->token,
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new Exception($data['message'] ?? 'Error en la petición');
        }

        return $data;
    }
}
```

### Ejemplo 1: Listar Todas las Sucursales

```php
public function getAllBranches() {
    $endpoint = "/companies/{$this->companyId}/branches";
    return $this->request($endpoint);
}

// Uso
$api = new BranchAPI('tu_token', 1);
$result = $api->getAllBranches();

echo "Total sucursales: " . $result['meta']['total_branches'] . "\n";
foreach ($result['data'] as $branch) {
    echo "- {$branch['codigo']}: {$branch['nombre']}\n";
}
```

### Ejemplo 2: Filtrar por Departamento

```php
public function getBranchesByDepartamento($departamento) {
    $endpoint = "/companies/{$this->companyId}/branches";
    $params = ['departamento' => $departamento];
    return $this->request($endpoint, $params);
}

// Uso
$result = $api->getBranchesByDepartamento('Lima');
```

### Ejemplo 3: Búsqueda General

```php
public function searchBranches($searchTerm) {
    $endpoint = "/companies/{$this->companyId}/branches";
    $params = ['search' => $searchTerm];
    return $this->request($endpoint, $params);
}

// Uso
$result = $api->searchBranches('miraflores');
```

### Ejemplo 4: Filtros Múltiples

```php
public function getBranchesWithFilters($filters) {
    $endpoint = "/companies/{$this->companyId}/branches";
    return $this->request($endpoint, $filters);
}

// Uso
$result = $api->getBranchesWithFilters([
    'departamento' => 'Lima',
    'distrito' => 'Miraflores',
    'activo' => true,
    'sort_by' => 'nombre',
    'sort_order' => 'asc'
]);
```

### Ejemplo 5: Con Paginación

```php
public function getBranchesPaginated($page = 1, $perPage = 10) {
    $endpoint = "/companies/{$this->companyId}/branches";
    $params = [
        'page' => $page,
        'per_page' => $perPage,
        'sort_by' => 'nombre'
    ];
    return $this->request($endpoint, $params);
}

// Uso
$result = $api->getBranchesPaginated(1, 10);

echo "Página {$result['meta']['current_page']} de {$result['meta']['last_page']}\n";
echo "Mostrando {$result['meta']['from']}-{$result['meta']['to']} de {$result['meta']['total']}\n";
```

### Ejemplo 6: Buscar por Código

```php
public function getBranchByCodigo($codigo) {
    $endpoint = "/companies/{$this->companyId}/branches/search/codigo";
    $params = ['codigo' => $codigo];

    try {
        $result = $this->request($endpoint, $params);
        return $result['data'];
    } catch (Exception $e) {
        return null;
    }
}

// Uso
$branch = $api->getBranchByCodigo('0001');
if ($branch) {
    echo "Sucursal encontrada: {$branch['nombre']}\n";
} else {
    echo "Sucursal no encontrada\n";
}
```

### Ejemplo 7: Buscar por Ubigeo

```php
public function getBranchesByUbigeo($ubigeo) {
    $endpoint = "/companies/{$this->companyId}/branches/search/ubigeo";
    $params = ['ubigeo' => $ubigeo];
    return $this->request($endpoint, $params);
}

// Uso
$result = $api->getBranchesByUbigeo('150122');
echo "Encontradas {$result['meta']['total']} sucursales en ubigeo 150122\n";
```

### Ejemplo 8: Clase Completa con Manejo de Errores

```php
<?php

class BranchService {
    private $api;

    public function __construct($token, $companyId) {
        $this->api = new BranchAPI($token, $companyId);
    }

    public function findActiveBranchesInDistrict($distrito) {
        try {
            return $this->api->getBranchesWithFilters([
                'distrito' => $distrito,
                'activo' => true
            ]);
        } catch (Exception $e) {
            error_log("Error buscando sucursales: " . $e->getMessage());
            return ['success' => false, 'data' => [], 'meta' => []];
        }
    }

    public function getAvailableSeries($branchCodigo) {
        try {
            $branch = $this->api->getBranchByCodigo($branchCodigo);

            if (!$branch) {
                return [];
            }

            return [
                'facturas' => $branch['series_factura'] ? explode(',', $branch['series_factura']) : [],
                'boletas' => $branch['series_boleta'] ? explode(',', $branch['series_boleta']) : [],
                'notas_credito' => $branch['series_nota_credito'] ? explode(',', $branch['series_nota_credito']) : [],
                'notas_debito' => $branch['series_nota_debito'] ? explode(',', $branch['series_nota_debito']) : [],
                'guias_remision' => $branch['series_guia_remision'] ? explode(',', $branch['series_guia_remision']) : []
            ];
        } catch (Exception $e) {
            error_log("Error obteniendo series: " . $e->getMessage());
            return [];
        }
    }
}

// Uso
$service = new BranchService('tu_token', 1);

// Buscar sucursales activas en Miraflores
$branches = $service->findActiveBranchesInDistrict('Miraflores');
print_r($branches);

// Obtener series disponibles de una sucursal
$series = $service->getAvailableSeries('0001');
print_r($series);
```

---

## Casos de Uso Comunes

### Caso 1: Selector de Sucursales para Formulario

**Necesidad:** Listar todas las sucursales activas para un selector dropdown.

**Solución:**
```javascript
async function loadBranchSelector() {
  const response = await fetch(
    `${API_BASE_URL}/companies/${COMPANY_ID}/branches?activo=true&sort_by=nombre&sort_order=asc`,
    { headers }
  );

  const data = await response.json();

  const select = document.getElementById('branch-select');
  data.data.forEach(branch => {
    const option = document.createElement('option');
    option.value = branch.id;
    option.textContent = `${branch.codigo} - ${branch.nombre}`;
    select.appendChild(option);
  });
}
```

### Caso 2: Buscar Sucursal Más Cercana por Ubigeo

**Necesidad:** Encontrar sucursales en el mismo distrito del cliente.

**Solución:**
```php
function findNearestBranches($clientUbigeo) {
    $api = new BranchAPI('token', 1);
    $result = $api->getBranchesByUbigeo($clientUbigeo);

    if ($result['meta']['total'] > 0) {
        return $result['data'];
    }

    // Si no hay en el mismo ubigeo, buscar en el mismo distrito
    // Extraer distrito del ubigeo y buscar
    return [];
}
```

### Caso 3: Tabla de Sucursales con Paginación

**Necesidad:** Mostrar lista paginada de sucursales con filtros.

**Solución:**
```javascript
class BranchTable {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
    this.currentPage = 1;
    this.perPage = 10;
    this.filters = {};
  }

  async load() {
    const params = {
      page: this.currentPage,
      per_page: this.perPage,
      ...this.filters
    };

    const queryString = new URLSearchParams(params).toString();
    const response = await fetch(
      `${API_BASE_URL}/companies/${COMPANY_ID}/branches?${queryString}`,
      { headers }
    );

    const data = await response.json();
    this.render(data);
  }

  render(data) {
    // Renderizar tabla con data.data
    // Renderizar paginación con data.meta
  }

  setFilter(key, value) {
    this.filters[key] = value;
    this.currentPage = 1;
    this.load();
  }

  nextPage() {
    this.currentPage++;
    this.load();
  }

  prevPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
      this.load();
    }
  }
}
```

### Caso 4: Autocompletado de Búsqueda

**Necesidad:** Búsqueda en tiempo real mientras el usuario escribe.

**Solución:**
```javascript
let searchTimeout;

function setupBranchSearch() {
  const input = document.getElementById('branch-search');

  input.addEventListener('input', (e) => {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(async () => {
      const searchTerm = e.target.value;

      if (searchTerm.length < 2) return;

      const response = await fetch(
        `${API_BASE_URL}/companies/${COMPANY_ID}/branches?search=${searchTerm}`,
        { headers }
      );

      const data = await response.json();
      displaySearchResults(data.data);
    }, 300); // Debounce 300ms
  });
}

function displaySearchResults(branches) {
  const results = document.getElementById('search-results');
  results.innerHTML = '';

  branches.forEach(branch => {
    const item = document.createElement('div');
    item.className = 'search-result-item';
    item.textContent = `${branch.codigo} - ${branch.nombre}`;
    item.onclick = () => selectBranch(branch);
    results.appendChild(item);
  });
}
```

### Caso 5: Validar Código de Sucursal Único

**Necesidad:** Verificar que un código de sucursal no esté duplicado.

**Solución:**
```javascript
async function validateBranchCode(codigo) {
  try {
    const response = await fetch(
      `${API_BASE_URL}/companies/${COMPANY_ID}/branches/search/codigo?codigo=${codigo}`,
      { headers }
    );

    if (response.status === 404) {
      return { valid: true, message: 'Código disponible' };
    }

    if (response.ok) {
      return { valid: false, message: 'Código ya existe' };
    }

    throw new Error('Error al validar código');
  } catch (error) {
    return { valid: false, message: error.message };
  }
}

// Uso en formulario
document.getElementById('codigo').addEventListener('blur', async (e) => {
  const result = await validateBranchCode(e.target.value);
  document.getElementById('codigo-error').textContent = result.message;
  document.getElementById('codigo-error').className = result.valid ? 'success' : 'error';
});
```

---

## Respuestas y Códigos de Estado

### Códigos de Estado HTTP

| Código | Descripción | Cuándo Ocurre |
|--------|-------------|---------------|
| `200 OK` | Petición exitosa | Búsqueda exitosa con o sin resultados |
| `400 Bad Request` | Parámetros inválidos | Falta parámetro requerido o formato incorrecto |
| `401 Unauthorized` | No autenticado | Token inválido o expirado |
| `404 Not Found` | Recurso no encontrado | Búsqueda por código sin resultado |
| `500 Internal Server Error` | Error del servidor | Error interno del sistema |

### Estructura de Respuesta Error

```json
{
  "success": false,
  "message": "Descripción del error"
}
```

### Errores Comunes

#### Error 1: Token Inválido

**Código:** `401 Unauthorized`

```json
{
  "message": "Unauthenticated."
}
```

**Solución:** Verificar que el token sea válido y esté en el header de autenticación.

#### Error 2: Parámetro Requerido Faltante

**Código:** `400 Bad Request`

```json
{
  "success": false,
  "message": "El parámetro \"codigo\" es requerido"
}
```

**Solución:** Incluir el parámetro requerido en la query string.

#### Error 3: Empresa No Encontrada

**Código:** `404 Not Found`

```json
{
  "success": false,
  "message": "No query results for model [App\\Models\\Company] 999"
}
```

**Solución:** Verificar que el `company_id` sea válido.

---

## Buenas Prácticas

### 1. Optimización de Peticiones

✅ **Usar paginación** para grandes volúmenes de datos:
```javascript
?per_page=20&page=1
```

✅ **Usar búsqueda específica** cuando sea posible:
```javascript
// Mejor
/branches/search/codigo?codigo=0001

// En lugar de
/branches?codigo=0001
```

✅ **Limitar campos** si el API lo soporta (futuro):
```javascript
?fields=id,codigo,nombre
```

### 2. Manejo de Errores

```javascript
async function safeBranchSearch(params) {
  try {
    const response = await fetch(url, { headers });

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Error desconocido');
    }

    return await response.json();
  } catch (error) {
    console.error('Error en búsqueda:', error);

    // Mostrar mensaje al usuario
    showNotification('Error al buscar sucursales', 'error');

    // Retornar datos vacíos en lugar de fallar
    return { success: false, data: [], meta: {} };
  }
}
```

### 3. Caché de Resultados

```javascript
class BranchCache {
  constructor(ttl = 300000) { // 5 minutos
    this.cache = new Map();
    this.ttl = ttl;
  }

  set(key, value) {
    this.cache.set(key, {
      value,
      timestamp: Date.now()
    });
  }

  get(key) {
    const item = this.cache.get(key);

    if (!item) return null;

    if (Date.now() - item.timestamp > this.ttl) {
      this.cache.delete(key);
      return null;
    }

    return item.value;
  }

  clear() {
    this.cache.clear();
  }
}

// Uso
const cache = new BranchCache();

async function getBranchesWithCache(filters) {
  const cacheKey = JSON.stringify(filters);
  const cached = cache.get(cacheKey);

  if (cached) {
    return cached;
  }

  const result = await fetchBranches(filters);
  cache.set(cacheKey, result);
  return result;
}
```

### 4. Debouncing para Búsqueda en Tiempo Real

```javascript
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

const debouncedSearch = debounce(async (searchTerm) => {
  const results = await searchBranches(searchTerm);
  displayResults(results);
}, 300);

// Uso
input.addEventListener('input', (e) => {
  debouncedSearch(e.target.value);
});
```

---

## Conclusión

El sistema de filtros y búsqueda de sucursales proporciona:

✅ **Flexibilidad:** Múltiples opciones de filtrado y búsqueda
✅ **Rendimiento:** Paginación y búsqueda optimizada
✅ **Facilidad:** Endpoints específicos para casos comunes
✅ **Escalabilidad:** Soporta grandes volúmenes de datos

Para documentación relacionada, consulta:
- [Sucursales y Correlativos](./Sucursales-correlativos.md)
- [Rutas y Endpoints API](./Rutas-enpoint-api.md)

---

**Última actualización:** Enero 2026
**Versión del API:** v1
