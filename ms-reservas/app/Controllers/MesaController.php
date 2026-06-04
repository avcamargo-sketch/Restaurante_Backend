<?php
namespace App\Controllers;

use App\Models\Mesa;
use Exception;

class MesaController
{
    private array $estados = ['disponible', 'reservada', 'ocupada', 'fuera_servicio'];

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
        if (!isset($data['capacidad']) || $data['capacidad'] <= 0) throw new Exception("Capacidad debe ser mayor a cero", 3);
        if (isset($data['estado']) && !in_array($data['estado'], $this->estados)) throw new Exception("Estado de mesa inválido", 5);
        
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
        if (isset($data['estado']) && !in_array($data['estado'], $this->estados)) {
            throw new Exception("Estado de mesa inválido", 5);
        }
        $mesa->update($data);
        return $mesa;
    }

    public function cambiarEstado($id, $estado) {
        $mesa = $this->getMesa($id);
        if (!in_array($estado, $this->estados)) {
            throw new Exception("Estado de mesa inválido", 5);
        }

        $mesa->estado = $estado;
        $mesa->save();
        return $mesa;
    }

    public function eliminarMesa($id) {
        $mesa = $this->getMesa($id);
        $mesa->delete();
        return true;
    }
}
