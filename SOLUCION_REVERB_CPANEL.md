# 🔧 Solución Completa para Reverb en cPanel

## ❌ Problemas Detectados

1. **Reverb NO está corriendo** - Los comandos muestran que no hay proceso en el puerto 8081
2. **Error en `.env`** - `REVERB_SCHEME=wss` debe ser `REVERB_SCHEME=https`
3. **Proxy reverso configurado** - El `.htaccess` está bien, pero necesita que Reverb esté corriendo

## ✅ Solución Paso a Paso

### Paso 1: Corregir el `.env`

Edita tu `.env` y cambia:

```env
# ❌ INCORRECTO
REVERB_SCHEME=wss

# ✅ CORRECTO
REVERB_SCHEME=https
```

El esquema debe ser `https` (para la URL), no `wss` (el protocolo WebSocket se determina automáticamente).

### Paso 2: Iniciar Reverb

Por SSH, ejecuta:

```bash
cd ~/chat.jonatanmayanga.com
php artisan reverb:start --host=127.0.0.1 --port=8081
```

**IMPORTANTE**: Deja esta terminal abierta. Si la cierras, Reverb se detendrá.

### Paso 3: Verificar que Reverb esté corriendo

En otra terminal SSH:

```bash
ps aux | grep reverb
# Deberías ver algo como:
# jotitaso  12345  0.0  2.5  ... php artisan reverb:start --host=127.0.0.1 --port=8081

netstat -tulpn | grep 8081
# Deberías ver:
# tcp  0  0  127.0.0.1:8081  0.0.0.0:*  LISTEN  12345/php
```

### Paso 4: Configurar Reverb para que corra siempre (Opcional pero Recomendado)

Para que Reverb siga corriendo después de cerrar SSH, usa `nohup`:

```bash
cd ~/chat.jonatanmayanga.com
nohup php artisan reverb:start --host=127.0.0.1 --port=8081 > storage/logs/reverb.log 2>&1 &
```

O mejor aún, usa **Supervisor** o **systemd** (ver instrucciones más abajo).

### Paso 5: Verificar el Proxy Reverso

Tu `.htaccess` está bien configurado. Solo asegúrate de que los módulos de Apache estén habilitados:

```bash
# Verificar módulos
apache2ctl -M | grep proxy
# Deberías ver: proxy_module, proxy_http_module, proxy_wstunnel_module
```

Si faltan módulos, contacta a tu proveedor de hosting para habilitarlos.

### Paso 6: Probar la Conexión

1. Recarga tu página del chat
2. Abre la consola (F12)
3. Deberías ver: "✅ WebSocket conectado"
4. El indicador de estado debería estar verde

## 🔄 Configurar Reverb como Servicio (Recomendado)

### Opción A: Usando Supervisor

1. **Crear archivo de configuración**:
```bash
sudo nano /etc/supervisor/conf.d/reverb.conf
```

2. **Agregar esta configuración**:
```ini
[program:reverb]
process_name=%(program_name)s
command=php /home/jotitaso/chat.jonatanmayanga.com/artisan reverb:start --host=127.0.0.1 --port=8081
autostart=true
autorestart=true
user=jotitaso
redirect_stderr=true
stdout_logfile=/home/jotitaso/chat.jonatanmayanga.com/storage/logs/reverb.log
stopwaitsecs=3600
```

3. **Reiniciar Supervisor**:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb
```

4. **Verificar estado**:
```bash
sudo supervisorctl status reverb
```

### Opción B: Usando nohup (Simple pero menos robusto)

```bash
cd ~/chat.jonatanmayanga.com
nohup php artisan reverb:start --host=127.0.0.1 --port=8081 > storage/logs/reverb.log 2>&1 &
```

## 📝 Resumen de Configuración

### `.env`:
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_KEY=mgacx7gc2e062tbmas96
REVERB_APP_SECRET=mgacx7gc2e062tbmas96-secret
REVERB_APP_ID=mgacx7gc2e062tbmas96-app
REVERB_HOST=chat.jonatanmayanga.com
REVERB_PORT=443
REVERB_SCHEME=https  # ✅ IMPORTANTE: https, NO wss
```

### `.htaccess`:
```apache
RewriteEngine On

# WebSocket Proxy
RewriteCond %{HTTP:Upgrade} websocket [NC]
RewriteCond %{HTTP:Connection} upgrade [NC]
RewriteRule ^app/(.*)$ ws://127.0.0.1:8081/app/$1 [P,L]

# HTTP Broadcast Auth
RewriteRule ^broadcasting/(.*)$ http://127.0.0.1:8081/broadcasting/$1 [P,L]

# Laravel Public
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ public/$1 [L]
```

### Reverb debe correr:
```bash
php artisan reverb:start --host=127.0.0.1 --port=8081
```

## 🔍 Verificar que Todo Funcione

1. **Verificar Reverb**:
```bash
ps aux | grep reverb
netstat -tulpn | grep 8081
```

2. **Ver logs de Reverb**:
```bash
tail -f ~/chat.jonatanmayanga.com/storage/logs/reverb.log
```

3. **Ver logs de Apache**:
```bash
tail -f /var/log/apache2/error.log
```

4. **Probar conexión WebSocket**:
```bash
curl -i -N \
  -H "Connection: Upgrade" \
  -H "Upgrade: websocket" \
  -H "Sec-WebSocket-Version: 13" \
  -H "Sec-WebSocket-Key: test" \
  https://chat.jonatanmayanga.com/app/mgacx7gc2e062tbmas96
```

## ⚠️ Problemas Comunes

### "WebSocket connection failed"
- Verifica que Reverb esté corriendo: `ps aux | grep reverb`
- Verifica que el puerto 8081 esté escuchando: `netstat -tulpn | grep 8081`

### "unavailable" en el estado
- Reverb no está corriendo o el proxy no está funcionando
- Verifica los logs de Apache: `tail -f /var/log/apache2/error.log`

### Reverb se detiene al cerrar SSH
- Usa `nohup` o configura Supervisor/systemd
- No cierres la terminal donde corre Reverb

