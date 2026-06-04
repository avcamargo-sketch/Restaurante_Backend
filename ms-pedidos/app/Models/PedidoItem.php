<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model {
    protected $table = 'pedido_items';
    public $timestamps = true;
    protected $fillable = ['pedido_id', 'producto_id', 'producto_nombre', 'precio_unitario', 'cantidad', 'subtotal'];
}
