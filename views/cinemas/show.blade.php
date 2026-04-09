@extends('layouts.app')

@section('title', 'Cinemas - CINEMAX')

@section('content')
<section>
    <div class="sec-head">
        <h2 class="sec-title">All <span class="acc">Cinemas</span></h2>
        <div class="filters">
            <select class="filter-select" style="background: #0f0f0f; color: white; border: 1px solid var(--border); border-radius: 8px; padding: 0.5rem 1rem;">
                <option>All Locations</option>
                <option>Kuala Lumpur</option>
                <option>Selangor</option>
                <option>Penang</option>
            </select>
        </div>
    </div>
    <div class="cinemas-grid">
        @php
            $allCinemas = [
                ['name' => 'MoovyMoovy Pavilion KL', 'loc' => 'Bukit Bintang, Kuala Lumpur', 'tags' => ['IMAX', 'Dolby', 'VIP']],
                ['name' => 'MoovyMoovy Sunway Pyramid', 'loc' => 'Petaling Jaya, Selangor', 'tags' => ['IMAX', '4DX', 'ScreenX']],
                ['name' => 'MoovyMoovy IOI City Mall', 'loc' => 'Putrajaya', 'tags' => ['Dolby', 'Beanieplex']],
                ['name' => 'MoovyMoovy Mid Valley', 'loc' => 'Kuala Lumpur', 'tags' => ['IMAX', 'Premier']],
                ['name' => 'MoovyMoovy KLCC', 'loc' => 'Kuala Lumpur', 'tags' => ['4DX', 'VIP', 'Dolby']],
                ['name' => 'MoovyMoovy 1 Utama', 'loc' => 'Petaling Jaya, Selangor', 'tags' => ['IMAX', 'ScreenX']],
                ['name' => 'MoovyMoovy Gurney Plaza', 'loc' => 'George Town, Penang', 'tags' => ['IMAX', 'Dolby']],
                ['name' => 'MoovyMoovy Queensbay', 'loc' => 'Bayan Lepas, Penang', 'tags' => ['4DX', 'VIP']],
                ['name' => 'MoovyMoovy Setia City', 'loc' => 'Shah Alam, Selangor', 'tags' => ['Dolby', 'Beanieplex']],
                ['name' => 'MoovyMoovy Aeon Tebrau', 'loc' => 'Johor Bahru, Johor', 'tags' => ['IMAX', 'ScreenX']],
                ['name' => 'MoovyMoovy Ipoh Parade', 'loc' => 'Ipoh, Perak', 'tags' => ['Dolby', 'VIP']],
                ['name' => 'MoovyMoovy Melaka', 'loc' => 'Melaka City', 'tags' => ['IMAX', '4DX']],
            ];
        @endphp
        @foreach($allCinemas as $cinema)
        <div class="cinema-card">
            <div class="cinema-name">{{ $cinema['name'] }}</div>
            <div class="cinema-loc">📍 {{ $cinema['loc'] }}</div>
            <div class="cinema-tags">
                @foreach($cinema['tags'] as $tag)
                <span class="ctag">{{ $tag }}</span>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection