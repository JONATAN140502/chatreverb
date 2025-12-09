# 🔍 Verificar que Reverb esté funcionando

## Problema: "no disponible" en el estado del chat

Si ves "no disponible" en el indicador de estado, significa que el WebSocket no se está conectando.

## ✅ Checklist de verificación

### 1. Verificar que Reverb esté corriendo

Por SSH, ejecuta:
```bash
# Verificar si Reverb está corriendo
ps aux | grep reverb

# O verificar el puerto 8080
netstat -tulpn | grep 8080
# Deberías ver algo como: tcp 0 0 127.0.0.1:8080 0.0.0.0:* LISTEN
```

Si no está corriendo, inícialo:
```bash
cd /home/tu_usuario/public_html
php artisan reverb:start --host=127.0.0.1 --port=8080
```

### 2. Verificar que el proxy reverso esté configurado

**Opción A: Verificar `.htaccess`**

1. Ve a cPanel → Archivos
2. Abre el archivo `.htaccess` en la raíz de tu dominio
3. Debe contener:
```apache
RewriteEngine On

RewriteCond %{HTTP:Upgrade} websocket [NC]
RewriteCond %{HTTP:Connection} upgrade [NC]
RewriteRule ^/app/(.*)$ ws://127.0.0.1:8080/app/$1 [P,L]

RewriteRule ^/broadcasting/(.*)$ http://127.0.0.1:8000/broadcasting/$1 [P,L]
```

**Opción B: Verificar módulos de Apache**

Por SSH:
```bash
apache2ctl -M | grep proxy
# Deberías ver: proxy_module, proxy_http_module, proxy_wstunnel_module
```

Si faltan módulos:
```bash
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod proxy_wstunnel
sudo systemctl restart apache2
```

### 3. Verificar logs de Apache

Por SSH:
```bash
tail -f /var/log/apache2/error.log
# O en algunos servidores:
tail -f /usr/local/apache/logs/error_log
```

Intenta conectarte al chat y observa si hay errores.

### 4. Probar la conexión WebSocket manualmente

Por SSH:
```bash
# Probar conexión WebSocket
curl -i -N \
  -H "Connection: Upgrade" \
  -H "Upgrade: websocket" \
  -H "Sec-WebSocket-Version: 13" \
  -H "Sec-WebSocket-Key: test" \
  https://chat.jonatanmayanga.com/app/mgacx7gc2e062tbmas96
```

Si funciona, deberías ver una respuesta HTTP 101 (Switching Protocols).

### 5. Verificar configuración de `.env`

Asegúrate de que tu `.env` tenga:
```env
BROADCAST_CONNECTION=reverb
BROADCAST_DRIVER=reverb

REVERB_APP_ID=tu_app_id
REVERB_APP_KEY=mgacx7gc2e062tbmas96
REVERB_APP_SECRET=tu_secret
REVERB_HOST=chat.jonatanmayanga.com
REVERB_PORT=443
REVERB_SCHEME=https
```

### 6. Verificar firewall

Asegúrate de que el puerto 443 esté abierto:
```bash
sudo ufw status
# O
sudo iptables -L -n | grep 443
```

### 7. Verificar certificado SSL

Asegúrate de que tu certificado SSL esté válido y activo en cPanel.

## 🔧 Solución rápida: Probar conexión local

Si quieres probar rápidamente si Reverb funciona:

1. **Ejecuta Reverb localmente**:
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

2. **Temporalmente cambia tu `.env`**:
```env
REVERB_HOST=tu-ip-publica
REVERB_PORT=8080
REVERB_SCHEME=http
```

3. **Abre el firewall para el puerto 8080** (solo para pruebas):
```bash
sudo ufw allow 8080/tcp
```

4. **Prueba la conexión** desde tu navegador

**⚠️ IMPORTANTE**: Esto es solo para pruebas. En producción, usa el proxy reverso.

## 📝 Logs útiles

### Ver logs de Reverb:
```bash
tail -f storage/logs/reverb.log
# O si usas Supervisor:
sudo supervisorctl tail -f reverb
```

### Ver logs de Laravel:
```bash
tail -f storage/logs/laravel.log
```

### Ver logs del navegador:
- Abre la consola (F12)
- Ve a la pestaña "Console"
- Busca errores relacionados con WebSocket

## 🆘 Si nada funciona

1. **Usa Pusher temporalmente** para verificar que el resto del código funciona
2. **Contacta a tu proveedor de hosting** para verificar:
   - Si permiten procesos en segundo plano
   - Si permiten proxy reverso
   - Si hay restricciones en WebSockets

