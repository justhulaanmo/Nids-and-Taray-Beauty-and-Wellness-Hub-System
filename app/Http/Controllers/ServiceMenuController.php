<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceMenuController extends Controller
{
    /**
     * Digital Menu to show to customers on phone
     */
    public function index()
    {
        $categories = ServiceCategory::where('is_active', true)
            ->with(['services' => function ($q) {
                $q->where('is_active', true)->orderBy('price', 'asc');
            }])
            ->orderBy('display_order')
            ->get();

        return view('menu.index', compact('categories'));
    }

    /**
     * Admin Service & Price Setter
     */
    public function manage()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('menu.index')->with('info', 'Only administrators can manage service prices.');
        }

        $categories = ServiceCategory::with('services')->orderBy('display_order')->get();

        return view('menu.manage', compact('categories'));
    }

    /**
     * Quick Update Price
     */
    public function updatePrice(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'price' => 'required|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:5',
        ]);

        $service = Service::findOrFail($id);
        $service->update([
            'price' => $request->price,
            'estimated_duration_minutes' => $request->estimated_duration_minutes ?? $service->estimated_duration_minutes,
        ]);

        return back()->with('success', "Price for '{$service->name}' updated to ₱" . number_format($service->price, 2) . ".");
    }

    /**
     * Store new service
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'estimated_duration_minutes' => 'required|integer|min:5',
        ]);

        Service::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'estimated_duration_minutes' => $request->estimated_duration_minutes,
            'allow_home_service' => true,
            'is_active' => true,
        ]);

        return back()->with('success', "New service added: {$request->name}.");
    }
}
