# FAPPv1

FAPPv1 es una aplicación web de gestión desarrollada con Laravel 12, Filament v3 y MySQL/MariaDB.
Permite administrar clientes, productos y documentos comerciales (presupuestos, pedidos, actuaciones y facturas) desde un mismo entorno.
Incluye generación de documentos con líneas de producto, cálculo automático de impuestos y exportaciones en distintos formatos.
Pensada para autónomos y pymes que necesitan una herramienta moderna y accesible.

## Capturas o GIFs

Coloca imágenes o animaciones dentro de `/docs/screenshots/` o en `/public/img/` y enlázalas aquí.

## Características principales

- **Clientes**: creación, edición y eliminación con datos fiscales completos.
- **Productos/Servicios**: catálogo con precios, IVA/IRPF y gestión opcional de stock.
- **Presupuestos → Pedidos → Actuaciones → Facturas**: flujo completo con numeración por serie.
- **Líneas de documento**: múltiples productos/servicios con cantidades, precios y descuentos.
- **Estados de documento**: borrador, confirmado, pagado o anulado.
- **Exportaciones**: generación de PDFs y otros formatos.
- **Totales automáticos**: cálculo de base imponible, IVA, IRPF y total final.
- **Dashboard**: panel principal con métricas básicas del negocio.

## Tecnologías utilizadas

- **Backend**: Laravel 12 (PHP 8.2)
- **Admin UI**: Filament v3
- **Base de datos**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, DataTables
- **Otros**: Vite, Laravel Pint/Testing Tools

## Requisitos previos

| Recurso       | Versión/Nota                                      |
|---------------|---------------------------------------------------|
| PHP           | 8.2+                                              |
| Composer      | Última versión estable                            |
| MySQL/MariaDB | Host `127.0.0.1`, puerto `3307`, usuario `root` sin contraseña |
| Node.js / npm | Para compilar los assets de frontend               |
| Extensiones PHP | `mbstring`, `openssl`, `pdo`, etc.             |

## Instalación en local

```bash
# 1. Clonar repositorio
git clone <url-del-repositorio> fappv1
cd fappv1

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS y compilar assets
npm install && npm run build

# 4. Configurar entorno
cp .env.example .env
# Edita .env con las credenciales de la base de datos:
# DB_HOST=127.0.0.1
# DB_PORT=3307
# DB_DATABASE=fappv1
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Ejecutar migraciones y semillas
php artisan migrate --seed

# 6. Usuario administrador de prueba
# (se crea automáticamente con el seeder: nacho@nacho.es / nacho)

# 7. Iniciar el servidor
php artisan serve
```

## Uso básico

1. Inicia sesión con `nacho@nacho.es` / `nacho`.
2. Crea uno o varios **clientes** y **productos**.
3. Genera **presupuestos**, conviértelos en **pedidos** o **actuaciones** y emite **facturas**.
4. Descarga o visualiza los documentos generados en PDF u otros formatos.

## Documentación completa

- [DOCUMENTACION_TECNICA.md](DOCUMENTACION_TECNICA.md)
- [CASOS_DE_USO.md](CASOS_DE_USO.md)

## Casos de uso resumidos

- **Emitir una factura**: seleccionar cliente, añadir líneas de producto, confirmar y exportar a PDF.
- **Registrar un pedido**: convertir un presupuesto existente y hacer seguimiento del estado hasta su entrega.
- **Gestionar una actuación**: registrar trabajos realizados, gastos asociados y emitir factura final.

## Licencia y créditos

Proyecto bajo licencia **MIT**.
Autor: **Ignacio Cuenca Moya**.
