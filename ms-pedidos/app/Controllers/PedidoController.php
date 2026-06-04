<?php
namespace App\Controllers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Database\Capsule\Manager as DB;
use Exception;

class PedidoController
{
    private array $estados = ['pendiente', 'en_preparacion', 'entregado', 'pagado', 'cancelado'];

    public function getPedidos($filtros = []) {
        $query = Pedido::with('items');

        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function getPedido($id) {
        $pedido = Pedido::with('items')->find($id);
        if (!$pedido) throw new Exception("Pedido no encontrado", 1);
        return $pedido;
    }

    public function crearPedido($data) {
        if (($data['mesa_estado'] ?? '') === 'disponible') {
            throw new Exception("No se permiten pedidos para mesas disponibles", 2);
        }

        $items = $data['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            throw new Exception("No se permiten pedidos vacios", 3);
        }

        $this->validarItems($items);

        return DB::transaction(function () use ($data, $items) {
            $pedido = Pedido::create([
                'mesa_id' => $data['mesa_id'],
                'mesa_nombre' => $data['mesa_nombre'] ?? '',
                'mesa_estado' => $data['mesa_estado'] ?? '',
                'estado' => 'pendiente',
                'subtotal' => 0,
                'total' => 0,
                'cantidad_total' => 0
            ]);

            foreach ($items as $item) {
                PedidoItem::create($this->datosItem($pedido->id, $item));
            }

            return $this->recalcular($pedido);
        });
    }

    public function cambiarEstado($id, $estado) {
        $pedido = $this->getPedido($id);
        if (!in_array($estado, $this->estados)) {
            throw new Exception("Estado de pedido invalido", 4);
        }

        $pedido->estado = $estado;
        $pedido->save();
        return $pedido->load('items');
    }

    public function agregarItem($pedidoId, $data) {
        $pedido = $this->getPedido($pedidoId);
        $this->validarItems([$data]);
        PedidoItem::create($this->datosItem($pedido->id, $data));
        return $this->recalcular($pedido)->load('items');
    }

    public function editarItem($pedidoId, $itemId, $data) {
        $item = PedidoItem::where('pedido_id', $pedidoId)->where('id', $itemId)->first();
        if (!$item) throw new Exception("Item no encontrado", 5);

        $cantidad = (int)($data['cantidad'] ?? 0);
        if ($cantidad < 1) throw new Exception("La cantidad debe ser mayor o igual a uno", 6);

        $item->cantidad = $cantidad;
        $item->subtotal = $cantidad * $item->precio_unitario;
        $item->save();

        return $this->recalcular(Pedido::find($pedidoId))->load('items');
    }

    public function eliminarItem($pedidoId, $itemId) {
        $item = PedidoItem::where('pedido_id', $pedidoId)->where('id', $itemId)->first();
        if (!$item) throw new Exception("Item no encontrado", 5);

        $item->delete();
        return $this->recalcular(Pedido::find($pedidoId))->load('items');
    }

    private function validarItems($items) {
        foreach ($items as $item) {
            if (empty($item['producto_nombre'])) throw new Exception("Producto requerido", 7);
            if (($item['precio_unitario'] ?? 0) <= 0) throw new Exception("Precio invalido", 8);
            if (($item['cantidad'] ?? 0) < 1) throw new Exception("Cantidad invalida", 9);
        }
    }

    private function datosItem($pedidoId, $item) {
        $cantidad = (int)$item['cantidad'];
        $precio = (float)$item['precio_unitario'];

        return [
            'pedido_id' => $pedidoId,
            'producto_id' => $item['producto_id'] ?? 0,
            'producto_nombre' => $item['producto_nombre'],
            'precio_unitario' => $precio,
            'cantidad' => $cantidad,
            'subtotal' => $cantidad * $precio
        ];
    }

    private function recalcular($pedido) {
        $items = PedidoItem::where('pedido_id', $pedido->id)->get();
        $pedido->subtotal = $items->sum('subtotal');
        $pedido->total = $pedido->subtotal;
        $pedido->cantidad_total = $items->sum('cantidad');
        $pedido->save();
        return $pedido;
    }
}
