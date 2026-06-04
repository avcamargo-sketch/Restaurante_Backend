<?php

use App\Controllers\PedidoController;
use Slim\App;

return function (App $app) {
    $app->get('/pedidos', function ($request, $response) {
        $controller = new PedidoController();
        $pedidos = $controller->getPedidos($request->getQueryParams());
        $response->getBody()->write($pedidos->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/pedidos/{id}', function ($request, $response, $args) {
        try {
            $controller = new PedidoController();
            $pedido = $controller->getPedido($args['id']);
            $response->getBody()->write($pedido->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->post('/pedidos', function ($request, $response) {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $controller = new PedidoController();
            $pedido = $controller->crearPedido($data);
            $response->getBody()->write($pedido->load('items')->toJson());
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/pedidos/{id}/estado', function ($request, $response, $args) {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $controller = new PedidoController();
            $pedido = $controller->cambiarEstado($args['id'], $data['estado'] ?? '');
            $response->getBody()->write($pedido->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->post('/pedidos/{id}/items', function ($request, $response, $args) {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $controller = new PedidoController();
            $pedido = $controller->agregarItem($args['id'], $data);
            $response->getBody()->write($pedido->toJson());
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/pedidos/{id}/items/{itemId}', function ($request, $response, $args) {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $controller = new PedidoController();
            $pedido = $controller->editarItem($args['id'], $args['itemId'], $data);
            $response->getBody()->write($pedido->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->delete('/pedidos/{id}/items/{itemId}', function ($request, $response, $args) {
        try {
            $controller = new PedidoController();
            $pedido = $controller->eliminarItem($args['id'], $args['itemId']);
            $response->getBody()->write($pedido->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });
};
