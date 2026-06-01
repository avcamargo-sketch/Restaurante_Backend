<?php
use App\Controllers\MesaController;
use App\Controllers\ReservaController;
use Slim\App;

return function (App $app) {
    
    // ========== MESAS ==========
    $app->get('/mesas', function ($request, $response) {
        $controller = new MesaController();
        $mesas = $controller->getMesas();
        $response->getBody()->write($mesas->toJson());
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $app->get('/mesa/{id}', function ($request, $response, $args) {
        try {
            $controller = new MesaController();
            $mesa = $controller->getMesa($args['id']);
            $response->getBody()->write($mesa->toJson());
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->post('/mesa', function ($request, $response) {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new MesaController();
            $mesa = $controller->crearMesa($data);
            $response->getBody()->write($mesa->toJson());
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = in_array($ex->getCode(), [2,3,4]) ? 400 : 500;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/mesa/{id}', function ($request, $response, $args) {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new MesaController();
            $mesa = $controller->editarMesa($args['id'], $data);
            $response->getBody()->write($mesa->toJson());
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->delete('/mesa/{id}', function ($request, $response, $args) {
        try {
            $controller = new MesaController();
            $controller->eliminarMesa($args['id']);
            $response->getBody()->write(json_encode(['msg' => 'Mesa eliminada']));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    });

    // ========== RESERVAS ==========
    $app->get('/reservas', function ($request, $response) {
        $params = $request->getQueryParams();
        $controller = new ReservaController();
        $reservas = $controller->getReservas($params);
        $response->getBody()->write($reservas->toJson());
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $app->get('/reserva/{id}', function ($request, $response, $args) {
        try {
            $controller = new ReservaController();
            $reserva = $controller->getReserva($args['id']);
            $response->getBody()->write($reserva->toJson());
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->post('/reserva', function ($request, $response) {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new ReservaController();
            $reserva = $controller->crearReserva($data);
            $response->getBody()->write($reserva->toJson());
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = in_array($ex->getCode(), [2,3,4,5,6,7]) ? 400 : 500;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/reserva/{id}', function ($request, $response, $args) {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new ReservaController();
            $reserva = $controller->editarReserva($args['id'], $data);
            $response->getBody()->write($reserva->toJson());
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/reserva/{id}/cancelar', function ($request, $response, $args) {
        try {
            $controller = new ReservaController();
            $reserva = $controller->cancelarReserva($args['id']);
            $response->getBody()->write($reserva->toJson());
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    });
};