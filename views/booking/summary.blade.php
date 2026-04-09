@extends('layouts.app')

@section('title', 'Booking Summary')

@section('content')
<section class="booking-summary-page">
    <div class="booking-summary-card">
        <h1>Booking Confirmed</h1>
        <p class="sub">Your seats are reserved in this demo flow (no database required).</p>

        <div class="summary-grid">
            <div>
                <span>Movie</span>
                <strong>{{ $movieTitle }}</strong>
            </div>
            <div>
                <span>Cinema</span>
                <strong>{{ $cinema }} • {{ $hall }} • {{ $format }}</strong>
            </div>
            <div>
                <span>Date & Time</span>
                <strong>{{ $date->format('D, d M Y') }} {{ $time }}</strong>
            </div>
            <div>
                <span>Seats</span>
                <strong>{{ $seats->join(', ') }}</strong>
            </div>
        </div>

        <div class="total-row">
            <span>Total Paid</span>
            <strong>RM {{ number_format($total, 2) }}</strong>
        </div>

        <a href="{{ route('movies.index') }}" class="summary-btn">Back to Movies</a>
    </div>
</section>

<style>
.booking-summary-page {
    min-height: 100vh;
    padding: 108px 5% 3rem;
}

.booking-summary-card {
    max-width: 740px;
    margin: 0 auto;
    border: 1px solid var(--border);
    border-radius: 18px;
    background: rgba(15, 15, 15, 0.92);
    padding: 1.5rem;
}

.booking-summary-card h1 {
    font-size: clamp(1.5rem, 3vw, 2.3rem);
    margin-bottom: 0.35rem;
}

.booking-summary-card .sub {
    color: var(--muted);
    margin-bottom: 1.2rem;
}

.summary-grid {
    display: grid;
    gap: 0.85rem;
}

.summary-grid span {
    color: var(--muted);
    font-size: 0.85rem;
}

.summary-grid strong {
    display: block;
    margin-top: 0.2rem;
}

.total-row {
    margin-top: 1.2rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-row span {
    color: var(--muted);
}

.total-row strong {
    font-size: 2rem;
    font-family: 'Bebas Neue', sans-serif;
    letter-spacing: 1px;
}

.summary-btn {
    margin-top: 1rem;
    display: inline-block;
    text-decoration: none;
    color: white;
    background: var(--grad-2);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-weight: 700;
}
</style>
@endsection
