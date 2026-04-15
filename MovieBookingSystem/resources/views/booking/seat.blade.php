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
                        @if($showtime->hall) , {{ $showtime->hall }} @endif
                        @if($showtime->format) , {{ $showtime->format }} @endif
                        • {{ $showtime->date->format('D, d M Y') }} {{ \Carbon\Carbon::parse($showtime->time)->format('H:i') }}
                    </p>
                </div>
            </div>

            <div class="screen-wrap">
                <div class="screen-glow"></div>
                <div class="screen-bar"></div>
                <span>SCREEN</span>
            </div>

            <div class="seat-map-wrap">
                <div class="seat-grid" id="seatGrid">
                    @foreach($rows as $rowIndex => $row)
                        @php
                            $rowCouplePairStarts = in_array($row, $coupleRows ?? [], true) ? ($couplePairStarts ?? []) : [];
                            $rowCoupleFollowers = collect($rowCouplePairStarts)->map(fn($start) => (int) $start + 1)->all();
                        @endphp
                        <div class="seat-row">
                            <span class="row-label">{{ $row }}</span>
                            @for($number = 1; $number <= $seatsPerRow; $number++)
                                @if(in_array($number - 1, $aisleAfterColumns ?? [], true))
                                    <span class="aisle" aria-hidden="true"></span>
                                @endif

                                @php
                                    $isPairStart = in_array($number, $rowCouplePairStarts, true);
                                    $isPairFollower = in_array($number, $rowCoupleFollowers, true);
                                @endphp

                                @if($isPairFollower)
                                    @continue
                                @endif

                                @if($isPairStart)
                                    @php
                                        $firstSeat = $row . $number;
                                        $secondNumber = min($number + 1, $seatsPerRow);
                                        $pairSeats = [$firstSeat];
                                        $secondSeat = null;
                                        if ($secondNumber !== $number) {
                                            $secondSeat = $row . $secondNumber;
                                            $pairSeats[] = $secondSeat;
                                        }
                                        $pairLabel = implode('-', $pairSeats);
                                        $isBooked = $bookedSeats->contains($firstSeat) || ($secondSeat !== null && $bookedSeats->contains($secondSeat));
                                    @endphp
                                    <button
                                        type="button"
                                        class="seat couple couple-pair {{ $isBooked ? 'booked' : '' }}"
                                        data-seat-group="{{ implode(',', $pairSeats) }}"
                                        data-seat-label="{{ $pairLabel }}"
                                        data-couple="1"
                                        {{ $isBooked ? 'disabled' : '' }}
                                        title="{{ ($showtime->format ?? '') === 'BEANIE' ? 'Beanie Sofa' : 'Couple Seats' }} {{ $pairLabel }}"
                                    >
                                        <i class="fas fa-couch"></i>
                                    </button>
                                @else
                                    @php
                                        $seatId = $row . $number;
                                        $isBooked = $bookedSeats->contains($seatId);
                                    @endphp
                                    <button
                                        type="button"
                                        class="seat {{ $isBooked ? 'booked' : '' }}"
                                        data-seat-group="{{ $seatId }}"
                                        data-seat-label="{{ $seatId }}"
                                        data-couple="0"
                                        {{ $isBooked ? 'disabled' : '' }}
                                        title="Seat {{ $seatId }}"
                                    >
                                        <i class="fas fa-couch"></i>
                                    </button>
                                @endif
                            @endfor
                            <span class="row-label">{{ $row }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="seat-legend">
                <span><i class="seat-dot available"></i> Available</span>
                <span><i class="seat-dot selected"></i> Selected</span>
                @if($hasCoupleSeats ?? false)
                    <span><i class="seat-dot couple"></i> {{ ($showtime->format ?? '') === 'BEANIE' ? 'Beanie Sofa (2 seats)' : 'Couple (2 seats, +RM ' . number_format((float) $coupleExtra, 2) . ' / seat)' }}</span>
                @endif
                <span><i class="seat-dot booked"></i> Booked</span>
            </div>
        </div>

        <aside class="booking-side">
            <h2>Booking Summary</h2>

            <div class="movie-mini">
                @php
                    $durationRaw = (string) ($showtime->movie->duration ?? 0);
                    if (preg_match('/^(\d+)\s*h(?:\s*(\d+)\s*m)?$/i', $durationRaw, $matches)) {
                        $durationMinutes = ((int) $matches[1] * 60) + (int) ($matches[2] ?? 0);
                    } elseif (preg_match('/^(\d+)\s*m(?:in(?:ute)?s?)?$/i', $durationRaw, $matches)) {
                        $durationMinutes = (int) $matches[1];
                    } else {
                        $durationMinutes = (int) $durationRaw;
                    }
                    if ($durationMinutes > 0 && $durationMinutes <= 12) { $durationMinutes *= 60; }
                    $durationMinutes = max($durationMinutes, 0);
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
            @if(($hasCoupleSeats ?? false) && (($showtime->format ?? '') !== 'BEANIE'))
                <div class="meta-line">
                    <span>{{ ($showtime->format ?? '') === 'BEANIE' ? 'Beanie Surcharge' : 'Couple Seat Add-on' }}</span>
                    <strong>+RM {{ number_format((float)$coupleExtra, 2) }} / seat</strong>
                </div>
            @endif

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
.seat-page { padding: 108px 5% 4rem; min-height: 100vh; }
.seat-shell { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 1rem; align-items: start; }
.seat-main, .booking-side { background: rgba(15, 15, 15, 0.9); border: 1px solid var(--border); border-radius: 18px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35); }
.seat-main { padding: 1.5rem; }
.seat-map-wrap {
    --seat-size: 32px;
    --seat-gap: 0.24rem;
    --row-label-size: 18px;
    --aisle-size: 14px;
}
.seat-head { display: flex; gap: 1rem; align-items: center; margin-bottom: 1.25rem; }
.seat-back { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; color: var(--white); border-radius: 12px; border: 1px solid var(--border); text-decoration: none; transition: all 0.25s; }
.seat-back:hover { border-color: var(--c1); color: var(--c1); }
.seat-title { font-size: clamp(1.35rem, 2.8vw, 2rem); margin-bottom: 0.2rem; }
.seat-sub { color: var(--muted); font-size: 0.9rem; }
.screen-wrap { --screen-width: 640px; width: var(--screen-width); margin: 1.5rem auto 2rem; text-align: center; position: relative; max-width: 100%; }
.screen-glow { height: 62px; margin: -2px auto 0; width: calc(var(--screen-width) * 1.08); max-width: calc(100% + 24px); background: radial-gradient(ellipse at center top, rgba(209, 106, 255, 0.44) 0%, rgba(209, 106, 255, 0.2) 36%, rgba(209, 106, 255, 0.06) 62%, rgba(209, 106, 255, 0) 100%); filter: blur(14px); transform: translateY(-3px); pointer-events: none; }
.screen-bar { height: 18px; width: 100%; margin: 0 auto; border-radius: 65% 65% 38% 38% / 100% 100% 0 0; background: linear-gradient(180deg, #f6d8ff 0%, #d16aff 40%, #6d089e 100%); box-shadow: 0 0 28px rgba(209, 106, 255, 0.6), inset 0 2px 2px rgba(255, 255, 255, 0.4); transform: perspective(700px) rotateX(-26deg); transform-origin: center top; }
.screen-wrap span { display: inline-block; margin-top: 0.65rem; color: var(--muted); letter-spacing: 5px; font-size: 0.72rem; }
.seat-map-wrap { position: relative; }
.seat-grid { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 0.65rem; overflow-x: visible; padding-bottom: 0.25rem; }
.seat-row { display: flex; justify-content: center; gap: var(--seat-gap); align-items: center; width: max-content; margin-inline: auto; }
.row-label { width: var(--row-label-size); flex: 0 0 var(--row-label-size); text-align: center; color: var(--muted); font-size: 0.82rem; font-weight: 600; }
.aisle {
    width: var(--aisle-size);
    flex: 0 0 var(--aisle-size);
    height: calc(var(--seat-size) + 0.65rem);
    margin-top: -0.325rem;
    margin-bottom: -0.325rem;
    border-radius: 0;
    background: transparent;
    border: none;
}
.seat { width: var(--seat-size); flex: 0 0 var(--seat-size); height: var(--seat-size); border: 1px solid var(--border); border-radius: 10px; background: rgba(255, 255, 255, 0.05); color: #c9b4dc; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; }
.seat i { font-size: 0.78rem; }
.seat:hover:not(:disabled) { border-color: var(--c1); transform: translateY(-1px); }
.seat.couple { border-color: rgba(255, 193, 7, 0.5); color: #ffce3a; background: rgba(255, 193, 7, 0.12); }
.seat.couple-pair { width: calc(var(--seat-size) * 2 + var(--seat-gap)); flex: 0 0 calc(var(--seat-size) * 2 + var(--seat-gap)); display: inline-flex; align-items: center; justify-content: center; padding: 0; }
.seat.selected { background: linear-gradient(135deg, rgba(150, 20, 208, 0.75), rgba(209, 106, 255, 0.8)); border-color: transparent; color: #fff; }
.seat.booked { opacity: 0.45; cursor: not-allowed; background: rgba(255, 255, 255, 0.06); }
.seat-legend { margin-top: 1.25rem; display: flex; gap: 1rem; flex-wrap: wrap; color: var(--muted); font-size: 0.82rem; }
.seat-legend span { display: inline-flex; gap: 0.4rem; align-items: center; }
.seat-dot { width: 10px; height: 10px; border-radius: 999px; border: 1px solid transparent; display: inline-block; }
.seat-dot.available { background: rgba(255, 255, 255, 0.3); }
.seat-dot.selected { background: #bb44f0; }
.seat-dot.couple { background: #ffce3a; }
.seat-dot.booked { background: rgba(255, 255, 255, 0.12); }
.booking-side { padding: 1.25rem; position: sticky; top: 90px; }
.booking-side h2 { font-size: 1.3rem; margin-bottom: 1rem; }
.movie-mini { border: 1px solid var(--border); border-radius: 14px; background: rgba(255, 255, 255, 0.03); padding: 0.7rem; display: flex; gap: 0.75rem; margin-bottom: 1rem; }
.movie-mini img, .movie-mini-placeholder { width: 56px; height: 78px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
.movie-mini-placeholder { display: flex; align-items: center; justify-content: center; font-weight: 700; background: linear-gradient(145deg, #26003f, #6d089e); }
.movie-mini small { color: var(--c1); letter-spacing: 1px; font-size: 0.65rem; font-weight: 700; }
.movie-mini strong { display: block; margin-top: 0.25rem; font-size: 0.95rem; }
.movie-mini p { margin-top: 0.25rem; color: var(--muted); font-size: 0.8rem; }
.meta-line { display: flex; justify-content: space-between; gap: 0.8rem; margin-bottom: 0.7rem; color: var(--muted); font-size: 0.85rem; }
.meta-line strong { color: var(--white); text-align: right; }
.selected-block { margin-top: 0.9rem; padding-top: 0.9rem; border-top: 1px solid var(--border); }
.selected-block > span { color: var(--muted); font-size: 0.85rem; }
.selected-list { margin-top: 0.6rem; display: flex; flex-wrap: wrap; gap: 0.45rem; min-height: 28px; color: var(--muted); font-size: 0.85rem; }
.seat-chip { border-radius: 999px; border: 1px solid var(--border); padding: 0.2rem 0.6rem; font-size: 0.78rem; color: var(--white); background: rgba(255, 255, 255, 0.04); }
.seat-chip.couple { border-color: rgba(255, 193, 7, 0.45); background: rgba(255, 193, 7, 0.12); color: #ffe08a; }
.total-wrap { margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; }
.total-wrap span { color: var(--muted); }
.total-wrap strong { font-size: 1.65rem; font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px; }
.proceed-btn { width: 100%; margin-top: 1rem; border: none; border-radius: 12px; padding: 0.85rem 1rem; font-weight: 700; color: #fff; cursor: pointer; background: var(--grad-2); transition: all 0.2s; }
.proceed-btn i { margin-left: 0.4rem; }
.proceed-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(150, 20, 208, 0.4); }
.proceed-btn:disabled { opacity: 0.5; cursor: not-allowed; }

@media (max-width: 1024px) { .seat-shell { grid-template-columns: 1fr; } .booking-side { position: static; } }
@media (max-width: 1024px) { .seat-map-wrap { --seat-size: 27px; --seat-gap: 0.2rem; --row-label-size: 16px; --aisle-size: 12px; } }
@media (max-width: 640px) { .seat-page { padding: 95px 4% 2.5rem; } .seat-main, .booking-side { border-radius: 14px; } .seat-map-wrap { --seat-size: 22px; --seat-gap: 0.15rem; --row-label-size: 12px; --aisle-size: 9px; } .seat { border-radius: 8px; } }
</style>

<script>
(() => {
    const basePrice = Number(@json((float) $showtime->price));
    const coupleExtra = Number(@json((float) $coupleExtra));
    const isBeanieHall = @json(strtoupper((string) ($showtime->format ?? '')) === 'BEANIE');
    const bookingQuery = @json($bookingQuery ?? []);
    const selectedSeatsWrap = document.getElementById('selectedSeats');
    const totalPriceEl = document.getElementById('totalPrice');
    const proceedBtn = document.getElementById('proceedBtn');
    const seatGrid = document.getElementById('seatGrid');
    const screenWrap = document.querySelector('.screen-wrap');
   
    const selected = new Map();

    const formatMoney = (value) => `RM ${value.toFixed(2)}`;

    const syncScreenWidth = () => {
        if (!seatGrid || !screenWrap) {
            return;
        }

        const rows = Array.from(seatGrid.querySelectorAll('.seat-row'));
        if (rows.length === 0) {
            return;
        }

        const seatBlockWidths = rows.map((row) => {
            const seats = Array.from(row.querySelectorAll('.seat'));
            if (seats.length === 0) {
                return 0;
            }

            const firstSeatRect = seats[0].getBoundingClientRect();
            const lastSeatRect = seats[seats.length - 1].getBoundingClientRect();
            return Math.ceil(lastSeatRect.right - firstSeatRect.left);
        }).filter((width) => width > 0);

        if (seatBlockWidths.length === 0) {
            return;
        }

        const seatBlockWidth = Math.max(...seatBlockWidths);
        const targetWidth = Math.max(Math.ceil(seatBlockWidth * 1.2), 220);
        screenWrap.style.setProperty('--screen-width', `${targetWidth}px`);
    };

    const renderSummary = () => {
        const selectedGroups = Array.from(selected.values())
            .sort((a, b) => a.label.localeCompare(b.label));

        if (selectedGroups.length === 0) {
            selectedSeatsWrap.textContent = 'None';
            totalPriceEl.textContent = formatMoney(0);
            proceedBtn.disabled = true;
            return;
        }

        // 渲染已选座位的 Chip
        selectedSeatsWrap.innerHTML = selectedGroups
            .map((group) => `<span class="seat-chip ${group.isCouple ? 'couple' : ''}">${group.label}${group.isCouple ? (isBeanieHall ? ' Beanie' : ' Couple') : ''}</span>`)
            .join('');

        // 计算总金额：(单人票价 + 加价) * 该组座位数量
        const total = selectedGroups.reduce((sum, group) => {
            const unitPrice = group.isCouple ? (basePrice + coupleExtra) : basePrice;
            return sum + (unitPrice * group.seats.length);
        }, 0);

        totalPriceEl.textContent = formatMoney(total);
        proceedBtn.disabled = false;
    };

    document.querySelectorAll('.seat:not(.booked)').forEach((seatBtn) => {
        seatBtn.addEventListener('click', () => {
            const seatLabel = seatBtn.dataset.seatLabel;
            const seatGroup = (seatBtn.dataset.seatGroup || '').split(',').filter(Boolean);
            const isCouple = seatBtn.dataset.couple === '1';

            if (selected.has(seatLabel)) {
                selected.delete(seatLabel);
                seatBtn.classList.remove('selected');
            } else {
                selected.set(seatLabel, {
                    label: seatLabel,
                    seats: seatGroup,
                    isCouple,
                });
                seatBtn.classList.add('selected');
            }

            renderSummary();
        });
    });

    proceedBtn.addEventListener('click', () => {
        // 获取所有具体的座位号（展开 Couple Seat 的两个 ID）
        const allSeats = Array.from(selected.values())
            .flatMap((group) => group.seats)
            .sort((a, b) => a.localeCompare(b));

        if (allSeats.length === 0) return;

        // 拼接成逗号分隔的字符串传给下一个页面
        const params = new URLSearchParams({ 
            ...bookingQuery, 
            seats: allSeats.join(',') 
        });
        window.location.href = `{{ route('booking.food') }}?${params.toString()}`;
    });

    window.addEventListener('resize', syncScreenWidth);
    if (typeof ResizeObserver !== 'undefined' && seatGrid) {
        const observer = new ResizeObserver(syncScreenWidth);
        observer.observe(seatGrid);
    }

    syncScreenWidth();
    renderSummary();
})();
</script>
@endsection