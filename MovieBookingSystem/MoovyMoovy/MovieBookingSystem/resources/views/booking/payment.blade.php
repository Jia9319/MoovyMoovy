@extends('layouts.app')

@section('title', 'Payment - ' . ($bookingQuery['title'] ?? 'Booking'))

@section('content')
<section class="payment-page">
    <div class="payment-shell">
        <div class="payment-main">
            <div class="payment-head">
                <a href="{{ route('booking.food', array_merge($bookingQuery, request()->query())) }}" class="seat-back" title="Back to food and beverage">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1>Payment</h1>
                    <p>Review details and choose payment method.</p>
                </div>
            </div>

            <div class="payment-grid">
                <div class="card-block">
                    <h3>Booking Details</h3>
                    <div class="line"><span>Movie</span><strong>{{ $bookingQuery['title'] }}</strong></div>
                    <div class="line"><span>Cinema</span><strong>{{ $bookingQuery['cinema'] }} • {{ $bookingQuery['hall'] }} • {{ $bookingQuery['format'] }}</strong></div>
                    <div class="line"><span>Date & Time</span><strong>{{ \Carbon\Carbon::parse($bookingQuery['date'])->format('D, d M Y') }} {{ $bookingQuery['time'] }}</strong></div>
                    <div class="line"><span>Seats</span><strong>{{ $seats->join(', ') }}</strong></div>
                </div>

                <div class="card-block">
                    <h3>Price Breakdown</h3>
                    <div class="line"><span>Seat Total</span><strong>RM {{ number_format((float) $seatTotal, 2) }}</strong></div>
                    @if($isTuesday)
                        <div class="line"><span>Tuesday Discount (50%)</span><strong style="color:#22c55e;">- RM {{ number_format((float) $discountAmount, 2) }}</strong></div>
                        <div class="line"><span>Seat Total After Discount</span><strong>RM {{ number_format((float) $discountedSeatTotal, 2) }}</strong></div>
                    @endif
                    <div class="line"><span>Food Total</span><strong>RM {{ number_format((float) $foodTotal, 2) }}</strong></div>
                    @if($foodLines->count())
                        @foreach($foodLines as $line)
                            <div class="line small">
                                <span>
                                    {{ $line['name'] }} x{{ $line['qty'] }}
                                    @if(($line['category'] ?? null) === 'beverage' && !empty($line['temperatureLabel']))
                                        • {{ $line['temperatureLocked'] ? 'Cold only' : $line['temperatureLabel'] }}
                                    @endif
                                </span>
                                <strong>RM {{ number_format((float) $line['lineTotal'], 2) }}</strong>
                            </div>
                        @endforeach
                    @endif
                    <div class="line total"><span>Grand Total</span><strong>RM {{ number_format((float) $grandTotal, 2) }}</strong></div>
                </div>
            </div>

            <form method="POST" action="{{ route('booking.ticket') }}" class="pay-form">
                @csrf
                @foreach(request()->query() as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <input type="hidden" name="seat_total" value="{{ $seatTotal }}">
                <input type="hidden" name="food_total" value="{{ $foodTotal }}">

                <div style="margin-bottom:1rem;">
                    <label for="promo_code" style="display:block;margin-bottom:0.5rem;color:var(--muted);font-size:0.9rem;">Promotion Code</label>
                    <div style="display:flex;gap:0.6rem;flex-wrap:wrap;">
                        <input
                            type="text"
                            id="promo_code"
                            name="promo_code"
                            placeholder="Enter promo code"
                            style="flex:1;min-width:220px;background:rgba(255,255,255,0.04);border:1px solid var(--border);border-radius:10px;padding:0.8rem 0.95rem;color:var(--white);outline:none;"
                        >
                        <button type="button" style="border:none;border-radius:10px;padding:0.8rem 1rem;font-weight:700;cursor:pointer;background:rgba(255,255,255,0.1);color:var(--white);border:1px solid var(--border);">
                            Apply
                        </button>
                    </div>
                </div>

                @if($isTuesday)
                    <p style="margin:0.5rem 0 0.8rem;color:#22c55e;font-size:0.9rem;">
                        Tuesday Promo Applied: 50% off movie seat tickets.
                    </p>
                @endif

                <h3>Payment Method</h3>
                <label class="method"><input type="radio" name="payment_method" value="tng" checked><span>Touch 'n Go eWallet</span></label>
                <label class="method"><input type="radio" name="payment_method" value="debit"><span>Debit Card</span></label>
                <label class="method"><input type="radio" name="payment_method" value="credit"><span>Credit Card</span></label>

                <button type="submit" class="pay-btn">Pay Now</button>
            </form>
        </div>
    </div>
</section>

<style>
.payment-page { min-height: 100vh; padding: 108px 5% 3rem; }
.payment-shell { max-width: 980px; margin: 0 auto; }
.payment-main { background: rgba(15, 15, 15, 0.92); border: 1px solid var(--border); border-radius: 18px; padding: 1.5rem; }
.payment-head { display: flex; gap: 1rem; align-items: center; margin-bottom: 1.2rem; }
.payment-head h1 { margin: 0; font-size: clamp(1.4rem, 3vw, 2rem); }
.payment-head p { color: var(--muted); margin-top: 0.2rem; }
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
.payment-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px,1fr)); gap: 0.8rem; }
.card-block { border: 1px solid var(--border); border-radius: 12px; padding: 0.9rem; background: rgba(255,255,255,0.02); }
.card-block h3 { margin-bottom: 0.8rem; }
.line { display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 0.45rem; }
.line span { color: var(--muted); }
.line.small { font-size: 0.85rem; }
.line.total { border-top: 1px solid var(--border); padding-top: 0.6rem; margin-top: 0.4rem; }
.pay-form { margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 1rem; }
.method { display: flex; align-items: center; gap: 0.55rem; margin-top: 0.55rem; color: var(--white); }
.pay-btn { margin-top: 0.9rem; border: none; border-radius: 10px; padding: 0.8rem 1rem; color: white; background: var(--grad-2); font-weight: 700; cursor: pointer; }
@media (max-width: 720px) { .payment-page { padding: 95px 4% 2rem; } }
</style>
@endsection