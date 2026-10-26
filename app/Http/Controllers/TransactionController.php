<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\StaffProfile;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $staffProfile = null;
        $isStaff = false;

        if ($user && $user->role !== 'admin') {
            $isStaff = true;
            $staffProfile = StaffProfile::where('user_id', $user->id)->first();
        }

        $query = Transaction::with(['items.service', 'items.staff', 'creator']);

        // Strict Role Isolation: If employee is logged in, ONLY show transactions where they are an assigned attendant
        if ($isStaff && $staffProfile) {
            $query->whereHas('items', function ($q) use ($staffProfile) {
                $q->where('staff_id', $staffProfile->id);
            });
        } elseif (!$isStaff && $request->filled('staff_id')) {
            // Admin filtering by specific staff
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('staff_id', $request->staff_id);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        $allStaff = (!$isStaff) ? StaffProfile::where('is_active', true)->orderBy('name')->get() : collect();

        return view('transactions.index', compact('transactions', 'isStaff', 'staffProfile', 'allStaff'));
    }

    public function create()
    {
        $user = Auth::user();
        $isStaff = $user && $user->role === 'staff';
        $myStaffProfile = StaffProfile::where('user_id', Auth::id())->first();

        // Base categories with active services
        $categoriesQuery = ServiceCategory::where('is_active', true)
            ->with(['services' => function ($q) {
                $q->where('is_active', true)->orderBy('price');
            }])
            ->orderBy('display_order');

        $categories = $categoriesQuery->get();

        // If employee is logged in, only show services for their specialization (All-Around sees all)
        if ($isStaff && $myStaffProfile && $myStaffProfile->specialization !== 'all_around') {
            $categories = $categories->filter(function ($cat) use ($myStaffProfile) {
                return $myStaffProfile->isQualifiedForCategory($cat);
            });
        }

        // 1. Strictly prioritize Available staff sorted by fair queue position (Turn #1, #2, etc.)
        $availableStaff = StaffProfile::where('is_active', true)
            ->where('status', 'available')
            ->orderBy('queue_position', 'asc')
            ->get();

        // 2. Staff currently busy, on home service, on break, or off duty
        $busyStaff = StaffProfile::where('is_active', true)
            ->where('status', '!=', 'available')
            ->orderByRaw("CASE status WHEN 'serving_in_shop' THEN 1 WHEN 'on_home_service' THEN 2 WHEN 'on_break' THEN 3 ELSE 4 END")
            ->orderBy('queue_position', 'asc')
            ->get();

        $staffList = $availableStaff->concat($busyStaff);

        return view('transactions.create', compact('categories', 'staffList', 'availableStaff', 'busyStaff', 'myStaffProfile', 'isStaff'));
    }

    public function store(Request $request, PayMongoService $payMongo)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'service_type' => 'required|in:in_shop,home_service',
            'home_service_address' => 'required_if:service_type,home_service|nullable|string|max:500',
            'home_service_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,paymongo_qr',
            'satisfaction_rating' => 'nullable|integer|between:1,5',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.staff_id' => 'required|exists:staff_profiles,id',
        ], [
            'services.required' => 'Please select at least one service.',
            'home_service_address.required_if' => 'The address is required for home service.',
        ]);

        return DB::transaction(function () use ($request, $payMongo) {
            $serviceType = $request->input('service_type');
            $homeFee = ($serviceType === 'home_service') ? (float) ($request->input('home_service_fee') ?? 100.00) : 0.00;

            $subtotal = 0;
            $itemsData = [];

            foreach ($request->input('services') as $srvInput) {
                $service = Service::with('category')->findOrFail($srvInput['service_id']);
                $staff = StaffProfile::findOrFail($srvInput['staff_id']);

                // Strict Role Validation: Staff must be qualified to perform this service
                if (!$staff->canPerformService($service)) {
                    $staffRole = ucfirst(str_replace('_', ' ', $staff->specialization));
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'services' => ["Cannot assign '{$service->name}' to {$staff->nickname} ({$staffRole}). Attendants can only perform services matching their specialization."]
                    ]);
                }

                $price = (float) $service->price;
                $commRate = (float) $staff->commission_rate;
                $commAmount = round(($price * $commRate) / 100, 2);

                $subtotal += $price;

                $itemsData[] = [
                    'service' => $service,
                    'staff' => $staff,
                    'price' => $price,
                    'commission_rate' => $commRate,
                    'commission_amount' => $commAmount,
                ];

                // Automatically update staff status if they were available
                if ($serviceType === 'home_service') {
                    $staff->update([
                        'status' => 'on_home_service',
                        'last_status_change' => now(),
                    ]);
                } else {
                    $staff->update([
                        'status' => 'serving_in_shop',
                        'last_status_change' => now(),
                    ]);
                }
            }

            $total = $subtotal + $homeFee;
            $trackingNumber = Transaction::generateTrackingNumber();

            $transaction = Transaction::create([
                'tracking_number' => $trackingNumber,
                'customer_name' => $request->input('customer_name'),
                'customer_phone' => $request->input('customer_phone'),
                'service_type' => $serviceType,
                'home_service_address' => $request->input('home_service_address'),
                'home_service_fee' => $homeFee,
                'subtotal' => $subtotal,
                'discount' => 0.00,
                'total_amount' => $total,
                'payment_method' => $request->input('payment_method'),
                'payment_status' => 'pending',
                'status' => 'in_progress',
                'notes' => $request->input('notes'),
                'satisfaction_rating' => $request->input('satisfaction_rating', 5),
                'created_by' => Auth::id(),
            ]);

            foreach ($itemsData as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'service_id' => $item['service']->id,
                    'staff_id' => $item['staff']->id,
                    'price' => $item['price'],
                    'commission_rate' => $item['commission_rate'],
                    'staff_commission_amount' => $item['commission_amount'],
                    'status' => 'in_progress',
                ]);
            }

            // If PayMongo selected, initiate checkout session
            if ($request->input('payment_method') === 'paymongo_qr') {
                $payResult = $payMongo->createCheckoutSession([
                    'transaction_id' => $transaction->id,
                    'tracking_number' => $trackingNumber,
                    'items' => collect($itemsData)->map(fn($i) => ['name' => $i['service']->name, 'price' => $i['price']])->toArray(),
                    'home_service_fee' => $homeFee,
                ]);

                if ($payResult['success']) {
                    $transaction->update([
                        'paymongo_checkout_id' => $payResult['checkout_id'],
                        'paymongo_checkout_url' => $payResult['checkout_url'],
                    ]);

                    return redirect($payResult['checkout_url']);
                }
            }

            return redirect()->route('transactions.show', $transaction->id)
                ->with('success', "Transaction #{$trackingNumber} for {$transaction->customer_name} recorded successfully!");
        });
    }

    public function show(int|string $id)
    {
        $user = Auth::user();
        $transaction = Transaction::with(['items.service', 'items.staff', 'creator'])->findOrFail($id);

        if ($user && $user->role !== 'admin') {
            $staffProfile = StaffProfile::where('user_id', $user->id)->first();
            $isAssigned = $staffProfile && $transaction->items()->where('staff_id', $staffProfile->id)->exists();
            if ($staffProfile && !$isAssigned) {
                abort(403, 'Unauthorized. You can only view transactions where you were an assigned attendant.');
            }
        }

        return view('transactions.show', compact('transaction'));
    }

    public function markPaid(Request $request, int|string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $method = $request->input('payment_method', $transaction->payment_method);
        $ref = $request->input('payment_reference') ?? (($method === 'cash') ? 'CASH-' . strtoupper(uniqid()) : 'QR-' . strtoupper(uniqid()));

        $transaction->update([
            'payment_status' => 'paid',
            'payment_method' => $method,
            'payment_reference' => $ref,
            'completed_at' => now(),
        ]);

        $label = ($method === 'paymongo_qr') ? 'PayMongo QR' : 'Cash';
        return back()->with('success', "Transaction #{$transaction->tracking_number} marked as Paid via {$label}!");
    }

    public function updatePaymentMethod(Request $request, int|string $id, PayMongoService $payMongo)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,paymongo_qr',
        ]);

        $transaction = Transaction::with(['items.service'])->findOrFail($id);
        $newMethod = $request->input('payment_method');

        $checkoutUrl = $transaction->paymongo_checkout_url;
        $checkoutId = $transaction->paymongo_checkout_id;

        if ($newMethod === 'paymongo_qr' && empty($checkoutUrl)) {
            $checkoutItems = collect($transaction->items)->map(fn($i) => [
                'name' => $i->service->name ?? 'Service',
                'price' => $i->price
            ])->all();

            $payResult = $payMongo->createCheckoutSession([
                'transaction_id' => $transaction->id,
                'tracking_number' => $transaction->tracking_number,
                'items' => $checkoutItems,
                'home_service_fee' => $transaction->home_service_fee,
            ]);

            if ($payResult['success']) {
                $checkoutId = $payResult['checkout_id'];
                $checkoutUrl = $payResult['checkout_url'];
            }
        }

        $transaction->update([
            'payment_method' => $newMethod,
            'paymongo_checkout_id' => $checkoutId,
            'paymongo_checkout_url' => $checkoutUrl,
        ]);

        $methodLabel = ($newMethod === 'paymongo_qr') ? 'PayMongo QR (GCash / Maya)' : 'Cash';

        return back()->with('success', "Payment method updated to {$methodLabel}.");
    }

    public function markCompleted(int|string $id)
    {
        $transaction = Transaction::with('items.staff')->findOrFail($id);

        $transaction->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Release staff back to available queue (1-query batch optimization)
        $maxQueue = StaffProfile::where('status', 'available')->max('queue_position') ?? 0;
        $requeuedStaffIds = [];

        foreach ($transaction->items as $item) {
            if ($item->staff && !in_array($item->staff->id, $requeuedStaffIds)) {
                $maxQueue++;
                $item->staff->update([
                    'status' => 'available',
                    'queue_position' => $maxQueue,
                    'last_status_change' => now(),
                ]);
                $requeuedStaffIds[] = $item->staff->id;
            }
        }

        return back()->with('success', "Service completed! Staff returned to the queue.");
    }
}
