<?php
namespace App\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Exception;

class ProductoController
{
    public function getProductos($filtros = []) {
        $query = Producto::with('categoria');
        
        if (!empty($filtros['categoria'])) {
            $query->where('categoria_id', $filtros['categoria']);
        }
        if (isset($filtros['disponible'])) {
            $query->where('disponible', $filtros['disponible']);
        }
        
        return $query->get();
    }

    public function getProducto($id) {
        $producto = Producto::with('categoria')->find($id);
        if (!$producto) throw new Exception("Producto no encontrado", 1);
        return $producto;
    }

    public function crearProducto($data) {
        if (empty($data['nombre'])) throw new Exception("Nombre requerido", 2);
        if ($data['precio'] <= 0) throw new Exception("Precio debe ser mayor a cero", 3);
        
        $existe = Producto::where('nombre', $data['nombre'])->first();
        if ($existe) throw new Exception("Producto ya existe", 4);

        return Producto::create([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'precio' => $data['precio'],
            'disponible' => $data['disponible'] ?? true,
            'categoria_id' => $data['categoria_id']
        ]);
    }

    public function editarProducto($id, $data) {
        $producto = $this->getProducto($id);
        if (isset($data['precio']) && $data['precio'] <= 0) {
            throw new Exception("Precio debe ser mayor a cero", 3);
        }
        $producto->update($data);
        return $producto;
    }

    public function eliminarProducto($id) {
        $producto = $this->getProducto($id);
        $producto->delete();
        return true;
    }

    public function getCategorias() {
        return Categoria::all();
    }
}