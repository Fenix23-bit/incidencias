-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS portal_incidencias
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE portal_incidencias;

-- 1. Tabla de usuario
CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE, -- Garantiza correo único
    password_hash VARCHAR(255) NOT NULL, -- Guardará el hash seguro
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    rol ENUM('Reportante', 'Administrador') DEFAULT 'Reportante'
) ENGINE=InnoDB;

-- 2. Tabla de Solicitudes
CREATE TABLE IF NOT EXISTS reporte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    asunto VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad ENUM('Baja', 'Media', 'Alta'),
    estado ENUM('Pendiente', 'Atendida', 'Cancelada') DEFAULT 'Pendiente', -- Estado inicial Pendiente
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_solicitudes_estudiantes 
        FOREIGN KEY (usuario_id) 
        REFERENCES usuario(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB;