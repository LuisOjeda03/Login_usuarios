<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LoginService;
use App\Services\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


// use Illuminate\Support\Facades\Hash; // BORRAR


class LoginController extends Controller{

    private $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function login_home(){
        return view('Login.login_view');
    }

    public function login_inicioSesion(Request $request){
        $correo = $request->input('correo');
        $nip = $request->input('nip');

        $usuario = $this->loginService->iniciarSesion($correo, $nip);
        if(!$usuario) return back()->with('error', "Ha ocurrido un error");
        
        if (!$usuario instanceof Usuario) return back()->with('error', $usuario);

        session(['usuario' => [
            'id' => $usuario->getId(),
            'correo' => $usuario->getCorreo()
            ]]);

        return view('Login.home_view', ['usuario' => $usuario]);
    }

    public function logout(){
        $usuarioSession = session('usuario');
        $correo = $usuarioSession['correo'];
        $this->loginService->cerrarSesion($correo);
        session()->flush();
        return redirect()->route('login_home');
    }

    public function login_registrarse(){
        return view('Login.registro_view');
    }

    public function registro_crearCuenta(Request $request){
        $correo = $request->input('correo');
        $nombre = $request->input('nombre');
        $apellido = $request->input('apellido');
        $nip = $request->input('nip');
        
        $resultado = $this->loginService->crearUsuario($correo, $nip, $nombre, $apellido);
        if($resultado != 1){
            return redirect()->back()->with('error', $resultado);
        }
        return redirect()->route('login_home')->with('success', 'Usuario creado correctamente');
    }
}