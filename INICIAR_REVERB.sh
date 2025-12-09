#!/bin/bash
# Script para iniciar Reverb en cPanel

# Cambiar al directorio del proyecto
cd ~/chat.jonatanmayanga.com

# Iniciar Reverb en el puerto 8081 (interno)
# Usar nohup para que siga corriendo después de cerrar la sesión SSH
nohup php artisan reverb:start --host=127.0.0.1 --port=8081 > storage/logs/reverb.log 2>&1 &

echo "Reverb iniciado en el puerto 8081"
echo "Para ver los logs: tail -f storage/logs/reverb.log"
echo "Para detener: pkill -f 'reverb:start'"

