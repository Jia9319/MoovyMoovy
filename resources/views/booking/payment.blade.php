@extends('layouts.app')

@section('title', 'Payment - ' . ($bookingQuery['title'] ?? 'Booking'))

@section('content')
<div id="booking-payment-root"></div>

<section class="payment-page" style="display:none;">
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
                    <div class="edit-actions">
                        <a href="{{ route('booking.seat', array_merge($bookingQuery, request()->query(), ['return_to' => 'payment'])) }}" class="edit-link">Edit Seats</a>
                        <a href="{{ route('booking.food', array_merge($bookingQuery, request()->query())) }}" class="edit-link">Edit Food & Beverage</a>
                    </div>
                </div>

                <div class="card-block">
                    <h3>Price Breakdown</h3>
                    <div class="line"><span>Seat Total</span><strong>RM {{ number_format((float) $seatTotal, 2) }}</strong></div>
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

                <h3>Payment Method</h3>
                <label class="method"><input type="radio" name="payment_method" value="tng" checked><span>Touch 'n Go eWallet</span></label>
                <label class="method"><input type="radio" name="payment_method" value="debit"><span>Debit Card</span></label>
                <label class="method"><input type="radio" name="payment_method" value="credit"><span>Credit Card</span></label>

                <div class="pay-footer">
                    <button type="submit" class="pay-btn">Pay Now <i class="fas fa-arrow-right"></i></button>
                </div>
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
.edit-actions { margin-top: 0.8rem; display: flex; flex-wrap: wrap; gap: 0.55rem; }
.edit-link {
    color: #fff;
    text-decoration: none;
    border: 1px solid var(--border);
    border-radius: 999px;
    padding: 0.3rem 0.75rem;
    font-size: 0.82rem;
}
.edit-link:hover { border-color: var(--c1); }
.pay-form { margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 1rem; display: flex; flex-direction: column; }
.pay-footer { margin-top: 0.9rem; width: fit-content; margin-left: auto; }
.method { display: flex; align-items: center; gap: 0.55rem; margin-top: 0.55rem; color: var(--white); }
.pay-btn { width: 100%; margin-top: 1rem; border: none; border-radius: 12px; padding: 0.85rem 1rem; font-weight: 700; color: #fff; cursor: pointer; background: var(--grad-2); transition: all 0.2s; }
.pay-btn i { margin-left: 0.4rem; }
.pay-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(150, 20, 208, 0.4); }
.pay-btn:disabled { opacity: 0.5; cursor: not-allowed; }
@media (max-width: 720px) { .payment-page { padding: 95px 4% 2rem; } }
</style>

@push('scripts')
<script>
    window.MoovyBookingPaymentData = @json($bookingPaymentData);
</script>
@endpush
@endsection