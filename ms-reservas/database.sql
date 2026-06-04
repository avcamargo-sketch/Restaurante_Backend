CREATE DATABASE IF NOT EXISTS restaurante_reservas;
USE restaurante_reservas;

CREATE TABLE IF NOT EXISTS mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(50) NOT NULL UNIQUE,
    capacidad INT NOT NULL,
    estado ENUM('disponible', 'reservada', 'ocupada', 'fuera_servicio') NOT NULL DEFAULT 'disponible',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(150) NOT NULL,
    telefono_cliente VARCHAR(50) NOT NULL,
    cantidad_personas INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    observaciones TEXT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'finalizada') NOT NULL DEFAULT 'pendiente',
    mesa_id INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id)
);

INSERT INTO mesas (numero, capacidad, estado, created_at, updated_at) VALUES
('Mesa 1', 4, 'disponible', NOW(), NOW()),
('Mesa 2', 2, 'disponible', NOW(), NOW()),
('Mesa 3', 6, 'disponible', NOW(), NOW());
