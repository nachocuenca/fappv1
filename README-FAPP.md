# FAPP Module Pack (Laravel 11 + Filament v3)

Este paquete añade el flujo **Presupuesto → Pedido → Actuación → Factura** con catálogo de productos/servicios,
numeración por usuario (series), y generación de PDF con **DomPDF** (driver intercambiable).

## Requisitos previos
- PHP 8.2+
- Composer
- Node (para assets si usas Filament)
- MySQL (tu entorno usa puerto 3307)
- Laravel 11 instalado y funcionando
- Filament v3
- spatie/laravel-permission
- barryvdh/laravel-dompdf

## Instalación rápida
1) Crear proyecto Laravel y dependencias:
   ```bash
   composer create-project laravel/laravel fapp
   cd fapp
   composer require filament/filament spatie/laravel-permission barryvdh/laravel-dompdf
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   php artisan migrate
   ```

2) Copia el contenido de esta carpeta dentro de tu proyecto Laravel (respeta rutas).
   - Si te pregunta sobre sobrescribir, di **sí** para archivos que pertenezcan a este pack.

3) Ajusta `.env` (usa tu puerto 3307):
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3307
   DB_DATABASE=factura_app_v23
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4) Migra y siembra:
   ```bash
   php artisan migrate --path=database/migrations/fapp --force
   php artisan db:seed --class=Database\Seeders\FappSeeder
   ```

5) Crea un usuario admin (si no quieres usar el seed) y entra a Filament:
   ```bash
   php artisan tinker
   >>> $u = \App\Models\User::factory()->create(['email'=>'admin@example.com', 'password'=>bcrypt('secret')]);
   >>> $u->assignRole('admin');
   ```
   Filament: `/admin`

## Notas
- Numeración única por **usuario + tipo + serie** usando tabla `series`.
- Un **pedido** pertenece a **una actuación** (opcional hasta asignar), y **una factura** puede agrupar **varias actuaciones** (`actuacion_factura`).
- PDFs con DomPDF vía `App\Services\Pdf\DompdfPdfService`. Puedes reemplazar el driver creando otra clase que implemente `PdfService`.
- Policies simplificadas: `admin` ve todo; `cliente` ve solo lo suyo (scope por `usuario_id`). Requiere `spatie/laravel-permission`.

¡Suerte y a facturar!
