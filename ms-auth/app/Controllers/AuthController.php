<?php
namespace App\Controllers;

use App\Models\Usuario;
use Exception;

class AuthController
{
    // ============================================
    // LOGIN: Genera token y activa sesión
    // Ahora permite login por usuario O por correo
    // ============================================
    function login($data)
    {
        // Validar que vengan credenciales (usuario o correo, más contraseña)
        if ((empty($data['usuario']) && empty($data['correo'])) || empty($data['contrasena'])) {
            throw new Exception("Falta usuario/correo o contraseña", 1);
        }

        // Buscar usuario por nombre de usuario O por correo
        if (!empty($data['usuario'])) {
            // Buscar por usuario
            $usuario = Usuario::where('usuario', $data['usuario'])->first();
        } else {
            // Buscar por correo
            $usuario = Usuario::where('correo', $data['correo'])->first();
        }

        // Validar que exista y la contraseña coincida
        if (!$usuario || $usuario->contrasena !== $data['contrasena']) {
            throw new Exception("Credenciales incorrectas", 2);
        }

        // Validar que el usuario esté activo
        if ($usuario->estado !== 'activo') {
            throw new Exception("Usuario inactivo", 3);
        }

        // Generar token simple (random string de 64 caracteres)
        $token = bin2hex(random_bytes(32));

        // Guardar token y activar sesión
        $usuario->token = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        // Retornar datos del usuario (sin la contraseña)
        return [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'usuario' => $usuario->usuario,
            'rol' => $usuario->rol,
            'token' => $token
        ];
    }

    // ============================================
    // LOGOUT: Invalida token
    // ============================================
    function logout($token)
    {
        $usuario = Usuario::where('token', $token)->first();

        if (!$usuario) {
            throw new Exception("Token no válido", 4);
        }

        $usuario->token = null;
        $usuario->sesion_activa = false;
        $usuario->save();

        return true;
    }

    // ============================================
    // VALIDAR SESIÓN: Verifica que el token sea válido
    // ============================================
    function validarSesion($token)
    {
        if (empty($token)) {
            throw new Exception("Token vacío", 5);
        }

        $usuario = Usuario::where('token', $token)
                          ->where('sesion_activa', true)
                          ->where('estado', 'activo')
                          ->first();

        if (!$usuario) {
            throw new Exception("Sesión no válida o expirada", 6);
        }

        return [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'usuario' => $usuario->usuario,
            'rol' => $usuario->rol
        ];
    }
}