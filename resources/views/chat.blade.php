<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat - {{ Auth::user()->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f0f2f5;
            height: 100vh;
            overflow: hidden;
        }
        .app-container {
            display: flex;
            height: 100vh;
        }
        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: #1877f2;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #42b72a;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        /* Sidebar */
        .sidebar {
            width: 300px;
            background: white;
            border-right: 1px solid #e4e6eb;
            margin-top: 56px;
            height: calc(100vh - 56px);
            overflow-y: auto;
        }
        .chat-tabs {
            display: flex;
            border-bottom: 1px solid #e4e6eb;
        }
        .chat-tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-weight: 600;
            color: #65676b;
        }
        .chat-tab.active {
            color: #1877f2;
            border-bottom-color: #1877f2;
        }
        .users-list {
            padding: 8px;
        }
        .user-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .user-item:hover {
            background: #f0f2f5;
        }
        .user-item.active {
            background: #e7f3ff;
        }
        .user-avatar-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1877f2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        .user-name {
            font-weight: 600;
            color: #050505;
        }
        /* Main Chat Area */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-top: 56px;
            height: calc(100vh - 56px);
            background: white;
        }
        .chat-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e4e6eb;
            background: white;
        }
        .chat-title {
            font-size: 20px;
            font-weight: 600;
            color: #050505;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f0f2f5;
        }
        .message {
            display: flex;
            margin-bottom: 12px;
            animation: fadeIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message.own {
            flex-direction: row-reverse;
        }
        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #1877f2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
            margin: 0 8px;
        }
        .message-content {
            max-width: 60%;
            background: white;
            padding: 8px 12px;
            border-radius: 18px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .message.own .message-content {
            background: #1877f2;
            color: white;
        }
        .message-header {
            display: flex;
            align-items: baseline;
            gap: 8px;
            margin-bottom: 4px;
        }
        .message-username {
            font-weight: 600;
            font-size: 13px;
            color: #1877f2;
        }
        .message.own .message-username {
            color: rgba(255,255,255,0.9);
        }
        .message-time {
            font-size: 11px;
            color: #65676b;
        }
        .message.own .message-time {
            color: rgba(255,255,255,0.7);
        }
        .message-text {
            color: #050505;
            word-wrap: break-word;
        }
        .message.own .message-text {
            color: white;
        }
        .chat-input-area {
            padding: 16px 20px;
            border-top: 1px solid #e4e6eb;
            background: white;
        }
        .input-container {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }
        .message-input {
            flex: 1;
            padding: 10px 16px;
            border: none;
            border-radius: 20px;
            background: #f0f2f5;
            font-size: 15px;
            resize: none;
            max-height: 100px;
            font-family: inherit;
        }
        .message-input:focus {
            outline: none;
            background: #e4e6eb;
        }
        .send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: #1877f2;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: background 0.2s;
        }
        .send-btn:hover {
            background: #166fe5;
        }
        .send-btn:disabled {
            background: #e4e6eb;
            cursor: not-allowed;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #65676b;
        }
        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #42b72a;
            display: inline-block;
            margin-left: 8px;
        }
        .status-indicator.disconnected {
            background: #f02849;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <div class="logo">💬 Chat</div>
        </div>
        <div class="header-right">
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <span>{{ Auth::user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Salir</button>
            </form>
        </div>
    </div>

    <div class="app-container">
        <div class="sidebar">
            <div class="chat-tabs">
                <div class="chat-tab active" onclick="switchTab('general')">General</div>
                <div class="chat-tab" onclick="switchTab('private')">Privado</div>
            </div>
            <div class="users-list" id="usersList">
                <div class="empty-state">Selecciona un usuario para chatear</div>
            </div>
        </div>

        <div class="chat-main">
            <div class="chat-header">
                <div class="chat-title" id="chatTitle">Chat General</div>
                <span class="status-indicator" id="statusIndicator"></span>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="empty-state">Cargando mensajes...</div>
            </div>
            <div class="chat-input-area">
                <div class="input-container">
                    <textarea 
                        id="messageInput" 
                        class="message-input" 
                        placeholder="Escribe un mensaje..."
                        rows="1"
                        maxlength="1000"
                    ></textarea>
                    <button class="send-btn" id="sendButton" onclick="sendMessage()">➤</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.min.js"></script>
    <script>
        let currentTab = 'general';
        let currentReceiverId = null;
        let currentChannel = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const currentUserId = {{ Auth::id() }};
        
        // Configurar Echo - Soporta tanto Reverb como Pusher
        window.Pusher = Pusher;
        
        // Detectar si usar Pusher (cPanel) o Reverb (local/VPS)
        const usePusher = '{{ env("BROADCAST_DRIVER", "reverb") }}' === 'pusher';
        const broadcaster = usePusher ? 'pusher' : 'pusher'; // Ambos usan pusher-js
        
        let echoConfig;
        
        if (usePusher) {
            // Configuración para Pusher (cPanel compartido)
            const pusherKey = '{{ env("PUSHER_APP_KEY", "") }}';
            const pusherCluster = '{{ env("PUSHER_APP_CLUSTER", "us2") }}';
            
            console.log('🔧 Configurando Pusher (cPanel):', { key: pusherKey, cluster: pusherCluster });
            
            echoConfig = {
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster,
                forceTLS: true,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                }
            };
        } else {
            // Configuración para Reverb (local/VPS)
            const reverbKey = '{{ env("REVERB_APP_KEY", "local-key") }}';
            let reverbHost = '{{ env("REVERB_HOST", "127.0.0.1") }}';
            const reverbPort = {{ env("REVERB_PORT", 8080) }};
            const reverbScheme = '{{ env("REVERB_SCHEME", "http") }}';
            
            // Asegurar que el host sea 127.0.0.1 si es localhost
            if (reverbHost === 'localhost') {
                reverbHost = '127.0.0.1';
            }
            
            console.log('🔧 Configurando Reverb (local/VPS):', { key: reverbKey, host: reverbHost, port: reverbPort, scheme: reverbScheme });
            
            echoConfig = {
                broadcaster: 'pusher',
                key: reverbKey,
                wsHost: reverbHost,
                wsPort: reverbPort,
                wssPort: reverbPort,
                forceTLS: reverbScheme === 'https',
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
                cluster: '', // Reverb no usa cluster
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                },
                encrypted: reverbScheme === 'https'
            };
        }
        
        window.Echo = new Echo(echoConfig);
        
        // Manejar conexión - actualizar estado
        setTimeout(() => {
            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                const pusher = window.Echo.connector.pusher;
                
                // Escuchar todos los eventos de Pusher para depurar
                pusher.connection.bind('connected', () => {
                    console.log('✅ WebSocket conectado');
                    updateStatus(true);
                });
                
                pusher.connection.bind('disconnected', () => {
                    console.log('❌ WebSocket desconectado');
                    updateStatus(false);
                });
                
                pusher.connection.bind('error', (err) => {
                    console.error('❌ Error WebSocket:', err);
                    updateStatus(false);
                });
                
                pusher.connection.bind('state_change', (states) => {
                    console.log('🔄 Estado WebSocket:', states.current);
                    if (states.current === 'connected') {
                        updateStatus(true);
                    } else if (states.current === 'disconnected' || states.current === 'failed' || states.current === 'unavailable') {
                        updateStatus(false);
                    }
                });
                
                // Escuchar eventos de suscripción
                pusher.bind('pusher:subscription_succeeded', (data) => {
                    console.log('✅ Suscripción exitosa:', data.channel);
                });
                
                pusher.bind('pusher:subscription_error', (data) => {
                    console.error('❌ Error de suscripción:', data);
                });
                
                // Escuchar todos los mensajes para depurar (si está disponible)
                if (typeof pusher.bind_all === 'function') {
                    pusher.bind_all((eventName, data) => {
                        if (eventName.startsWith('pusher:') || eventName.includes('message')) {
                            console.log('📨 Evento recibido:', eventName, data);
                        }
                    });
                }
                
                // Verificar estado inicial
                if (pusher.connection.state === 'connected') {
                    updateStatus(true);
                } else {
                    // Intentar conectar
                    pusher.connect();
                }
            }
        }, 100);

        // Cambiar entre chat general y privado
        function switchTab(tab) {
            currentTab = tab;
            currentReceiverId = null;
            
            document.querySelectorAll('.chat-tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            if (tab === 'general') {
                document.getElementById('chatTitle').textContent = 'Chat General';
                document.getElementById('usersList').innerHTML = '<div class="empty-state">Chat general activo</div>';
                loadMessages();
            } else {
                document.getElementById('chatTitle').textContent = 'Selecciona un usuario';
                loadUsers();
            }
        }

        // Cargar lista de usuarios
        async function loadUsers() {
            try {
                const response = await fetch('/chat/users');
                const data = await response.json();
                
                if (data.success) {
                    const usersList = document.getElementById('usersList');
                    if (data.users.length === 0) {
                        usersList.innerHTML = '<div class="empty-state">No hay otros usuarios</div>';
                        return;
                    }
                    
                    usersList.innerHTML = data.users.map(user => `
                        <div class="user-item" onclick="selectUser(${user.id}, '${escapeHtml(user.name)}')">
                            <div class="user-avatar-small">${user.name.charAt(0).toUpperCase()}</div>
                            <div class="user-name">${escapeHtml(user.name)}</div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Error al cargar usuarios:', error);
            }
        }

        // Seleccionar usuario para chat privado
        function selectUser(userId, userName) {
            currentReceiverId = userId;
            document.getElementById('chatTitle').textContent = `Chat con ${userName}`;
            document.querySelectorAll('.user-item').forEach(item => item.classList.remove('active'));
            event.currentTarget.classList.add('active');
            loadMessages();
        }

        // Cargar mensajes iniciales
        async function loadMessages() {
            try {
                // Desuscribirse del canal anterior
                if (currentChannel && window.Echo) {
                    try {
                        window.Echo.leave(currentChannel);
                    } catch(e) {
                        // Ignorar errores
                    }
                    currentChannel = null;
                }
                
                const url = `/chat/messages?last_message_id=0&type=${currentTab}${currentReceiverId ? '&receiver_id=' + currentReceiverId : ''}`;
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success) {
                    const messagesContainer = document.getElementById('chatMessages');
                    messagesContainer.innerHTML = '';
                    
                    if (data.messages.length > 0) {
                        data.messages.forEach(message => {
                            addMessageToDOM(message);
                        });
                    } else {
                        messagesContainer.innerHTML = '<div class="empty-state">No hay mensajes aún. ¡Sé el primero en escribir!</div>';
                    }
                    
                    scrollToBottom();
                    subscribeToChannel();
                }
            } catch (error) {
                console.error('Error al cargar mensajes:', error);
                updateStatus(false);
            }
        }

        // Suscribirse a canal de WebSocket (Reverb)
        let currentChannelInstance = null;
        
        function subscribeToChannel() {
            if (!window.Echo || !window.Echo.channel || !window.Echo.private) {
                console.log('Echo no está disponible aún, reintentando...');
                setTimeout(subscribeToChannel, 500);
                return;
            }
            
            // Desuscribirse del canal anterior si existe
            if (currentChannelInstance) {
                try {
                    console.log('🔌 Desuscribiéndose del canal anterior');
                    window.Echo.leave(currentChannel);
                    currentChannelInstance = null;
                } catch(e) {
                    console.error('Error al dejar canal:', e);
                }
            }

            if (currentTab === 'general') {
                currentChannel = 'chat.general';
                try {
                    console.log('📡 Suscribiéndose a canal general:', currentChannel);
                    currentChannelInstance = window.Echo.channel(currentChannel);
                    
                    // Remover listeners anteriores si existen
                    try {
                        if (currentChannelInstance && typeof currentChannelInstance.stopListening === 'function') {
                            currentChannelInstance.stopListening('.message.sent');
                        }
                    } catch(e) {
                        // Ignorar si no se puede remover
                    }
                    
                    currentChannelInstance.listen('.message.sent', (e) => {
                        console.log('✅ Mensaje recibido en general:', e);
                        console.log('📦 Datos completos:', JSON.stringify(e, null, 2));
                        if (e.message) {
                            console.log('👤 User ID del mensaje:', e.message.user_id, 'vs Current User:', currentUserId);
                            // Verificar que no sea nuestro propio mensaje (ya lo agregamos manualmente)
                            if (e.message.user_id !== currentUserId) {
                                console.log('➕ Agregando mensaje del otro usuario');
                                console.log('📝 Datos del mensaje a agregar:', e.message);
                                
                                // Verificar si el mensaje ya existe en el DOM para evitar duplicados
                                const existingMessage = document.querySelector(`[data-message-id="${e.message.id}"]`);
                                if (existingMessage) {
                                    console.log('⚠️ El mensaje ya existe en el DOM, ignorando');
                                    return;
                                }
                                
                                addMessageToDOM(e.message);
                                scrollToBottom();
                                console.log('✅ Mensaje agregado y scroll realizado');
                            } else {
                                console.log('⏭️ Ignorando mensaje propio (ya está en la vista)');
                            }
                        } else {
                            console.warn('⚠️ El mensaje no tiene datos');
                        }
                    });
                    console.log('✅ Suscrito a canal general');
                } catch(e) {
                    console.error('❌ Error al suscribirse a canal general:', e);
                }
            } else if (currentReceiverId) {
                const userId1 = Math.min(currentUserId, currentReceiverId);
                const userId2 = Math.max(currentUserId, currentReceiverId);
                const channelName = `chat.private.${userId1}.${userId2}`;
                currentChannel = channelName;
                try {
                    if (typeof window.Echo.private === 'function') {
                        console.log('📡 Suscribiéndose a canal privado:', channelName);
                        currentChannelInstance = window.Echo.private(channelName);
                        
                        // Remover listeners anteriores si existen
                        try {
                            if (currentChannelInstance && typeof currentChannelInstance.stopListening === 'function') {
                                currentChannelInstance.stopListening('.message.sent');
                            }
                        } catch(e) {
                            // Ignorar si no se puede remover
                        }
                        
                        currentChannelInstance.listen('.message.sent', (e) => {
                            console.log('✅ Mensaje recibido en canal privado:', e);
                            console.log('📦 Datos completos:', JSON.stringify(e, null, 2));
                            if (e.message) {
                                console.log('👤 User ID del mensaje:', e.message.user_id, 'vs Current User:', currentUserId);
                                // Verificar que no sea nuestro propio mensaje (ya lo agregamos manualmente)
                                if (e.message.user_id !== currentUserId) {
                                    console.log('➕ Agregando mensaje del otro usuario');
                                    console.log('📝 Datos del mensaje a agregar:', e.message);
                                    
                                    // Verificar si el mensaje ya existe en el DOM para evitar duplicados
                                    const existingMessage = document.querySelector(`[data-message-id="${e.message.id}"]`);
                                    if (existingMessage) {
                                        console.log('⚠️ El mensaje ya existe en el DOM, ignorando');
                                        return;
                                    }
                                    
                                    addMessageToDOM(e.message);
                                    scrollToBottom();
                                    console.log('✅ Mensaje agregado y scroll realizado');
                                } else {
                                    console.log('⏭️ Ignorando mensaje propio (ya está en la vista)');
                                }
                            } else {
                                console.warn('⚠️ El mensaje no tiene datos');
                            }
                        });
                        console.log('✅ Suscrito a canal privado:', channelName);
                    } else {
                        console.error('❌ Echo.private no es una función');
                    }
                } catch(e) {
                    console.error('❌ Error al suscribirse a canal privado:', e);
                }
            }
        }

        // Enviar mensaje
        async function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const sendButton = document.getElementById('sendButton');
            const message = messageInput.value.trim();

            if (!message) return;
            if (currentTab === 'private' && !currentReceiverId) {
                alert('Por favor, selecciona un usuario primero');
                return;
            }

            sendButton.disabled = true;

            try {
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: message,
                        type: currentTab,
                        receiver_id: currentReceiverId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Agregar el mensaje inmediatamente a la vista del remitente
                    if (data.message) {
                        addMessageToDOM(data.message);
                        scrollToBottom();
                    }
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                    messageInput.focus();
                } else {
                    alert('Error al enviar el mensaje');
                }
            } catch (error) {
                console.error('Error al enviar mensaje:', error);
                alert('Error al enviar el mensaje. Por favor, intenta de nuevo.');
            } finally {
                sendButton.disabled = false;
            }
        }

        // Agregar mensaje al DOM
        function addMessageToDOM(message) {
            console.log('🎨 addMessageToDOM llamado con:', message);
            const messagesContainer = document.getElementById('chatMessages');
            
            if (!messagesContainer) {
                console.error('❌ No se encontró el contenedor de mensajes');
                return;
            }
            
            const loading = messagesContainer.querySelector('.empty-state');
            if (loading) {
                console.log('🗑️ Removiendo estado vacío');
                loading.remove();
            }

            const isOwn = message.user_id === currentUserId;
            const date = new Date(message.created_at);
            const timeString = date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            const userName = message.user ? message.user.name : 'Usuario';
            const userInitial = userName.charAt(0).toUpperCase();

            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isOwn ? 'own' : ''}`;
            messageDiv.setAttribute('data-message-id', message.id);
            messageDiv.innerHTML = `
                <div class="message-avatar">${userInitial}</div>
                <div class="message-content">
                    <div class="message-header">
                        <span class="message-username">${escapeHtml(userName)}</span>
                        <span class="message-time">${timeString}</span>
                    </div>
                    <div class="message-text">${escapeHtml(message.message)}</div>
                </div>
            `;

            messagesContainer.appendChild(messageDiv);
            console.log('✅ Mensaje agregado al DOM. Total de mensajes:', messagesContainer.children.length);
        }

        // Scroll al final
        function scrollToBottom() {
            const messagesContainer = document.getElementById('chatMessages');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                console.log('📜 Scroll realizado. ScrollTop:', messagesContainer.scrollTop, 'ScrollHeight:', messagesContainer.scrollHeight);
            } else {
                console.error('❌ No se encontró el contenedor para hacer scroll');
            }
        }

        // Actualizar estado
        function updateStatus(connected) {
            const indicator = document.getElementById('statusIndicator');
            if (!indicator) return;
            
            if (connected) {
                indicator.classList.remove('disconnected');
                indicator.title = 'Conectado';
            } else {
                indicator.classList.add('disconnected');
                indicator.title = 'Desconectado';
            }
        }
        
        // Inicializar estado como conectado
        updateStatus(true);

        // Escapar HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Enviar con Enter
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Auto-resize textarea
        document.getElementById('messageInput').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Cargar mensajes al iniciar
        window.addEventListener('DOMContentLoaded', function() {
            // Estado inicial conectado
            updateStatus(true);
            // Cargar mensajes
            loadMessages();
        });

        // Desconectar al cerrar
        window.addEventListener('beforeunload', function() {
            if (window.Echo && currentChannel) {
                try {
                    window.Echo.leave(currentChannel);
                } catch(e) {
                    // Ignorar errores al cerrar
                }
            }
        });
    </script>
</body>
</html>
