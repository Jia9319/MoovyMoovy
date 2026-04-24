@extends('layouts.app')

@section('title', 'Cinemas - MoovyMoovy')

@section('content')

<div class="cinemas-page">
    <div class="cinemas-hero">
        <h1>Our Cinemas</h1>
        <p>Experience movies in luxury at our premium locations</p>
    </div>

    <div class="cinemas-container">
        <div class="filters-section">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search by cinema name or location...">
            </div>
            <div class="filter-group">
                <select id="cityFilter" class="filter-select">
                    <option value="">All Cities</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="cinemasGrid">
            <div class="cinemas-grid">
                @forelse($cinemas as $cinema)
                <div class="cinema-card" onclick="location.href='{{ route('cinemas.show', $cinema->id) }}'">
                    
                    <div class="cinema-info">
                        <h3 class="cinema-name">{{ $cinema->name }}</h3>
                        <div class="cinema-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $cinema->location }}</span>
                        </div>
                        <div class="cinema-address">
                            {{ $cinema->address }}, {{ $cinema->city }}
                        </div>
                        <div class="cinema-facilities">
                            @php
                                $facilities = is_array($cinema->facilities) ? $cinema->facilities : ($cinema->facilities ? explode(',', $cinema->facilities) : []);
                            @endphp
                            @foreach(array_slice($facilities, 0, 3) as $facility)
                                <span class="facility-tag">{{ trim($facility) }}</span>
                            @endforeach
                        </div>
                        <a href="{{ route('cinemas.show', $cinema->id) }}" class="view-details">
                            View Details →
                        </a>
                    </div>
                </div>
                @empty
                <div class="no-cinemas">
                    <i class="fas fa-building"></i>
                    <h3>No Cinemas Found</h3>
                    <p>Try adjusting your search or filter criteria.</p>
                </div>
                @endforelse
            </div>

            @if(method_exists($cinemas, 'links'))
            <div class="pagination">
                {{ $cinemas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.cinemas-page {
    padding: 120px 5% 60px;
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0010 0%, #120020 100%);
}

.cinemas-hero {
    text-align: center;
    margin-bottom: 3rem;
}

.cinemas-hero h1 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 3rem;
    background: linear-gradient(135deg, #d16aff, #9614d0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.cinemas-hero p {
    color: rgba(255,255,255,0.6);
    font-size: 1.1rem;
}

.cinemas-container {
    max-width: 1400px;
    margin: 0 auto;
}

.filters-section {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    min-width: 300px;
}

.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.5);
}

.search-box input {
    width: 100%;
    padding: 12px 12px 12px 40px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    color: white;
    font-size: 0.9rem;
}

.search-box input:focus {
    outline: none;
    border-color: #d16aff;
}

.filter-select {
    padding: 12px 16px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    color: white;
    cursor: pointer;
}

.cinemas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
}

.cinema-card {
    background: #120020;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s;
    cursor: pointer;
}

.cinema-card:hover {
    transform: translateY(-5px);
    border-color: #d16aff;
    box-shadow: 0 20px 40px rgba(102,0,148,0.3);
}

.cinema-image {
    height: 200px;
    overflow: hidden;
    position: relative;
}

.cinema-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.cinema-card:hover .cinema-image img {
    transform: scale(1.05);
}

.cinema-image-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #1a0033, #660094);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: rgba(255,255,255,0.3);
}

.cinema-info {
    padding: 1.25rem;
}

.cinema-name {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.cinema-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #d16aff;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}

.cinema-address {
    color: rgba(255,255,255,0.5);
    font-size: 0.8rem;
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.cinema-facilities {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.facility-tag {
    background: rgba(209,106,255,0.15);
    color: #d16aff;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
}

.view-details {
    display: inline-block;
    background: linear-gradient(135deg, #9614d0, #660094);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
}

.view-details:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(150,20,208,0.4);
}

.pagination {
    display: flex;
    justify-content: center;
    margin-top: 3rem;
}

.pagination nav[role="navigation"] span,
.pagination nav[role="navigation"] a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    margin: 0 4px;
    background: #120020;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    color: white;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination nav[role="navigation"] a:hover {
    border-color: #d16aff;
    color: #d16aff;
}

.pagination nav[role="navigation"] span[aria-current] {
    background: linear-gradient(135deg, #9614d0, #660094);
    border-color: transparent;
}

.no-cinemas {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem;
    background: #120020;
    border-radius: 20px;
}

.no-cinemas i {
    font-size: 4rem;
    color: rgba(255,255,255,0.3);
    margin-bottom: 1rem;
}

.no-cinemas h3 {
    font-size: 1.3rem;
    margin-bottom: 0.5rem;
    color: white;
}

.no-cinemas p {
    color: rgba(255,255,255,0.5);
}

@media (max-width: 768px) {
    .cinemas-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .cinema-image {
        height: 180px;
    }
    
    .filters-section {
        flex-direction: column;
    }
    
    .search-box {
        min-width: auto;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const cityFilter = document.getElementById('cityFilter');
    const cinemasGrid = document.getElementById('cinemasGrid');
    
    let debounceTimer;
    
    function fetchCinemas() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const search = searchInput.value;
            const city = cityFilter.value;
            
            fetch(`{{ route('cinemas.search') }}?search=${encodeURIComponent(search)}&city=${encodeURIComponent(city)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                cinemasGrid.innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
        }, 500);
    }
    
    searchInput.addEventListener('keyup', fetchCinemas);
    cityFilter.addEventListener('change', fetchCinemas);
});
</script>

@endsection