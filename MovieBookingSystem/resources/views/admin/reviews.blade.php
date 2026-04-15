@extends('layouts.admin')

@section('content')
<div class="mb-12">
    <h1 class="bebas text-7xl tracking-wide uppercase">
        Review <span class="text-neon">Moderation</span>
    </h1>
    <div class="w-24 h-1 bg-neon mt-2"></div>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-900/30 border border-green-500/30 text-green-400 rounded uppercase text-xs tracking-widest">
        {{ session('success') }}
    </div>
@endif

<div class="bg-[#1a0230] rounded-lg p-4 flex uppercase text-[10px] font-black tracking-[0.2em] text-slate-300">
    <div class="w-1/4 pl-4">User</div>
    <div class="w-1/4 text-center">Movie</div>
    <div class="w-1/4 text-center">Rating</div>
    <div class="w-1/4 text-right pr-4">Actions</div>
</div>

<div class="mt-2 space-y-1">
    @forelse($reviews as $review)
<div class="flex items-center p-6 border-b border-purple-900/20 hover:bg-purple-900/10 transition">
    <div class="w-1/4 font-bold uppercase tracking-tight">
        {{ $review->user_name ?? 'N/A' }}
    </div>
    <div class="w-1/4 text-center text-slate-400 italic text-sm">
        {{ $review->movie_title ?? 'N/A' }}
    </div>
    <div class="w-1/4 text-center text-slate-400 text-sm">
        {{ $review->rating }}/10
    </div>
    <div class="w-1/4 text-right">
        <form action="/admin/reviews/{{ $review->id }}" method="POST"
            onsubmit="return confirm('Delete this review?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                class="text-red-400 text-[10px] font-black tracking-widest border border-red-400/30 px-4 py-1 rounded hover:bg-red-400 hover:text-black transition">
                DELETE
            </button>
        </form>
    </div>
</div>
@empty
    <div class="p-20 text-center text-slate-600 uppercase tracking-widest text-xs">
        No reviews found.
    </div>
    @endforelse
</div>
@endsection