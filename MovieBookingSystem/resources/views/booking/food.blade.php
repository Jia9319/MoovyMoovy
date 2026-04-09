@extends('layouts.app')

@section('title', 'Food & Beverage - ' . ($bookingQuery['title'] ?? 'Booking'))

@section('content')
    <section class="food-page">
        <div class="food-shell">
            <div class="food-main">
                <div class="food-head">
                    <a href="{{ route('booking.seat', $bookingQuery) }}" class="seat-back" title="Back to seat selection">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1>Add Food & Beverages</h1>
                        <p>Optional step. You can skip and continue to payment.</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('booking.payment') }}" id="foodForm">
                    @foreach($bookingQuery as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="menu-block">
                        <h2 class="menu-title">Food</h2>
                        <div class="food-grid">
                            @foreach($foodItems as $key => $item)
                                <div class="food-card beverage-card">
                                    <h3>{{ $item['name'] }}</h3>
                                    <p>RM {{ number_format((float) $item['price'], 2) }}</p>
                                    <div class="qty-row">
                                        <label for="{{ $key }}_qty">Qty</label>
                                        <div class="qty-control">
                                            <button type="button" class="qty-btn" data-action="decrement"
                                                data-target="{{ $key }}_qty">-</button>
                                            <input id="{{ $key }}_qty" name="{{ $key }}_qty" type="number" min="0" step="1"
                                                value="0" data-price="{{ $item['price'] }}" class="food-qty" readonly>
                                            <button type="button" class="qty-btn" data-action="increment"
                                                data-target="{{ $key }}_qty">+</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="menu-block">
                        <h2 class="menu-title">Beverages</h2>
                        <div class="food-grid">
                            @foreach($beverageItems as $key => $item)
                                <div class="food-card">
                                    <h3>{{ $item['name'] }}</h3>
                                    <p>RM {{ number_format((float) $item['price'], 2) }}</p>
                                    <div class="qty-row">
                                        <label for="{{ $key }}_qty">Qty</label>
                                        <div class="qty-control">
                                            <button type="button" class="qty-btn" data-action="decrement"
                                                data-target="{{ $key }}_qty">-</button>
                                            <input id="{{ $key }}_qty" name="{{ $key }}_qty" type="number" min="0" step="1"
                                                value="0" data-price="{{ $item['price'] }}" class="food-qty" readonly>
                                            <button type="button" class="qty-btn" data-action="increment"
                                                data-target="{{ $key }}_qty">+</button>
                                        </div>
                                    </div>
                                    <div class="temp-row">
                                        <label for="{{ $key }}_temp">Serve</label>
                                        @if(count($item['temperature_options'] ?? []) === 1)
                                            <input type="hidden" name="{{ $key }}_temp"
                                                value="{{ $item['temperature_options'][0] }}">
                                            <span class="temp-badge">{{ ucfirst($item['temperature_options'][0]) }} only</span>
                                        @else
                                            <div class="temp-toggle" role="group"
                                                aria-label="Serve temperature for {{ $item['name'] }}">
                                                @foreach($item['temperature_options'] as $tempOption)
                                                    <input type="radio" id="{{ $key }}_temp_{{ $tempOption }}" name="{{ $key }}_temp"
                                                        value="{{ $tempOption }}" class="temp-radio"
                                                        @checked(($item['default_temperature'] ?? 'cold') === $tempOption)>
                                                    <label for="{{ $key }}_temp_{{ $tempOption }}"
                                                        class="temp-pill">{{ ucfirst($tempOption) }}</label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="food-footer">
                        <div>
                            <small>Seats: {{ $seats->join(', ') }}</small>
                            <div class="totals">
                                <span>Seat Total: RM {{ number_format((float) $seatTotal, 2) }}</span>
                                <span id="foodTotalText">Food Total: RM 0.00</span>
                                <strong id="grandTotalText">Grand Total: RM
                                    {{ number_format((float) $seatTotal, 2) }}</strong>
                            </div>
                        </div>
                        <div class="actions">
                            <button type="button" id="skipFoodBtn" class="ghost-btn">Skip Food</button>
                            <button type="submit" class="next-btn">Proceed to Payment <i
                                    class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <style>
        .food-page {
            min-height: 100vh;
            padding: 108px 5% 3rem;
        }

        .food-shell {
            max-width: 980px;
            margin: 0 auto;
        }

        .food-main {
            background: rgba(15, 15, 15, 0.92);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1.5rem;
        }

        .food-head {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.2rem;
        }

        .food-head h1 {
            font-size: clamp(1.4rem, 3vw, 2rem);
            margin: 0;
        }

        .food-head p {
            color: var(--muted);
            margin-top: 0.2rem;
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

        .seat-back:hover {
            color: #fff !important;
            border-color: var(--c1);
        }

        .menu-block {
            margin-top: 1rem;
        }

        .menu-title {
            font-size: 1.05rem;
            margin-bottom: 0.65rem;
            color: var(--white);
            letter-spacing: 0.6px;
        }

        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 0.85rem;
        }

        .food-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.9rem;
            background: rgba(255, 255, 255, 0.02);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .food-card h3 {
            margin: 0;
            font-size: 1rem;
        }

        .food-card p {
            margin-top: 0.3rem;
            color: var(--c1);
            font-weight: 700;
        }

        .qty-row {
            order: 3;
            margin-top: 0.4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .beverage-card .qty-row {
            margin-top: 0.35rem;
        }

        .qty-row label {
            color: var(--muted);
            font-size: 0.9rem;
        }

        .qty-control {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 0.2rem;
            background: rgba(255, 255, 255, 0.03);
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 7px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: var(--c1);
        }

        .qty-row input {
            width: 42px;
            height: 30px;
            border: none;
            text-align: center;
            background: transparent;
            color: white;
            line-height: 30px;
        }

        .temp-row {
            order: 2;
            margin-top: 0.7rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.7rem;
        }

        .temp-row label {
            color: var(--muted);
            font-size: 0.9rem;
        }

        .temp-badge {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 0.65rem;
            border-radius: 9px;
            border: 1px solid rgba(209, 106, 255, 0.3);
            background: rgba(209, 106, 255, 0.08);
            color: #fff;
            font-size: 0.9rem;
        }

        .temp-toggle {
            display: inline-flex;
            gap: 0.35rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .temp-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .temp-pill {
            min-width: 72px;
            min-height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 0 0.7rem;
            background: rgba(255, 255, 255, 0.04);
            color: var(--white);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .temp-radio:checked+.temp-pill {
            background: rgba(209, 106, 255, 0.18);
            border-color: var(--c1);
            color: #fff;
        }

        .food-footer {
            margin-top: 1rem;
            border-top: 1px solid var(--border);
            padding-top: 1rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: end;
        }

        .totals {
            margin-top: 0.4rem;
            display: grid;
            gap: 0.2rem;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .totals strong {
            color: var(--white);
            font-size: 1rem;
        }

        .actions {
            display: flex;
            gap: 0.6rem;
        }

        .ghost-btn,
        .next-btn {
            border: none;
            border-radius: 10px;
            padding: 0.72rem 1rem;
            font-weight: 700;
            cursor: pointer;
        }

        .ghost-btn {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border: 1px solid var(--border);
        }

        .next-btn {
            background: var(--grad-2);
            color: #fff;
        }

        @media (max-width: 720px) {
            .food-page {
                padding: 95px 4% 2rem;
            }

            .food-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <script>
        (() => {
            const seatTotal = Number(@json((float) $seatTotal));
            const qtyInputs = document.querySelectorAll('.food-qty');
            const qtyButtons = document.querySelectorAll('.qty-btn');
            const foodTotalText = document.getElementById('foodTotalText');
            const grandTotalText = document.getElementById('grandTotalText');
            const skipFoodBtn = document.getElementById('skipFoodBtn');
            const foodForm = document.getElementById('foodForm');

            const updateTotals = () => {
                let foodTotal = 0;
                qtyInputs.forEach((input) => {
                    const rawQty = Number(input.value || 0);
                    const qty = Math.max(rawQty, 0);
                    input.value = String(qty);
                    const price = Number(input.dataset.price || 0);
                    foodTotal += qty * price;
                });
                foodTotalText.textContent = `Food Total: RM ${foodTotal.toFixed(2)}`;
                grandTotalText.textContent = `Grand Total: RM ${(seatTotal + foodTotal).toFixed(2)}`;
            };

            qtyButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const targetId = button.dataset.target;
                    const action = button.dataset.action;
                    const input = document.getElementById(targetId);
                    if (!input) return;
                    const current = Number(input.value || 0);
                    input.value = String(action === 'increment' ? current + 1 : Math.max(current - 1, 0));
                    updateTotals();
                });
            });

            qtyInputs.forEach((input) => {
                input.addEventListener('input', updateTotals);
            });
            skipFoodBtn.addEventListener('click', () => {
                qtyInputs.forEach((input) => { input.value = '0'; });
                updateTotals();
                foodForm.submit();
            });
            updateTotals();
        })();
    </script>
@endsection