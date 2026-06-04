<?php
namespace App\Controllers;

use App\Models\Usuario;
use Exception;

class AuthController
{
    public function login($data)
    {
        $login = trim($data['usuario'] ?? $data['login'] ?? '');
        $contrasena = $data['contrasena'] ?? $data['password'] ?? '';

        if ($login === '' || $contrasena === '') {
            throw new Exception("Usuario y contraseña requeridos", 1);
        }

        $usuario = Usuario::where('usuario', $login)
                          ->orWhere('correo', $login)
                          ->first();

        if (!$usuario || !$this->validarContrasena($contrasena, $usuario->contrasena)) {
            throw new Exception("Credenciales incorrectas", 2);
        }

        if (($usuario->estado ?? 'activo') !== 'activo') {
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
        if (!$token) {
            return false;
        }

        $usuario = Usuario::where('token', $token)
                          ->where('sesion_activa', true)
                          ->first();
        return $usuario ? true : false;
    }

    private function validarContrasena($plana, $guardada)
    {
        if (password_get_info($guardada)['algo']) {
            return password_verify($plana, $guardada);
        }

        return $plana === $guardada;
    }
}
