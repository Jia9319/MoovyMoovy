@extends('layouts.app')

@section('title', 'E-Ticket - ' . $title)

@section('content')
<section class="ticket-page">
    <div class="ticket-card">
        <div class="ticket-head">
            <a href="{{ route('home') }}" class="seat-back" title="Back to home">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1>Your E-Ticket</h1>
                <p class="sub">Payment successful. Show this QR code at cinema entry.</p>
            </div>
        </div>

        <div class="ticket-layout">
            @php
                $seatTotalValue = $seatTotal ?? $seat_total ?? 0;
                $foodTotalValue = $foodTotal ?? $food_total ?? 0;
            @endphp
            <div class="ticket-details">
                <div class="row"><span>Ticket Code</span><strong>{{ $ticketCode }}</strong></div>
                <div class="row"><span>Movie</span><strong>{{ $title }}</strong></div>
                <div class="row"><span>Cinema</span><strong>{{ $cinema }} • {{ $hall }} • {{ $format }}</strong></div>
                <div class="row"><span>Date & Time</span><strong>{{ $date->format('D, d M Y') }} {{ $time }}</strong></div>
                <div class="row"><span>Seats</span><strong>{{ $seats }}</strong></div>
                <div class="row"><span>Payment</span><strong>{{ strtoupper($paymentMethod ?? $payment_method ?? 'N/A') }}</strong></div>
                @if(!empty($promoCode))
                    <div class="row"><span>Promo Code</span><strong>{{ $promoCode }}</strong></div>
                @endif
                <div class="row"><span>Seat Total</span><strong>RM {{ number_format((float) $seatTotalValue, 2) }}</strong></div>
                @if($isTuesday)
                    <div class="row"><span>Tuesday Discount (50%)</span><strong style="color:#22c55e;">- RM {{ number_format((float) $discountAmount, 2) }}</strong></div>
                @endif
                <div class="row"><span>Food Total</span><strong>RM {{ number_format((float) $foodTotalValue, 2) }}</strong></div>
                <div class="addons-block">
                    @if(!empty($foodLines) && collect($foodLines)->count())
                        @foreach($foodLines as $line)
                            <div class="row small">
                                <span>
                                    {{ $line['name'] }} x{{ $line['qty'] }}
                                    @if(($line['category'] ?? null) === 'beverage' && !empty($line['temperatureLabel']))
                                        • {{ $line['temperatureLocked'] ? 'Cold only' : $line['temperatureLabel'] }}
                                    @endif
                                </span>
                                <strong>RM {{ number_format((float) $line['lineTotal'], 2) }}</strong>
                            </div>
                        @endforeach
                    @else
                        <div class="addons-empty">No food or beverage added.</div>
                    @endif
                </div>
                <div class="row total"><span>Total</span><strong>RM {{ number_format((float) $grandTotal, 2) }}</strong></div>
            </div>
            <div class="qr-wrap">
                <img src="{{ $qrUrl }}" alt="Ticket QR Code">
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('home') }}" class="back-btn">Back to Home</a>
        </div>
    </div>
</section>

<style>
.ticket-page { min-height: 100vh; padding: 108px 5% 3rem; }
.ticket-card {
    max-width: 920px;
    margin: 0 auto;
    border: 1px solid rgba(209, 106, 255, 0.35);
    border-radius: 22px;
    background: linear-gradient(145deg, rgba(18, 9, 24, 0.98), rgba(12, 12, 12, 0.95));
    padding: 1.5rem;
    position: relative;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(209, 106, 255, 0.15) inset;
    overflow: hidden;
}

.ticket-head {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.seat-back {
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff !important;
    border-radius: 12px;
    border: 1px solid var(--border);
    text-decoration: none;
}

.seat-back:hover { color: #fff !important; border-color: var(--c1); }

.ticket-card::before,
.ticket-card::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--bg);
    border: 1px solid var(--border);
    transform: translateY(-50%);
}

.ticket-card::before { left: -15px; }
.ticket-card::after { right: -15px; }

.ticket-card h1 { margin: 0; font-size: clamp(1.5rem, 3vw, 2.2rem); }
.ticket-card .sub { color: var(--muted); margin-top: 0.4rem; }
.ticket-layout { margin-top: 1rem; display: grid; grid-template-columns: 1fr 280px; gap: 1rem; }
.ticket-details {
    border: 1px dashed rgba(209, 106, 255, 0.4);
    border-radius: 14px;
    padding: 1rem;
    background: rgba(255,255,255,0.02);
}
.addons-block {
    margin: 0.3rem 0 0.8rem;
    padding-top: 0.65rem;
    border-top: 1px solid var(--border);
}
.addons-title {
    margin-bottom: 0.45rem;
    color: var(--white);
    font-size: 0.88rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-transform: uppercase;
}
.addons-empty {
    color: var(--muted);
    font-size: 0.9rem;
    padding: 0.2rem 0 0.1rem;
}
.row { display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 0.5rem; }
.row span { color: var(--muted); }
.row.total { border-top: 1px solid var(--border); padding-top: 0.7rem; margin-top: 0.4rem; }
.qr-wrap {
    border: 1px solid rgba(209, 106, 255, 0.35);
    border-radius: 14px;
    display: grid;
    place-items: center;
    background: radial-gradient(circle at top, rgba(209, 106, 255, 0.12), rgba(255,255,255,0.02));
    padding: 0.8rem;
    position: relative;
}

.qr-wrap::before {
    content: 'SCAN TO ENTER';
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 0.65rem;
    letter-spacing: 1.8px;
    color: var(--muted);
}

.qr-wrap img {
    width: 240px;
    height: 240px;
    border-radius: 8px;
    background: #fff;
    padding: 8px;
    border: 2px solid rgba(0,0,0,0.15);
    margin-top: 1.1rem;
}
.actions { margin-top: 1rem; }
.back-btn { text-decoration: none; color: #fff; background: var(--grad-2); border-radius: 10px; padding: 0.75rem 1rem; font-weight: 700; display: inline-block; }
@media (max-width: 760px) {
    .ticket-page { padding: 95px 4% 2rem; }
    .ticket-layout { grid-template-columns: 1fr; }
    .qr-wrap img { width: 220px; height: 220px; }
}
</style>
@endsection
