<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ChatsController extends Controller
{
    /*  public function __construct()
    {
        $this->middleware('auth');
    } */

    public function index()
    {
        return view('chat');
    }

    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'recipient_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();

        $message = Message::create([
            'user_id' => $user->id,
            'message' => $request->input('message'),
            'recipient_id' => $request->input('recipient_id'),
        ]);

        // Emitir o evento
        broadcast(
            new MessageSent(
                $user,
                $message,
            )
        )->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso!',
            'data' => $message
        ], 201);
    }



    public function listUsers()
    {
        $authUser = Auth::user();

        $users = User::where('id', '!=', $authUser->id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Lista de usuários disponíveis para conversa.',
            'data' => $users
        ], 200);
    }

    public function listChatsWithLastMessages()
    {
        $authUser = Auth::user();

        $chats = Message::where('user_id', $authUser->id)
            ->orWhere('recipient_id', $authUser->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($authUser) {
                return $message->user_id === $authUser->id ? $message->recipient_id : $message->user_id;
            });

        return response()->json([
            'success' => true,
            'message' => 'Lista de chats com as últimas mensagens.',
            'data' => $chats
        ], 200);
    }
}
