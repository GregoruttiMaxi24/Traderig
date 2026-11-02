<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BidController extends Controller
{
    /**
     * Registra una puja en una subasta.
     *
     * Toda la validación de negocio se hace dentro de una transacción con la fila de la
     * subasta bloqueada (lockForUpdate): si dos personas pujan al mismo tiempo, la segunda
     * espera a que termine la primera y recién ahí valida contra el precio ya actualizado.
     */
    public function store(Request $request, Auction $auction): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'decimal:0,2', 'gt:0', 'max:6000000'],
        ], [
            'amount.required' => 'Ingresá el monto de tu puja.',
            'amount.decimal' => 'Ingresá un monto válido, por ejemplo: 1500 o 1500.50.',
            'amount.gt' => 'El monto debe ser mayor a cero.',
            'amount.max' => 'El monto es demasiado alto.',
        ]);

        DB::transaction(function () use ($auction, $request, $data) {
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

            if (bccomp($data['amount'], $minimum, 2) < 0) {
                throw ValidationException::withMessages([
                    'amount' => 'La puja mínima es '.Money::format($minimum).'.',
                ]);
            }

            Bid::create([
                'auction_id' => $locked->id,
                'user_id' => $request->user()->id,
                'amount' => $data['amount'],
            ]);

            $locked->update(['current_price' => $data['amount']]);
        });

        return redirect()->route('listings.show', $auction->listing_id)
            ->with('success', '¡Tu puja fue registrada!');
    }
}