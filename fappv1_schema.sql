CREATE DATABASE IF NOT EXISTS fappv1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fappv1;

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- Drop existing tables
DROP TABLE IF EXISTS actuacion_factura;
DROP TABLE IF EXISTS factura_productos;
DROP TABLE IF EXISTS actuacion_productos;
DROP TABLE IF EXISTS pedido_productos;
DROP TABLE IF EXISTS presupuesto_productos;
DROP TABLE IF EXISTS facturas;
DROP TABLE IF EXISTS actuaciones;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS presupuestos;
DROP TABLE IF EXISTS series;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS role_has_permissions;
DROP TABLE IF EXISTS model_has_roles;
DROP TABLE IF EXISTS model_has_permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS failed_jobs;
DROP TABLE IF EXISTS job_batches;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS users;

-- Infraestructura Laravel ------------------------------------------------------

-- Tabla de usuarios
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tokens de reseteo de contraseña
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sesiones
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity),
    CONSTRAINT sessions_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bloqueos de cache
CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jobs de cola
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lotes de jobs
CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jobs fallidos
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permisos
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY permissions_name_guard_name_unique (name, guard_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY roles_name_guard_name_unique (name, guard_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permisos asignados a modelos
CREATE TABLE model_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type),
    INDEX model_has_permissions_model_id_model_type_index (model_id, model_type),
    CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id)
        REFERENCES permissions(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles asignados a modelos
CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    INDEX model_has_roles_model_id_model_type_index (model_id, model_type),
    CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id)
        REFERENCES roles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación roles-permisos
CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id)
        REFERENCES permissions(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id)
        REFERENCES roles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Núcleo ERP -------------------------------------------------------------------

-- Clientes
CREATE TABLE clientes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    cif VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    telefono VARCHAR(255) NULL,
    direccion VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX clientes_usuario_id_nombre_index (usuario_id, nombre),
    CONSTRAINT clientes_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Productos
CREATE TABLE productos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    precio DECIMAL(14,2) NOT NULL DEFAULT 0,
    iva_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX productos_usuario_id_activo_index (usuario_id, activo),
    CONSTRAINT productos_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Series de numeración
CREATE TABLE series (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    tipo ENUM('presupuesto','pedido','factura') NOT NULL,
    serie VARCHAR(20) NOT NULL DEFAULT 'A',
    siguiente_numero BIGINT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY series_usuario_tipo_serie_unique (usuario_id, tipo, serie),
    CONSTRAINT series_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Presupuestos
CREATE TABLE presupuestos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    cliente_id BIGINT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    numero BIGINT UNSIGNED NOT NULL DEFAULT 0,
    serie VARCHAR(20) NOT NULL DEFAULT 'A',
    estado ENUM('borrador','enviado','aceptado','rechazado') NOT NULL DEFAULT 'borrador',
    validez_dias INT UNSIGNED NULL,
    notas TEXT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    base_imponible DECIMAL(14,2) NOT NULL DEFAULT 0,
    iva_total DECIMAL(14,2) NOT NULL DEFAULT 0,
    irpf_total DECIMAL(14,2) NOT NULL DEFAULT 0,
    total DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY presupuestos_usuario_serie_numero_unique (usuario_id, serie, numero),
    INDEX presupuestos_usuario_cliente_estado_fecha_activo_index (usuario_id, cliente_id, estado, fecha, activo),
    CONSTRAINT presupuestos_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT presupuestos_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Actuaciones
CREATE TABLE actuaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    cliente_id BIGINT UNSIGNED NOT NULL,
    codigo VARCHAR(255) NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado ENUM('abierta','en_proceso','completada') NOT NULL DEFAULT 'abierta',
    notas TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX actuaciones_usuario_cliente_estado_fecha_inicio_index (usuario_id, cliente_id, estado, fecha_inicio),
    CONSTRAINT actuaciones_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT actuaciones_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pedidos
CREATE TABLE pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    cliente_id BIGINT UNSIGNED NOT NULL,
    presupuesto_id BIGINT UNSIGNED NULL,
    actuacion_id BIGINT UNSIGNED NULL,
    fecha DATE NOT NULL,
    numero BIGINT UNSIGNED NOT NULL DEFAULT 0,
    serie VARCHAR(20) NOT NULL DEFAULT 'A',
    estado ENUM('borrador','confirmado','servido','cerrado') NOT NULL DEFAULT 'borrador',
    notas TEXT NULL,
    base_imponible DECIMAL(14,2) NOT NULL DEFAULT 0,
    iva_total DECIMAL(14,2) NOT NULL DEFAULT 0,
    irpf_total DECIMAL(14,2) NOT NULL DEFAULT 0,
    total DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY pedidos_usuario_serie_numero_unique (usuario_id, serie, numero),
    INDEX pedidos_usuario_cliente_estado_fecha_index (usuario_id, cliente_id, estado, fecha),
    CONSTRAINT pedidos_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT pedidos_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT pedidos_presupuesto_id_foreign FOREIGN KEY (presupuesto_id) REFERENCES presupuestos(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT pedidos_actuacion_id_foreign FOREIGN KEY (actuacion_id) REFERENCES actuaciones(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Facturas
CREATE TABLE facturas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    cliente_id BIGINT UNSIGNED NOT NULL,
    presupuesto_id BIGINT UNSIGNED NULL,
    numero BIGINT UNSIGNED NOT NULL DEFAULT 0,
    serie VARCHAR(20) NOT NULL DEFAULT 'A',
    fecha DATE NOT NULL,
    estado ENUM('borrador','enviado','pagado') NOT NULL DEFAULT 'borrador',
    notas TEXT NULL,
    base_imponible DECIMAL(14,2) NOT NULL DEFAULT 0,
    iva_total DECIMAL(14,2) NOT NULL DEFAULT 0,
    irpf_total DECIMAL(14,2) NOT NULL DEFAULT 0,
    total DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY facturas_usuario_serie_numero_unique (usuario_id, serie, numero),
    INDEX facturas_usuario_cliente_estado_fecha_index (usuario_id, cliente_id, estado, fecha),
    CONSTRAINT facturas_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT facturas_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT facturas_presupuesto_id_foreign FOREIGN KEY (presupuesto_id) REFERENCES presupuestos(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Líneas de presupuestos
CREATE TABLE presupuesto_productos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    presupuesto_id BIGINT UNSIGNED NOT NULL,
    producto_id BIGINT UNSIGNED NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad DECIMAL(12,3) NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(14,2) NOT NULL DEFAULT 0,
    iva_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    irpf_porcentaje DECIMAL(5,2) NULL,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX presupuesto_productos_presupuesto_id_index (presupuesto_id),
    CONSTRAINT presupuesto_productos_presupuesto_id_foreign FOREIGN KEY (presupuesto_id) REFERENCES presupuestos(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT presupuesto_productos_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Líneas de actuaciones
CREATE TABLE actuacion_productos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actuacion_id BIGINT UNSIGNED NOT NULL,
    producto_id BIGINT UNSIGNED NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad DECIMAL(12,3) NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(14,2) NOT NULL DEFAULT 0,
    iva_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    irpf_porcentaje DECIMAL(5,2) NULL,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX actuacion_productos_actuacion_id_index (actuacion_id),
    CONSTRAINT actuacion_productos_actuacion_id_foreign FOREIGN KEY (actuacion_id) REFERENCES actuaciones(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT actuacion_productos_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Líneas de pedidos
CREATE TABLE pedido_productos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    producto_id BIGINT UNSIGNED NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad DECIMAL(12,3) NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(14,2) NOT NULL DEFAULT 0,
    iva_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    irpf_porcentaje DECIMAL(5,2) NULL,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX pedido_productos_pedido_id_index (pedido_id),
    CONSTRAINT pedido_productos_pedido_id_foreign FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT pedido_productos_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Líneas de facturas
CREATE TABLE factura_productos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    factura_id BIGINT UNSIGNED NOT NULL,
    producto_id BIGINT UNSIGNED NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad DECIMAL(12,3) NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(14,2) NOT NULL DEFAULT 0,
    iva_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    irpf_porcentaje DECIMAL(5,2) NULL,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX factura_productos_factura_id_index (factura_id),
    CONSTRAINT factura_productos_factura_id_foreign FOREIGN KEY (factura_id) REFERENCES facturas(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT factura_productos_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación actuaciones-facturas
CREATE TABLE actuacion_factura (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actuacion_id BIGINT UNSIGNED NOT NULL,
    factura_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY actuacion_factura_actuacion_factura_unique (actuacion_id, factura_id),
    INDEX actuacion_factura_factura_id_index (factura_id),
    CONSTRAINT actuacion_factura_actuacion_id_foreign FOREIGN KEY (actuacion_id) REFERENCES actuaciones(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT actuacion_factura_factura_id_foreign FOREIGN KEY (factura_id) REFERENCES facturas(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Datos de ejemplo -------------------------------------------------------------
-- INSERT INTO users (id, name, email, password, created_at) VALUES
--   (1, 'Nacho', 'nacho@nacho.es', '$2y$12$giCHHJBqNSLtxfEEmjYci.HtdBCHAqJWLK94IhSK1fTBnhH61gsAm', NOW());
-- INSERT INTO clientes (id, usuario_id, nombre, email) VALUES
--   (1,1,'Cliente Uno','cliente1@example.com'),
--   (2,1,'Cliente Dos','cliente2@example.com');
-- INSERT INTO productos (id, usuario_id, nombre, precio) VALUES
--   (1,1,'Mano de obra',35.00),
--   (2,1,'Split AC 3000fg',599.00);
-- INSERT INTO presupuestos (id, usuario_id, cliente_id, fecha, numero, serie, estado, base_imponible, iva_total, irpf_total, total, created_at) VALUES
--   (1,1,1,CURDATE(),1,'A','borrador',669.00,140.49,0.00,809.49,NOW());
-- INSERT INTO presupuesto_productos (presupuesto_id, producto_id, descripcion, cantidad, precio_unitario, subtotal) VALUES
--   (1,1,'Mano de obra',2,35.00,70.00),
--   (1,2,'Split AC 3000fg',1,599.00,599.00);
-- (Agregar registros similares para pedidos, actuaciones y facturas según sea necesario.)

-- Limpieza segura --------------------------------------------------------------
-- DELETE FROM actuacion_factura;
-- DELETE FROM factura_productos;
-- DELETE FROM actuacion_productos;
-- DELETE FROM pedido_productos;
-- DELETE FROM presupuesto_productos;
-- DELETE FROM facturas;
-- DELETE FROM actuaciones;
-- DELETE FROM pedidos;
-- DELETE FROM presupuestos;
-- DELETE FROM series;
-- DELETE FROM productos;
-- DELETE FROM clientes;
