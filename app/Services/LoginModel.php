<?php

namespace App\Services;

use App\Repositories\LoginRepository;
use Illuminate\Support\Facades\Hash;
use DateTime;

class LoginModel
{

    private $loginRepository;

    public function __construct(LoginRepository $loginRepository)
    {
        $this->loginRepository = $loginRepository;
    }

    public function encontrarUsuario($correo)
    {
        $this->loginRepository->beginTransaction();

        $usuario = $this->loginRepository->buscarUsuarioPorCorreo($correo);
        if (!$usuario) return null;

        return new Usuario(
            $usuario->id,
            $usuario->correo,
            $usuario->nip,
            $usuario->nombre,
            $usuario->apellido,
            (bool) $usuario->sesion_activa,
            (int) $usuario->intentos_login,
            $usuario->ultimo_intento ? new DateTime($usuario->ultimo_intento) : null,
            $usuario->bloqueado_hasta ? new DateTime($usuario->bloqueado_hasta) : null
        );
    }

    public function crearUsuario($correo, $nip, $nombre, $apellido)
    {
        $existe = $this->encontrarUsuario($correo);
        if ($existe) {
            $this->loginRepository->rollbackTransaction();
            return "Advertencia: El correo ya se encuentra registrado";
        }

        $nipHash = Hash::make($nip);

        $usuario = $this->loginRepository->crearUsuario($correo, $nipHash, $nombre, $apellido);
        if (!$usuario) {
            $this->loginRepository->rollbackTransaction();
            return "Advertencia: No se ha podido crear el usuario";
        }

        $this->loginRepository->commitTransaction();
        return 1;
    }

    public function actualizarSesion(Usuario $usuario)
    {
        $this->loginRepository->actualizarStatusUsuario($usuario);
        $this->loginRepository->commitTransaction();
    }

    public function iniciarSesion(string $correo, string $nip)
    {
        $usuario = $this->encontrarUsuario($correo);
        if (!$usuario) return "Correo o contraseña incorrectos.";

        $fechaActual = new DateTime();
        $usuario->setUltimoIntento($fechaActual);
        $bloqueadoHasta = $usuario->getBloqueadoHasta();

        if ($bloqueadoHasta && $bloqueadoHasta > $usuario->getUltimoIntento()) {
            $this->actualizarSesion($usuario);
            $fecha = $usuario->getBloqueadoHasta()->format('Y-m-d H:i:s');
            return "Advertencia: Esta cuenta ha sido bloqueada hasta " . $fecha;
        }

        if (!Hash::check($nip, $usuario->getNip())) {

            $usuario->aumentarIntentosLogin();

            if ($usuario->getIntentosLogin() > 3) {
                $ultimoIntento = $usuario->getUltimoIntento();
                $nuevoBloqueo = (clone $ultimoIntento)->modify('+30 minutes');
                $usuario->setBloqueadoHasta($nuevoBloqueo);
                $usuario->reiniciarIntentosLogin();
                $this->actualizarSesion($usuario);
                $fecha = $usuario->getBloqueadoHasta()->format('Y-m-d H:i:s');
                return "Advertencia: Esta cuenta ha sido bloqueada hasta " . $fecha;
            }

            $this->actualizarSesion($usuario);
            return "Correo o contraseña incorrectos.";
        }

        if ($usuario->isSesionActiva()) {
            $this->loginRepository->rollbackTransaction();
            return "Advertencia: Ya hay una sesión activa para esta cuenta";
        }

        $usuario->iniciarSesion();
        $usuario->reiniciarIntentosLogin();
        $this->actualizarSesion($usuario);

        return $usuario;
    }

    public function cerrarSesion($correo)
    {
        $usuario = $this->encontrarUsuario($correo);
        if (!$usuario) return null;

        $usuario->cerrarSesion();
        $this->actualizarSesion($usuario);
    }
}
