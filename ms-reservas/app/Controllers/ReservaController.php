<?php
namespace App\Controllers;

use App\Models\Reserva;
use App\Models\Mesa;
use Exception;

class ReservaController
{
    public function getReservas($filtros = []) {
        $query = Reserva::with('mesa');
        
        if (!empty($filtros['fecha'])) {
            $query->where('fecha', $filtros['fecha']);
        }
        if (!empty($filtros['cliente'])) {
            $query->where('nombre_cliente', 'like', '%' . $filtros['cliente'] . '%');
        }
        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }
        
        return $query->get();
    }

    public function getReserva($id) {
        $reserva = Reserva::with('mesa')->find($id);
        if (!$reserva) throw new Exception("Reserva no encontrada", 1);
        return $reserva;
    }

    public function crearReserva($data) {
        if (empty($data['nombre_cliente']) || empty($data['telefono_cliente'])) {
            throw new Exception("Datos del cliente requeridos", 2);
        }
        
        $fechaReserva = strtotime($data['fecha']);
        $hoy = strtotime(date('Y-m-d'));
        if ($fechaReserva < $hoy) {
            throw new Exception("No se permiten reservas en fechas pasadas", 3);
        }

        $mesa = Mesa::find($data['mesa_id']);
        if (!$mesa) throw new Exception("Mesa no existe", 4);
        if ($mesa->estado === 'fuera_servicio') {
            throw new Exception("Mesa fuera de servicio", 5);
        }
        if ($data['cantidad_personas'] > $mesa->capacidad) {
            throw new Exception("Capacidad excedida", 6);
        }

        $existe = Reserva::where('mesa_id', $data['mesa_id'])
                         ->where('fecha', $data['fecha'])
                         ->where('hora', $data['hora'])
                         ->whereIn('estado', ['pendiente', 'confirmada'])
                         ->first();
        if ($existe) throw new Exception("Mesa ya reservada en ese horario", 7);

        return Reserva::create([
            'nombre_cliente' => $data['nombre_cliente'],
            'telefono_cliente' => $data['telefono_cliente'],
            'cantidad_personas' => $data['cantidad_personas'],
            'fecha' => $data['fecha'],
            'hora' => $data['hora'],
            'observaciones' => $data['observaciones'] ?? null,
            'estado' => 'pendiente',
            'mesa_id' => $data['mesa_id']
        ]);
    }

    public function editarReserva($id, $data) {
        $reserva = $this->getReserva($id);
        $reserva->update($data);
        return $reserva;
    }

    public function cancelarReserva($id) {
        $reserva = $this->getReserva($id);
        $reserva->estado = 'cancelada';
        $reserva->save();
        return $reserva;
    }
}
