<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Only administrators can record operational expenses.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'expense_date' => 'required|date',
        ]);

        $validated['created_by'] = $user->id;

        Expense::create($validated);

        return back()->with('success', "Expense '{$validated['description']}' of ₱" . number_format($validated['amount'], 2) . " successfully added!");
    }

    public function destroy(int|string $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $expense = Expense::findOrFail($id);
        $desc = $expense->description;
        $amount = $expense->amount;
        $expense->delete();

        return back()->with('success', "Expense '{$desc}' (₱" . number_format($amount, 2) . ") deleted.");
    }
}
