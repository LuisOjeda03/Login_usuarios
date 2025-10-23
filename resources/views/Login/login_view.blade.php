<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inicio de Sesión</title>
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
    .login-card {
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

  <div class="login-card">
    <h3 class="text-center mb-4">Iniciar Sesión</h3>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login_inicioSesion') }}">                  
    @csrf
      <div class="mb-3">
        <label for="correo" class="form-label">Correo</label>
        <input type="text" class="form-control" id="correo" name="correo" placeholder="Ingresa tu Correo" required>
      </div>
      <div class="mb-3">
        <label for="nip" class="form-label">Nip</label>
        <input type="password" class="form-control" id="nip" name="nip" placeholder="Ingresa tu Nip" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
      </div>
    </form>
    <div class="d-grid mt-2">
        <a href="{{ route('login_registrarse') }}" class="btn btn-secondary">Registrarse</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>