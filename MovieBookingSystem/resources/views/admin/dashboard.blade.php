@extends('layouts.admin')

@section('content')
<div class="mb-12">
    <h1 class="bebas text-7xl tracking-wide uppercase">
        Admin <span class="text-neon">Console</span>
    </h1>
    <div class="w-24 h-1 bg-neon mt-2"></div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
    <div class="card-stat p-10 relative overflow-hidden">
        <h3 class="uppercase text-xs font-black tracking-widest text-slate-400 mb-4">Database Inventory</h3>
        <div class="flex items-baseline gap-4">
            <span class="text-8xl font-black">{{ count($movies) }}</span>
            <span class="text-slate-500 font-bold uppercase tracking-tighter">Movies</span>
        </div>
    </div>

    <div class="card-stat p-10 border-neon">
        <h3 class="uppercase text-xs font-black tracking-widest text-slate-400 mb-4">Reported Reviews</h3>
        <div class="flex items-baseline gap-4">
            <span class="text-8xl font-black text-neon">{{ $reportCount }}</span>
        </div>
    </div>
</div>

<div class="bg-[#1a0230] rounded-lg p-4 flex uppercase text-[10px] font-black tracking-[0.2em] text-slate-300">
    <div class="w-1/3 pl-4">Movie Title</div>
    <div class="w-1/3 text-center">Genre</div>
    <div class="w-1/3 text-right pr-4">Actions</div>
</div>

<div class="mt-2 space-y-1">
    @forelse($movies as $movie)
    <div class="flex items-center p-6 border-b border-purple-900/20 hover:bg-purple-900/10 transition">
        <div class="w-1/3 font-bold uppercase tracking-tight">{{ $movie->title }}</div>
        <div class="w-1/3 text-center text-slate-400 italic text-sm">{{ $movie->genre }}</div>
        <div class="w-1/3 text-right">
            <button class="text-neon text-[10px] font-black tracking-widest border border-neon/30 px-4 py-1 rounded hover:bg-neon hover:text-black transition">
                EDIT
            </button>
        </div>
    </div>
    @empty
    <div class="p-20 text-center text-slate-600 uppercase tracking-widest text-xs">
        No records found in `movies` table.
    </div>
    @endforelse
</div>
@endsection