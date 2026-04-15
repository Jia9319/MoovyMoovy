@extends('layouts.admin')

@section('content')
<div class="mb-12">
    <h1 class="bebas text-7xl tracking-wide uppercase">
        Movies <span class="text-neon">Management</span>
    </h1>
    <div class="w-24 h-1 bg-neon mt-2"></div>
</div>

<div class="bg-[#1a0230] rounded-lg p-4 flex uppercase text-[10px] font-black tracking-[0.2em] text-slate-300">
    <div class="w-1/4 pl-4">Movie Title</div>
    <div class="w-1/4 text-center">Genre</div>
    <div class="w-1/4 text-center">Release Date</div>
    <div class="w-1/4 text-right pr-4">Actions</div>
</div>

<div class="mt-2 space-y-1">
    @forelse($movies as $movie)
    <div class="flex items-center p-6 border-b border-purple-900/20 hover:bg-purple-900/10 transition">
        <div class="w-1/4 font-bold uppercase tracking-tight">{{ $movie->title }}</div>
        <div class="w-1/4 text-center text-slate-400 italic text-sm">{{ $movie->genre }}</div>
        <div class="w-1/4 text-center text-slate-400 text-sm">{{ $movie->release_date }}</div>
        <div class="w-1/4 text-right">
    <a href="/movies/{{ $movie->id }}/edit" 
       class="text-neon text-[10px] font-black tracking-widest border border-neon/30 px-4 py-1 rounded hover:bg-neon hover:text-black transition">
        EDIT
    </a>
</div>
    </div>
    @empty
    <div class="p-20 text-center text-slate-600 uppercase tracking-widest text-xs">
        No movies found.
    </div>
    @endforelse
</div>
@endsection