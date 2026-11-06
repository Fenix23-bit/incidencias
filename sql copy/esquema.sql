-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS portal_asesorias
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE portal_asesorias;

-- 1. Tabla de Estudiantes
CREATE TABLE IF NOT EXISTS estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE, -- Garantiza correo único
    password_hash VARCHAR(255) NOT NULL, -- Guardará el hash seguro
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabla de Solicitudes
CREATE TABLE IF NOT EXISTS solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    materia VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    disponibilidad VARCHAR(255) NOT NULL,
    estado ENUM('Pendiente', 'Atendida', 'Cancelada') DEFAULT 'Pendiente', -- Estado inicial Pendiente
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_solicitudes_estudiantes 
        FOREIGN KEY (estudiante_id) 
        REFERENCES estudiantes(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB;