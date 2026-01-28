<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Initier un paiement
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'phone' => 'required|string',
            'payment_method' => 'required|string|in:orange_money,wave,free_money'
        ]);

        $user = $request->user();

        // Générer un identifiant de transaction unique
        $transactionId = 'TXN-' . time() . '-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Log de la transaction
        Log::info("PAYMENT INITIATED", [
            'user_id' => $user->id,
            'transaction_id' => $transactionId,
            'amount' => $request->amount,
            'phone' => $request->phone,
            'payment_method' => $request->payment_method
        ]);

        // TODO: Intégrer ici l'API de paiement (Orange Money, Wave, etc.)
        // Pour l'instant, simulation de la réponse

        return response()->json([
            'success' => true,
            'message' => 'Paiement initié avec succès',
            'data' => [
                'transaction_id' => $transactionId,
                'amount' => $request->amount,
                'phone' => $request->phone,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                ]
            ]
        ], 200);
    }
}
