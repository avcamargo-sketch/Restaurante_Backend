<?php
use Slim\Factory\AppFactory;

// Cargar autoload de Composer
require __DIR__ . '/../vendor/autoload.php';

// Cargar configuración de base de datos
require __DIR__ . '/../app/Config/database.php';

// Cargar middleware CORS
$cors = require __DIR__ . '/../app/Middleware/CorsMiddleware.php';

// Cargar rutas
$endpoints = require __DIR__ . '/../app/Routes/endpoints.php';

// Crear la aplicación Slim
$app = AppFactory::create();

// Aplicar CORS
$cors($app);

// Registrar rutas
$endpoints($app);

// ¡Arrancar!
$app->run();
