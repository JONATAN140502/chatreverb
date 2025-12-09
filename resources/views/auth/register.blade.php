<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Chat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .register-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1), 0 8px 16px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            color: #1877f2;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        h2 {
            text-align: center;
            color: #1c1e21;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            font-size: 16px;
            background: #f5f6f7;
        }
        input:focus {
            outline: none;
            border-color: #1877f2;
            background: white;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-primary {
            background: #42b72a;
            color: white;
        }
        .btn-primary:hover {
            background: #36a420;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #1877f2;
            text-decoration: none;
            font-weight: bold;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .error {
            background: #f02849;
            color: white;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        @if ($errors->any())
            .error-list {
                background: #f02849;
                color: white;
                padding: 10px;
                border-radius: 6px;
                margin-bottom: 15px;
                font-size: 14px;
            }
            .error-list ul {
                margin-left: 20px;
            }
        @endif
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">💬 Chat</div>
        <h2>Crear Cuenta</h2>
        
        @if ($errors->any())
            <div class="error-list">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <input type="text" name="name" placeholder="Nombre completo" value="{{ old('name') }}" required autofocus>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña (mínimo 6 caracteres)" required>
            </div>
            <div class="form-group">
                <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrarse</button>
        </form>

        <div class="login-link">
            <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </div>
</body>
</html>

