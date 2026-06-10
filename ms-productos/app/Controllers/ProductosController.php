<?php
namespace App\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Exception;

class ProductosController
{
    

    // ---------- Listar todas las categorías ----------
    function getCategorias() {
        return Categoria::all();
    }

    // ---------- Obtener categoría por ID ----------
    function getCategoria($id) {
        $categoria = Categoria::find($id);
        if (empty($categoria)) {
            throw new Exception("Categoria $id no existe", 1);
        }
        return $categoria;
    }

    // ---------- Crear categoría ----------
    function guardarCategoria($data) {
        if (empty($data['nombre'])) {
            throw new Exception("Falta nombre de categoría", 2);
        }

        // Verificar que no exista categoría duplicada
        $existe = Categoria::where('nombre', $data['nombre'])->first();
        if ($existe) {
            throw new Exception("Categoria " . $data['nombre'] . " ya existe", 3);
        }

        $categoria = new Categoria();
        $categoria->nombre = $data['nombre'];
        $categoria->descripcion = empty($data['descripcion']) ? null : $data['descripcion'];
        $categoria->save();

        return $categoria;
    }

    // ---------- Editar categoría ----------
    function modificarCategoria($id, $data) {
        $categoria = $this->getCategoria($id);

        if (!empty($data['nombre'])) {
            // Verificar que no exista otra categoría con ese nombre
            $existe = Categoria::where('nombre', $data['nombre'])->where('id', '!=', $id)->first();
            if ($existe) {
                throw new Exception("Categoria " . $data['nombre'] . " ya existe", 3);
            }
            $categoria->nombre = $data['nombre'];
        }

        if (isset($data['descripcion'])) {
            $categoria->descripcion = $data['descripcion'];
        }

        $categoria->save();
        return $categoria;
    }

    // ---------- Eliminar categoría ----------
    function borrarCategoria($id) {
        $categoria = $this->getCategoria($id);

        // Verificar que no tenga productos asociados
        $productos = Producto::where('categoria_id', $id)->count();
        if ($productos > 0) {
            throw new Exception("Categoria tiene productos asociados", 4);
        }

        $categoria->delete();
        return true;
    }

    

    // ---------- Listar todos los productos ----------
    function getProductos($filtros = []) {
        $query = Producto::query();

        // Filtrar por categoría
        if (!empty($filtros['categoria'])) {
            $query->where('categoria_id', $filtros['categoria']);
        }

        // Filtrar por disponibilidad
        if (isset($filtros['disponible'])) {
            $query->where('disponible', $filtros['disponible']);
        }

        return $query->get();
    }

    // ---------- Obtener producto por ID ----------
    function getProducto($id) {
        $producto = Producto::find($id);
        if (empty($producto)) {
            throw new Exception("Producto $id no existe", 5);
        }
        return $producto;
    }

    // ---------- Crear producto ----------
    function guardarProducto($data) {
        // Validaciones
        if (empty($data['nombre']) || empty($data['precio']) || empty($data['categoria_id'])) {
            throw new Exception("Faltan datos obligatorios", 6);
        }

        if ($data['precio'] <= 0) {
            throw new Exception("Precio debe ser mayor a cero", 7);
        }

        // Verificar que no exista producto duplicado
        $existe = Producto::where('nombre', $data['nombre'])->first();
        if ($existe) {
            throw new Exception("Producto " . $data['nombre'] . " ya existe", 8);
        }

        // Verificar que la categoría exista
        $categoria = Categoria::find($data['categoria_id']);
        if (empty($categoria)) {
            throw new Exception("Categoria no existe", 9);
        }

        $producto = new Producto();
        $producto->nombre = $data['nombre'];
        $producto->descripcion = empty($data['descripcion']) ? null : $data['descripcion'];
        $producto->precio = $data['precio'];
        $producto->disponible = isset($data['disponible']) ? $data['disponible'] : true;
        $producto->categoria_id = $data['categoria_id'];
        $producto->save();

        return $producto;
    }

    // ---------- Editar producto ----------
    function modificarProducto($id, $data) {
        $producto = $this->getProducto($id);

        if (!empty($data['nombre'])) {
            // Verificar que no exista otro producto con ese nombre
            $existe = Producto::where('nombre', $data['nombre'])->where('id', '!=', $id)->first();
            if ($existe) {
                throw new Exception("Producto " . $data['nombre'] . " ya existe", 8);
            }
            $producto->nombre = $data['nombre'];
        }

        if (isset($data['descripcion'])) {
            $producto->descripcion = $data['descripcion'];
        }

        if (!empty($data['precio'])) {
            if ($data['precio'] <= 0) {
                throw new Exception("Precio debe ser mayor a cero", 7);
            }
            $producto->precio = $data['precio'];
        }

        if (isset($data['disponible'])) {
            $producto->disponible = $data['disponible'];
        }

        if (!empty($data['categoria_id'])) {
            $categoria = Categoria::find($data['categoria_id']);
            if (empty($categoria)) {
                throw new Exception("Categoria no existe", 9);
            }
            $producto->categoria_id = $data['categoria_id'];
        }

        $producto->save();
        return $producto;
    }

    // ---------- Eliminar producto ----------
    function borrarProducto($id) {
        $producto = $this->getProducto($id);
        $producto->delete();
        return true;
    }
}

