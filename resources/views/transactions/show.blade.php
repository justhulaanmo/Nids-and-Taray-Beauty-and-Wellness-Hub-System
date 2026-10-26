@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900 bg-white border border-stone-200 px-3.5 py-2 rounded-xl transition shadow-xs inline-flex items-center space-x-1.5">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Orders</span>
        </a>

        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-3.5 py-2 bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 rounded-xl text-xs font-bold transition shadow-xs inline-flex items-center space-x-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Receipt</span>
            </button>
        </div>
    </div>

    <!-- Receipt Card -->
    <div class="bg-white rounded-3xl border border-emerald-900/10 shadow-xs overflow-hidden text-stone-800" id="printableReceipt">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-800 to-teal-800 p-6 sm:p-7 relative text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-white/20 text-emerald-100 text-[10px] font-bold uppercase tracking-wider mb-1.5 border border-white/20">
                        Official Service Receipt
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">Nids & Taray</h1>
                    <p class="text-[11px] font-semibold text-emerald-100/90 tracking-widest uppercase">Beauty & Wellness Hub</p>
                </div>
                <div class="sm:text-right">
                    <span class="block text-[10px] text-emerald-100/80 font-bold uppercase">Tracking #</span>
                    <span class="font-mono text-lg font-black text-white">{{ $transaction->tracking_number }}</span>
                    <span class="block text-[10px] text-emerald-100/80 mt-0.5">{{ $transaction->created_at->format('M d, Y &bull; h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-5">

            <!-- Customer & Type Info -->
            <div class="grid grid-cols-2 gap-3 pb-4 border-b border-stone-100 text-xs">
                <div>
                    <span class="text-stone-400 uppercase font-bold tracking-wider block text-[10px] mb-0.5">Client Name</span>
                    <span class="text-base font-black text-stone-900 block">{{ $transaction->customer_name }}</span>
                    @if($transaction->customer_phone)
                        <span class="text-stone-500 text-xs block mt-0.5 inline-flex items-center space-x-1">
                            <i data-lucide="phone" class="w-3 h-3 text-stone-400"></i>
                            <span>{{ $transaction->customer_phone }}</span>
                        </span>
                    @endif
                </div>

                <div class="text-right">
                    <span class="text-stone-400 uppercase font-bold tracking-wider block text-[10px] mb-0.5">Location</span>
                    @if($transaction->service_type === 'home_service')
                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                            <i data-lucide="home" class="w-3 h-3"></i>
                            <span>Home Service</span>
                        </span>
                        @if($transaction->home_service_address)
                            <span class="text-stone-600 text-[11px] block mt-1 truncate inline-flex items-center space-x-1 justify-end">
                                <i data-lucide="map-pin" class="w-3 h-3 text-stone-400"></i>
                                <span>{{ $transaction->home_service_address }}</span>
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                            <i data-lucide="store" class="w-3 h-3"></i>
                            <span>In-Shop (Salon)</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div>
                <h2 class="text-[11px] font-black uppercase tracking-wider text-stone-400 mb-2">Services & Assigned Staff</h2>
                <div class="border border-stone-200 rounded-2xl overflow-hidden bg-white">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-stone-50 text-stone-500 font-bold uppercase tracking-wider text-[11px] border-b border-stone-200">
                            <tr>
                                <th class="py-2.5 px-3">Service</th>
                                <th class="py-2.5 px-3">Staff</th>
                                <th class="py-2.5 px-3 text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($transaction->items as $item)
                                <tr>
                                    <td class="py-3 px-3 font-bold text-stone-900">
                                        {{ $item->service->name ?? 'Service' }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-extrabold text-emerald-800">{{ $item->staff->nickname ?? 'Attendant' }}</span>
                                        @if(Auth::user()->role === 'admin' || (Auth::user()->staffProfile && Auth::user()->staffProfile->id === $item->staff_id))
                                            <span class="text-[10px] text-stone-400 block">{{ $item->commission_rate }}% share (₱{{ number_format($item->staff_commission_amount, 2) }})</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right font-black text-emerald-800">
                                        ₱{{ number_format($item->price, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Calculations -->
            <div class="bg-stone-50 rounded-2xl p-4 space-y-1.5 border border-stone-200 text-xs">
                <div class="flex justify-between text-stone-600">
                    <span>Subtotal:</span>
                    <span class="font-bold text-stone-900">₱{{ number_format($transaction->subtotal, 2) }}</span>
                </div>
                @if($transaction->home_service_fee > 0)
                    <div class="flex justify-between text-purple-700">
                        <span>Home Service Fee:</span>
                        <span class="font-bold">₱{{ number_format($transaction->home_service_fee, 2) }}</span>
                    </div>
                @endif
                <div class="pt-2 border-t border-stone-200 flex justify-between items-baseline">
                    <span class="text-xs font-black uppercase text-stone-700">Total Amount:</span>
                    <span class="text-2xl font-black text-emerald-800">₱{{ number_format($transaction->total_amount, 2) }}</span>
                </div>
            </div>

            <!-- Customer Satisfaction SOP Rating Badge -->
            <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-2xl flex items-center justify-between text-xs">
                <div class="flex items-center space-x-2">
                    <i data-lucide="star" class="w-4 h-4 text-amber-500 fill-amber-400"></i>
                    <span class="font-bold text-amber-900">Customer Satisfaction:</span>
                </div>
                <div class="flex items-center space-x-1 font-black text-amber-800">
                    @for($i = 0; $i < ($transaction->satisfaction_rating ?? 5); $i++)
                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                    @endfor
                    <span class="ml-1 text-xs">({{ ($transaction->satisfaction_rating ?? 5) == 5 ? 'Excellent' : 'Good' }})</span>
                </div>
            </div>

            <!-- Payment Status & Mode of Payment Switcher Box -->
            <div class="p-4 sm:p-5 rounded-2xl border {{ $transaction->payment_status === 'paid' ? 'bg-emerald-50 border-emerald-200 text-emerald-950' : 'bg-amber-50 border-amber-200 text-amber-950' }} space-y-3.5 text-xs">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="font-black uppercase tracking-wider text-xs flex items-center space-x-1.5 {{ $transaction->payment_status === 'paid' ? 'text-emerald-800' : 'text-amber-800' }}">
                            @if($transaction->payment_status === 'paid')
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-700"></i>
                                <span>PAID &bull; RECEIPT CONFIRMED</span>
                            @else
                                <i data-lucide="clock" class="w-4 h-4 text-amber-700"></i>
                                <span>UNPAID &bull; PENDING PAYMENT</span>
                            @endif
                        </div>
                        <div class="text-[11px] text-stone-600 mt-1 flex flex-wrap items-center gap-1.5">
                            <span>Payment Method:</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-extrabold text-[11px] {{ $transaction->payment_method === 'paymongo_qr' ? 'bg-indigo-100 text-indigo-900 border border-indigo-200' : 'bg-amber-100 text-amber-900 border border-amber-200' }}">
                                <i data-lucide="{{ $transaction->payment_method === 'paymongo_qr' ? 'qr-code' : 'banknote' }}" class="w-3 h-3 mr-1"></i>
                                {{ $transaction->payment_method === 'paymongo_qr' ? 'PayMongo QR (GCash / Maya / QRPh)' : 'Cash Payment' }}
                            </span>
                            @if($transaction->payment_reference)
                                <span>&bull; Ref: <code class="font-mono bg-white px-1.5 py-0.5 rounded border border-stone-200 text-stone-800 font-bold">{{ $transaction->payment_reference }}</code></span>
                            @endif
                        </div>
                    </div>

                    <!-- Mode of Payment Switcher (Customer Changed Mind) -->
                    <div class="no-print flex items-center space-x-1.5">
                        <form action="{{ route('transactions.update-payment-method', $transaction->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            @if($transaction->payment_method === 'cash')
                                <input type="hidden" name="payment_method" value="paymongo_qr">
                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-indigo-50 text-indigo-700 hover:text-indigo-900 border border-indigo-200 font-bold text-[11px] shadow-2xs transition inline-flex items-center space-x-1">
                                    <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                    <span>Switch to QR (GCash/Maya)</span>
                                </button>
                            @else
                                <input type="hidden" name="payment_method" value="cash">
                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-amber-50 text-amber-800 hover:text-amber-950 border border-amber-200 font-bold text-[11px] shadow-2xs transition inline-flex items-center space-x-1">
                                    <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                    <span>Switch to Cash</span>
                                </button>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Payment Actions (Unpaid) -->
                @if($transaction->payment_status !== 'paid')
                    <div class="no-print pt-2.5 border-t border-amber-200/80 flex flex-wrap items-center justify-between gap-2.5">
                        <div class="flex items-center space-x-2">
                            @if($transaction->payment_method === 'paymongo_qr')
                                <a href="{{ $transaction->paymongo_checkout_url ?? route('paymongo.simulate', $transaction->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-xs transition inline-flex items-center space-x-1.5">
                                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                                    <span>Open QR Code / Checkout</span>
                                </a>
                            @endif
                        </div>

                        <!-- Mark as Paid Buttons (Choose Cash or QR directly) -->
                        <div class="flex items-center space-x-2 ml-auto">
                            <form action="{{ route('transactions.mark-paid', $transaction->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="payment_method" value="cash">
                                <button type="submit" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-xs transition inline-flex items-center space-x-1.5">
                                    <i data-lucide="banknote" class="w-3.5 h-3.5"></i>
                                    <span>Mark Paid (Cash)</span>
                                </button>
                            </form>

                            <form action="{{ route('transactions.mark-paid', $transaction->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="payment_method" value="paymongo_qr">
                                <button type="submit" class="px-3.5 py-2 bg-indigo-700 hover:bg-indigo-800 text-white font-bold rounded-xl text-xs shadow-xs transition inline-flex items-center space-x-1.5">
                                    <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                                    <span>Mark Paid (QR)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- SOP Step: Clean & Sanitize Equipment / Release Staff -->
            @if($transaction->status !== 'completed')
                <div class="no-print p-4 bg-stone-50 border border-stone-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="font-black text-stone-900 block flex items-center space-x-1.5">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-800"></i>
                            <span>Clean & Sanitize Equipment</span>
                        </span>
                        <span class="text-stone-500 text-[11px]">Once tools are sanitized, click to return staff to the active queue.</span>
                    </div>
                    <form action="{{ route('transactions.mark-completed', $transaction->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-xs transition whitespace-nowrap inline-flex items-center space-x-1.5">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Complete & Return to Queue</span>
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>

</div>

<style>
@media print {
    .no-print, header, footer, nav { display: none !important; }
    body { background: white !important; color: black !important; }
    #printableReceipt { border: none !important; box-shadow: none !important; color: black !important; background: white !important; }
}
</style>
@endsection
