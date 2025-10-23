<?php

namespace App\Services;

use DateTime;

class Usuario{
    private int $id;
    private string $correo;
    private string $nip;
    private string $nombre;
    private string $apellido;
    private bool $sesion_activa;
    private int $intentos_login;
    private ?DateTime $ultimo_intento;
    private ?DateTime $bloqueado_hasta;

    public function __construct(
        int $id,
        string $correo,
        string $nip,
        string $nombre,
        string $apellido,
        bool $sesion_activa = false,
        int $intentos_login = 0,
        ?DateTime $ultimo_intento = null,
        ?DateTime $bloqueado_hasta = null
    ) {
        $this->id = $id;
        $this->correo = $correo;
        $this->nip = $nip;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->sesion_activa = $sesion_activa;
        $this->intentos_login = $intentos_login;
        $this->ultimo_intento = $ultimo_intento;
        $this->bloqueado_hasta = $bloqueado_hasta;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function getNip(): string
    {
        return $this->nip;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getApellido(): string
    {
        return $this->apellido;
    }

    public function isSesionActiva(): bool
    {
        return $this->sesion_activa;
    }

    public function getIntentosLogin(): int
    {
        return $this->intentos_login;
    }

    public function getUltimoIntento(): ?DateTime
    {
        return $this->ultimo_intento;
    }

    public function getBloqueadoHasta(): ?DateTime
    {
        return $this->bloqueado_hasta;
    }

    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }

    public function setNip(string $nip): void
    {
        $this->nip = $nip;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setApellido(string $apellido): void
    {
        $this->apellido = $apellido;
    }

    public function setSesionActiva(bool $sesion_activa): void
    {
        $this->sesion_activa = $sesion_activa;
    }

    public function setIntentosLogin(int $intentos_login): void
    {
        $this->intentos_login = $intentos_login;
    }

    public function setUltimoIntento(?DateTime $ultimo_intento): void
    {
        $this->ultimo_intento = $ultimo_intento;
    }

    public function setBloqueadoHasta(?DateTime $bloqueado_hasta): void
    {
        $this->bloqueado_hasta = $bloqueado_hasta;
    }

    public function aumentarIntentosLogin(){
        $this->intentos_login++;
    }

    public function reiniciarIntentosLogin(){
        $this->intentos_login = 0;
    }

    public function iniciarSesion(){
        $this->setSesionActiva(true);
    }

    public function cerrarSesion(){
        $this->setSesionActiva(false);
    }
}