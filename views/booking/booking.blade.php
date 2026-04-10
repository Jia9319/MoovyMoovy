@extends('layouts.app')

@section('title', 'Choose Cinema & Time - ' . $movie['title'])

@section('content')
<section class="booking-select-page">
    <div class="booking-select-card">
        <div class="booking-select-head">
            <a href="{{ route('home') }}" class="seat-back" title="Back to home">
                <i class="fas fa-arrow-left"></i>
            </a>
            @php
                $durationMinutes = max((int) $movie['duration'], 0);
                $durationText = intdiv($durationMinutes, 60) . 'h ' . ($durationMinutes % 60) . 'm';

                $randomCinemas = collect($cinemas)->random(min(6, count($cinemas)))->values();
                
                $randomTimes = collect($times)->random(min(10, count($times)))->sort()->values();
            @endphp
            <div>
                <h1>{{ $movie['title'] }}</h1>
                <p>{{ $movie['genre'] }} • {{ $durationText }}</p>
            </div>
        </div>

        <form method="GET" action="{{ route('booking.seat') }}" class="booking-form" id="bookingForm">
            <input type="hidden" name="movie_id" value="{{ $movie['id'] }}">
            <input type="hidden" name="title" value="{{ $movie['title'] }}">
            <input type="hidden" name="genre" value="{{ $movie['genre'] }}">
            <input type="hidden" name="duration" value="{{ $movie['duration'] }}">
            
            <input type="hidden" name="hall" id="hallInput" value="{{ $randomCinemas[0]['hall'] }}">
            <input type="hidden" name="format" id="formatInput" value="{{ $types[0]['label'] }}">
            <input type="hidden" name="price" id="priceInput" value="{{ $types[0]['price'] }}">

            <div class="booking-grid">
                <div>
                    <label>Cinema</label>
                    <div class="options" id="cinemaOptions">
                        @foreach($randomCinemas as $index => $cinema)
                            <button
                                type="button"
                                class="option-btn cinema-option {{ $index === 0 ? 'active' : '' }}"
                                data-cinema="{{ $cinema['name'] }}"
                                data-hall="{{ $cinema['hall'] }}"
                            >
                                <strong>{{ $cinema['name'] }}</strong>
                                <span>{{ $cinema['hall'] }}</span>
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="cinema" id="cinemaInput" value="{{ $randomCinemas[0]['name'] }}">
                </div>

                <div>
                    <label>Experience</label>
                    <div class="chips" id="typeOptions">
                        @foreach($types as $idx => $type)
                            <button type="button" class="chip-btn type-option {{ $idx === 0 ? 'active' : '' }}" data-format="{{ $type['label'] }}" data-price="{{ $type['price'] }}">
                                {{ $type['label'] }} • RM {{ number_format((float) $type['price'], 2) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label>Select Date</label>
                    <div class="chips">
                        @foreach($dates as $idx => $date)
                            <label class="chip">
                                <input type="radio" name="date" value="{{ $date }}" {{ $idx === 0 ? 'checked' : '' }}>
                                <span>{{ \Carbon\Carbon::parse($date)->format('D, d M') }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label>Available Showtimes</label>
                    <div class="chips">
                        @foreach($randomTimes as $idx => $time)
                            <label class="chip">
                                <input type="radio" name="time" value="{{ $time }}" {{ $idx === 0 ? 'checked' : '' }}>
                                <span>{{ $time }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="booking-footer">
                <div>
                    <small>Selection Summary</small>
                    <p id="selectedCinemaText">Cinema: {{ $randomCinemas[0]['name'] }} • {{ $randomCinemas[0]['hall'] }}</p>
                    <p id="selectedTypeText" style="color: var(--muted); margin-top: 0.15rem;">Type: {{ $types[0]['label'] }} • RM {{ number_format((float) $types[0]['price'], 2) }}</p>
                </div>
                <button type="submit" class="next-btn">
                    Pick Your Seats
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</section>

<style>
.booking-select-page { min-height: 100vh; padding: 108px 5% 3rem; }
.booking-select-card { max-width: 980px; margin: 0 auto; background: rgba(15, 15, 15, 0.92); border: 1px solid var(--border); border-radius: 18px; padding: 1.5rem; }
.booking-select-head { display: flex; gap: 1rem; align-items: center; margin-bottom: 1.2rem; }
.seat-back { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; color: #fff !important; border-radius: 12px; border: 1px solid var(--border); text-decoration: none; transition: all 0.25s; }
.seat-back:hover { border-color: var(--c1); color: #fff !important; box-shadow: 0 0 0 1px rgba(209, 106, 255, 0.35); }
.booking-select-head h1 { margin: 0; font-size: clamp(1.4rem, 3vw, 2rem); }
.booking-select-head p { margin-top: 0.2rem; color: var(--muted); }
.booking-grid { display: grid; gap: 1.25rem; }
.booking-form label { display: inline-block; margin-bottom: 0.55rem; color: var(--muted); font-size: 0.9rem; }
.options { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.7rem; }
.option-btn { text-align: left; border: 1px solid var(--border); border-radius: 12px; padding: 0.8rem 0.9rem; background: rgba(255, 255, 255, 0.02); color: var(--white); cursor: pointer; }
.option-btn strong { display: block; margin-bottom: 0.2rem; }
.option-btn span { color: var(--muted); font-size: 0.85rem; }
.option-btn.active { border-color: var(--c1); box-shadow: 0 0 0 1px rgba(209, 106, 255, 0.3); }
.chips { display: flex; flex-wrap: wrap; gap: 0.55rem; }
.chip input { display: none; }
.chip-btn { border: 1px solid var(--border); background: transparent; color: var(--muted); padding: 0.5rem 0.75rem; border-radius: 999px; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; }
.chip-btn.active { border-color: var(--c1); color: var(--white); background: rgba(209, 106, 255, 0.2); }
.chip span { display: inline-block; padding: 0.5rem 0.75rem; border-radius: 999px; border: 1px solid var(--border); color: var(--muted); font-size: 0.85rem; cursor: pointer; transition: all 0.2s; }
.chip input:checked + span { border-color: var(--c1); color: var(--white); background: rgba(209, 106, 255, 0.2); }
.booking-footer { margin-top: 1.5rem; border-top: 1px solid var(--border); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
.booking-footer small { color: var(--muted); }
.booking-footer p { margin-top: 0.2rem; }
.next-btn { border: none; border-radius: 12px; padding: 0.8rem 1.1rem; font-weight: 700; color: #fff; background: var(--grad-2); cursor: pointer; }

@media (max-width: 720px) {
    .booking-select-page { padding: 95px 4% 2rem; }
    .booking-footer { flex-direction: column; align-items: flex-start; }
}
</style>

<script>
(() => {
    const cinemaOptions = document.querySelectorAll('.cinema-option');
    const cinemaInput = document.getElementById('cinemaInput');
    const hallInput = document.getElementById('hallInput');
    const formatInput = document.getElementById('formatInput');
    const priceInput = document.getElementById('priceInput');
    const selectedCinemaText = document.getElementById('selectedCinemaText');
    const selectedTypeText = document.getElementById('selectedTypeText');
    const typeOptions = document.querySelectorAll('.type-option');

    let currentCinema = cinemaInput.value;
    let currentHall = hallInput.value;
    let currentFormat = formatInput.value;
    let currentPrice = priceInput.value;

    const refreshSummary = () => {
        selectedCinemaText.textContent = `Cinema: ${currentCinema} • ${currentHall}`;
        selectedTypeText.textContent = `Type: ${currentFormat} • RM ${Number(currentPrice).toFixed(2)}`;
    };

    cinemaOptions.forEach((option) => {
        option.addEventListener('click', () => {
            cinemaOptions.forEach((x) => x.classList.remove('active'));
            option.classList.add('active');

            cinemaInput.value = option.dataset.cinema;
            hallInput.value = option.dataset.hall;
            currentCinema = option.dataset.cinema;
            currentHall = option.dataset.hall;
            refreshSummary();
        });
    });

    typeOptions.forEach((option) => {
        option.addEventListener('click', () => {
            typeOptions.forEach((x) => x.classList.remove('active'));
            option.classList.add('active');

            formatInput.value = option.dataset.format;
            priceInput.value = option.dataset.price;
            currentFormat = option.dataset.format;
            currentPrice = option.dataset.price;
            refreshSummary();
        });
    });

    refreshSummary();
})();
</script>
@endsection