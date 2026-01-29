# 🏢 Sucursales y Correlativos - Sistema de Facturación Electrónica SUNAT

Documentación completa sobre la gestión de sucursales (branches) y sus correlativos de documentos electrónicos.

## 📑 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Sucursales (Branches)](#sucursales-branches)
   - [Endpoints de Sucursales](#endpoints-de-sucursales)
   - [Listar Sucursales con Filtros](#listar-sucursales-con-filtros)
   - [Búsqueda por Código](#búsqueda-por-código)
   - [Búsqueda por Ubigeo](#búsqueda-por-ubigeo)
   - [CRUD Completo](#crud-completo-de-sucursales)
3. [Correlativos](#correlativos)
   - [¿Qué son los Correlativos?](#qué-son-los-correlativos)
   - [Tipos de Documentos](#tipos-de-documentos-sunat)
   - [Gestión de Series](#gestión-de-series)
   - [Endpoints de Correlativos](#endpoints-de-correlativos)
   - [Creación Individual](#creación-individual-de-correlativos)
   - [Creación por Lote](#creación-por-lote-batch)
4. [Ejemplos Prácticos](#ejemplos-prácticos)
5. [Mejores Prácticas](#mejores-prácticas)

---

## Introducción

Las **sucursales** son establecimientos anexos donde una empresa realiza operaciones comerciales y emite comprobantes electrónicos. Cada sucursal puede tener múltiples **correlativos** (series de numeración) para diferentes tipos de documentos SUNAT.

### Conceptos Clave

- **Sucursal (Branch):** Establecimiento físico de la empresa identificado por un código único y ubigeo
- **Correlativo:** Sistema de numeración secuencial para documentos electrónicos
- **Serie:** Prefijo alfanumérico que identifica el tipo y origen del documento (Ej: F001, B001)
- **Ubigeo:** Código de 6 dígitos que identifica distrito, provincia y departamento según INEI

---

## Sucursales (Branches)

### Endpoints de Sucursales

Base URL: `{{base_url}}/api/v1`

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/branches` | Listar todas las sucursales |
| POST | `/branches` | Crear nueva sucursal |
| GET | `/branches/{id}` | Obtener sucursal específica |
| PUT/PATCH | `/branches/{id}` | Actualizar sucursal |
| DELETE | `/branches/{id}` | Desactivar sucursal |
| POST | `/branches/{id}/activate` | Activar sucursal |
| GET | `/companies/{company_id}/branches` | Listar sucursales de una empresa con filtros |
| GET | `/companies/{company_id}/branches/search/codigo` | Buscar por código exacto |
| GET | `/companies/{company_id}/branches/search/ubigeo` | Buscar por ubigeo |

---

### Listar Sucursales con Filtros

**Endpoint:** `GET /api/v1/companies/{company_id}/branches`

Este endpoint permite listar sucursales con múltiples opciones de filtrado y búsqueda.

#### Parámetros de Query (Todos opcionales)

| Parámetro | Tipo | Descripción | Ejemplo |
|-----------|------|-------------|---------|
| `codigo` | string | Búsqueda parcial por código | `?codigo=0001` |
| `ubigeo` | string | Búsqueda exacta por ubigeo | `?ubigeo=150101` |
| `nombre` | string | Búsqueda parcial por nombre | `?nombre=Principal` |
| `distrito` | string | Búsqueda parcial por distrito | `?distrito=Lima` |
| `provincia` | string | Búsqueda parcial por provincia | `?provincia=Lima` |
| `departamento` | string | Búsqueda parcial por departamento | `?departamento=Lima` |
| `activo` | boolean | Filtrar por estado | `?activo=true` |
| `search` | string | Búsqueda general en todos los campos | `?search=lima` |
| `sort_by` | string | Campo para ordenar (default: nombre) | `?sort_by=codigo` |
| `sort_order` | string | Orden: asc o desc (default: asc) | `?sort_order=desc` |
| `per_page` | int | Paginación (máx 100) | `?per_page=10` |

#### Ejemplos de Uso

**1. Listar todas las sucursales de una empresa:**
```http
GET /api/v1/companies/1/branches
```

**2. Buscar sucursales en Lima:**
```http
GET /api/v1/companies/1/branches?departamento=Lima
```

**3. Buscar sucursales activas en Lima ordenadas por código:**
```http
GET /api/v1/companies/1/branches?departamento=Lima&activo=true&sort_by=codigo&sort_order=asc
```

**4. Búsqueda general (busca en múltiples campos):**
```http
GET /api/v1/companies/1/branches?search=san isidro
```

**5. Con paginación:**
```http
GET /api/v1/companies/1/branches?per_page=10&page=1
```

**6. Combinar múltiples filtros:**
```http
GET /api/v1/companies/1/branches?distrito=Miraflores&provincia=Lima&activo=true
```

#### Respuesta Exitosa

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
      "series_boleta": "B001,B002",
      "series_nota_credito": "FC01",
      "series_nota_debito": "FD01",
      "series_guia_remision": "T001",
      "activo": true,
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  ],
  "meta": {
    "company_id": 1,
    "company_name": "EMPRESA SAC",
    "total_branches": 5,
    "active_branches": 4
  }
}
```

#### Respuesta con Paginación

```json
{
  "success": true,
  "data": [...],
  "meta": {
    "company_id": 1,
    "company_name": "EMPRESA SAC",
    "current_page": 1,
    "per_page": 10,
    "total": 25,
    "last_page": 3,
    "from": 1,
    "to": 10
  }
}
```

---

### Búsqueda por Código

**Endpoint:** `GET /api/v1/companies/{company_id}/branches/search/codigo`

Busca una sucursal específica por su código exacto.

#### Parámetros

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `codigo` | string | Sí | Código exacto de la sucursal |

#### Ejemplo

```http
GET /api/v1/companies/1/branches/search/codigo?codigo=0001
```

#### Respuesta Exitosa

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
    "series_factura": "F001,F002",
    "activo": true
  }
}
```

#### Respuesta Error (No encontrada)

```json
{
  "success": false,
  "message": "No se encontró ninguna sucursal con el código proporcionado"
}
```

---

### Búsqueda por Ubigeo

**Endpoint:** `GET /api/v1/companies/{company_id}/branches/search/ubigeo`

Busca todas las sucursales ubicadas en un ubigeo específico.

#### Parámetros

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `ubigeo` | string | Sí | Código de ubigeo (6 dígitos) |

#### Ejemplo

```http
GET /api/v1/companies/1/branches/search/ubigeo?ubigeo=150122
```

#### Respuesta Exitosa

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "codigo": "0001",
      "nombre": "Sucursal Miraflores",
      "ubigeo": "150122",
      "distrito": "Miraflores"
    },
    {
      "id": 3,
      "codigo": "0003",
      "nombre": "Sucursal Miraflores 2",
      "ubigeo": "150122",
      "distrito": "Miraflores"
    }
  ],
  "meta": {
    "company_id": 1,
    "ubigeo": "150122",
    "total": 2
  }
}
```

---

### CRUD Completo de Sucursales

#### 1. Crear Sucursal

**Endpoint:** `POST /api/v1/branches`

**Body:**
```json
{
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
  "activo": true
}
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Sucursal creada exitosamente",
  "data": {
    "id": 1,
    "company_id": 1,
    "codigo": "0001",
    "nombre": "Sucursal Principal",
    "company": {
      "id": 1,
      "ruc": "20123456789",
      "razon_social": "EMPRESA SAC"
    }
  }
}
```

#### 2. Obtener Sucursal

**Endpoint:** `GET /api/v1/branches/{id}`

**Respuesta:**
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
    "activo": true,
    "company": {
      "id": 1,
      "ruc": "20123456789",
      "razon_social": "EMPRESA SAC",
      "nombre_comercial": "Empresa"
    }
  }
}
```

#### 3. Actualizar Sucursal

**Endpoint:** `PUT /api/v1/branches/{id}`

**Body:**
```json
{
  "nombre": "Sucursal Principal - Actualizada",
  "telefono": "01-9876543",
  "email": "nueva@empresa.com"
}
```

#### 4. Desactivar/Activar Sucursal

**Desactivar:** `DELETE /api/v1/branches/{id}`
**Activar:** `POST /api/v1/branches/{id}/activate`

---

## Correlativos

### ¿Qué son los Correlativos?

Los correlativos son sistemas de numeración secuencial para documentos electrónicos. Cada sucursal puede tener múltiples correlativos para diferentes tipos de documentos.

#### Estructura de un Correlativo

- **Tipo de Documento:** Código SUNAT (01, 03, 07, etc.)
- **Serie:** Prefijo de 4 caracteres (F001, B001, etc.)
- **Número Correlativo:** Secuencia numérica de 6 dígitos

**Ejemplo:** `F001-000123`
- Serie: `F001`
- Correlativo: `000123`

---

### Tipos de Documentos SUNAT

| Código | Tipo de Documento | Series Comunes |
|--------|-------------------|----------------|
| 01 | Factura | F001, F002, F003 |
| 03 | Boleta de Venta | B001, B002 |
| 07 | Nota de Crédito | FC01, BC01 |
| 08 | Nota de Débito | FD01, BD01 |
| 09 | Guía de Remisión | T001, T002 |
| 17 | Nota de Venta | NV01 |
| 20 | Comprobante de Retención | R001 |
| RC | Resumen de Anulaciones | RC-{fecha} |
| RA | Resumen Diario | RA-{fecha} |

---

### Gestión de Series

Las series se almacenan en la tabla `branches` en campos específicos por tipo de documento:

- `series_factura` - Series para facturas
- `series_boleta` - Series para boletas
- `series_nota_credito` - Series para notas de crédito
- `series_nota_debito` - Series para notas de débito
- `series_guia_remision` - Series para guías de remisión

#### Formato de Almacenamiento

Las series se guardan como **strings separados por comas** (NO como JSON):

✅ **Correcto:**
```
F001
F001,F002,F003
B001,B002
```

❌ **Incorrecto:**
```
["F001"]
["F001","F002"]
"F001"
```

> **Nota:** El sistema limpia automáticamente comillas y convierte formatos JSON legacy a strings limpios.

---

### Endpoints de Correlativos

Base URL: `{{base_url}}/api/v1`

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/branches/{branch_id}/correlatives` | Listar correlativos de una sucursal |
| POST | `/branches/{branch_id}/correlatives` | Crear correlativo individual |
| PUT | `/branches/{branch_id}/correlatives/{id}` | Actualizar correlativo |
| DELETE | `/branches/{branch_id}/correlatives/{id}` | Eliminar correlativo |
| POST | `/branches/{branch_id}/correlatives/batch` | Crear correlativos por lote |
| POST | `/branches/{branch_id}/correlatives/{id}/increment` | Incrementar correlativo |
| GET | `/correlatives/document-types` | Obtener tipos de documentos disponibles |

---

### Creación Individual de Correlativos

**Endpoint:** `POST /api/v1/branches/{branch_id}/correlatives`

#### Body

```json
{
  "tipo_documento": "01",
  "serie": "F001",
  "correlativo_inicial": 0
}
```

#### Parámetros

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `tipo_documento` | string | Sí | Código del tipo de documento (01, 03, etc.) |
| `serie` | string | Sí | Serie alfanumérica (4 caracteres, A-Z0-9) |
| `correlativo_inicial` | int | No | Número inicial del correlativo (default: 0) |

> **Nota:** Las comillas en la serie son automáticamente removidas. Puedes enviar `"F001"` o `F001`.

#### Respuesta Exitosa

```json
{
  "success": true,
  "message": "Correlativo creado exitosamente",
  "data": {
    "id": 1,
    "branch_id": 1,
    "tipo_documento": "01",
    "tipo_documento_nombre": "Factura",
    "serie": "F001",
    "correlativo_actual": 0,
    "numero_completo": "F001-000000",
    "proximo_numero": "F001-000001"
  }
}
```

#### Efectos Secundarios

Al crear un correlativo, **automáticamente se actualiza** el campo de series en la tabla `branches`:

```sql
-- Antes
series_factura: NULL

-- Después de crear correlativo tipo 01, serie F001
series_factura: "F001"

-- Después de crear otro correlativo tipo 01, serie F002
series_factura: "F001,F002"
```

---

### Creación por Lote (Batch)

**Endpoint:** `POST /api/v1/branches/{branch_id}/correlatives/batch`

Permite crear múltiples correlativos en una sola petición. Ideal para inicializar una sucursal nueva.

#### Body

```json
{
  "correlativos": [
    {
      "tipo_documento": "01",
      "serie": "F001",
      "correlativo_actual": 1
    },
    {
      "tipo_documento": "03",
      "serie": "B001",
      "correlativo_actual": 56
    },
    {
      "tipo_documento": "07",
      "serie": "FC01",
      "correlativo_actual": 1
    },
    {
      "tipo_documento": "08",
      "serie": "FD01",
      "correlativo_actual": 1
    },
    {
      "tipo_documento": "09",
      "serie": "T001",
      "correlativo_actual": 1
    },
    {
      "tipo_documento": "17",
      "serie": "NV01",
      "correlativo_actual": 0
    }
  ]
}
```

#### Campos Aceptados

Puedes usar **cualquiera** de estos campos para el valor inicial:
- `correlativo_inicial` (legacy)
- `correlativo_actual` (recomendado)

El sistema prioriza: `correlativo_actual` > `correlativo_inicial` > `0`

#### Respuesta Exitosa

```json
{
  "success": true,
  "message": "6 correlativos creados exitosamente",
  "data": {
    "created": [
      {
        "id": 1,
        "tipo_documento": "01",
        "tipo_documento_nombre": "Factura",
        "serie": "F001",
        "correlativo_actual": 1,
        "numero_completo": "F001-000001"
      },
      {
        "id": 2,
        "tipo_documento": "03",
        "tipo_documento_nombre": "Boleta de Venta",
        "serie": "B001",
        "correlativo_actual": 56,
        "numero_completo": "B001-000056"
      }
    ],
    "errors": [],
    "branch_series": {
      "series_factura": ["F001"],
      "series_boleta": ["B001"],
      "series_nota_credito": ["FC01"],
      "series_nota_debito": ["FD01"],
      "series_guia_remision": ["T001"]
    }
  },
  "meta": {
    "created_count": 6,
    "error_count": 0,
    "total_requested": 6
  }
}
```

#### Manejo de Errores

Si algunos correlativos ya existen, el sistema continúa procesando los demás:

```json
{
  "success": true,
  "message": "4 correlativos creados exitosamente",
  "data": {
    "created": [...],
    "errors": [
      {
        "index": 0,
        "error": "Ya existe correlativo para tipo 01 serie F001"
      },
      {
        "index": 2,
        "error": "Ya existe correlativo para tipo 07 serie FC01"
      }
    ]
  },
  "meta": {
    "created_count": 4,
    "error_count": 2,
    "total_requested": 6
  }
}
```

---

### Listar Correlativos

**Endpoint:** `GET /api/v1/branches/{branch_id}/correlatives`

#### Respuesta

```json
{
  "success": true,
  "data": {
    "branch": {
      "id": 1,
      "codigo": "0001",
      "nombre": "Sucursal Principal",
      "company_id": 1
    },
    "correlatives": [
      {
        "id": 1,
        "branch_id": 1,
        "tipo_documento": "01",
        "tipo_documento_nombre": "Factura",
        "serie": "F001",
        "correlativo_actual": 125,
        "numero_completo": "F001-000125",
        "proximo_numero": "F001-000126",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T15:45:00.000000Z"
      },
      {
        "id": 2,
        "branch_id": 1,
        "tipo_documento": "03",
        "tipo_documento_nombre": "Boleta de Venta",
        "serie": "B001",
        "correlativo_actual": 1523,
        "numero_completo": "B001-001523",
        "proximo_numero": "B001-001524",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-16T09:20:00.000000Z"
      }
    ]
  },
  "meta": {
    "total": 6,
    "tipos_disponibles": {
      "01": "Factura",
      "03": "Boleta de Venta",
      "07": "Nota de Crédito",
      "08": "Nota de Débito",
      "09": "Guía de Remisión",
      "17": "Nota de Venta",
      "20": "Comprobante de Retención",
      "RC": "Resumen de Anulaciones",
      "RA": "Resumen Diario"
    }
  }
}
```

---

### Actualizar Correlativo

**Endpoint:** `PUT /api/v1/branches/{branch_id}/correlatives/{correlative_id}`

#### Body

```json
{
  "tipo_documento": "01",
  "serie": "F001",
  "correlativo_actual": 150
}
```

> **Advertencia:** Actualizar el correlativo actual puede causar duplicación de números de comprobantes. Úsalo con precaución.

---

### Eliminar Correlativo

**Endpoint:** `DELETE /api/v1/branches/{branch_id}/correlatives/{correlative_id}`

#### Efectos

1. Elimina el correlativo de la tabla `correlatives`
2. Remueve la serie del campo correspondiente en la tabla `branches`

**Ejemplo:**
```sql
-- Antes de eliminar
series_factura: "F001,F002,F003"

-- Después de eliminar correlativo con serie F002
series_factura: "F001,F003"
```

#### Respuesta

```json
{
  "success": true,
  "message": "Correlativo eliminado exitosamente"
}
```

---

### Incrementar Correlativo

**Endpoint:** `POST /api/v1/branches/{branch_id}/correlatives/{correlative_id}/increment`

Incrementa manualmente el correlativo en 1. Este endpoint es para uso interno del sistema.

#### Respuesta

```json
{
  "success": true,
  "message": "Correlativo incrementado exitosamente",
  "data": {
    "id": 1,
    "serie": "F001",
    "correlativo_anterior": 125,
    "correlativo_actual": 126,
    "numero_usado": "F001-000126",
    "proximo_numero": "F001-000127"
  }
}
```

---

### Obtener Tipos de Documentos

**Endpoint:** `GET /api/v1/correlatives/document-types`

Retorna todos los tipos de documentos disponibles para correlativos.

#### Respuesta

```json
{
  "success": true,
  "data": [
    {
      "codigo": "01",
      "nombre": "Factura"
    },
    {
      "codigo": "03",
      "nombre": "Boleta de Venta"
    },
    {
      "codigo": "07",
      "nombre": "Nota de Crédito"
    },
    {
      "codigo": "08",
      "nombre": "Nota de Débito"
    },
    {
      "codigo": "09",
      "nombre": "Guía de Remisión"
    },
    {
      "codigo": "17",
      "nombre": "Nota de Venta"
    },
    {
      "codigo": "20",
      "nombre": "Comprobante de Retención"
    },
    {
      "codigo": "RC",
      "nombre": "Resumen de Anulaciones"
    },
    {
      "codigo": "RA",
      "nombre": "Resumen Diario"
    }
  ]
}
```

---

## Ejemplos Prácticos

### Ejemplo 1: Configurar una Nueva Sucursal Completa

**Paso 1: Crear la sucursal**
```http
POST /api/v1/branches
Content-Type: application/json

{
  "company_id": 1,
  "codigo": "0001",
  "nombre": "Sucursal Miraflores",
  "direccion": "Av. Larco 1234",
  "ubigeo": "150122",
  "distrito": "Miraflores",
  "provincia": "Lima",
  "departamento": "Lima",
  "telefono": "01-2345678",
  "email": "miraflores@empresa.com"
}
```

**Paso 2: Crear correlativos por lote**
```http
POST /api/v1/branches/1/correlatives/batch
Content-Type: application/json

{
  "correlativos": [
    {
      "tipo_documento": "01",
      "serie": "F001",
      "correlativo_actual": 0
    },
    {
      "tipo_documento": "03",
      "serie": "B001",
      "correlativo_actual": 0
    },
    {
      "tipo_documento": "07",
      "serie": "FC01",
      "correlativo_actual": 0
    },
    {
      "tipo_documento": "08",
      "serie": "FD01",
      "correlativo_actual": 0
    }
  ]
}
```

**Resultado:** Sucursal completamente configurada con 4 series listas para emitir documentos.

---

### Ejemplo 2: Buscar Sucursales en una Zona Específica

```http
GET /api/v1/companies/1/branches?distrito=Miraflores&activo=true&sort_by=nombre
```

**Resultado:** Todas las sucursales activas en Miraflores, ordenadas alfabéticamente.

---

### Ejemplo 3: Migrar Correlativos Existentes

Si tienes correlativos con valores antiguos y quieres actualizarlos:

```http
POST /api/v1/branches/1/correlatives/batch
Content-Type: application/json

{
  "correlativos": [
    {
      "tipo_documento": "01",
      "serie": "F001",
      "correlativo_actual": 1523
    },
    {
      "tipo_documento": "03",
      "serie": "B001",
      "correlativo_actual": 8456
    }
  ]
}
```

---

### Ejemplo 4: Verificar Series en Base de Datos

Después de crear correlativos, puedes verificar que las series se guardaron correctamente:

```sql
SELECT
    id,
    codigo,
    nombre,
    series_factura,
    series_boleta,
    series_nota_credito,
    series_nota_debito,
    series_guia_remision
FROM branches
WHERE id = 1;
```

**Resultado esperado:**
```
series_factura: "F001,F002"
series_boleta: "B001"
series_nota_credito: "FC01"
series_nota_debito: "FD01"
series_guia_remision: "T001"
```

✅ Sin comillas, sin JSON, solo valores limpios separados por comas.

---

## Mejores Prácticas

### 1. Nomenclatura de Series

**Facturas:**
- Serie principal: `F001`
- Series adicionales: `F002`, `F003`, etc.
- Series especiales: `FEXP` (exportación), `FCRE` (crédito)

**Boletas:**
- Serie principal: `B001`
- Series adicionales: `B002`, `B003`, etc.

**Notas de Crédito:**
- Facturas: `FC01`, `FC02`
- Boletas: `BC01`, `BC02`

**Notas de Débito:**
- Facturas: `FD01`, `FD02`
- Boletas: `BD01`, `BD02`

**Guías de Remisión:**
- `T001`, `T002` (T de Transporte)

---

### 2. Gestión de Correlativos

✅ **Recomendaciones:**
- Crear correlativos al configurar la sucursal
- Usar valores iniciales reales si es migración
- NO modificar correlativos en producción sin respaldo
- Monitorear el incremento automático

❌ **Evitar:**
- Cambiar series manualmente en la BD
- Eliminar correlativos con documentos emitidos
- Duplicar series entre sucursales de la misma empresa
- Usar caracteres especiales en series

---

### 3. Búsqueda de Sucursales

**Para interfaces de usuario:**
```javascript
// Búsqueda general mientras el usuario escribe
GET /companies/1/branches?search={userInput}

// Filtros específicos en formularios
GET /companies/1/branches?departamento={dept}&activo=true
```

**Para selección de sucursal por código:**
```javascript
GET /companies/1/branches/search/codigo?codigo={codigo}
```

**Para mapas/geolocalización:**
```javascript
GET /companies/1/branches/search/ubigeo?ubigeo={ubigeo}
```

---

### 4. Limpieza de Datos Legacy

Si tienes datos antiguos con formato JSON en las series:

```sql
-- Identificar registros con formato JSON
SELECT id, codigo, series_factura
FROM branches
WHERE series_factura LIKE '%[%'
   OR series_factura LIKE '%]%'
   OR series_factura LIKE '%"%';
```

**Solución:** Simplemente crea o actualiza un correlativo y el sistema limpiará automáticamente el formato:

```http
POST /api/v1/branches/1/correlatives
{
  "tipo_documento": "01",
  "serie": "F001",
  "correlativo_actual": 0
}
```

---

### 5. Validaciones Importantes

**Al crear sucursales:**
- Verificar que el ubigeo sea válido (6 dígitos)
- Validar que el código sea único por empresa
- Confirmar que la empresa esté activa

**Al crear correlativos:**
- La serie debe ser alfanumérica (A-Z, 0-9)
- Máximo 4 caracteres
- No debe existir la misma combinación (tipo + serie) en la sucursal
- El tipo de documento debe ser válido según SUNAT

---

## Casos de Uso Comunes

### Caso 1: Multi-Sucursal con Series Independientes

**Escenario:** Empresa con 5 sucursales, cada una con sus propias series.

**Sucursal 001 (Lima):**
- Facturas: F001, F002
- Boletas: B001

**Sucursal 002 (Arequipa):**
- Facturas: F001, F002 (mismas series, diferente sucursal)
- Boletas: B001

✅ **Permitido:** Diferentes sucursales pueden tener las mismas series.

---

### Caso 2: Numeración Continua al Cambiar de Año

**Pregunta:** ¿Debo reiniciar los correlativos cada año?

**Respuesta:** No es obligatorio según SUNAT. Puedes:
- Mantener numeración continua: `F001-000001`, `F001-000002`, ...
- O crear nueva serie por año: `F001` (2024), `F002` (2025)

**Recomendación:** Numeración continua es más simple y evita problemas.

---

### Caso 3: Recuperar Numeración Perdida

**Escenario:** El correlativo actual es 100, pero el último documento emitido fue el 95.

**Solución:**
```http
PUT /api/v1/branches/1/correlatives/1
{
  "tipo_documento": "01",
  "serie": "F001",
  "correlativo_actual": 95
}
```

Próximo documento será: `F001-000096`

---

## Troubleshooting

### Problema 1: Series con Comillas en la BD

**Síntoma:**
```sql
series_factura: "\"F001\""
series_factura: "[\"F001\"]"
```

**Solución:** Ejecuta cualquier operación de correlativo (crear, actualizar, eliminar) y el sistema limpiará automáticamente.

---

### Problema 2: Correlativo No se Guarda

**Síntoma:** El campo `correlativo_actual` queda en 0 después de crear.

**Causa:** Estás enviando `correlativo_inicial` en lugar de `correlativo_actual` en batch.

**Solución:**
```json
{
  "correlativo_actual": 56  // ✅ Correcto
}
```

---

### Problema 3: No Encuentra Sucursal por Código

**Síntoma:** GET con filtro `?codigo=0001` no retorna resultados.

**Causa:** El endpoint de listado usa búsqueda parcial con LIKE.

**Solución:** Usa el endpoint específico:
```http
GET /companies/1/branches/search/codigo?codigo=0001
```

---

## Conclusión

Este sistema de sucursales y correlativos proporciona:

✅ **Flexibilidad:** Múltiples sucursales y series por empresa
✅ **Búsqueda Avanzada:** Filtros por ubigeo, código, nombre, ubicación
✅ **Gestión Automática:** Series actualizadas automáticamente
✅ **Limpieza de Datos:** Formato consistente sin comillas ni JSON
✅ **Escalabilidad:** Creación por lote para configuración rápida
✅ **Integridad:** Validaciones en cada nivel

Para más información sobre otros módulos del sistema, consulta:
- [Resúmenes Diarios de Boletas](./Resumenes-diarios-boletas.md)
- [Webhooks](./webhooks.md)
- [Rutas y Endpoints API](./Rutas-enpoint-api.md)

---

**Última actualización:** Enero 2025
**Versión del API:** v1
