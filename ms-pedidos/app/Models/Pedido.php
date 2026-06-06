<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model {
    protected $table = 'pedidos';
    public $timestamps = true;
    
    // Permitir asignación masiva de estos campos
    protected $fillable = ['mesa_id', 'fecha', 'hora', 'subtotal', 'total', 'cantidad_total', 'estado'];
}