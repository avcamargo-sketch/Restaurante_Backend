<?php
namespace App\Controllers;

use App\Models\Reserva;
use App\Models\Mesa;
use Exception;

class ReservaController
{
    private array $estados = ['pendiente', 'confirmada', 'cancelada', 'finalizada'];

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
        $this->validarReserva($data);

        return Reserva::create([
            'nombre_cliente' => $data['nombre_cliente'],
            'telefono_cliente' => $data['telefono_cliente'],
            'cantidad_personas' => $data['cantidad_personas'],
            'fecha' => $data['fecha'],
            'hora' => $data['hora'],
            'observaciones' => $data['observaciones'] ?? null,
            'estado' => $data['estado'] ?? 'pendiente',
            'mesa_id' => $data['mesa_id']
        ]);
    }

    public function editarReserva($id, $data) {
        $reserva = $this->getReserva($id);
        $data = array_merge($reserva->toArray(), $data);
        $this->validarReserva($data, $id);
        $reserva->update($data);
        return $reserva;
    }

    public function cancelarReserva($id) {
        $reserva = $this->getReserva($id);
        $reserva->estado = 'cancelada';
        $reserva->save();
        return $reserva;
    }

    private function validarReserva($data, $ignorarId = null) {
        if (empty($data['nombre_cliente']) || empty($data['telefono_cliente'])) {
            throw new Exception("Datos del cliente requeridos", 2);
        }
        if (empty($data['fecha']) || empty($data['hora'])) {
            throw new Exception("Fecha y hora requeridas", 2);
        }
        if (!isset($data['cantidad_personas']) || $data['cantidad_personas'] <= 0) {
            throw new Exception("La cantidad de personas debe ser mayor a cero", 2);
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
        if (isset($data['estado']) && !in_array($data['estado'], $this->estados)) {
            throw new Exception("Estado de reserva inválido", 8);
        }

        $query = Reserva::where('mesa_id', $data['mesa_id'])
                         ->where('fecha', $data['fecha'])
                         ->where('hora', $data['hora'])
                         ->whereIn('estado', ['pendiente', 'confirmada'])
                         ;
        if ($ignorarId) {
            $query->where('id', '!=', $ignorarId);
        }

        $existe = $query->first();
        if ($existe) throw new Exception("Mesa ya reservada en ese horario", 7);
    }
}
