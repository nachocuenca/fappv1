# Casos de Uso - fappv1

## Propósito general
La aplicación gestiona el ciclo completo de trabajo de una empresa de servicios: desde la captación de un cliente hasta la emisión de la factura. El flujo principal es **Presupuesto → Pedido → Actuación → Factura**【F:README-FAPP.md†L1-L4】. 

**Tipos de usuarios**
- **Administrador**: tiene acceso a todos los datos y puede configurar el sistema.
- **Usuario cliente**: solo accede a sus propios registros gracias al filtro `scopeMine` presente en los modelos【F:Cliente.php†L23-L29】.

**Problemática que resuelve**
- Centraliza información de clientes, catálogo de productos y documentación comercial.
- Automatiza la numeración de documentos por usuario y serie【F:README-FAPP.md†L52-L54】.
- Facilita la generación de documentos PDF y exportaciones CSV/XLSX.

**Beneficios**
- Trazabilidad de cada proyecto: un pedido puede enlazarse a una actuación y varias actuaciones agruparse en una factura【F:README-FAPP.md†L52-L55】.
- Ahorro de tiempo en tareas administrativas y reducción de errores manuales.
- Datos siempre disponibles para auditorías o consultas.

## 1. Gestión de clientes
**Actor(es):** administrador, usuario cliente.

| Precondición | Descripción |
|--------------|-------------|
| Usuario autenticado | Debe iniciar sesión con rol permitido. |
| Datos básicos disponibles | Nombre y, opcionalmente, CIF, email, teléfono y dirección. |

**Flujo principal**
1. Acceder a **Clientes** en el panel `/admin`.
2. Crear, editar o borrar registros usando el formulario de Filament.
3. Opcionalmente exportar la lista a CSV/XLSX mediante la acción *Exportar*【F:Filament/Resources/ClienteResource.php†L66-L75】.

**Resultado esperado**
- Cliente registrado o actualizado en la tabla `clientes` con campos de contacto y propietario (`usuario_id`)【F:Cliente.php†L12-L20】.

| Postcondición | Descripción |
|---------------|-------------|
| Registro persistido | El cliente queda disponible para asociarlo a presupuestos, pedidos, actuaciones o facturas. |
| Exportaciones | Los datos seleccionados se descargan en formato CSV/XLSX. |

## 2. Gestión de productos
**Actor(es):** administrador, usuario cliente.

| Precondición | Descripción |
|--------------|-------------|
| Usuario autenticado | Debe tener rol válido. |
| Información del producto | Nombre, descripción, precio e IVA aplicable. |

**Flujo principal**
1. Entrar en **Productos** desde el panel.
2. Añadir o editar artículos del catálogo reutilizable.
3. Activar o desactivar la disponibilidad del producto.

**Resultado esperado**
- Producto almacenado con precio e IVA por defecto en la tabla `productos`【F:Producto.php†L12-L16】.

| Postcondición | Descripción |
|---------------|-------------|
| Catálogo actualizado | Los productos quedan disponibles para líneas de presupuestos, pedidos, actuaciones y facturas. |

## 3. Creación de presupuestos
**Actor(es):** administrador, usuario cliente.

| Precondición | Descripción |
|--------------|-------------|
| Cliente existente | El presupuesto debe asociarse a un cliente válido. |
| Productos opcionales | Pueden utilizarse ítems del catálogo para las líneas. |

**Flujo principal**
1. Abrir **Presupuestos** en `/admin`.
2. Completar datos generales: serie, número, fecha, validez y estado.
3. Añadir líneas de productos con cantidades, precios e impuestos【F:PresupuestoProducto.php†L12-L17】.
4. Guardar y, si procede, enviar al cliente.

**Resultado esperado**
- Presupuesto registrado con totales de base imponible, IVA, IRPF y total calculado【F:Presupuesto.php†L12-L24】.

| Postcondición | Descripción |
|---------------|-------------|
| Documento activo | Queda disponible para convertirse en pedido. |
| Notificación opcional | Puede enviarse al cliente por email con el PDF correspondiente. |

## 4. Conversión de presupuesto a pedido y de pedido a actuación
**Actor(es):** administrador, usuario cliente.

| Precondición | Descripción |
|--------------|-------------|
| Presupuesto aceptado | Debe estar en estado *aceptado*.
| Catálogo de productos | Disponible para reutilizar líneas. |

**Flujo principal**
1. Desde el presupuesto aprobado, seleccionar la acción **Convertir a pedido**.
2. Revisar y completar datos del pedido (serie, número, estado).
3. Una vez confirmado, asignar el pedido a una **actuación** cuando comience el trabajo.

**Resultado esperado**
- Pedido creado enlazado al presupuesto (`presupuesto_id`) y, posteriormente, a la actuación (`actuacion_id`)【F:Pedido.php†L12-L21】.

| Postcondición | Descripción |
|---------------|-------------|
| Pedido trazable | El pedido mantiene referencia al presupuesto original. |
| Actuación planificada | El pedido queda asociado a la actuación correspondiente. |

## 5. Gestión de actuaciones
**Actor(es):** administrador, usuario cliente.

| Precondición | Descripción |
|--------------|-------------|
| Pedido asociado | La actuación suele derivarse de uno o varios pedidos. |
| Recursos disponibles | Personal y productos necesarios. |

**Flujo principal**
1. Crear una **actuación** indicando cliente, fechas y estado inicial (`abierta`, `en_proceso` o `completada`)【F:Actuacion.php†L12-L14】.
2. Registrar líneas de productos/servicios usados durante la intervención【F:ActuacionProducto.php†L12-L23】.
3. Actualizar el estado conforme avanza el trabajo y cerrar cuando finalice.

**Resultado esperado**
- Actuación registrada y vinculada a pedidos y productos. Puede generar una o varias facturas posteriores【F:Actuacion.php†L16-L20】.

| Postcondición | Descripción |
|---------------|-------------|
| Historial de trabajo | La actuación guarda notas, fechas y materiales empleados. |
| Preparación de facturación | Las actuaciones completadas pueden facturarse. |

## 6. Creación y emisión de facturas con cálculo de IVA/IRPF
**Actor(es):** administrador, usuario cliente.

| Precondición | Descripción |
|--------------|-------------|
| Actuación completada | Se requiere al menos una actuación finalizada. |
| Datos fiscales configurados | Serie, número y porcentajes de impuestos. |

**Flujo principal**
1. Generar la factura desde una actuación o desde el listado de facturas.
2. Añadir líneas de productos o importar desde actuaciones.
3. Confirmar totales y estado (*borrador*, *enviado*, *pagado*).
4. Emitir el PDF para envío al cliente usando el servicio DomPDF【F:Services/Pdf/DompdfPdfService.php†L7-L12】.

**Resultado esperado**
- Factura almacenada con totales de base imponible, IVA e IRPF, y con referencia a las actuaciones asociadas【F:Factura.php†L12-L22】.

| Postcondición | Descripción |
|---------------|-------------|
| Documento fiscal | La factura queda lista para impresión o envío. |
| Asociación de actuaciones | Registro en la tabla pivot `actuacion_factura` si agrupa varias actuaciones. |

## 7. Exportación de datos (PDF, CSV)
**Actor(es):** administrador, usuario cliente.

| Precondición | Descripción |
|--------------|-------------|
| Registros existentes | Debe haber datos en el módulo elegido. |

**Flujo principal**
1. Desde cualquier listado (clientes, productos, etc.), seleccionar registros.
2. Usar la acción **Exportar** para descargar CSV/XLSX【F:Filament/Resources/ClienteResource.php†L66-L75】.
3. Para PDF, utilizar las opciones de generación de documentos que emplean la plantilla `resources/views/pdf/documento.blade.php`【F:resources/views/pdf/documento.blade.php†L1-L37】.

**Resultado esperado**
- Archivos exportados que pueden compartirse o archivarse.

| Postcondición | Descripción |
|---------------|-------------|
| Descarga completada | El usuario obtiene el archivo en su equipo. |

## Ejemplos prácticos
### Ciclo completo: Cliente → Presupuesto → Pedido → Actuación → Factura
1. **Alta de cliente** en `/admin/clientes`.
2. **Creación de presupuesto** asociando productos existentes.
3. **Conversión a pedido** cuando el cliente acepta.
4. **Planificación de actuación** para ejecutar el trabajo del pedido.
5. **Generación de factura** desde la actuación y envío del PDF al cliente.

### Referencias de vistas
- Panel de administración Filament disponible en `/admin`.
- Plantilla PDF base en `resources/views/pdf/documento.blade.php`.
