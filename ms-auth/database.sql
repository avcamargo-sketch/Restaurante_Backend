CREATE DATABASE IF NOT EXISTS restaurante_auth;
USE restaurante_auth;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    usuario VARCHAR(80) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol VARCHAR(50) NOT NULL DEFAULT 'admin',
    token VARCHAR(255) NULL,
    sesion_activa TINYINT(1) NOT NULL DEFAULT 0,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

INSERT INTO usuarios (nombre, correo, usuario, contrasena, rol, estado, created_at, updated_at)
VALUES ('Administrador', 'admin@restaurante.com', 'admin', '123456', 'admin', 'activo', NOW(), NOW());
