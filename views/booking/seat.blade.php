@extends('layouts.app')

@section('title', 'Select Seats - ' . $showtime->movie->title)

@section('content')
<section class="seat-page">
	<div class="seat-shell">
		<div class="seat-main">
			<div class="seat-head">
				<a href="{{ route('booking.select', $bookingQuery ?? []) }}" class="seat-back" title="Back to choose cinema and time">
					<i class="fas fa-arrow-left"></i>
				</a>
				<div>
					<h1 class="seat-title">{{ $showtime->movie->title }}</h1>
					<p class="seat-sub">
						{{ $showtime->cinema }}
						@if($showtime->hall)
							, {{ $showtime->hall }}
						@endif
						@if($showtime->format)
							, {{ $showtime->format }}
						@endif
						• {{ $showtime->date->format('D, d M Y') }} {{ \Carbon\Carbon::parse($showtime->time)->format('H:i') }}
					</p>
				</div>
			</div>

			<div class="screen-wrap">
				<div class="screen-glow"></div>
				<div class="screen-bar"></div>
				<span>SCREEN</span>
			</div>

			<div class="seat-grid" id="seatGrid">
				@foreach($rows as $row)
					<div class="seat-row">
						<span class="row-label">{{ $row }}</span>
						@for($number = 1; $number <= $seatsPerRow; $number++)
							@php
								$seatId = $row . $number;
								$isVip = in_array($row, $vipRows, true);
								$isBooked = $bookedSeats->contains($seatId);
							@endphp
							<button
								type="button"
								class="seat {{ $isVip ? 'vip' : '' }} {{ $isBooked ? 'booked' : '' }}"
								data-seat="{{ $seatId }}"
								data-vip="{{ $isVip ? 1 : 0 }}"
								{{ $isBooked ? 'disabled' : '' }}
								title="Seat {{ $seatId }}"
							>
								<i class="fas fa-couch"></i>
							</button>
						@endfor
					</div>
				@endforeach
			</div>

			<div class="seat-legend">
				<span><i class="seat-dot available"></i> Available</span>
				<span><i class="seat-dot selected"></i> Selected</span>
				<span><i class="seat-dot vip"></i> VIP (+RM {{ number_format($vipExtra, 2) }})</span>
				<span><i class="seat-dot booked"></i> Booked</span>
			</div>
		</div>

		<aside class="booking-side">
			<h2>Booking Summary</h2>

			<div class="movie-mini">
				@php
					$durationMinutes = max((int) $showtime->movie->duration, 0);
					$durationText = intdiv($durationMinutes, 60) . 'h ' . ($durationMinutes % 60) . 'm';
				@endphp
				@if($showtime->movie->poster)
					<img src="{{ asset('storage/' . $showtime->movie->poster) }}" alt="{{ $showtime->movie->title }} poster">
				@else
					<div class="movie-mini-placeholder">{{ strtoupper(substr($showtime->movie->title, 0, 2)) }}</div>
				@endif
				<div>
					<small>NOW SHOWING</small>
					<strong>{{ $showtime->movie->title }}</strong>
					<p>{{ $showtime->movie->genre }} • {{ $durationText }}</p>
				</div>
			</div>

			<div class="meta-line">
				<span>Cinema</span>
				<strong>{{ $showtime->cinema }}</strong>
			</div>
			<div class="meta-line">
				<span>Date & Time</span>
				<strong>{{ $showtime->date->format('M d, Y') }} {{ \Carbon\Carbon::parse($showtime->time)->format('H:i') }}</strong>
			</div>
			<div class="meta-line">
				<span>Base Price</span>
				<strong>RM {{ number_format((float)$showtime->price, 2) }}</strong>
			</div>

			<div class="selected-block">
				<span>Selected Seats</span>
				<div id="selectedSeats" class="selected-list">None</div>
			</div>

			<div class="total-wrap">
				<span>Total Price</span>
				<strong id="totalPrice">RM 0.00</strong>
			</div>

			<button type="button" id="proceedBtn" class="proceed-btn" disabled>
				Continue to Food & Beverage
				<i class="fas fa-arrow-right"></i>
			</button>
		</aside>
	</div>
</section>

<style>
.seat-page {
	padding: 108px 5% 4rem;
	min-height: 100vh;
}

.seat-shell {
	display: grid;
	grid-template-columns: minmax(0, 1fr) 340px;
	gap: 1.5rem;
	align-items: start;
}

.seat-main,
.booking-side {
	background: rgba(15, 15, 15, 0.9);
	border: 1px solid var(--border);
	border-radius: 18px;
	box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
}

.seat-main {
	padding: 1.5rem;
}

.seat-head {
	display: flex;
	gap: 1rem;
	align-items: center;
	margin-bottom: 1.25rem;
}

.seat-back {
	width: 40px;
	height: 40px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	color: var(--white);
	border-radius: 12px;
	border: 1px solid var(--border);
	text-decoration: none;
	transition: all 0.25s;
}

.seat-back:hover {
	border-color: var(--c1);
	color: var(--c1);
}

.seat-title {
	font-size: clamp(1.35rem, 2.8vw, 2rem);
	margin-bottom: 0.2rem;
}

.seat-sub {
	color: var(--muted);
	font-size: 0.9rem;
}

.screen-wrap {
	margin: 1.5rem auto 2rem;
	max-width: 720px;
	text-align: center;
	position: relative;
}

.screen-glow {
	height: 16px;
	margin: 0 auto;
	width: min(100%, 640px);
	background: radial-gradient(circle, rgba(209, 106, 255, 0.45) 0%, rgba(209, 106, 255, 0) 70%);
	filter: blur(10px);
}

.screen-bar {
	height: 8px;
	width: min(100%, 640px);
	margin: 0 auto;
	border-radius: 999px;
	background: var(--grad-1);
	box-shadow: 0 0 24px var(--glow-intense);
}

.screen-wrap span {
	display: inline-block;
	margin-top: 0.65rem;
	color: var(--muted);
	letter-spacing: 5px;
	font-size: 0.72rem;
}

.seat-grid {
	width: 100%;
	display: grid;
	gap: 0.65rem;
	overflow-x: auto;
	padding-bottom: 0.25rem;
}

.seat-row {
	display: grid;
	grid-template-columns: 24px repeat(10, minmax(34px, 1fr));
	gap: 0.45rem;
	align-items: center;
	min-width: 560px;
}

.row-label {
	color: var(--muted);
	font-size: 0.82rem;
	font-weight: 600;
}

.seat {
	height: 34px;
	border: 1px solid var(--border);
	border-radius: 10px;
	background: rgba(255, 255, 255, 0.05);
	color: #c9b4dc;
	cursor: pointer;
	transition: all 0.2s;
	display: inline-flex;
	align-items: center;
	justify-content: center;
}

.seat:hover:not(:disabled) {
	border-color: var(--c1);
	transform: translateY(-1px);
}

.seat.vip {
	border-color: rgba(255, 193, 7, 0.5);
	color: #ffce3a;
	background: rgba(255, 193, 7, 0.12);
}

.seat.selected {
	background: linear-gradient(135deg, rgba(150, 20, 208, 0.75), rgba(209, 106, 255, 0.8));
	border-color: transparent;
	color: #fff;
}

.seat.booked {
	opacity: 0.45;
	cursor: not-allowed;
	background: rgba(255, 255, 255, 0.06);
}

.seat-legend {
	margin-top: 1.25rem;
	display: flex;
	gap: 1rem;
	flex-wrap: wrap;
	color: var(--muted);
	font-size: 0.82rem;
}

.seat-legend span {
	display: inline-flex;
	gap: 0.4rem;
	align-items: center;
}

.seat-dot {
	width: 10px;
	height: 10px;
	border-radius: 999px;
	border: 1px solid transparent;
	display: inline-block;
}

.seat-dot.available {
	background: rgba(255, 255, 255, 0.3);
}

.seat-dot.selected {
	background: #bb44f0;
}

.seat-dot.vip {
	background: #ffce3a;
}

.seat-dot.booked {
	background: rgba(255, 255, 255, 0.12);
}

.booking-side {
	padding: 1.25rem;
	position: sticky;
	top: 90px;
}

.booking-side h2 {
	font-size: 1.3rem;
	margin-bottom: 1rem;
}

.movie-mini {
	border: 1px solid var(--border);
	border-radius: 14px;
	background: rgba(255, 255, 255, 0.03);
	padding: 0.7rem;
	display: flex;
	gap: 0.75rem;
	margin-bottom: 1rem;
}

.movie-mini img,
.movie-mini-placeholder {
	width: 56px;
	height: 78px;
	border-radius: 10px;
	object-fit: cover;
	flex-shrink: 0;
}

.movie-mini-placeholder {
	display: flex;
	align-items: center;
	justify-content: center;
	font-weight: 700;
	background: linear-gradient(145deg, #26003f, #6d089e);
}

.movie-mini small {
	color: var(--c1);
	letter-spacing: 1px;
	font-size: 0.65rem;
	font-weight: 700;
}

.movie-mini strong {
	display: block;
	margin-top: 0.25rem;
	font-size: 0.95rem;
}

.movie-mini p {
	margin-top: 0.25rem;
	color: var(--muted);
	font-size: 0.8rem;
}

.meta-line {
	display: flex;
	justify-content: space-between;
	gap: 0.8rem;
	margin-bottom: 0.7rem;
	color: var(--muted);
	font-size: 0.85rem;
}

.meta-line strong {
	color: var(--white);
	text-align: right;
}

.selected-block {
	margin-top: 0.9rem;
	padding-top: 0.9rem;
	border-top: 1px solid var(--border);
}

.selected-block > span {
	color: var(--muted);
	font-size: 0.85rem;
}

.selected-list {
	margin-top: 0.6rem;
	display: flex;
	flex-wrap: wrap;
	gap: 0.45rem;
	min-height: 28px;
	color: var(--muted);
	font-size: 0.85rem;
}

.seat-chip {
	border-radius: 999px;
	border: 1px solid var(--border);
	padding: 0.2rem 0.6rem;
	font-size: 0.78rem;
	color: var(--white);
	background: rgba(255, 255, 255, 0.04);
}

.total-wrap {
	margin-top: 1rem;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.total-wrap span {
	color: var(--muted);
}

.total-wrap strong {
	font-size: 1.65rem;
	font-family: 'Bebas Neue', sans-serif;
	letter-spacing: 1px;
}

.proceed-btn {
	width: 100%;
	margin-top: 1rem;
	border: none;
	border-radius: 12px;
	padding: 0.85rem 1rem;
	font-weight: 700;
	color: #fff;
	cursor: pointer;
	background: var(--grad-2);
	transition: all 0.2s;
}

.proceed-btn i {
	margin-left: 0.4rem;
}

.proceed-btn:hover:not(:disabled) {
	transform: translateY(-2px);
	box-shadow: 0 10px 20px rgba(150, 20, 208, 0.4);
}

.proceed-btn:disabled {
	opacity: 0.5;
	cursor: not-allowed;
}

@media (max-width: 1024px) {
	.seat-shell {
		grid-template-columns: 1fr;
	}

	.booking-side {
		position: static;
	}
}

@media (max-width: 640px) {
	.seat-page {
		padding: 95px 4% 2.5rem;
	}

	.seat-main,
	.booking-side {
		border-radius: 14px;
	}

	.seat-row {
		grid-template-columns: 20px repeat(10, minmax(30px, 1fr));
		min-width: 460px;
	}

	.seat {
		height: 30px;
		border-radius: 8px;
	}
}
</style>

<script>
(() => {
	const basePrice = Number(@json((float) $showtime->price));
	const vipExtra = Number(@json((float) $vipExtra));
	const bookingQuery = @json($bookingQuery ?? []);
	const selectedSeatsWrap = document.getElementById('selectedSeats');
	const totalPriceEl = document.getElementById('totalPrice');
	const proceedBtn = document.getElementById('proceedBtn');
	const selected = new Map();

	const formatMoney = (value) => `RM ${value.toFixed(2)}`;

	const renderSummary = () => {
		const seats = Array.from(selected.entries())
			.sort((a, b) => a[0].localeCompare(b[0]));

		if (seats.length === 0) {
			selectedSeatsWrap.textContent = 'None';
			totalPriceEl.textContent = formatMoney(0);
			proceedBtn.disabled = true;
			return;
		}

		selectedSeatsWrap.innerHTML = seats
			.map(([seatId]) => `<span class="seat-chip">${seatId}</span>`)
			.join('');

		const total = seats.reduce((sum, [, isVip]) => {
			return sum + basePrice + (isVip ? vipExtra : 0);
		}, 0);

		totalPriceEl.textContent = formatMoney(total);
		proceedBtn.disabled = false;
	};

	document.querySelectorAll('.seat:not(.booked)').forEach((seatBtn) => {
		seatBtn.addEventListener('click', () => {
			const seatId = seatBtn.dataset.seat;
			const isVip = seatBtn.dataset.vip === '1';

			if (selected.has(seatId)) {
				selected.delete(seatId);
				seatBtn.classList.remove('selected');
			} else {
				selected.set(seatId, isVip);
				seatBtn.classList.add('selected');
			}

			renderSummary();
		});
	});

	proceedBtn.addEventListener('click', () => {
		const seats = Array.from(selected.keys()).sort((a, b) => a.localeCompare(b));
		if (seats.length === 0) {
			return;
		}

		const params = new URLSearchParams({ ...bookingQuery, seats: seats.join(',') });
		window.location.href = `{{ route('booking.food') }}?${params.toString()}`;
	});

	renderSummary();
})();
</script>
@endsection
