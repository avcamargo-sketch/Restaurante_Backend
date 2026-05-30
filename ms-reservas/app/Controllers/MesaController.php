<?php
namespace App\Controllers;

use App\Models\Mesa;
use Exception;

class MesaController
{
    public function getMesas() {
        return Mesa::all();
    }

    public function getMesa($id) {
        $mesa = Mesa::find($id);
        if (!$mesa) throw new Exception("Mesa no encontrada", 1);
        return $mesa;
    }

    public function crearMesa($data) {
        if (empty($data['numero'])) throw new Exception("Número requerido", 2);
        if ($data['capacidad'] <= 0) throw new Exception("Capacidad debe ser mayor a cero", 3);
        
        $existe = Mesa::where('numero', $data['numero'])->first();
        if ($existe) throw new Exception("Mesa ya existe", 4);

        return Mesa::create([
            'numero' => $data['numero'],
            'capacidad' => $data['capacidad'],
            'estado' => $data['estado'] ?? 'disponible'
        ]);
    }

    public function editarMesa($id, $data) {
        $mesa = $this->getMesa($id);
        if (isset($data['capacidad']) && $data['capacidad'] <= 0) {
            throw new Exception("Capacidad debe ser mayor a cero", 3);
        }
        $mesa->update($data);
        return $mesa;
    }

    public function eliminarMesa($id) {
        $mesa = $this->getMesa($id);
        $mesa->delete();
        return true;
    }
}
