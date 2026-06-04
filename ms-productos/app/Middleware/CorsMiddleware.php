<?php
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    // Manejar peticiones OPTIONS (preflight de CORS)
    $app->options('/{routes:.+}', fn($req, $res) => $res);
    
    // Middleware CORS
    $app->add(function (Request $request, $handler) {
        // Obtener el origen de la petición
        $origin = $request->getHeaderLine('Origin') ?: '*';
        
        // Procesar la petición
        $response = $handler->handle($request);
        
        // Agregar headers CORS a la respuesta
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
        
        // Si es OPTIONS, responder 200 sin procesar más
        if ($request->getMethod() === 'OPTIONS') {
            return $response->withStatus(200);
        }
        
        return $response;
    });
};
