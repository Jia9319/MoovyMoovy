@extends('layouts.admin')

@section('content')
<div class="mb-12">
    <h1 class="bebas text-7xl tracking-wide uppercase">
        Booking <span class="text-neon">Management</span>
    </h1>
    <div class="w-24 h-1 bg-neon mt-2"></div>
</div>

<div class="bg-[#1a0230] rounded-lg p-4 flex uppercase text-[10px] font-black tracking-[0.2em] text-slate-300">
    <div class="w-1/4 pl-4">User</div>
    <div class="w-1/4 text-center">Movie</div>
    <div class="w-1/4 text-center">Total Price</div>
    <div class="w-1/4 text-right pr-4">Status</div>
</div>

<div class="mt-2 space-y-1">
    @forelse($bookings as $booking)
    <div class="flex items-center p-6 border-b border-purple-900/20 hover:bg-purple-900/10 transition">
        <div class="w-1/4 font-bold uppercase tracking-tight">
            {{ $booking->user->name ?? 'N/A' }}
        </div>
        <div class="w-1/4 text-center text-slate-400 italic text-sm">
            {{ $booking->showtime->movie->title ?? 'N/A' }}
        </div>
        <div class="w-1/4 text-center text-slate-400 text-sm">
            RM {{ number_format($booking->total_price, 2) }}
        </div>
        <div class="w-1/4 text-right">
            <span class="text-neon text-[10px] font-black tracking-widest border border-neon/30 px-4 py-1 rounded">
                {{ strtoupper($booking->status ?? 'confirmed') }}
            </span>
        </div>
    </div>
    @empty
    <div class="p-20 text-center text-slate-600 uppercase tracking-widest text-xs">
        No bookings found.
    </div>
    @endforelse
</div>
@endsection