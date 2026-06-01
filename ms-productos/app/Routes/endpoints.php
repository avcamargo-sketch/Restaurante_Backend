<?php
use App\Controllers\ProductoController;
use Slim\App;

return function (App $app) {
    
    // ========== PRODUCTOS ==========
    $app->get('/productos', function ($request, $response) {
        $params = $request->getQueryParams();
        $controller = new ProductoController();
        $productos = $controller->getProductos($params);
        $response->getBody()->write($productos->toJson());
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $app->get('/producto/{id}', function ($request, $response, $args) {
        try {
            $controller = new ProductoController();
            $producto = $controller->getProducto($args['id']);
            $response->getBody()->write($producto->toJson());
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->post('/producto', function ($request, $response) {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new ProductoController();
            $producto = $controller->crearProducto($data);
            $response->getBody()->write($producto->toJson());
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = in_array($ex->getCode(), [2,3,4]) ? 400 : 500;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/producto/{id}', function ($request, $response, $args) {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new ProductoController();
            $producto = $controller->editarProducto($args['id'], $data);
            $response->getBody()->write($producto->toJson());
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->delete('/producto/{id}', function ($request, $response, $args) {
        try {
            $controller = new ProductoController();
            $controller->eliminarProducto($args['id']);
            $response->getBody()->write(json_encode(['msg' => 'Producto eliminado']));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    });

    // ========== CATEGORÍAS ==========
    $app->get('/categorias', function ($request, $response) {
        $controller = new ProductoController();
        $categorias = $controller->getCategorias();
        $response->getBody()->write($categorias->toJson());
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
};