<?php

use App\Controllers\MesaController;
use App\Controllers\ReservaController;
use Slim\App;

return function (App $app) {
    $app->get('/mesas', function ($request, $response) {
        $controller = new MesaController();
        $mesas = $controller->getMesas();
        $response->getBody()->write($mesas->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/mesas/{id}', function ($request, $response, $args) {
        try {
            $controller = new MesaController();
            $mesa = $controller->getMesa($args['id']);
            $response->getBody()->write($mesa->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->post('/mesas', function ($request, $response) {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $controller = new MesaController();
            $mesa = $controller->crearMesa($data);
            $response->getBody()->write($mesa->toJson());
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/mesas/{id}', function ($request, $response, $args) {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $controller = new MesaController();
            $mesa = $controller->editarMesa($args['id'], $data);
            $response->getBody()->write($mesa->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/mesas/{id}/estado', function ($request, $response, $args) {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $controller = new MesaController();
            $mesa = $controller->cambiarEstado($args['id'], $data['estado'] ?? '');
            $response->getBody()->write($mesa->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->get('/reservas', function ($request, $response) {
        $params = $request->getQueryParams();
        $controller = new ReservaController();
        $reservas = $controller->getReservas($params);
        $response->getBody()->write($reservas->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/reservas/{id}', function ($request, $response, $args) {
        try {
            $controller = new ReservaController();
            $reserva = $controller->getReserva($args['id']);
            $response->getBody()->write($reserva->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->post('/reservas', function ($request, $response) {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $controller = new ReservaController();
            $reserva = $controller->crearReserva($data);
            $response->getBody()->write($reserva->toJson());
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/reservas/{id}', function ($request, $response, $args) {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $controller = new ReservaController();
            $reserva = $controller->editarReserva($args['id'], $data);
            $response->getBody()->write($reserva->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/reservas/{id}/cancelar', function ($request, $response, $args) {
        try {
            $controller = new ReservaController();
            $reserva = $controller->cancelarReserva($args['id']);
            $response->getBody()->write($reserva->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });
};
