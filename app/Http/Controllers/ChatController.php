<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Mostrar la vista del chat
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('chat', compact('users'));
    }

    /**
     * Enviar un mensaje
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'required|in:general,private',
            'receiver_id' => 'nullable|exists:users,id',
        ]);

        $message = Message::create([
            'user_id' => Auth::id(),
            'receiver_id' => $request->type === 'private' ? $request->receiver_id : null,
            'type' => $request->type,
            'message' => $request->message,
        ]);

        $message->load('user');

        // Emitir evento para broadcasting (síncrono, no en cola)
        try {
            $channel = $message->type === 'general' 
                ? 'chat.general' 
                : 'chat.private.' . min($message->user_id, $message->receiver_id) . '.' . max($message->user_id, $message->receiver_id);
            
            // Emitir evento síncronamente (ShouldBroadcastNow lo hace automáticamente)
            $event = new MessageSent($message);
            
            Log::info('📤 Intentando emitir evento MessageSent', [
                'message_id' => $message->id,
                'type' => $message->type,
                'user_id' => $message->user_id,
                'receiver_id' => $message->receiver_id,
                'channel' => $channel,
            ]);
            
            // Emitir el evento
            broadcast($event)->toOthers();
            
            Log::info('✅ Evento MessageSent emitido exitosamente', [
                'message_id' => $message->id,
                'channel' => $channel,
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error al emitir evento de broadcasting: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Obtener mensajes nuevos (Long Polling)
     */
    public function getMessages(Request $request): JsonResponse
    {
        $lastMessageId = $request->input('last_message_id', 0);
        $type = $request->input('type', 'general');
        $receiverId = $request->input('receiver_id');
        
        if ($lastMessageId == 0) {
            return $this->getAllMessages($type, $receiverId);
        }

        // Polling simple - solo devolver mensajes nuevos si existen
        $query = Message::where('id', '>', $lastMessageId)
            ->with('user');

        if ($type === 'general') {
            $query->where('type', 'general');
        } else {
            $query->where('type', 'private')
                ->where(function($q) use ($receiverId) {
                    $q->where(function($q2) use ($receiverId) {
                        $q2->where('user_id', Auth::id())
                           ->where('receiver_id', $receiverId);
                    })->orWhere(function($q2) use ($receiverId) {
                        $q2->where('user_id', $receiverId)
                           ->where('receiver_id', Auth::id());
                    });
                });
        }

        $messages = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'last_message_id' => $messages->count() > 0 ? $messages->last()->id : $lastMessageId,
        ]);
    }

    /**
     * Obtener todos los mensajes
     */
    private function getAllMessages($type = 'general', $receiverId = null): JsonResponse
    {
        $query = Message::with('user');

        if ($type === 'general') {
            $query->where('type', 'general');
        } else {
            $query->where('type', 'private')
                ->where(function($q) use ($receiverId) {
                    $q->where(function($q2) use ($receiverId) {
                        $q2->where('user_id', Auth::id())
                           ->where('receiver_id', $receiverId);
                    })->orWhere(function($q2) use ($receiverId) {
                        $q2->where('user_id', $receiverId)
                           ->where('receiver_id', Auth::id());
                    });
                });
        }

        $messages = $query->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'last_message_id' => $messages->count() > 0 ? $messages->last()->id : 0,
        ]);
    }

    /**
     * Obtener lista de usuarios
     */
    public function getUsers(): JsonResponse
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return response()->json(['success' => true, 'users' => $users]);
    }
}
