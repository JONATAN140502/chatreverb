# Configuración para cPanel

## ⚠️ Limitaciones de Reverb en cPanel Compartido

Reverb **NO funciona directamente** en hosting compartido de cPanel porque:

1. **No puedes ejecutar procesos en segundo plano** - Reverb necesita `php artisan reverb:start` corriendo constantemente
2. **Puertos bloqueados** - El puerto 8080 generalmente está bloqueado
3. **Sin acceso SSH** - La mayoría de planes compartidos no permiten SSH

## ✅ Soluciones para cPanel

### Opción 1: Usar Pusher (Recomendado para cPanel)

Pusher es un servicio externo que funciona perfectamente en cPanel compartido.

#### Pasos:

1. **Crear cuenta en Pusher** (gratis hasta 200,000 mensajes/día):
   - Ve a https://pusher.com
   - Crea una cuenta gratuita
   - Crea una nueva app
   - Copia tus credenciales

2. **Configurar `.env` en cPanel**:
   ```env
   BROADCAST_CONNECTION=pusher
   BROADCAST_DRIVER=pusher
   
   PUSHER_APP_ID=tu_app_id
   PUSHER_APP_KEY=tu_app_key
   PUSHER_APP_SECRET=tu_app_secret
   PUSHER_APP_CLUSTER=us2
   ```

3. **Instalar dependencia** (si no está):
   ```bash
   composer require pusher/pusher-php-server
   ```

4. **Actualizar `config/broadcasting.php`** (ya está configurado)

5. **¡Listo!** El chat funcionará automáticamente con Pusher

### Opción 2: VPS con Reverb

Si tienes un VPS o servidor dedicado:

1. **Configurar `.env`**:
   ```env
   BROADCAST_CONNECTION=reverb
   BROADCAST_DRIVER=reverb
   
   REVERB_APP_ID=tu_app_id
   REVERB_APP_KEY=tu_app_key
   REVERB_APP_SECRET=tu_app_secret
   REVERB_HOST=tu-dominio.com
   REVERB_PORT=8080
   REVERB_SCHEME=https
   ```

2. **Ejecutar Reverb como servicio**:
   ```bash
   # Opción A: Usando Supervisor (recomendado)
   # Crear archivo: /etc/supervisor/conf.d/reverb.conf
   [program:reverb]
   process_name=%(program_name)s
   command=php /ruta/a/tu/proyecto/artisan reverb:start --host=0.0.0.0 --port=8080
   autostart=true
   autorestart=true
   user=www-data
   redirect_stderr=true
   stdout_logfile=/ruta/a/tu/proyecto/storage/logs/reverb.log
   
   # Luego ejecutar:
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start reverb
   
   # Opción B: Usando systemd
   # Crear archivo: /etc/systemd/system/reverb.service
   [Unit]
   Description=Laravel Reverb Server
   After=network.target
   
   [Service]
   Type=simple
   User=www-data
   WorkingDirectory=/ruta/a/tu/proyecto
   ExecStart=/usr/bin/php artisan reverb:start --host=0.0.0.0 --port=8080
   Restart=always
   
   [Install]
   WantedBy=multi-user.target
   
   # Luego ejecutar:
   sudo systemctl daemon-reload
   sudo systemctl enable reverb
   sudo systemctl start reverb
   ```

3. **Configurar firewall** (si es necesario):
   ```bash
   sudo ufw allow 8080/tcp
   ```

### Opción 3: Servicio de Reverb Externo

Puedes ejecutar Reverb en un VPS separado y apuntar tu aplicación de cPanel a él.

## 🔄 Cambiar entre Reverb y Pusher

El código ya está preparado para cambiar automáticamente según `BROADCAST_DRIVER`:

- `BROADCAST_DRIVER=pusher` → Usa Pusher (cPanel)
- `BROADCAST_DRIVER=reverb` → Usa Reverb (VPS/local)

## 📝 Variables de Entorno

### Para Pusher (cPanel):
```env
BROADCAST_CONNECTION=pusher
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=xxxxx
PUSHER_APP_KEY=xxxxx
PUSHER_APP_SECRET=xxxxx
PUSHER_APP_CLUSTER=us2
```

### Para Reverb (VPS/Local):
```env
BROADCAST_CONNECTION=reverb
BROADCAST_DRIVER=reverb
REVERB_APP_ID=xxxxx
REVERB_APP_KEY=xxxxx
REVERB_APP_SECRET=xxxxx
REVERB_HOST=tu-dominio.com
REVERB_PORT=8080
REVERB_SCHEME=https
```

## ✅ Verificación

Después de configurar, verifica en la consola del navegador:
- Deberías ver: "🔧 Configurando Pusher (cPanel)" o "🔧 Configurando Reverb (local/VPS)"
- El indicador de estado debería estar verde (conectado)

