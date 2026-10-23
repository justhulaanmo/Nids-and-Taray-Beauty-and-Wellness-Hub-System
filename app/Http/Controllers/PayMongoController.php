<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class PayMongoController extends Controller
{
    /**
     * Show Simulated QR / Checkout Page (Perfect for testing & defense presentation)
     */
    public function simulate($transaction_id)
    {
        $transaction = Transaction::with(['items.service', 'items.staff'])->findOrFail($transaction_id);

        return view('payments.simulate', compact('transaction'));
    }

    /**
     * Confirm simulated payment
     */
    public function confirmSimulation(Request $request, $transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        
        $request->validate([
            'channel' => 'required|in:gcash,maya,qrph',
            'reference' => 'nullable|string',
        ]);

        $channel = strtoupper($request->input('channel'));
        $ref = $request->input('reference') ?? 'PM-' . $channel . '-' . rand(100000, 999999);

        $transaction->update([
            'payment_status' => 'paid',
            'payment_method' => 'paymongo_qr',
            'payment_reference' => $ref,
            'completed_at' => now(),
        ]);

        return redirect()->route('transactions.show', $transaction->id)
            ->with('success', "Payment received successfully via PayMongo ({$channel})! Reference: {$ref}");
    }
}
