# Configurar Reverb con HTTPS en cPanel (Puerto 443)

## 📋 Requisitos

1. Acceso SSH a tu servidor cPanel
2. Reverb corriendo en el puerto 8080 (interno)
3. Certificado SSL configurado en cPanel

## 🔧 Paso 1: Configurar Reverb para correr en puerto interno

1. **Edita tu `.env`**:
   ```env
   BROADCAST_CONNECTION=reverb
   BROADCAST_DRIVER=reverb
   
   REVERB_APP_ID=tu_app_id
   REVERB_APP_KEY=tu_app_key
   REVERB_APP_SECRET=tu_app_secret
   REVERB_HOST=tu-dominio.com
   REVERB_PORT=443
   REVERB_SCHEME=https
   ```

2. **Ejecuta Reverb en el puerto 8080 (interno)**:
   ```bash
   php artisan reverb:start --host=127.0.0.1 --port=8080
   ```

## 🔧 Paso 2: Configurar Proxy Reverso en cPanel

### Opción A: Usando Apache (cPanel estándar)

1. **Accede a cPanel → Archivos → Editor de archivos**

2. **Edita el archivo `.htaccess` en la raíz de tu dominio** o crea uno si no existe:
   ```apache
   # Habilitar mod_rewrite y mod_proxy
   RewriteEngine On
   
   # Proxy para WebSocket de Reverb
   RewriteCond %{HTTP:Upgrade} websocket [NC]
   RewriteCond %{HTTP:Connection} upgrade [NC]
   RewriteRule ^/app/(.*)$ ws://127.0.0.1:8080/app/$1 [P,L]
   
   # Proxy para broadcasting/auth
   RewriteRule ^/broadcasting/(.*)$ http://127.0.0.1:8000/broadcasting/$1 [P,L]
   ```

3. **O mejor, edita el archivo de configuración de Apache directamente**:
   
   En cPanel, ve a **Configuración de Apache** o edita el archivo de configuración de tu dominio.
   
   Agrega esto dentro de tu VirtualHost:
   ```apache
   <VirtualHost *:443>
       ServerName tu-dominio.com
       DocumentRoot /home/usuario/public_html
       
       # SSL Configuration
       SSLEngine on
       SSLCertificateFile /path/to/certificate.crt
       SSLCertificateKeyFile /path/to/private.key
       
       # Proxy para WebSocket de Reverb
       ProxyPreserveHost On
       ProxyRequests Off
       
       # WebSocket para Reverb
       RewriteEngine On
       RewriteCond %{HTTP:Upgrade} websocket [NC]
       RewriteCond %{HTTP:Connection} upgrade [NC]
       RewriteRule ^/app/(.*)$ ws://127.0.0.1:8080/app/$1 [P,L]
       
       # Proxy HTTP para broadcasting
       ProxyPass /broadcasting/ http://127.0.0.1:8000/broadcasting/
       ProxyPassReverse /broadcasting/ http://127.0.0.1:8000/broadcasting/
   </VirtualHost>
   ```

### Opción B: Usando Nginx (si tu cPanel lo soporta)

Si tu cPanel tiene Nginx, edita la configuración de Nginx:

```nginx
location /app/ {
    proxy_pass http://127.0.0.1:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_read_timeout 86400;
}

location /broadcasting/ {
    proxy_pass http://127.0.0.1:8000;
    proxy_http_version 1.1;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
}
```

## 🔧 Paso 3: Habilitar módulos de Apache

Necesitas habilitar estos módulos de Apache:

```bash
# Conecta por SSH y ejecuta:
sudo a2enmod rewrite
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod proxy_wstunnel
sudo systemctl restart apache2
```

O desde cPanel:
1. Ve a **Configuración de Apache** → **Módulos de Apache**
2. Habilita:
   - `mod_rewrite`
   - `mod_proxy`
   - `mod_proxy_http`
   - `mod_proxy_wstunnel`

## 🔧 Paso 4: Ejecutar Reverb como servicio

Para que Reverb esté siempre corriendo, configúralo como servicio:

### Usando Supervisor (Recomendado)

1. **Instala Supervisor** (si no está instalado):
   ```bash
   sudo yum install supervisor  # CentOS/RHEL
   # o
   sudo apt-get install supervisor  # Debian/Ubuntu
   ```

2. **Crea el archivo de configuración**:
   ```bash
   sudo nano /etc/supervisor/conf.d/reverb.conf
   ```

3. **Agrega esta configuración**:
   ```ini
   [program:reverb]
   process_name=%(program_name)s
   command=php /home/usuario/public_html/artisan reverb:start --host=127.0.0.1 --port=8080
   autostart=true
   autorestart=true
   user=usuario
   redirect_stderr=true
   stdout_logfile=/home/usuario/public_html/storage/logs/reverb.log
   stopwaitsecs=3600
   ```

4. **Reinicia Supervisor**:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start reverb
   ```

### Usando systemd (Alternativa)

1. **Crea el archivo de servicio**:
   ```bash
   sudo nano /etc/systemd/system/reverb.service
   ```

2. **Agrega esta configuración**:
   ```ini
   [Unit]
   Description=Laravel Reverb Server
   After=network.target

   [Service]
   Type=simple
   User=usuario
   WorkingDirectory=/home/usuario/public_html
   ExecStart=/usr/bin/php artisan reverb:start --host=127.0.0.1 --port=8080
   Restart=always
   RestartSec=10

   [Install]
   WantedBy=multi-user.target
   ```

3. **Habilita y inicia el servicio**:
   ```bash
   sudo systemctl daemon-reload
   sudo systemctl enable reverb
   sudo systemctl start reverb
   ```

## ✅ Paso 5: Verificar la configuración

1. **Verifica que Reverb esté corriendo**:
   ```bash
   ps aux | grep reverb
   # o
   sudo supervisorctl status reverb
   # o
   sudo systemctl status reverb
   ```

2. **Verifica que el proxy funcione**:
   - Abre tu navegador
   - Ve a la consola (F12)
   - Deberías ver: "🔧 Configurando Reverb (local/VPS)" con `scheme: 'https'`
   - El indicador de estado debería estar verde (conectado)

3. **Prueba la conexión WebSocket**:
   ```bash
   curl -i -N \
     -H "Connection: Upgrade" \
     -H "Upgrade: websocket" \
     -H "Sec-WebSocket-Version: 13" \
     -H "Sec-WebSocket-Key: test" \
     https://tu-dominio.com/app/tu-key
   ```

## 🔍 Solución de problemas

### Error: "WebSocket connection failed"

1. Verifica que Reverb esté corriendo:
   ```bash
   netstat -tulpn | grep 8080
   ```

2. Verifica que el proxy esté configurado correctamente

3. Verifica los logs de Apache:
   ```bash
   tail -f /var/log/apache2/error.log
   ```

### Error: "mod_proxy_wstunnel not found"

Si tu servidor no tiene `mod_proxy_wstunnel`, usa esta alternativa en `.htaccess`:

```apache
RewriteEngine On

# WebSocket para Reverb
RewriteCond %{HTTP:Upgrade} =websocket [NC]
RewriteRule /app/(.*) ws://127.0.0.1:8080/app/$1 [P,L]

RewriteCond %{HTTP:Upgrade} !=websocket [NC]
RewriteRule /app/(.*) http://127.0.0.1:8080/app/$1 [P,L]
```

### Reverb se desconecta frecuentemente

Aumenta el timeout en la configuración del proxy:

```apache
ProxyTimeout 86400
```

## 📝 Notas importantes

- Reverb debe correr en `127.0.0.1:8080` (solo localhost) por seguridad
- El proxy reverso redirige el tráfico público (HTTPS:443) al puerto interno (8080)
- Asegúrate de que el firewall permita conexiones en el puerto 443
- El puerto 8080 NO debe estar expuesto públicamente

