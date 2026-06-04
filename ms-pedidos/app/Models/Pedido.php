<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model {
    protected $table = 'pedidos';
    public $timestamps = true;
    protected $fillable = ['mesa_id', 'mesa_nombre', 'mesa_estado', 'estado', 'subtotal', 'total', 'cantidad_total'];

    public function items() {
        return $this->hasMany(PedidoItem::class, 'pedido_id');
    }
}
