<?php
namespace App\Controllers;

use App\Models\Pedido;
use App\Models\DetallePedido;
use Exception;

class PedidosController
{
    // ---------- Listar todos los pedidos ----------
    function getPedidos($filtros = []) {
        $query = Pedido::query();
        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }
        return $query->get();
    }

    // ---------- Obtener pedido por ID ----------
    function getPedido($id) {
        $pedido = Pedido::find($id);
        if (empty($pedido)) {
            throw new Exception("Pedido $id no existe", 1);
        }
        $detalles = DetallePedido::where('pedido_id', $id)->get();
        $resultado = $pedido->toArray();
        $resultado['detalles'] = $detalles->toArray();
        return $resultado;
    }

    // ---------- Crear pedido ----------
    function guardarPedido($data) {
        if (empty($data['mesa_id'])) {
            throw new Exception("Falta mesa", 2);
        }
        if (empty($data['productos']) || !is_array($data['productos']) || count($data['productos']) == 0) {
            throw new Exception("El pedido no puede estar vacío", 3);
        }

        $subtotal = 0;
        $cantidadTotal = 0;

        foreach ($data['productos'] as $producto) {
            if (empty($producto['cantidad']) || $producto['cantidad'] < 1) {
                throw new Exception("Cantidad debe ser mayor o igual a uno", 4);
            }
            $cantidadTotal += $producto['cantidad'];
            $subtotal += $producto['cantidad'] * $producto['precio_unitario'];
        }

        $pedido = new Pedido();
        $pedido->mesa_id = $data['mesa_id'];
        $pedido->fecha = date('Y-m-d');
        $pedido->hora = date('H:i:s');
        $pedido->subtotal = $subtotal;
        $pedido->total = $subtotal;
        $pedido->cantidad_total = $cantidadTotal;
        $pedido->estado = 'pendiente';
        $pedido->save();

        foreach ($data['productos'] as $producto) {
            $detalle = new DetallePedido();
            $detalle->pedido_id = $pedido->id;
            $detalle->producto_id = $producto['producto_id'];
            $detalle->nombre_producto = $producto['nombre_producto'];
            $detalle->cantidad = $producto['cantidad'];
            $detalle->precio_unitario = $producto['precio_unitario'];
            $detalle->subtotal = $producto['cantidad'] * $producto['precio_unitario'];
            $detalle->save();
        }

        return $this->getPedido($pedido->id);
    }

    // ---------- Agregar productos ----------
    function agregarProductos($id, $productos) {
        $pedido = Pedido::find($id);
        if (empty($pedido)) {
            throw new Exception("Pedido $id no existe", 1);
        }
        if ($pedido->estado == 'pagado' || $pedido->estado == 'cancelado') {
            throw new Exception("No se puede modificar pedido " . $pedido->estado, 5);
        }
        if (empty($productos) || !is_array($productos) || count($productos) == 0) {
            throw new Exception("Debe agregar al menos un producto", 6);
        }

        $subtotalNuevo = 0;
        $cantidadNueva = 0;

        foreach ($productos as $producto) {
            if (empty($producto['cantidad']) || $producto['cantidad'] < 1) {
                throw new Exception("Cantidad debe ser mayor o igual a uno", 4);
            }
            $cantidadNueva += $producto['cantidad'];

            $detalleExistente = DetallePedido::where('pedido_id', $id)
                ->where('producto_id', $producto['producto_id'])
                ->first();

            if ($detalleExistente) {
                $detalleExistente->cantidad += $producto['cantidad'];
                $detalleExistente->subtotal = $detalleExistente->cantidad * $detalleExistente->precio_unitario;
                $detalleExistente->save();
                $subtotalNuevo += $producto['cantidad'] * $detalleExistente->precio_unitario;
            } else {
                $detalle = new DetallePedido();
                $detalle->pedido_id = $id;
                $detalle->producto_id = $producto['producto_id'];
                $detalle->nombre_producto = $producto['nombre_producto'];
                $detalle->cantidad = $producto['cantidad'];
                $detalle->precio_unitario = $producto['precio_unitario'];
                $detalle->subtotal = $producto['cantidad'] * $producto['precio_unitario'];
                $detalle->save();
                $subtotalNuevo += $detalle->subtotal;
            }
        }

        $pedido->subtotal += $subtotalNuevo;
        $pedido->total += $subtotalNuevo;
        $pedido->cantidad_total += $cantidadNueva;
        $pedido->save();

        return $this->getPedido($id);
    }

    // ---------- Cambiar estado ----------
    function cambiarEstadoPedido($id, $estado) {
        $pedido = Pedido::find($id);
        if (empty($pedido)) {
            throw new Exception("Pedido $id no existe", 1);
        }
        $estadosPermitidos = ['pendiente', 'en_preparacion', 'entregado', 'pagado', 'cancelado'];
        if (!in_array($estado, $estadosPermitidos)) {
            throw new Exception("Estado no válido", 7);
        }
        $pedido->estado = $estado;
        $pedido->save();
        return $this->getPedido($id);
    }

    // ---------- Cancelar ----------
    function cancelarPedido($id) {
        return $this->cambiarEstadoPedido($id, 'cancelado');
    }

    // ---------- Eliminar producto ----------
    function eliminarProducto($pedidoId, $productoId) {
        $pedido = Pedido::find($pedidoId);
        if (empty($pedido)) {
            throw new Exception("Pedido $pedidoId no existe", 1);
        }
        if ($pedido->estado == 'pagado' || $pedido->estado == 'cancelado') {
            throw new Exception("No se puede modificar pedido " . $pedido->estado, 5);
        }

        $detalles = DetallePedido::where('pedido_id', $pedidoId)->get();
        $detalle = null;
        foreach ($detalles as $d) {
            if ($d->producto_id == $productoId) {
                $detalle = $d;
                break;
            }
        }

        if (empty($detalle)) {
            throw new Exception("Producto no encontrado en el pedido", 8);
        }

        $pedido->subtotal -= $detalle->subtotal;
        $pedido->total -= $detalle->subtotal;
        $pedido->cantidad_total -= $detalle->cantidad;
        $pedido->save();

        $detalle->delete();

        $cantidadDetalles = DetallePedido::where('pedido_id', $pedidoId)->count();
        if ($cantidadDetalles == 0) {
            $pedido->estado = 'cancelado';
            $pedido->subtotal = 0;
            $pedido->total = 0;
            $pedido->cantidad_total = 0;
            $pedido->save();
        }

        return $this->getPedido($pedidoId);
    }
}
