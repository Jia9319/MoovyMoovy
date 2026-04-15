@extends('layouts.admin')

@section('content')
<div class="mb-12">
    <h1 class="bebas text-7xl tracking-wide uppercase">
        Reported <span class="text-neon">Reviews</span>
    </h1>
    <div class="w-24 h-1 bg-neon mt-2"></div>
</div>

<div class="bg-[#1a0230] rounded-lg p-4 flex uppercase text-[10px] font-black tracking-[0.2em] text-slate-300">
    <div class="w-1/4 pl-4">Reported By</div>
    <div class="w-1/4 text-center">Review</div>
    <div class="w-1/4 text-center">Reason</div>
    <div class="w-1/4 text-right pr-4">Actions</div>
</div>

<div class="mt-2 space-y-1">
    @forelse($reports as $report)
<div class="flex items-center p-6 border-b border-purple-900/20 hover:bg-purple-900/10 transition">
    <div class="w-1/4 font-bold uppercase tracking-tight">
        {{ $report->reporter_name ?? 'Unknown' }}
    </div>
    <div class="w-1/4 text-center text-slate-400 text-sm">
        {{ \Illuminate\Support\Str::limit($report->content ?? 'N/A', 40) }}
    </div>
    <div class="w-1/4 text-center text-slate-400 text-sm">
        {{ $report->reason ?? 'No reason given' }}
    </div>
    <div class="w-1/4 text-right">
        <form method="POST" action="/admin/reviews/{{ $report->review_id }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                class="text-red-400 text-[10px] font-black tracking-widest border border-red-400/30 px-4 py-1 rounded hover:bg-red-500 hover:text-white transition">
                DELETE
            </button>
        </form>
    </div>
</div>
@empty
<div class="p-20 text-center text-slate-600 uppercase tracking-widest text-xs">
    No reported reviews.
</div>
@endforelse
</div>
@endsection