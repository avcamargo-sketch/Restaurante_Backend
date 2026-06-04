<?php
use App\Controllers\AuthController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    
    // ============================================
    // LOGIN: POST /login
    // ============================================
    $app->post('/login', function (Request $request, Response $response) {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new AuthController();
            $resultado = $controller->login($data);
            
            $response->getBody()->write(json_encode($resultado));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 401;
            $msg = $ex->getMessage();
            
            $response->getBody()->write(json_encode(['msg' => $msg]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ============================================
    // LOGOUT: POST /logout
    // ============================================
    $app->post('/logout', function (Request $request, Response $response) {
        try {
            $authHeader = $request->getHeaderLine('Authorization');
            $token = str_replace('Bearer ', '', $authHeader);
            
            $controller = new AuthController();
            $controller->logout($token);
            
            $response->getBody()->write(json_encode(['msg' => 'Sesión cerrada']));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ============================================
    // VALIDAR SESIÓN: GET /validar
    // ============================================
    $app->get('/validar', function (Request $request, Response $response) {
        try {
            $authHeader = $request->getHeaderLine('Authorization');
            $token = str_replace('Bearer ', '', $authHeader);
            
            $controller = new AuthController();
            $resultado = $controller->validarSesion($token);
            
            $response->getBody()->write(json_encode($resultado));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
        }
    });
};