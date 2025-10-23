<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/login', [LoginController::class, 'login_home']) -> name('login_home');
Route::post('/home', [LoginController::class, 'login_inicioSesion']) -> name('login_inicioSesion');
Route::post('/logout', [LoginController::class, 'logout']) -> name('logout');
Route::get('/crear-cuenta', [LoginController::class, 'login_registrarse']) -> name('login_registrarse');
Route::post('/crear-cuenta', [LoginController::class, 'registro_crearCuenta']) -> name('registro_crearCuenta');