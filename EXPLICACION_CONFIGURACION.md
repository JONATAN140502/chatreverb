# 📚 Explicación: Dos Configuraciones (Frontend vs Backend)

## 🔍 ¿Por qué dos configuraciones?

Hay **dos configuraciones diferentes** porque:

1. **Frontend (JavaScript)**: Se conecta desde el navegador del usuario → necesita usar el puerto público (443) a través del proxy
2. **Backend (Laravel)**: Envía eventos desde el servidor → necesita conectarse directamente al puerto interno (8081) donde Reverb está corriendo

## 🎨 Frontend (Vista/JavaScript)

**Archivo**: `resources/views/chat.blade.php`

**Configuración**:
```javascript
const reverbHost = 'chat.jonatanmayanga.com';  // Dominio público
const reverbPort = 443;                         // Puerto público (HTTPS)
const reverbScheme = 'https';                   // HTTPS
```

**Flujo**:
```
Navegador → wss://chat.jonatanmayanga.com:443/app/...
         ↓ (a través del proxy en .htaccess)
         → ws://127.0.0.1:8081/app/... (Reverb)
```

**Variables de entorno usadas**:
- `REVERB_HOST` → Dominio público
- `REVERB_PORT` → Puerto público (443)
- `REVERB_SCHEME` → Esquema público (https)

## ⚙️ Backend (Laravel)

**Archivo**: `config/broadcasting.php`

**Configuración**:
```php
'options' => [
    'host' => '127.0.0.1',    // Localhost (interno)
    'port' => 8081,            // Puerto interno donde Reverb está corriendo
    'scheme' => 'http',        // HTTP internamente
],
```

**Flujo**:
```
Laravel → http://127.0.0.1:8081 (Reverb directamente)
```

**Variables de entorno usadas**:
- `REVERB_SERVER_HOST` → 127.0.0.1 (localhost)
- `REVERB_SERVER_PORT` → 8081 (puerto interno)
- `REVERB_SERVER_SCHEME` → http (internamente)

## 📝 Configuración en `.env`

```env
# ============================================
# CONFIGURACIÓN PARA EL FRONTEND (JavaScript)
# ============================================
# El navegador usa estas para conectarse a través del proxy
REVERB_HOST=chat.jonatanmayanga.com
REVERB_PORT=443
REVERB_SCHEME=https

# ============================================
# CONFIGURACIÓN PARA EL BACKEND (Laravel)
# ============================================
# Laravel usa estas para enviar eventos directamente a Reverb
REVERB_SERVER_HOST=127.0.0.1
REVERB_SERVER_PORT=8081
REVERB_SERVER_SCHEME=http

# ============================================
# CREDENCIALES (compartidas)
# ============================================
REVERB_APP_KEY=mgacx7gc2e062tbmas96
REVERB_APP_SECRET=mgacx7gc2e062tbmas96-secret
REVERB_APP_ID=mgacx7gc2e062tbmas96-app
```

## 🔄 Flujo Completo

```
┌─────────────────┐
│   Navegador     │
│   (Usuario)     │
└────────┬────────┘
         │
         │ wss://chat.jonatanmayanga.com:443/app/...
         │ (HTTPS, puerto público)
         ↓
┌─────────────────┐
│  Proxy Apache   │
│   (.htaccess)   │
└────────┬────────┘
         │
         │ ws://127.0.0.1:8081/app/...
         │ (HTTP, puerto interno)
         ↓
┌─────────────────┐
│     Reverb      │
│  (Puerto 8081)  │
└─────────────────┘
         ↑
         │ http://127.0.0.1:8081
         │ (Laravel envía eventos aquí)
         │
┌─────────────────┐
│    Laravel      │
│   (Backend)     │
└─────────────────┘
```

## ✅ Resumen

| Componente | Host | Puerto | Esquema | Propósito |
|------------|------|--------|---------|-----------|
| **Frontend** | `chat.jonatanmayanga.com` | `443` | `https` | Conexión desde navegador |
| **Backend** | `127.0.0.1` | `8081` | `http` | Laravel envía eventos |
| **Reverb** | `127.0.0.1` | `8081` | `http` | Servidor WebSocket |

## 🎯 ¿Por qué no usar el mismo puerto?

- **Frontend**: No puede conectarse directamente a `127.0.0.1:8081` desde el navegador (es localhost del servidor, no del cliente)
- **Backend**: No necesita pasar por el proxy, puede conectarse directamente al puerto interno (más rápido y eficiente)

## 📌 Archivos de Configuración

1. **Frontend**: `resources/views/chat.blade.php` (líneas ~387-390)
2. **Backend**: `config/broadcasting.php` (líneas ~38-42)
3. **Reverb Server**: `config/reverb.php` (líneas ~32-33)

