@extends('layouts.app')

@section('title', 'E-Ticket - ' . (optional($ticket->movie)->title ?? $ticket->ticket_code))

@section('content')
<section class="ticket-page">
    <div class="ticket-card">
        <div class="ticket-head">
            <a href="javascript:void(0);" onclick="window.history.back();" class="seat-back" title="Go back">
            <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1>Ticket Details</h1>
                <p class="sub">Show this QR code at cinema entry.</p>
            </div>
        </div>

        <div class="ticket-layout">
            @php
                $seatTotalValue = $ticket->seat_total ?? 0;
                $foodTotalValue = $ticket->food_total ?? 0;
                $seatsDisplay = is_array($ticket->seats) ? implode(', ', $ticket->seats) : (string) $ticket->seats;
                $hallLabel = trim((string) $ticket->hall);
                $hallLabel = preg_replace('/^\s*hall\s*/i', '', $hallLabel);
                $locationLabel = trim((string) $ticket->cinema);

                if ($hallLabel !== '') {
                    $locationLabel .= ' • Hall ' . $hallLabel;
                }

                if (!empty($ticket->format)) {
                    $locationLabel .= ' • ' . $ticket->format;
                }
            @endphp

            <div class="ticket-details">
                <div class="row"><span>Ticket Code</span><strong>{{ $ticket->ticket_code }}</strong></div>
                <div class="row"><span>Movie</span><strong>{{ optional($ticket->movie)->title ?? 'Unknown Movie' }}</strong></div>
                <div class="row"><span>Cinema</span><strong>{{ $locationLabel }}</strong></div>
                <div class="row"><span>Date & Time</span><strong>{{ $ticket->date->format('D, d M Y') }} {{ $ticket->time }}</strong></div>
                <div class="row"><span>Seats</span><strong>{{ $seatsDisplay }}</strong></div>
                <div class="row"><span>Payment</span><strong>{{ strtoupper($ticket->payment_method ?? 'N/A') }}</strong></div>
                @if(!empty($ticket->promo_code))
                    <div class="row"><span>Promo Code</span><strong>{{ $ticket->promo_code }}</strong></div>
                @endif
                <div class="row"><span>Seat Total</span><strong>RM {{ number_format((float) $seatTotalValue, 2) }}</strong></div>
                @if((float) $ticket->discount_amount > 0)
                    <div class="row"><span>Tuesday Discount (50%)</span><strong style="color:#22c55e;">- RM {{ number_format((float) $ticket->discount_amount, 2) }}</strong></div>
                @endif
                <div class="row"><span>Food Total</span><strong>RM {{ number_format((float) $foodTotalValue, 2) }}</strong></div>
                <div class="addons-block">
                    @if(!empty($ticket->food_lines) && collect($ticket->food_lines)->count())
                        @foreach($ticket->food_lines as $line)
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
                <div class="row total"><span>Total</span><strong>RM {{ number_format((float) $ticket->grand_total, 2) }}</strong></div>
            </div>
            <div class="qr-wrap">
                <img src="{{ $ticket->qr_url }}" alt="Ticket QR Code">
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('home') }}" class="back-btn">Back to Home </a>
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

.actions {
    margin-top: 1rem;
    width: 100%;
}

.back-btn {
    width: 100%;
    border: none;
    border-radius: 12px;
    padding: 0.85rem 1rem;
    font-weight: 700;
    color: #fff;
    cursor: pointer;
    background: var(--grad-2);
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    text-decoration: none;
}

.back-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(150, 20, 208, 0.4);
}

@media (max-width: 760px) {
    .ticket-page { padding: 95px 4% 2rem; }
    .ticket-layout { grid-template-columns: 1fr; }
    .qr-wrap img { width: 220px; height: 220px; }
}
</style>

@push('scripts')
<script>
    window.MoovyBookingSummaryData = @json($ticket);
</script>
@endpush
@endsection