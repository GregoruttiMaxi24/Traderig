<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class BidController extends Controller
{
    /**
     * Registra una puja en una subasta.
     *
     * Toda la validación de negocio se hace dentro de una transacción con la fila de la
     * subasta bloqueada (lockForUpdate): si dos personas pujan al mismo tiempo, la segunda
     * espera a que termine la primera y recién ahí valida contra el precio ya actualizado.
     * Los montos son pesos enteros, así que se comparan directamente con los operadores comunes.
     */
       public function store(Request $request, Auction $auction): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:6000000'],
        ], [
            'amount.required' => 'Ingresá el monto de tu puja.',
            'amount.integer' => 'Ingresá un monto en pesos enteros, sin centavos. Por ejemplo: 1500.',
            'amount.min' => 'El monto debe ser mayor a cero.',
            'amount.max' => 'El monto es demasiado alto.',
        ]);

        // validate() devuelve el valor como texto; se lo pasa a número entero.
        $amount = (int) $data['amount'];

        DB::transaction(function () use ($auction, $request, $amount) {
            // Releemos la subasta bloqueándola: a partir de acá los datos son los definitivos.
            $locked = Auction::lockForUpdate()->findOrFail($auction->id);
            $locked->load('listing');

            if ($locked->listing->status !== 'active' || ! $locked->isOpen()) {
                throw ValidationException::withMessages(['amount' => 'Esta subasta ya no está abierta.']);
            }

            if ($locked->listing->user_id === $request->user()->id) {
                throw ValidationException::withMessages(['amount' => 'No podés pujar en tu propia subasta.']);
            }

            $minimum = $locked->minimumNextBid();

            if ($amount < $minimum) {
                throw ValidationException::withMessages([
                    'amount' => 'La puja mínima es '.Money::format($minimum).'.',
                ]);
            }

            Bid::create([
                'auction_id' => $locked->id,
                'user_id' => $request->user()->id,
                'amount' => $amount,
            ]);

            $locked->update(['current_price' => $amount]);
        });

    // Responde con JSON para el panel de Vue o redirige a la publicación según el tipo de solicitud
        if ($request->expectsJson()) {
            return response()->json(['message' => '¡Tu puja fue registrada!'], 201);
        }

        return redirect()->route('listings.show', $auction->listing_id)
            ->with('success', '¡Tu puja fue registrada!');
    }
}