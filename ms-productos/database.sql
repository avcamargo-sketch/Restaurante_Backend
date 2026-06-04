CREATE DATABASE IF NOT EXISTS restaurante_productos;
USE restaurante_productos;

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL UNIQUE,
    categoria VARCHAR(80) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    disponible TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

INSERT INTO productos (nombre, categoria, precio, disponible, created_at, updated_at) VALUES
('Sopa del dia', 'Entradas', 12000, 1, NOW(), NOW()),
('Limonada natural', 'Bebidas', 7000, 1, NOW(), NOW()),
('Bandeja especial', 'Platos fuertes', 28000, 1, NOW(), NOW()),
('Postre de la casa', 'Postres', 9000, 1, NOW(), NOW());
