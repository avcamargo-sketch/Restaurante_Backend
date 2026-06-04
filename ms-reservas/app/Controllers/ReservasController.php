<?php
namespace App\Controllers;

use App\Models\Mesa;
use App\Models\Reserva;
use Exception;

class ReservasController
{
    // ============================================================
    // MESAS
    // ============================================================

    // ---------- Listar todas las mesas ----------
    function getMesas() {
        return Mesa::all();
    }

    // ---------- Obtener una mesa por ID ----------
    function getMesa($id) {
        $mesa = Mesa::find($id);
        if (empty($mesa)) {
            throw new Exception("Mesa $id no existe", 1);
        }
        return $mesa;
    }

    // ---------- Crear mesa ----------
    function guardarMesa($data) {
        // Validaciones
        if (empty($data['numero']) || empty($data['capacidad'])) {
            throw new Exception("Falta número o capacidad", 2);
        }
        if ($data['capacidad'] <= 0) {
            throw new Exception("Capacidad debe ser mayor a cero", 3);
        }

        // Verificar que no exista mesa duplicada
        $existe = Mesa::where('numero', $data['numero'])->first();
        if ($existe) {
            throw new Exception("Mesa " . $data['numero'] . " ya existe", 4);
        }

        $mesa = new Mesa();
        $mesa->numero = $data['numero'];
        $mesa->capacidad = $data['capacidad'];
        $mesa->estado = empty($data['estado']) ? 'disponible' : $data['estado'];
        $mesa->save();

        return $mesa;
    }

    // ---------- Editar mesa ----------
    function modificarMesa($id, $data) {
        $mesa = $this->getMesa($id);

        if (!empty($data['numero'])) {
            // Verificar que no exista otra mesa con ese número
            $existe = Mesa::where('numero', $data['numero'])->where('id', '!=', $id)->first();
            if ($existe) {
                throw new Exception("Mesa " . $data['numero'] . " ya existe", 4);
            }
            $mesa->numero = $data['numero'];
        }

        if (!empty($data['capacidad'])) {
            if ($data['capacidad'] <= 0) {
                throw new Exception("Capacidad debe ser mayor a cero", 3);
            }
            $mesa->capacidad = $data['capacidad'];
        }

        if (!empty($data['estado'])) {
            $mesa->estado = $data['estado'];
        }

        $mesa->save();
        return $mesa;
    }

    // ---------- Cambiar estado de mesa ----------
    function cambiarEstadoMesa($id, $estado) {
        $mesa = $this->getMesa($id);
        
        $estadosPermitidos = ['disponible', 'reservada', 'ocupada', 'fuera_servicio'];
        if (!in_array($estado, $estadosPermitidos)) {
            throw new Exception("Estado no válido", 5);
        }

        $mesa->estado = $estado;
        $mesa->save();
        return $mesa;
    }

    // ---------- Eliminar mesa ----------
    function borrarMesa($id) {
        $mesa = $this->getMesa($id);
        
        // Verificar que no tenga reservas
        $reservas = Reserva::where('mesa_id', $id)->count();
        if ($reservas > 0) {
            throw new Exception("Mesa tiene reservas asociadas", 6);
        }

        $mesa->delete();
        return true;
    }

    // ============================================================
    // RESERVAS
    // ============================================================

    // ---------- Listar todas las reservas ----------
    function getReservas($filtros = []) {
        $query = Reserva::query();

        // Filtrar por fecha
        if (!empty($filtros['fecha'])) {
            $query->where('fecha', $filtros['fecha']);
        }

        // Filtrar por cliente (nombre)
        if (!empty($filtros['cliente'])) {
            $query->where('nombre_cliente', 'like', '%' . $filtros['cliente'] . '%');
        }

        // Filtrar por estado
        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        return $query->get();
    }

    // ---------- Obtener una reserva por ID ----------
    function getReserva($id) {
        $reserva = Reserva::find($id);
        if (empty($reserva)) {
            throw new Exception("Reserva $id no existe", 7);
        }
        return $reserva;
    }

    // ---------- Crear reserva ----------
    function guardarReserva($data) {
        // Validaciones
        if (empty($data['nombre_cliente']) || empty($data['telefono_cliente']) || 
            empty($data['cantidad_personas']) || empty($data['fecha']) || 
            empty($data['hora']) || empty($data['mesa_id'])) {
            throw new Exception("Faltan datos obligatorios", 8);
        }

        // Validar fecha no pasada
        $fechaReserva = strtotime($data['fecha']);
        $hoy = strtotime(date('Y-m-d'));
        if ($fechaReserva < $hoy) {
            throw new Exception("No se permiten reservas en fechas pasadas", 9);
        }

        // Validar mesa existe
        $mesa = Mesa::find($data['mesa_id']);
        if (empty($mesa)) {
            throw new Exception("Mesa no existe", 10);
        }

        // Validar mesa no esté fuera de servicio
        if ($mesa->estado == 'fuera_servicio') {
            throw new Exception("Mesa fuera de servicio", 11);
        }

        // Validar capacidad
        if ($data['cantidad_personas'] > $mesa->capacidad) {
            throw new Exception("Cantidad excede capacidad de mesa", 12);
        }

        // Validar no doble reserva para misma mesa y horario
        $existeReserva = Reserva::where('mesa_id', $data['mesa_id'])
            ->where('fecha', $data['fecha'])
            ->where('hora', $data['hora'])
            ->where('estado', '!=', 'cancelada')
            ->where('estado', '!=', 'finalizada')
            ->first();

        if ($existeReserva) {
            throw new Exception("Mesa ya reservada para esa fecha y hora", 13);
        }

        $reserva = new Reserva();
        $reserva->nombre_cliente = $data['nombre_cliente'];
        $reserva->telefono_cliente = $data['telefono_cliente'];
        $reserva->cantidad_personas = $data['cantidad_personas'];
        $reserva->fecha = $data['fecha'];
        $reserva->hora = $data['hora'];
        $reserva->observaciones = empty($data['observaciones']) ? null : $data['observaciones'];
        $reserva->estado = 'pendiente';
        $reserva->mesa_id = $data['mesa_id'];
        $reserva->save();

        // Actualizar estado de mesa a reservada
        $mesa->estado = 'reservada';
        $mesa->save();

        return $reserva;
    }

    // ---------- Editar reserva ----------
    function modificarReserva($id, $data) {
        $reserva = $this->getReserva($id);

        // No permitir editar si está finalizada o cancelada
        if ($reserva->estado == 'finalizada' || $reserva->estado == 'cancelada') {
            throw new Exception("No se puede editar reserva " . $reserva->estado, 14);
        }

        if (!empty($data['nombre_cliente'])) {
            $reserva->nombre_cliente = $data['nombre_cliente'];
        }
        if (!empty($data['telefono_cliente'])) {
            $reserva->telefono_cliente = $data['telefono_cliente'];
        }
        if (!empty($data['cantidad_personas'])) {
            $reserva->cantidad_personas = $data['cantidad_personas'];
        }
        if (!empty($data['fecha'])) {
            $reserva->fecha = $data['fecha'];
        }
        if (!empty($data['hora'])) {
            $reserva->hora = $data['hora'];
        }
        if (isset($data['observaciones'])) {
            $reserva->observaciones = $data['observaciones'];
        }

        // Si cambia la mesa
        if (!empty($data['mesa_id']) && $data['mesa_id'] != $reserva->mesa_id) {
            $mesa = Mesa::find($data['mesa_id']);
            if (empty($mesa)) {
                throw new Exception("Mesa no existe", 10);
            }
            if ($mesa->estado == 'fuera_servicio') {
                throw new Exception("Mesa fuera de servicio", 11);
            }
            if ($data['cantidad_personas'] > $mesa->capacidad) {
                throw new Exception("Cantidad excede capacidad de mesa", 12);
            }

            // Liberar mesa anterior
            $mesaAnterior = Mesa::find($reserva->mesa_id);
            if ($mesaAnterior && $mesaAnterior->estado == 'reservada') {
                $mesaAnterior->estado = 'disponible';
                $mesaAnterior->save();
            }

            $reserva->mesa_id = $data['mesa_id'];
            $mesa->estado = 'reservada';
            $mesa->save();
        }

        $reserva->save();
        return $reserva;
    }

    // ---------- Cancelar reserva ----------
    function cancelarReserva($id) {
        $reserva = $this->getReserva($id);

        if ($reserva->estado == 'cancelada' || $reserva->estado == 'finalizada') {
            throw new Exception("Reserva ya está " . $reserva->estado, 15);
        }

        $reserva->estado = 'cancelada';
        $reserva->save();

        // Liberar mesa
        $mesa = Mesa::find($reserva->mesa_id);
        if ($mesa && $mesa->estado == 'reservada') {
            $mesa->estado = 'disponible';
            $mesa->save();
        }

        return $reserva;
    }

    // ---------- Cambiar estado de reserva ----------
    function cambiarEstadoReserva($id, $estado) {
        $reserva = $this->getReserva($id);

        $estadosPermitidos = ['pendiente', 'confirmada', 'cancelada', 'finalizada'];
        if (!in_array($estado, $estadosPermitidos)) {
            throw new Exception("Estado no válido", 16);
        }

        // Si finaliza, liberar mesa
        if ($estado == 'finalizada' || $estado == 'cancelada') {
            $mesa = Mesa::find($reserva->mesa_id);
            if ($mesa) {
                $mesa->estado = 'disponible';
                $mesa->save();
            }
        }

        $reserva->estado = $estado;
        $reserva->save();
        return $reserva;
    }
}
