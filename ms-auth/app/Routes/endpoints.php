<?php
use App\Controllers\AuthController;
use Slim\App;

return function (App $app) {
    
    
    $app->post('/login', function ($request, $response) {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new AuthController();
            $resultado = $controller->login($data);
            $response->getBody()->write(json_encode($resultado));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = ($ex->getCode() == 1) ? 400 : 401;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

   
    $app->post('/logout', function ($request, $response) {
        try {
            $token = str_replace('Bearer ', '', $request->getHeaderLine('Authorization'));
            $controller = new AuthController();
            $controller->logout($token);
            $response->getBody()->write(json_encode(['msg' => 'Sesión cerrada']));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

   
    $app->get('/validar', function ($request, $response) {
        $token = str_replace('Bearer ', '', $request->getHeaderLine('Authorization'));
        $controller = new AuthController();
        $valido = $controller->validarSesion($token);
        $response->getBody()->write(json_encode(['valido' => $valido]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
};