<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crear Cuenta</title>
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .register-card {
      max-width: 400px;
      width: 100%;
      background-color: #fff;
      border-radius: 1rem;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      padding: 2rem;
    }
  </style>
</head>
<body>

  <div class="register-card">
    <h3 class="text-center mb-4">Crear Nueva Cuenta</h3>

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('registro_crearCuenta') }}">
      @csrf
      <div class="mb-3">
        <label for="correo" class="form-label">Correo</label>
        <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingresa tu correo" required>
      </div>
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa tu nombre" required>
      </div>
      <div class="mb-3">
        <label for="apellido" class="form-label">Apellido</label>
        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingresa tu apellido" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Nip</label>
        <input type="password" class="form-control" id="nip" name="nip" placeholder="Ingresa tu nip" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Crear Cuenta</button>
      </div>
    </form>

    <div class="text-center mt-3">
      <a href="{{ route('login_home') }}">¿Ya tienes cuenta? Inicia sesión</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>