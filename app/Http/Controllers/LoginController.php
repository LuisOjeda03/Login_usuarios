<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LoginService;
use App\Services\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    private $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function login_home()
    {
        return view('Login.login_view');
    }

    public function login_inicioSesion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correo' => ['required', 'email', 'regex:/^[\w\.-]+@[\w\.-]+\.\w{2,4}$/'],
            'nip' => ['required', 'string', 'min:6']
        ], [
            'correo.email' => 'El correo debe ser una dirección de correo válida.',
            'correo.regex' => 'El correo no tiene un formato válido.',
            'nip.required' => 'El nip es obligatorio.',
            'nip.min' => 'El nip debe tener al menos 6 caracteres.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $correo = $request->input('correo');
        $nip = $request->input('nip');

        $usuario = $this->loginService->iniciarSesion($correo, $nip);
        if (!$usuario) return back()->with('error', "Ha ocurrido un error");

        if (!$usuario instanceof Usuario) return back()->with('error', $usuario);

        session(['usuario' => [
            'id' => $usuario->getId(),
            'correo' => $usuario->getCorreo()
        ]]);

        return view('Login.home_view', ['usuario' => $usuario]);
    }

    public function logout()
    {
        $usuarioSession = session('usuario');
        $correo = $usuarioSession['correo'] ?? null;

        if ($correo) {
            $this->loginService->cerrarSesion($correo);
        }

        session()->flush();
        return redirect()->route('login_home');
    }

    public function login_registrarse()
    {
        return view('Login.registro_view');
    }

    public function registro_crearCuenta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correo' => ['required', 'email', 'regex:/^[\w\.-]+@[\w\.-]+\.\w{2,4}$/'],
            'nip' => ['required', 'string', 'min:6', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255'
        ], [
            'correo.email' => 'El correo debe ser una dirección de correo válida.',
            'correo.regex' => 'El correo no tiene un formato válido.',
            'nip.required' => 'El nip es obligatorio.',
            'nip.min' => 'El nip debe tener al menos 6 caracteres.',
            'nip.regex' => 'El nip debe contener al menos una minúscula, una mayúscula, un número y un carácter especial.',
            'nombre.required' => 'El nombre es requerido.',
            'apellido.required' => 'El apellido es requerido.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $correo = $request->input('correo');
        $nombre = $request->input('nombre');
        $apellido = $request->input('apellido');
        $nip = $request->input('nip');

        $resultado = $this->loginService->crearUsuario($correo, $nip, $nombre, $apellido);

        if ($resultado != 1) {
            return back()->with('error', $resultado)->withInput();
        }

        return redirect()->route('login_home')->with('success', 'Usuario creado correctamente');
    }
}
