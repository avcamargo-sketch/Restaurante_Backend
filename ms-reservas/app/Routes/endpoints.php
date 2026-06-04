<?php
use App\Controllers\ReservasController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    
    // ============================================================
    // MESAS
    // ============================================================

    // ---------- Listar todas las mesas ----------
    $app->get('/mesas', function (Request $request, Response $response) {
        try {
            $controller = new ReservasController();
            $mesas = $controller->getMesas();
            
            $response->getBody()->write($mesas->toJson());
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

    // ---------- Consultar mesa por ID ----------
    $app->get('/mesa/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $controller = new ReservasController();
            $mesa = $controller->getMesa($id);
            
            $response->getBody()->write($mesa->toJson());
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

    // ---------- Crear mesa ----------
    $app->post('/mesa', function (Request $request, Response $response) {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new ReservasController();
            $mesa = $controller->guardarMesa($data);
            
            $response->getBody()->write($mesa->toJson());
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 2 || $ex->getCode() == 3 || $ex->getCode() == 4) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Editar mesa ----------
    $app->put('/mesa/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new ReservasController();
            $mesa = $controller->modificarMesa($id, $data);
            
            $response->getBody()->write($mesa->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 1) {
                $code = 404;
            } else if ($ex->getCode() == 3 || $ex->getCode() == 4) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Cambiar estado de mesa ----------
    $app->put('/mesa/{id}/estado', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new ReservasController();
            $mesa = $controller->cambiarEstadoMesa($id, $data['estado']);
            
            $response->getBody()->write($mesa->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 1) {
                $code = 404;
            } else if ($ex->getCode() == 5) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Eliminar mesa ----------
    $app->delete('/mesa/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $controller = new ReservasController();
            $controller->borrarMesa($id);
            
            $response->getBody()->write(json_encode(['msg' => 'Mesa eliminada']));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 1) {
                $code = 404;
            } else if ($ex->getCode() == 6) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ============================================================
    // RESERVAS
    // ============================================================

    // ---------- Listar reservas (con filtros opcionales) ----------
    $app->get('/reservas', function (Request $request, Response $response) {
        try {
            $params = $request->getQueryParams();
            $filtros = [];
            
            if (!empty($params['fecha'])) {
                $filtros['fecha'] = $params['fecha'];
            }
            if (!empty($params['cliente'])) {
                $filtros['cliente'] = $params['cliente'];
            }
            if (!empty($params['estado'])) {
                $filtros['estado'] = $params['estado'];
            }
            
            $controller = new ReservasController();
            $reservas = $controller->getReservas($filtros);
            
            $response->getBody()->write($reservas->toJson());
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

    // ---------- Consultar reserva por ID ----------
    $app->get('/reserva/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $controller = new ReservasController();
            $reserva = $controller->getReserva($id);
            
            $response->getBody()->write($reserva->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = ($ex->getCode() == 7) ? 404 : 400;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Crear reserva ----------
    $app->post('/reserva', function (Request $request, Response $response) {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new ReservasController();
            $reserva = $controller->guardarReserva($data);
            
            $response->getBody()->write($reserva->toJson());
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() >= 8 && $ex->getCode() <= 13) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Editar reserva ----------
    $app->put('/reserva/{id}', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new ReservasController();
            $reserva = $controller->modificarReserva($id, $data);
            
            $response->getBody()->write($reserva->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 7) {
                $code = 404;
            } else if ($ex->getCode() >= 10 && $ex->getCode() <= 14) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Cancelar reserva ----------
    $app->put('/reserva/{id}/cancelar', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $controller = new ReservasController();
            $reserva = $controller->cancelarReserva($id);
            
            $response->getBody()->write($reserva->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 7) {
                $code = 404;
            } else if ($ex->getCode() == 15) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    // ---------- Cambiar estado de reserva ----------
    $app->put('/reserva/{id}/estado', function (Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new ReservasController();
            $reserva = $controller->cambiarEstadoReserva($id, $data['estado']);
            
            $response->getBody()->write($reserva->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 7) {
                $code = 404;
            } else if ($ex->getCode() == 16) {
                $code = 406;
            }
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    });
};
