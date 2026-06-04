<?php

use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $app->options('/{routes:.+}', fn($request, $response) => $response);

    $app->add(function (Request $request, $handler) {
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Slim\Psr7\Response();
        } else {
            $response = $handler->handle($request);
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    });
};
