# Documentación Técnica de fappv1

## 1. Resumen general

**Propósito**

Aplicación de gestión empresarial desarrollada en Laravel y Filament para manejar clientes, productos y el ciclo completo de documentos comerciales: presupuestos, pedidos, actuaciones (servicios) y facturas. Permite definir líneas de detalle con impuestos, generar PDFs y exportaciones, y controlar numeraciones por usuario.

**Flujo entre módulos**

1. **Clientes** y **Productos** son entidades base.
2. Se emite un **Presupuesto** para un cliente. Las líneas se guardan en `presupuesto_productos`.
3. Un presupuesto puede transformarse en **Pedido** y/o **Factura**. Ambos mantienen el vínculo con el presupuesto original y con las **Actuaciones** realizadas.
4. Las **Actuaciones** representan trabajos sobre un cliente y pueden asociarse a pedidos y facturas, incluyendo sus propios productos (`actuacion_productos`).
5. **Series** controla la numeración secuencial de presupuestos, pedidos y facturas por usuario y tipo de documento.
6. Se dispone de un servicio para generar PDFs desde vistas Blade.

## 2. Arquitectura

**Stack tecnológico**

- PHP 8.2
- Laravel Framework 12
- Filament 3.3 (panel administrativo)
- Spatie Laravel-Permission 6.12 (roles y permisos)
- Barryvdh DomPDF 3.1.1 (generación PDF)
- Vite + Tailwind CSS 4 (front-end)

**Mapa de carpetas principales**

| Carpeta | Contenido |
| --- | --- |
| `app/Models` | Modelos Eloquent |
| `app/Filament` | Recursos y páginas de Filament |
| `app/Services` | Servicios (PDF, numeración de series) |
| `database/migrations` | Migraciones principales |
| `database/migrations/fapp` | Migraciones de dominio utilizadas en pruebas |
| `database/seeders` | Seeders (datos iniciales) |
| `resources/views` | Vistas Blade (`pdf/documento.blade.php`) |
| `routes` | Definición de rutas web |

## 3. Modelo de datos

### Diagrama ER

```mermaid
erDiagram
    users ||--o{ clientes : tiene
    users ||--o{ productos : tiene
    users ||--o{ presupuestos : tiene
    users ||--o{ pedidos : tiene
    users ||--o{ facturas : tiene
    clientes ||--o{ presupuestos : recibe
    clientes ||--o{ pedidos : recibe
    clientes ||--o{ facturas : recibe
    presupuestos ||--o{ presupuesto_productos : contiene
    pedidos ||--o{ pedido_productos : contiene
    facturas ||--o{ factura_productos : contiene
    actuaciones ||--o{ pedido : origina
    actuaciones ||--o{ actuacion_productos : usa
    actuaciones }o--o{ facturas : facturada
    productos ||--o{ presupuesto_productos : ``
    productos ||--o{ pedido_productos : ``
    productos ||--o{ actuacion_productos : ``
    productos ||--o{ factura_productos : ``
    series }|--|| users : controla
```

### Definición de tablas

#### users

| Campo | Tipo | PK/FK | Nullable | Default | Comentarios |
| --- | --- | --- | --- | --- | --- |
| id | bigIncrements | PK | No | – | |
| name | string | | No | – | |
| email | string | Unique | No | – | |
| email_verified_at | timestamp | | Sí | – | |
| password | string | | No | – | |
| remember_token | string | | Sí | – | |
| created_at/updated_at | timestamps | | No | – | |

#### series

| Campo | Tipo | PK/FK | Nullable | Default | Enum/Valores |
| --- | --- | --- | --- | --- | --- |
| id | bigIncrements | PK | No | – | |
| usuario_id | foreignId → users | FK | No | – | `CASCADE` update, `RESTRICT` delete |
| tipo | enum | | No | – | `presupuesto`, `pedido`, `factura` |
| serie | string(20) | | No | 'A' | |
| siguiente_numero | unsignedBigInteger | | No | 1 | |
| timestamps | | | | | |
| **Única** | (`usuario_id`,`tipo`,`serie`) | | | | |

#### clientes

| Campo | Tipo | PK/FK | Nullable | Default | |
| id | bigIncrements | PK | No | – | |
| usuario_id | foreignId → users | FK | No | – | `CASCADE` update, `RESTRICT` delete |
| nombre | string | | No | – | |
| cif | string | | Sí | – | |
| email | string | | Sí | – | |
| telefono | string | | Sí | – | |
| direccion | string | | Sí | – | |
| timestamps | | | | | index(`usuario_id`,`nombre`) |

#### productos

| Campo | Tipo | PK/FK | Nullable | Default | |
| id | bigIncrements | PK | No | – | |
| usuario_id | foreignId → users | FK | No | – | `CASCADE` update, `RESTRICT` delete |
| nombre | string | | No | – | |
| descripcion | text | | Sí | – | |
| precio | decimal(12,2) | | No | 0 | |
| iva_porcentaje | decimal(5,2) | | No | 21 | |
| activo | boolean | | No | 1 | |
| timestamps | | | | | index(`usuario_id`,`activo`) |

#### presupuestos

| Campo | Tipo | PK/FK | Nullable | Default | Enum/Valores |
| id | bigIncrements | PK | No | – | |
| usuario_id | foreignId → users | FK | No | – | `CASCADE` update, `RESTRICT` delete |
| cliente_id | foreignId → clientes | FK | No | – | `CASCADE` update, `RESTRICT` delete |
| fecha | date | | No | – | |
| numero | integer | | No | – | |
| serie | string | | No | 'PRES' | |
| estado | enum | | No | 'borrador' | `borrador`,`enviado`,`aceptado`,`rechazado` |
| validez_dias | unsignedInteger | Sí | – | |
| notas | text | Sí | – | |
| activo | unsignedTinyInteger | | No | 1 | |
| base_imponible | decimal(14,2) | | No | – | |
| iva_total | decimal(14,2) | | No | 0 | |
| irpf_total | decimal(14,2) | | No | 0 | |
| total | decimal(14,2) | | No | – | |
| timestamps | | | | | |
| **Única** | (`usuario_id`,`serie`,`numero`) y (`serie`,`numero`) |
| **Índices** | (`usuario_id`,`cliente_id`,`estado`,`fecha`,`activo`) |

#### presupuesto_productos

| Campo | Tipo | PK/FK | Nullable | Default | |
| id | bigIncrements | PK | No | – | |
| presupuesto_id | foreignId → presupuestos | FK | No | – | `CASCADE` delete/update |
| producto_id | foreignId → productos | FK | Sí | – | `NULL ON DELETE` |
| descripcion | string(255) | | No | – | |
| cantidad | decimal(12,3) | | No | 1 | |
| precio_unitario | decimal(12,2) | | No | 0 | |
| iva_porcentaje | decimal(5,2) | | No | 21 | |
| irpf_porcentaje | decimal(5,2) | | Sí | – | |
| subtotal | decimal(14,2) | | No | 0 | |
| timestamps | | | | | index(`presupuesto_id`) |

#### actuaciones

| Campo | Tipo | PK/FK | Nullable | Default | Enum |
| id | bigIncrements | PK | No | – | |
| usuario_id | foreignId → users | FK | No | – | `CASCADE` update, `RESTRICT` delete |
| cliente_id | foreignId → clientes | FK | No | – | `CASCADE` update, `RESTRICT` delete |
| codigo | string | Sí | – | |
| fecha_inicio | date | Sí | – | |
| fecha_fin | date | Sí | – | |
| estado | enum | | No | 'abierta' | `abierta`,`en_proceso`,`completada` |
| notas | text | Sí | – | |
| timestamps | | | | | index(`usuario_id`,`cliente_id`,`estado`,`fecha_inicio`) |

#### actuacion_productos

| Campo | Tipo | PK/FK | Nullable | Default | |
| id | bigIncrements | PK | No | – | |
| actuacion_id | foreignId → actuaciones | FK | No | – | `CASCADE` delete/update |
| producto_id | foreignId → productos | FK | Sí | – | `NULL ON DELETE` |
| descripcion | string | | No | – | |
| cantidad | decimal(12,3) | | No | 1 | |
| precio_unitario | decimal(12,2) | | No | 0 | |
| iva_porcentaje | decimal(5,2) | | No | 21 | |
| irpf_porcentaje | decimal(5,2) | | Sí | – | |
| subtotal | decimal(14,2) | | No | 0 | |
| timestamps | | | | | index(`actuacion_id`) |

#### pedidos

| Campo | Tipo | PK/FK | Nullable | Default | Enum/Valores |
| id | bigIncrements | PK | No | – | |
| usuario_id | foreignId → users | FK | No | – | `CASCADE` update, `RESTRICT` delete |
| cliente_id | foreignId → clientes | FK | No | – | `CASCADE` update, `RESTRICT` delete |
| presupuesto_id | foreignId → presupuestos | FK | Sí | – | `NULL ON DELETE` |
| actuacion_id | foreignId → actuaciones | FK | Sí | – | `NULL ON DELETE` |
| fecha | date | Sí | – | |
| numero | unsignedBigInteger | | No | 0 | |
| serie | string(20) | | No | 'A' | |
| estado | enum | | No | 'borrador' | `borrador`,`confirmado`,`servido`,`cerrado` |
| notas | text | Sí | – | |
| base_imponible | decimal(14,2) | | No | 0 | |
| iva_total | decimal(14,2) | | No | 0 | |
| irpf_total | decimal(14,2) | | No | 0 | |
| total | decimal(14,2) | | No | 0 | |
| timestamps | | | | | |
| **Única** | (`usuario_id`,`serie`,`numero`) |
| **Índices** | (`usuario_id`,`cliente_id`,`estado`,`fecha`) |

#### pedido_productos

| Campo | Tipo | PK/FK | Nullable | Default | |
| id | bigIncrements | PK | No | – | |
| pedido_id | foreignId → pedidos | FK | No | – | `CASCADE` delete/update |
| producto_id | foreignId → productos | FK | Sí | – | `NULL ON DELETE` |
| descripcion | string(255) | | No | – | |
| cantidad | decimal(12,3) | | No | 1 | |
| precio_unitario | decimal(12,2) | | No | 0 | |
| iva_porcentaje | decimal(5,2) | | No | 21 | |
| irpf_porcentaje | decimal(5,2) | | Sí | – | |
| subtotal | decimal(14,2) | | No | 0 | |
| timestamps | | | | | index(`pedido_id`) |

#### facturas

| Campo | Tipo | PK/FK | Nullable | Default | Enum/Valores |
| id | bigIncrements | PK | No | – | |
| usuario_id | foreignId → users | FK | No | – | `CASCADE` delete/update |
| cliente_id | foreignId → clientes | FK | No | – | `CASCADE` delete/update |
| presupuesto_id | foreignId → presupuestos | FK | Sí | – | `NULL ON DELETE` |
| numero | unsignedBigInteger | | No | 0 | |
| serie | string(20) | | No | 'A' | |
| fecha | date | | No | – | |
| estado | enum | | No | 'borrador' | `borrador`,`enviado`,`pagado` |
| notas | text | Sí | – | |
| base_imponible | decimal(14,2) | | No | 0 | |
| iva_total | decimal(14,2) | | No | 0 | |
| irpf_total | decimal(14,2) | | No | 0 | |
| total | decimal(14,2) | | No | 0 | |
| timestamps | | | | | |
| **Única** | (`usuario_id`,`serie`,`numero`) |

#### factura_productos

| Campo | Tipo | PK/FK | Nullable | Default | |
| id | bigIncrements | PK | No | – | |
| factura_id | foreignId → facturas | FK | No | – | `CASCADE` delete/update |
| producto_id | foreignId → productos | FK | Sí | – | `SET NULL` |
| descripcion | string | | No | – | |
| cantidad | decimal(12,3) | | No | 1 | |
| precio_unitario | decimal(12,2) | | No | 0 | |
| iva_porcentaje | decimal(5,2) | | No | 21 | |
| irpf_porcentaje | decimal(5,2) | | Sí | – | |
| subtotal | decimal(14,2) | | No | 0 | |
| timestamps | | | | | |

#### actuacion_factura (pivot)

| Campo | Tipo | PK/FK | Nullable | Default | |
| id | bigIncrements | PK | No | – | |
| actuacion_id | foreignId → actuaciones | FK | No | – | `CASCADE` delete/update |
| factura_id | foreignId → facturas | FK | No | – | `CASCADE` delete/update |
| timestamps | | | | | |
| **Única** | (`actuacion_id`,`factura_id`) |
| **Índice** | (`factura_id`) |

#### Tablas de permisos (Spatie)

- `roles(id, name, guard_name, ...)`
- `permissions(id, name, guard_name, ...)`
- `model_has_roles`, `model_has_permissions`, `role_has_permissions`

### Relaciones y restricciones

- Eliminaciones en cascada para líneas de detalle al borrar el documento principal (`presupuesto_productos`, `pedido_productos`, `factura_productos`, `actuacion_productos`).
- `presupuesto_id`, `actuacion_id` y `producto_id` son `NULL ON DELETE` en tablas relacionadas para preservar el histórico de documentos.
- Unicidad de (`usuario_id`,`serie`,`numero`) garantiza numeración única por usuario en presupuestos, pedidos y facturas. También existe una restricción adicional en presupuestos para (`serie`,`numero`) global.

## 4. Modelos Eloquent

| Modelo | Relaciones | $fillable | $casts | $hidden |
| --- | --- | --- | --- | --- |
| **User** | `HasRoles`; `hasMany` implícito | name, email, password | email_verified_at:datetime, password:hashed | password, remember_token |
| **Cliente** | `belongsTo User` | usuario_id, nombre, cif, email, telefono, direccion | – | – |
| **Producto** | `belongsTo User` | usuario_id, nombre, descripcion, precio, iva_porcentaje, activo | – | – |
| **Presupuesto** | `belongsTo User`, `belongsTo Cliente` | usuario_id, cliente_id, fecha, numero, serie, estado, validez_dias, notas, activo, base_imponible, iva_total, irpf_total, total | fecha:date, validez_dias:int, activo:bool, base_imponible/iva_total/irpf_total/total:decimal:2 | – |
| **PresupuestoProducto** | `belongsTo Presupuesto`, `belongsTo Producto` | presupuesto_id, producto_id, descripcion, cantidad, precio_unitario, iva_porcentaje, irpf_porcentaje, subtotal | – | – |
| **Actuacion** | `belongsTo User`, `belongsTo Cliente`, `hasMany Pedido`, `hasMany ActuacionProducto`, `belongsToMany Factura` | usuario_id, cliente_id, codigo, fecha_inicio, fecha_fin, estado, notas | – | – |
| **ActuacionProducto** | `belongsTo Actuacion`, `belongsTo Producto` | actuacion_id, producto_id, descripcion, cantidad, precio_unitario, iva_porcentaje, irpf_porcentaje, subtotal | – | – |
| **Pedido** | `belongsTo User`, `belongsTo Cliente`, `belongsTo Actuacion`, `belongsTo Presupuesto`, `hasMany PedidoProducto` | usuario_id, cliente_id, presupuesto_id, actuacion_id, fecha, numero, serie, estado, notas, base_imponible, iva_total, irpf_total, total | – | – |
| **PedidoProducto** | `belongsTo Pedido`, `belongsTo Producto` | pedido_id, producto_id, descripcion, cantidad, precio_unitario, iva_porcentaje, irpf_porcentaje, subtotal | – | – |
| **Factura** | `belongsTo User`, `belongsTo Cliente`, `belongsTo Presupuesto`, `hasMany FacturaProducto`, `belongsToMany Actuacion` | usuario_id, cliente_id, presupuesto_id, fecha, numero, serie, estado, notas, base_imponible, iva_total, irpf_total, total | – | – |
| **FacturaProducto** | `belongsTo Factura`, `belongsTo Producto` | factura_id, producto_id, descripcion, cantidad, precio_unitario, iva_porcentaje, irpf_porcentaje, subtotal | – | – |

Todos los modelos definen un `scopeMine()` que filtra por `usuario_id` cuando el usuario autenticado no tiene el rol `admin`.

## 5. Filament Resources

### ClienteResource
- **Formulario**: `TextInput nombre (required)`, `TextInput cif`, `TextInput direccion`, `TextInput telefono (tel)`, `TextInput email (email)`.
- **Tabla**: columnas `nombre`, `cif`, `telefono`, `email`.
- **Acciones**: editar, eliminar. Bulk: exportar (pxlrbt/filament-excel), eliminar.

### ProductoResource
- **Formulario**: `nombre` (required), `descripcion`, `precio` (numeric, step 0.01), `iva_porcentaje` (default 21), `activo` (toggle).
- **Tabla**: `nombre`, `descripcion` limitada, `precio` (money), `iva_porcentaje`, `activo` (boolean).
- **Acciones**: editar. Bulk: exportar, eliminar.

### PresupuestoResource
- **Formulario**: `serie` (required), `numero` (numeric, required), `fecha` (required), `validez_dias` (numeric), `cliente_id` (select filtrado por usuario), `base_imponible`, `iva_total`, `irpf_total`, `total`, `estado` (enum, default `borrador` con validación), `activo` (toggle), `notas` (textarea).
- **Tabla**: `serie`, `numero`, `fecha`, `validez_dias`, `cliente.nombre`, `base_imponible`, `iva_total`, `irpf_total`, `total`, `estado` (badge), `activo` (boolean).
- **Acciones**: editar, eliminar. Bulk: exportar, eliminar.

### PedidoResource
- **Formulario**: `cliente_id` (select filtrado por usuario, required), `serie` (required), `numero` (numeric, required), `fecha` (default now), `base_imponible`, `iva_total`, `irpf_total`, `total`, `estado` (enum con validación), `notas` (textarea).
- **Tabla**: `serie`, `numero`, `cliente.nombre`, `fecha`, `base_imponible`, `iva_total`, `irpf_total`, `total`, `estado` (badge).
- **Acciones**: editar. Bulk: exportar, eliminar.

### FacturaResource
- **Formulario**: `cliente_id` (select filtrado por usuario), `serie` (required), `numero` (numeric, required), `fecha`, `estado` (enum), `base_imponible`, `iva_total`, `irpf_total`, `total`.
  - *Nota:* no expone el campo `notas` presente en la tabla.
- **Tabla**: `numero`, `serie`, `cliente.nombre`, `fecha`, `estado` (badge), `total` (money).
- **Acciones**: editar. Bulk: exportar, eliminar.

### ActuacionResource
- **Formulario**: `cliente_id` (select filtrado), `codigo` (max 50), `fecha_inicio` (default now), `fecha_fin`, `estado` (enum con validación), `notas` (textarea).
- **Tabla**: `codigo`, `cliente.nombre`, `estado` (badge), `fecha_inicio`.
- **Relación**: `ProductosRelationManager` para líneas de productos de la actuación.
- **Acciones**: editar. Bulk: exportar, eliminar.

Todos los resources sobreescriben `getEloquentQuery()` para aplicar `scopeMine()` cuando el usuario no es `admin`.

## 6. Migraciones y Seeders

**Orden sugerido de migraciones**
1. `0001_01_01_000000_create_users_table`
2. `0001_01_01_000001_create_cache_table`
3. `0001_01_01_000002_create_jobs_table`
4. `2025_08_07_000001_create_series_table`
5. `2025_08_07_000250_create_facturas_tables`
6. `2025_08_07_205014_create_permission_tables`
7. `2025_08_08_000000_create_presupuestos_table`
8. `2025_08_08_000100_update_presupuesto_productos_table`
9. `2025_08_08_000200_update_pedido_productos_table`
10. `2025_08_08_000250_update_factura_productos_table`
11. `2025_08_08_000300_update_facturas_estado_enum`
12. `2025_08_09_000200_create_actuacion_productos_table`
13. `2025_08_10_000000_update_presupuestos_table`

Las migraciones del directorio `migrations/fapp` duplican y crean las tablas de dominio para propósitos de test.

**Seeders**

| Seeder | Datos |
| --- | --- |
| `DatabaseSeeder` | Usuario de prueba `test@example.com`. |
| `FappSeeder` | Roles `admin` y `cliente`; usuario admin `nacho@nacho.es`; cliente y productos demo; presupuesto ejemplo con líneas; invoca `PresupuestoSeeder`. |
| `PresupuestoSeeder` | Genera 15 presupuestos aleatorios para clientes existentes. |

## 7. Reglas de negocio

- **Unicidad de documentos**: combinación (`usuario_id`,`serie`,`numero`) en presupuestos, pedidos y facturas. En presupuestos además se fuerza (`serie`,`numero`) global.
- **Estados**:
  - Presupuestos: `borrador`, `enviado`, `aceptado`, `rechazado`.
  - Pedidos: `borrador`, `confirmado`, `servido`, `cerrado`.
  - Facturas: `borrador`, `enviado`, `pagado`.
  - Actuaciones: `abierta`, `en_proceso`, `completada`.
  - Series: tipo `presupuesto`, `pedido`, `factura`.
- **Totales e impuestos**: cada línea guarda `cantidad`, `precio_unitario`, `iva_porcentaje` e `irpf_porcentaje` calculando `subtotal`. Las tablas principales almacenan `base_imponible`, `iva_total`, `irpf_total` y `total`. El IRPF se resta del total.
- **Numeración**: `SeriesService::nextNumber()` genera secuencias por `usuario_id`, `tipo` y `serie` dentro de una transacción con `SELECT ... FOR UPDATE`.

## 8. Seguridad

- **Roles y permisos**: implementados con `spatie/laravel-permission`. Roles creados por defecto: `admin` y `cliente`.
- **Filtrado por usuario**: todos los modelos principales definen `scopeMine()` y los recursos Filament limitan los datos a `usuario_id` del usuario autenticado salvo que tenga rol `admin`.
- **Policies**: no se han definido clases de policy específicas; se confía en roles y scopes.

## 9. Plan de despliegue

**Entorno local**
1. `git clone` del repositorio.
2. `composer install` y `npm install`.
3. Copiar `.env.example` a `.env` y configurar base de datos.
4. `php artisan key:generate`.
5. `php artisan migrate --seed` (ejecuta `FappSeeder`).
6. `npm run dev` para servir assets y `php artisan serve` para la aplicación.

**Producción**
1. Instalar dependencias con `composer install --optimize-autoloader --no-dev` y `npm ci && npm run build`.
2. Configurar variables de entorno y permisos de `storage/` y `bootstrap/cache`.
3. Ejecutar `php artisan migrate --force` y `php artisan db:seed --class=FappSeeder` si es necesario.
4. Configurar un servidor web (Nginx/Apache) apuntando a `public/` y un proceso para colas si se usan.

## 10. Checklist de pruebas

| Caso | Pasos |
| --- | --- |
| **Crear cliente** | Acceder a Filament → Clientes → Crear → rellenar campos obligatorios. |
| **Crear presupuesto con líneas** | Filament → Presupuestos → Crear → añadir datos y líneas. Verificar cálculos de totales. |
| **Convertir a pedido/factura** | Crear pedido/factura vinculando un presupuesto y añadir líneas. |
| **Exportar CSV/Excel** | En listados de resources utilizar acción bulk `Export`. |
| **Generar PDF** | Usar servicio `DompdfPdfService` con vista `pdf/documento` para renderizar documento. |
| **Control de numeración** | Crear varios documentos y validar incremento según `series`. |
| **Restricción por usuario** | Crear registros con distintos usuarios y verificar visibilidad según rol. |
| **Estados** | Cambiar estados y comprobar que sólo se permiten valores definidos. |

## Inconsistencias detectadas

- `FacturaResource` no incluye en el formulario el campo `notas` que existe en la tabla `facturas`.

