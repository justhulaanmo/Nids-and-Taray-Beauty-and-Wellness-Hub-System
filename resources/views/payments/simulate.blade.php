@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-5">

    <!-- Header -->
    <div class="text-center space-y-1">
        <div class="inline-flex items-center space-x-1.5 px-3 py-1 bg-emerald-100 border border-emerald-200 rounded-full text-emerald-800 text-xs font-bold">
            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-800"></i>
            <span>PayMongo Sandbox Checkout</span>
        </div>
        <h1 class="text-xl font-black text-stone-900">Pay via QR / E-Wallet</h1>
        <p class="text-xs text-stone-500">Scan the QR code using GCash, Maya, or any QRPh banking app.</p>
    </div>

    <!-- Payment Container Card -->
    <div class="bg-white rounded-3xl border border-emerald-900/10 shadow-xs overflow-hidden">
        
        <!-- Summary Strip -->
        <div class="bg-stone-50 p-4 text-stone-900 flex items-center justify-between border-b border-stone-200">
            <div>
                <span class="text-[10px] text-stone-500 block font-bold uppercase tracking-wider">Nids & Taray Hub</span>
                <span class="text-sm font-black block text-stone-900">{{ $transaction->customer_name }}</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] text-stone-500 block font-bold uppercase tracking-wider">Total</span>
                <span class="text-xl font-black text-emerald-800">₱{{ number_format($transaction->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="p-6 space-y-5 text-center">

            <!-- Simulated QR Code Card -->
            <div class="inline-block p-4 bg-white border border-stone-200 rounded-2xl shadow-sm">
                <div class="w-44 h-44 bg-stone-50 rounded-xl flex flex-col items-center justify-center p-3 relative group">
                    <div class="absolute top-1 bg-emerald-700 text-white font-bold text-[9px] px-2 py-0.5 rounded-full tracking-wider">
                        QRPh &bull; GCash &bull; Maya
                    </div>

                    <i data-lucide="qr-code" class="w-24 h-24 text-stone-800 mt-2"></i>

                    <div class="text-[10px] text-stone-600 font-mono mt-1 font-extrabold">
                        {{ $transaction->tracking_number }}
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <span class="text-xs font-bold text-stone-800 block">Select Payment Channel (Test Mode):</span>
                <span class="text-[11px] text-stone-500 block">Choose an e-wallet to simulate instant payment confirmation.</span>
            </div>

            <!-- Simulation Action Buttons -->
            <form action="{{ route('paymongo.confirm-simulation', $transaction->id) }}" method="POST" class="space-y-3">
                @csrf

                <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                    <label class="p-3 border border-stone-200 rounded-2xl cursor-pointer bg-stone-50 hover:border-blue-400 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/70 transition flex flex-col items-center justify-center space-y-1">
                        <input type="radio" name="channel" value="gcash" checked class="sr-only">
                        <i data-lucide="smartphone" class="w-5 h-5 text-blue-600"></i>
                        <span class="block text-stone-800">GCash QR</span>
                    </label>

                    <label class="p-3 border border-stone-200 rounded-2xl cursor-pointer bg-stone-50 hover:border-emerald-400 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/70 transition flex flex-col items-center justify-center space-y-1">
                        <input type="radio" name="channel" value="maya" class="sr-only">
                        <i data-lucide="wallet" class="w-5 h-5 text-emerald-700"></i>
                        <span class="block text-stone-800">Maya QR</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-xs transition inline-flex items-center justify-center space-x-1.5">
                    <span>Confirm Payment (Simulate)</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="pt-3 border-t border-stone-100">
                <a href="{{ route('transactions.show', $transaction->id) }}" class="text-xs font-semibold text-stone-500 hover:text-stone-800 inline-flex items-center space-x-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Back to Order Details</span>
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
