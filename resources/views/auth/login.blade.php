<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Chat</title>
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
        .login-container {
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
            background: #1877f2;
            color: white;
        }
        .btn-primary:hover {
            background: #166fe5;
        }
        .divider {
            text-align: center;
            margin: 20px 0;
            color: #8a8d91;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #dadde1;
        }
        .divider::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #dadde1;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            color: #1877f2;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
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
        .remember {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .remember input {
            width: auto;
            margin-right: 8px;
        }
        .remember label {
            color: #1c1e21;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">💬 Chat</div>
        <h2>Iniciar Sesión</h2>
        
        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <input type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <div class="remember">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Recordarme</label>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>

        <div class="divider">o</div>

        <div class="register-link">
            <a href="{{ route('register') }}">Crear cuenta nueva</a>
        </div>
    </div>
</body>
</html>

