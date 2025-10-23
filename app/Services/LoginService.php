<?php

namespace App\Services;
use App\Repositories\LoginRepository;
use Illuminate\Support\Facades\Hash;
use DateTime;

class LoginService{
    
    private $LoginRepository;

    public function __construct(LoginRepository $LoginRepository){
        $this->LoginRepository = $LoginRepository;
    }

    public function encontrarUsuario($correo){
        $this->LoginRepository->beginTransaction();

        $usuarioEncontrado = $this->LoginRepository->buscarUsuarioCorreo($correo);
        if(!$usuarioEncontrado) return null;

        return new Usuario(
            $usuarioEncontrado->id,
            $usuarioEncontrado->correo,
            $usuarioEncontrado->nip,
            $usuarioEncontrado->nombre,
            $usuarioEncontrado->apellido,
            (bool) $usuarioEncontrado->sesion_activa,
            (int) $usuarioEncontrado->intentos_login,
            $usuarioEncontrado->ultimo_intento ? new DateTime($usuarioEncontrado->ultimo_intento) : null,
            $usuarioEncontrado->bloqueado_hasta ? new DateTime($usuarioEncontrado->bloqueado_hasta) : null
        );
    }

    public function crearUsuario($correo, $nip, $nombre, $apellido){
        $existe = $this->encontrarUsuario($correo);
        if($existe) {
            $this->LoginRepository->rollbackTransaction();
            return "Advertencia: El correo ya encuentra registrado";
        }

        $nipHash = Hash::make($nip);

        $usuario = $this->LoginRepository->crearUsuario($correo, $nipHash, $nombre, $apellido);
        if(!$usuario) {
            $this->LoginRepository->rollbackTransaction();
            return "Advertencia: No se ha podido crear el usuario";
        }

        $this->LoginRepository->commitTransaction();
        return 1;
    }

    public function actualizarSesion(Usuario $usuario){
        $this->LoginRepository->actualizarStatusUsuario($usuario);
        $this->LoginRepository->commitTransaction();
    }

    public function iniciarSesion(string $correo, string $nip){
        $usuario = $this->encontrarUsuario($correo);
        if(!$usuario) return "Correo o contraseña incorrectos";

        $ahora = new DateTime();
        $usuario->setUltimoIntento($ahora);
        $bloqueadoHasta = $usuario->getBloqueadoHasta();
        if($bloqueadoHasta && $bloqueadoHasta > $usuario->getUltimoIntento()){
            $this->actualizarSesion($usuario);
            $fecha = $usuario->getBloqueadoHasta()->format('y-m-d H:i:s');
            return "Advertencia: Esta cuenta ha sido bloqueada hasta " . $fecha;
        }

        if (!Hash::check($nip, $usuario->getNip())) {
            $usuario->aumentarIntentosLogin();

            if($usuario->getIntentosLogin() > 3){
                $nuevoBloqueo = ($usuario->getUltimoIntento())->modify('+1 minutes');  // CAMBIAR A 30     
                $usuario->setBloqueadoHasta($nuevoBloqueo);
            }
            $this->actualizarSesion($usuario);
            return "Correo o contraseña incorrectos";
        }

        if($usuario->isSesionActiva()) return "Advertencia: Ya hay una sesión activa para esta cuenta";
        
        $usuario->iniciarSesion();  // entonces hacer los set private?
        $usuario->reiniciarIntentosLogin();
        
       $this->actualizarSesion($usuario);
        
        return $usuario;
    }

    public function cerrarSesion($correo){
        $usuario = $this->encontrarUsuario($correo);
        if(!$usuario) return null;

        $usuario->cerrarSesion();
        $this->actualizarSesion($usuario);
    }
}