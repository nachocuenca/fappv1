# FAPP FULL PACK (Laravel 12 + Filament v3)
Incluye:
- Correcciones PSR-4 para Pages de Filament.
- Resources completos: Productos, Presupuestos, **Pedidos**, **Actuaciones**, **Facturas**.
- Acciones de flujo: Presupuesto → Pedido, Pedido → asignar a Actuación, Actuación → generar Factura (1 actuación = 1 factura).
- Servicios: SeriesService, PdfService (DomPDF driver).
- Migraciones complementarias en `database/migrations/fapp` (series, productos, clientes opcional, presupuestos+líneas, pedidos+líneas, actuaciones, pivot actuacion_factura).

## Instalación
1) En un Laravel limpio (12.x) con PHP 8.2:
   ```bash
   composer require filament/filament:^3 spatie/laravel-permission:^6.12 barryvdh/laravel-dompdf:^3 openspout/openspout:^4.28 -W
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   ```
2) Copia este pack sobre tu proyecto (respetando rutas). **Elimina** los antiguos archivos que tenían varias clases en un solo .php:
   - app/Filament/Resources/*/Pages/Pages.php (si existían)
   - app/Services/PdfService.php (antiguo) y app/Services/DompdfPdfService.php (antiguo)

3) Configura la DB (.env) y migra:
   ```bash
   php artisan migrate --path=database/migrations/fapp --force
   ```

4) (Opcional) Seeder de demo:
   ```bash
   php artisan db:seed --class=Database\\Seeders\\FappSeeder
   ```

5) Crea un usuario admin y entra a /admin:
   ```bash
   php artisan tinker
   $u = \App\Models\User::factory()->create(['email'=>'admin@example.com','password'=>bcrypt('secret')]);
   $u->assignRole('admin');
   ```

## Notas
- Numeración única por usuario+tipo+serie (tabla `series`). Usa `SeriesService::nextNumber()`.
- Varias **Pedidos → 1 Actuación** (campo `actuacion_id` en pedidos).
- **1 o varias Actuaciones → 1 Factura**: por simplicidad, el botón "Generar Factura" en Actuación crea **1 factura por actuación**. (Puedes luego agrupar varias creando la factura desde la vista de Facturas y añadiendo más actuaciones; plantilla lista).
- Exportaciones avanzadas (XLSX) requieren `filament/excel`. Este pack evita dependencias extra para no chocar versiones.
