<?php
use App\Controllers\ProductosController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    
    // ============================================================
    // CATEGORÍAS
    // ============================================================

    // ---------- Listar todas las categorías ----------
    $app->get('/categorias', function (Request $request, Response $response) {
        try {
            $controller = new ProductosController();
            $categorias = $controller->getCategorias();
            
            $response->getBody()->write($categorias->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'Error en el servicio']));
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Consultar categoría por ID ----------
    $app->get('/categoria/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $controller = new ProductosController();
            $categoria = $controller->getCategoria($id);
            
            $response->getBody()->write($categoria->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = ($ex->getCode() == 1) ? 404 : 400;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Crear categoría ----------
    $app->post('/categoria', function (Request $request, Response $response) {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new ProductosController();
            $categoria = $controller->guardarCategoria($data);
            
            $response->getBody()->write($categoria->toJson());
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 2 || $ex->getCode() == 3) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Editar categoría ----------
    $app->put('/categoria/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new ProductosController();
            $categoria = $controller->modificarCategoria($id, $data);
            
            $response->getBody()->write($categoria->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 1) {
                $code = 404;
            } else if ($ex->getCode() == 3) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Eliminar categoría ----------
    $app->delete('/categoria/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $controller = new ProductosController();
            $controller->borrarCategoria($id);
            
            $response->getBody()->write(json_encode(['msg' => 'Categoria eliminada']));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 1) {
                $code = 404;
            } else if ($ex->getCode() == 4) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ============================================================
    // PRODUCTOS
    // ============================================================

    // ---------- Listar productos (con filtros opcionales) ----------
    $app->get('/productos', function (Request $request, Response $response) {
        try {
            $params = $request->getQueryParams();
            $filtros = [];
            
            if (!empty($params['categoria'])) {
                $filtros['categoria'] = $params['categoria'];
            }
            if (isset($params['disponible'])) {
                $filtros['disponible'] = $params['disponible'];
            }
            
            $controller = new ProductosController();
            $productos = $controller->getProductos($filtros);
            
            $response->getBody()->write($productos->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'Error en el servicio']));
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Consultar producto por ID ----------
    $app->get('/producto/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $controller = new ProductosController();
            $producto = $controller->getProducto($id);
            
            $response->getBody()->write($producto->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = ($ex->getCode() == 5) ? 404 : 400;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Crear producto ----------
    $app->post('/producto', function (Request $request, Response $response) {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new ProductosController();
            $producto = $controller->guardarProducto($data);
            
            $response->getBody()->write($producto->toJson());
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() >= 6 && $ex->getCode() <= 9) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Editar producto ----------
    $app->put('/producto/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new ProductosController();
            $producto = $controller->modificarProducto($id, $data);
            
            $response->getBody()->write($producto->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 5) {
                $code = 404;
            } else if ($ex->getCode() >= 7 && $ex->getCode() <= 9) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Eliminar producto ----------
    $app->delete('/producto/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $controller = new ProductosController();
            $controller->borrarProducto($id);
            
            $response->getBody()->write(json_encode(['msg' => 'Producto eliminado']));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = ($ex->getCode() == 5) ? 404 : 400;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });
};
