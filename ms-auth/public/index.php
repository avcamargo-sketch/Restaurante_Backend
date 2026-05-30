<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Config/database.php';

$cors = require __DIR__ . '/../app/Middleware/CorsMiddleware.php';
$endpoints = require __DIR__ . '/../app/Routes/endpoints.php';

$app = AppFactory::create();

// CORS
$cors($app);

// Rutas
$endpoints($app);

$app->run();
