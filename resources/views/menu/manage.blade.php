@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-stone-900 tracking-tight flex items-center space-x-2">
                <i data-lucide="sliders" class="w-6 h-6 text-emerald-800"></i>
                <span>Price Setter & Service Manager</span>
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Admin tool to update official prices and manage services.
            </p>
        </div>
        <a href="{{ route('menu.index') }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 font-bold text-xs transition shadow-2xs inline-flex items-center space-x-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>View Menu</span>
        </a>
    </div>

    <!-- Add New Service Box -->
    <div class="bg-white rounded-2xl border border-emerald-900/10 p-5 shadow-xs space-y-4">
        <h2 class="text-sm font-extrabold text-emerald-900 uppercase tracking-wider flex items-center space-x-2">
            <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-800"></i>
            <span>Add New Service</span>
        </h2>

        <form action="{{ route('services.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Category</label>
                <select name="category_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 text-stone-800 font-medium">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Service Name</label>
                <input type="text" name="name" required placeholder="e.g. Foot Scrub Deluxe"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 text-stone-800 font-medium placeholder-stone-400">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Price (₱)</label>
                <input type="number" step="10" name="price" required placeholder="350"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 text-emerald-800 font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Duration (Minutes)</label>
                <div class="flex space-x-2">
                    <input type="number" name="estimated_duration_minutes" value="45" required
                        class="w-full px-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 text-stone-800 font-medium">
                    <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition whitespace-nowrap inline-flex items-center space-x-1">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Add Service</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Existing Services Price Editor -->
    <div class="space-y-5">
        @foreach($categories as $cat)
            <div class="bg-white rounded-2xl border border-emerald-900/10 p-5 shadow-xs space-y-3">
                <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider border-b border-stone-100 pb-2">
                    {{ $cat->name }}
                </h3>

                <div class="divide-y divide-stone-100">
                    @foreach($cat->services as $srv)
                        <form action="{{ route('services.update-price', $srv->id) }}" method="POST" class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            @csrf
                            @method('PATCH')

                            <div>
                                <span class="font-extrabold text-stone-900 text-sm block">{{ $srv->name }}</span>
                                <span class="text-xs text-stone-500">Duration: {{ $srv->estimated_duration_minutes }} mins</span>
                            </div>

                            <div class="flex items-center space-x-3 self-end sm:self-center">
                                <div class="flex items-center space-x-1.5">
                                    <span class="text-xs font-bold text-stone-400">₱</span>
                                    <input type="number" step="10" name="price" value="{{ $srv->price }}" required
                                        class="w-24 px-3 py-1.5 text-xs font-bold text-emerald-800 rounded-lg bg-stone-50 border border-stone-200 text-right">
                                </div>

                                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-stone-100 hover:bg-emerald-700 hover:text-white text-stone-800 border border-stone-200 text-xs font-bold transition">
                                    Save
                                </button>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
