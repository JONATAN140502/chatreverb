# 🔧 Corregir .htaccess para /broadcasting/auth

## ❌ Problema

El `.htaccess` actual está redirigiendo `/broadcasting/auth` a Reverb (puerto 8081), pero esa ruta debe ser manejada por Laravel, no por Reverb.

## ✅ Solución

Elimina la línea que redirige `/broadcasting/` a Reverb. Laravel maneja automáticamente `/broadcasting/auth` cuando broadcasting está configurado.

### .htaccess CORRECTO:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Proxy para WebSocket de Reverb (puerto 443 -> 8081 interno)
    # Solo para conexiones WebSocket
    RewriteCond %{HTTP:Upgrade} websocket [NC]
    RewriteCond %{HTTP:Connection} upgrade [NC]
    RewriteRule ^app/(.*)$ ws://127.0.0.1:8081/app/$1 [P,L]

    # ❌ ELIMINA ESTA LÍNEA:
    # RewriteRule ^broadcasting/(.*)$ http://127.0.0.1:8081/broadcasting/$1 [P,L]
    
    # Laravel maneja /broadcasting/auth automáticamente
    # No necesitas redirigirlo, déjalo pasar a Laravel normalmente

    # Redirige todo el tráfico a la carpeta public (Laravel)
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# php -- BEGIN cPanel-generated handler, do not edit
# Set the "ea-php82" package as the default "PHP" programming language.
<IfModule mime_module>
  AddHandler application/x-httpd-ea-php82___lsphp .php .php8 .phtml
</IfModule>
# php -- END cPanel-generated handler, do not edit
```

## 📝 Explicación

- **`/app/*`** → Se redirige a Reverb (puerto 8081) para WebSockets
- **`/broadcasting/auth`** → Laravel lo maneja automáticamente (NO redirigir)
- **Todo lo demás** → Se redirige a `public/` (Laravel normal)

## ✅ Después de corregir

1. Guarda el `.htaccess` corregido
2. Recarga la página del chat
3. Intenta suscribirte a un canal privado
4. Deberías ver que `/broadcasting/auth` responde con 200 (no 404)

