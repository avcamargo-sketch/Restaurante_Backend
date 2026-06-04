<?php
use Slim\Factory\AppFactory;

// Cargar autoload de Composer (clases de Slim, Eloquent, etc.)
require __DIR__ . '/../vendor/autoload.php';

// Cargar configuración de base de datos (Eloquent)
require __DIR__ . '/../app/Config/database.php';

// Cargar middleware CORS
$cors = require __DIR__ . '/../app/Middleware/CorsMiddleware.php';

// Cargar rutas/endpoints
$endpoints = require __DIR__ . '/../app/Routes/endpoints.php';

// Crear la aplicación Slim
$app = AppFactory::create();

// Aplicar CORS
$cors($app);

// Registrar rutas
$endpoints($app);

// ¡Arrancar la aplicación!
$app->run();