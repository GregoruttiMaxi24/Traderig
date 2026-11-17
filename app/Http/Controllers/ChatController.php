<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class ChatController extends Controller
{
    

/**
 * Abre una conversación existente con el vendedor de la publicación o crea una nueva
 * 
 */
    public function start(Request $request, Listing $listing): RedirectResponse

    {
        abort_unless($listing->status === 'active', 404);

        $user = $request->user();

        if ($listing->user_id === $user->id) {
            return back()->with('error', 'Esta es tu publicación: no podés chatear con vos mismo.');
        }

        $conversation = Conversation::firstOrCreate(
            ['listing_id' => $listing->id, 'buyer_id' => $user->id],
            ['seller_id' => $listing->user_id],
        );

        return redirect()->route('chats.show', $conversation);
    }


    /**
     * Lista las conversaciones del usuario, ya sea como comprador o como vendedor.
     * las conversaciones que  tuvieron actividad más reciente aparecen primero.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = Conversation::query()
            ->where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })

            ->with(['listing', 'buyer', 'seller'])
           ->latest('updated_at')
           ->get();

        return view('chats.index', compact('conversations'));
    }


/**
 * Sirve para mostrar un chat. Solo puede verlo el comprador o el vendedor.
 * 
*/
    public function show(Request $request, Conversation $conversation): View
    {
        $this->authorizeParticipant($request, $conversation);

        $conversation->load(['listing', 'buyer', 'seller']);

        return view('chats.show', compact('conversation'));
    }



/**
 * 
 * Devuelve en JSON los mensajes de la conversación
 * sirve para que el panel de Vue los pida.
 * 
 */
    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request, $conversation);

                $conversation->load('messages.sender');

        $messages = [];

        foreach ($conversation->messages as $message){   //transforma cada mensaje al formato que espera el front, aplicando el método formatMessage uno por uno.
            $messages[] = $this->formatMessage($message);
        }

        return response()->json(['messages' => $messages]);
    }


    /**
    * Sirve para guardar un mensaje nuevo escrito por el usuario y lo devuelve en JSON
    */
    public function send(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request, $conversation);

        //verificaciones:
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ], [
            'message.required' => 'Escribí un mensaje antes de enviarlo.',
            'message.max' => 'El mensaje puede tener hasta 1000 caracteres.',
        ]);



        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'message' => trim($data['message']),
        ]);

        //Se actualiza la fecha de la conversación para que suba en la lista de chats. 
         $conversation->updated_at = now();
        $conversation->save(); //para evitar ponerlo en el fillable. 


         $message->load('sender');

        return response()->json(['message' => $this->formatMessage($message)], 201);

    }

    
    /**
     * Corta con error 403 si el usuario no es el comprador ni el vendedor de la conversación.
     */
    private function authorizeParticipant(Request $request, Conversation $conversation): void{

            abort_unless($conversation->hasParticipant($request->user()), 403);
        }

    
    /**
     * 
     * Prepara los datos del mensaje para mostrarlos en pantalla
     * 
     */
    private function formatMessage(Message $message): array{

        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->name,
             'message' => $message->message,
             'created_at' => $message->created_at->toIso8601String(), // Formatear la fecha
       ];
    
    }  

}
