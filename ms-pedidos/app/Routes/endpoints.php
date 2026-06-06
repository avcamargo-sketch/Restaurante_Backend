<?php
use App\Controllers\PedidosController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    
    // ---------- Listar pedidos ----------
    $app->get('/pedidos', function (Request $request, Response $response) {
        try {
            $params = $request->getQueryParams();
            $filtros = [];
            if (!empty($params['estado'])) {
                $filtros['estado'] = $params['estado'];
            }
            $controller = new PedidosController();
            $pedidos = $controller->getPedidos($filtros);
            $response->getBody()->write($pedidos->toJson());
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'Error en el servicio', 'error' => $ex->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Consultar pedido ----------
    $app->get('/pedido/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $controller = new PedidosController();
            $pedido = $controller->getPedido($id);
            $response->getBody()->write(json_encode($pedido));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = ($ex->getCode() == 1) ? 404 : 400;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Crear pedido ----------
    $app->post('/pedido', function (Request $request, Response $response) {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new PedidosController();
            $pedido = $controller->guardarPedido($data);
            $response->getBody()->write(json_encode($pedido));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = ($ex->getCode() >= 2 && $ex->getCode() <= 4) ? 406 : 400;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Agregar productos ----------
    $app->post('/pedido/{id}/productos', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new PedidosController();
            $pedido = $controller->agregarProductos($id, $data['productos']);
            $response->getBody()->write(json_encode($pedido));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = ($ex->getCode() == 1) ? 404 : (($ex->getCode() >= 4 && $ex->getCode() <= 6) ? 406 : 400);
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Cambiar estado ----------
    $app->put('/pedido/{id}/estado', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new PedidosController();
            $pedido = $controller->cambiarEstadoPedido($id, $data['estado']);
            $response->getBody()->write(json_encode($pedido));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = ($ex->getCode() == 1) ? 404 : (($ex->getCode() == 7) ? 406 : 400);
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Cancelar pedido ----------
    $app->put('/pedido/{id}/cancelar', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $controller = new PedidosController();
            $pedido = $controller->cancelarPedido($id);
            $response->getBody()->write(json_encode($pedido));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = ($ex->getCode() == 1) ? 404 : 400;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Eliminar producto ----------
    $app->delete('/pedido/{pedido_id}/producto/{producto_id}', function (Request $request, Response $response, $args) {
        try {
            $pedidoId = $args['pedido_id'];
            $productoId = $args['producto_id'];
            $controller = new PedidosController();
            $pedido = $controller->eliminarProducto($pedidoId, $productoId);
            $response->getBody()->write(json_encode($pedido));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = ($ex->getCode() == 1) ? 404 : (($ex->getCode() == 5 || $ex->getCode() == 8) ? 406 : 400);
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });
};
