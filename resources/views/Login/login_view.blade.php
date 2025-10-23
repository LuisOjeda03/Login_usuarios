<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar Sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

  <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
    <h3 class="card-title text-center mb-4">Iniciar Sesión</h3>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('login_inicioSesion') }}">
      @csrf
      <div class="mb-3">
        <label for="correo" class="form-label">Correo</label>
        <input type="text" class="form-control" id="correo" name="correo" value="{{ old('correo') }}" placeholder="Ingresa tu correo" required>
      </div>
      <div class="mb-3">
        <label for="nip" class="form-label">Nip</label>
        <input type="password" class="form-control" id="nip" name="nip" placeholder="Ingresa tu nip" required>
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