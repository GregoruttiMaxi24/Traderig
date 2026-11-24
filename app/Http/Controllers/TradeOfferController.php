<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\TradeOffer;
use Illuminate\Support\Facades\DB;

class TradeOfferController extends Controller
{
    /**
     * Muestra el formulario para proponer un intercambio por una publicación.
     * 
     */
    public function create(Request $request, Listing $listing)
    {
        if ($listing->status !== 'active') {
            abort(404);
        }

        $user = $request->user();

        if ($listing->user_id === $user->id) {
            return back()->with('error', 'Esta es tu publicación: no podés proponerte un intercambio a vos mismo.');
        }

        if (! $listing->accepts_trade) {
            return redirect()->route('listings.show', $listing)
                ->with('error', 'Esta publicación no acepta intercambios.');
        }

        // Publicaciones propias que se pueden ofrecer a cambio: activas y de venta directa.
        $myListings = $user->listings()
            ->active()
            ->where('type', 'sale')
            ->orderBy('title')
            ->get();

        return view('trade-offers.create', compact('listing', 'myListings'));
    }



        /**
     * Guarda la propuesta de intercambio y abre el chat con el dueño de la publicación.
     */
    public function store(Request $request, Listing $listing)
    {
        if ($listing->status !== 'active') {
            abort(404);
        }

        $user = $request->user();

        if ($listing->user_id === $user->id) {
            return back()->with('error', 'Esta es tu publicación: no podés proponerte un intercambio a vos mismo.');
        }

        if (!$listing->accepts_trade) {
            return redirect()->route('listings.show', $listing)
                ->with('error', 'Esta publicación no acepta intercambios.');
        }


            $request->validate([
                'cash_amount' => 'nullable|numeric|min:0',
                'message' => 'nullable|max:1000',
            ], [
                'cash_amount.numeric' => 'La diferencia de dinero debe ser un valor numérico',
                'cash_amount.min' => 'La diferencia de dinero no puede ser negativa',
                'message.max' => 'El mensaje puede tener hasta 1000 caracteres',
            ]);


        // Datos del formulario.
        $offeredId = $request->input('offered_listing_id');
        $cash = $request->input('cash_amount');
        $direction = $request->input('cash_direction');
        $message = $request->input('message');

        if ($cash === null) {
            $cash = 0;
        }

        $cash = (int) $cash;

    //La publicación que se ofrece tiene que ser del ese usuario, estar activa y ser de venta directa.
        if ($offeredId !== null) {
            $offered = $user->listings()->active()->where('type', 'sale')->find($offeredId);

            if ($offered === null) {
                return back()->withInput()
                    ->with('error', 'La publicación que ofrecés no es válida.');
            }
        }

        //La propuesta tiene que ofrecer algo: o una publicación, o dinero o las dos cosas.
        if ($offeredId === null && $cash === 0) {
            return back()->withInput()
                ->with('error', 'Ofrecé una publicación, dinero o las dos cosas.');
        }

        // Si hay plata en el intercambio hay que decir quién lo paga. Si no hay, no se guarda dirección.
        if ($cash > 0 && $direction !== 'offerer_pays' && $direction !== 'owner_pays') {
            return back()->withInput()
                ->with('error', 'Indicá quién paga la diferencia de dinero.');
        }

        if ($cash === 0) {
            $direction = null;
        }

    //una sola propuesta pendiente por persona y publicación
        $pending = TradeOffer::where('listing_id', $listing->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($pending !== null) {
            return redirect()->route('listings.show', $listing)
                ->with('error', 'Ya tenés una propuesta pendiente para esta publicación.');
        }

    // La Propuesta y el chat se crean juntos si falla algo, no queda nada guardado.
        $conversation = DB::transaction(function () use ($listing, $user, $message, $offeredId, $cash, $direction) {
            TradeOffer::create([
                'listing_id' => $listing->id,
                'user_id' => $user->id,
                'offered_listing_id' => $offeredId,
                'message' => $message,
                'cash_amount' => $cash,
                'cash_direction' => $direction,
            ]);

            // Puede existir ya una conversación entre los dos por esta publicación.
            $conversation = Conversation::where('listing_id', $listing->id)
                ->where('buyer_id', $user->id)
                ->first();

            if ($conversation === null) {
                $conversation = Conversation::create([
                    'listing_id' => $listing->id,
                    'buyer_id' => $user->id,
                    'seller_id' => $listing->user_id,
                ]);
            }

            // Si escribió un mensaje en la propuesta, es el primero del chat.
            if ($message !== null) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id,
                    'message' => $message,
                ]);
            }

            return $conversation;
        });

        return redirect()->route('chats.show', $conversation)
            ->with('success', 'Enviaste tu propuesta de intercambio.');
    }
}