@extends('layouts.admin')

@section('content')
<div class="mb-12">
    <h1 class="bebas text-7xl tracking-wide uppercase">
        System <span class="text-neon">Reports</span>
    </h1>
    <div class="w-24 h-1 bg-neon mt-2"></div>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
    <div class="card-stat p-8 text-center">
        <h3 class="uppercase text-[10px] font-black tracking-widest text-slate-400 mb-3">Total Movies</h3>
        <span class="text-6xl font-black text-white">{{ $totalMovies }}</span>
    </div>
    <div class="card-stat p-8 text-center">
        <h3 class="uppercase text-[10px] font-black tracking-widest text-slate-400 mb-3">Total Bookings</h3>
        <span class="text-6xl font-black text-white">{{ $totalBookings }}</span>
    </div>
    <div class="card-stat p-8 text-center">
        <h3 class="uppercase text-[10px] font-black tracking-widest text-slate-400 mb-3">Total Users</h3>
        <span class="text-6xl font-black text-white">{{ $totalUsers }}</span>
    </div>
    <div class="card-stat p-8 text-center">
        <h3 class="uppercase text-[10px] font-black tracking-widest text-slate-400 mb-3">Total Revenue</h3>
        <span class="text-4xl font-black text-neon">RM {{ number_format($totalRevenue, 2) }}</span>
    </div>
</div>

{{-- Top Movies --}}
<div class="mb-6">
    <h2 class="bebas text-3xl tracking-wide text-neon mb-4">Top Movies by Bookings</h2>
</div>

<div class="bg-[#1a0230] rounded-lg p-4 flex uppercase text-[10px] font-black tracking-[0.2em] text-slate-300">
    <div class="w-1/2 pl-4">Movie Title</div>
    <div class="w-1/4 text-center">Genre</div>
    <div class="w-1/4 text-right pr-4">Total Bookings</div>
</div>

<div class="mt-2 space-y-1">
    @forelse($topMovies as $movie)
    <div class="flex items-center p-6 border-b border-purple-900/20 hover:bg-purple-900/10 transition">
        <div class="w-1/2 font-bold uppercase tracking-tight">{{ $movie->title }}</div>
        <div class="w-1/4 text-center text-slate-400 italic text-sm">{{ $movie->genre }}</div>
        <div class="w-1/4 text-right text-neon font-black">{{ $movie->bookings_count ?? 0 }}</div>
    </div>
    @empty
    <div class="p-20 text-center text-slate-600 uppercase tracking-widest text-xs">
        No data available yet.
    </div>
    @endforelse
</div>
@endsection