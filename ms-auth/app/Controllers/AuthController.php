<?php
namespace App\Controllers;

use App\Models\Usuario;
use Exception;

class AuthController
{
    public function login($data)
    {
        if (empty($data['usuario']) || empty($data['contrasena'])) {
            throw new Exception("Usuario y contraseña requeridos", 1);
        }

        $usuario = Usuario::where('usuario', $data['usuario'])
                          ->orWhere('correo', $data['usuario'])
                          ->first();

        if (!$usuario || $usuario->contrasena !== $data['contrasena']) {
            throw new Exception("Credenciales incorrectas", 2);
        }

        if ($usuario->estado !== 'activo') {
            throw new Exception("Usuario inactivo", 3);
        }

        $token = bin2hex(random_bytes(32));
        $usuario->token = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        return [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'usuario' => $usuario->usuario,
            'rol' => $usuario->rol,
            'token' => $token
        ];
    }

    public function logout($token)
    {
        $usuario = Usuario::where('token', $token)->first();
        if ($usuario) {
            $usuario->token = null;
            $usuario->sesion_activa = false;
            $usuario->save();
            return true;
        }
        throw new Exception("Token no válido", 4);
    }

    public function validarSesion($token)
    {
        $usuario = Usuario::where('token', $token)
                          ->where('sesion_activa', true)
                          ->first();
        return $usuario ? true : false;
    }
}