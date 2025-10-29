<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LoginModel;
use App\Services\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    private $loginModel;

    public function __construct(LoginModel $loginModel)
    {
        $this->loginModel = $loginModel;
    }

    public function login_home()
    {
        return view('Login.login_view');
    }

    public function login_inicioSesion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correo' => ['required', 'string', 'regex:/^[\w\.-]+@[\w\.-]+\.\w{2,4}$/'],
            'nip' => ['required', 'string']
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.regex' => 'El correo no tiene un formato válido.',
            'nip.required' => 'El nip es obligatorio.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $correo = $request->input('correo');
        $nip = $request->input('nip');

        $usuario = $this->loginModel->iniciarSesion($correo, $nip);
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
            $this->loginModel->cerrarSesion($correo);
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
            'correo' => ['required', 'string', 'regex:/^[\w\.-]+@[\w\.-]+\.\w{2,4}$/'],
            'nip' => ['required', 'string','regex:/^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_]).+$/'],
            'nombre' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/'],
            'apellido' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/']
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.regex' => 'El correo no tiene un formato válido.',
            'nip.required' => 'El nip es obligatorio.',
            'nip.regex' => 'El nip debe contener al menos una minúscula, una mayúscula, un número y un carácter especial.',
            'nombre.required' => 'El nombre es requerido.',
            'nombre.regex' => 'El nombre solo puede contener letras.',
            'apellido.required' => 'El apellido es requerido.',
            'apellido.regex' => 'El apellido solo puede contener letras.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $correo = $request->input('correo');
        $nombre = $request->input('nombre');
        $apellido = $request->input('apellido');
        $nip = $request->input('nip');

        $resultado = $this->loginModel->crearUsuario($correo, $nip, $nombre, $apellido);

        if ($resultado != 1) {
            return back()->with('error', $resultado)->withInput();
        }

        return redirect()->route('login_home')->with('success', 'Usuario creado correctamente');
    }
}