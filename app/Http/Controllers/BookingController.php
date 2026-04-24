<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    private function bookingSessionKey(): string
    {
        return 'booking.state';
    }

    private function bookingAuthPayload(Request $request): array
    {
        return [
            'isAuthenticated' => (bool) $request->user(),
            'userId' => optional($request->user())->id,
        ];
    }

    public function authStatus(Request $request)
    {
        return response()->json([
            'authenticated' => (bool) $request->user(),
            'userId' => optional($request->user())->id,
        ]);
    }

    private function currentBookingState(Request $request): array
    {
        return array_merge(
            (array) $request->session()->get($this->bookingSessionKey(), []),
            $request->query(),
            $request->except(['_token'])
        );
    }

    private function persistBookingState(Request $request, array $state): array
    {
        $merged = array_merge((array) $request->session()->get($this->bookingSessionKey(), []), $state);

        $filtered = array_filter($merged, function ($value) {
            return $value !== null && $value !== '';
        });

        $request->session()->put($this->bookingSessionKey(), $filtered);

        return $filtered;
    }

    private function resolvePosterUrl(?string $poster): ?string
    {
        $poster = trim((string) $poster);

        if ($poster === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $poster) || str_starts_with($poster, '/')) {
            return $poster;
        }

        return asset('storage/' . ltrim($poster, '/'));
    }

    private function buildFoodSelections(Request $request): array
    {
        $menu = $this->getFoodMenu();
        $quantities = [];
        $temperatures = [];
        $entries = [];

        foreach ($menu as $key => $item) {
            $entries[] = [$key, $item];
            $quantities[$key] = max((int) $request->input($key . '_qty', 0), 0);

            if (($item['category'] ?? null) === 'beverage') {
                $temperatureOptions = $item['temperature_options'] ?? ['cold'];
                $defaultTemperature = $item['default_temperature'] ?? $temperatureOptions[0] ?? 'cold';
                $selectedTemperature = strtolower((string) $request->input($key . '_temp', $defaultTemperature));

                if (count($temperatureOptions) === 1) {
                    $selectedTemperature = $temperatureOptions[0];
                } elseif (!in_array($selectedTemperature, $temperatureOptions, true)) {
                    $selectedTemperature = $defaultTemperature;
                }

                $temperatures[$key] = $selectedTemperature;
            }
        }

        return [
            'quantities' => $quantities,
            'temperatures' => $temperatures,
            'allEntries' => $entries,
        ];
    }

    private function normalizeDurationMinutes($rawDuration, int $default = 120): int
    {
        $value = trim((string) $rawDuration);

        if ($value === '') {
            return $default;
        }

        if (preg_match('/^(\d+)\s*h(?:\s*(\d+)\s*m)?$/i', $value, $matches)) {
            $hours = (int) $matches[1];
            $minutes = isset($matches[2]) ? (int) $matches[2] : 0;
            return max(($hours * 60) + $minutes, 1);
        }

        if (preg_match('/^(\d+)\s*m(?:in(?:ute)?s?)?$/i', $value, $matches)) {
            return max((int) $matches[1], 1);
        }

        $duration = (int) $value;
        if ($duration <= 0) {
            return $default;
        }

        // Some legacy entries were stored in hours (for example "2"), so convert safely.
        if ($duration <= 12) {
            return $duration * 60;
        }

        return $duration;
    }

    private function getSeatExperienceConfig(string $format, float $basePrice): array
    {
        $normalized = strtoupper(trim($format));

        switch ($normalized) {
            case '3D':
                return [
                    'hallType' => '3D',
                    'rows' => range('A', 'P'), // 16 rows
                    'seatsPerRow' => 18,
                    'aisleAfterColumns' => [4, 14],
                    'coupleRows' => ['N', 'O', 'P'], // last 3 rows
                    'couplePairStarts' => [1, 3, 5, 7, 9, 11, 13, 15, 17], // full row couples
                    'coupleExtra' => 10.0,
                ];
            case 'IMAX':
                return [
                    'hallType' => 'IMAX',
                    'rows' => range('A', 'V'), // 22 rows
                    'seatsPerRow' => 22,
                    'aisleAfterColumns' => [6, 16],
                    'coupleRows' => ['T', 'U', 'V'], // rows 20-22
                    'couplePairStarts' => [1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21], // full row couples
                    'coupleExtra' => 15.0,
                ];
            case 'BEANIE':
                return [
                    'hallType' => 'BEANIE',
                    'rows' => range('A', 'F'),
                    'seatsPerRow' => 8,
                    'aisleAfterColumns' => [4],
                    'coupleRows' => range('A', 'F'),
                    'couplePairStarts' => [1, 3, 5, 7],
                    'coupleExtra' => 0.0,
                ];
            case 'BONNIE':
                return $this->getSeatExperienceConfig('BEANIE', $basePrice);
            case '2D':
            default:
                return [
                    'hallType' => '2D',
                    'rows' => range('A', 'N'), // 14 rows
                    'seatsPerRow' => 16,
                    'aisleAfterColumns' => [4, 12],
                    'coupleRows' => ['M', 'N'], // rows 13-14
                    'couplePairStarts' => [1, 3, 5, 7, 9, 11, 13, 15], // full row couples
                    'coupleExtra' => 5.0,
                ];
        }
    }

    private function isCoupleSeatId(string $seatId, array $seatConfig): bool
    {
        if (!preg_match('/^([A-Za-z]+)(\d+)$/', trim($seatId), $matches)) {
            return false;
        }

        $rowCode = strtoupper($matches[1]);
        $column = (int) $matches[2];
        $seatsPerRow = (int) ($seatConfig['seatsPerRow'] ?? 0);
        $coupleRows = $seatConfig['coupleRows'] ?? [];
        $couplePairStarts = $seatConfig['couplePairStarts'] ?? [];

        if ($column <= 0 || $seatsPerRow <= 0) {
            return false;
        }

        if (!in_array($rowCode, $coupleRows, true)) {
            return false;
        }

        foreach ($couplePairStarts as $start) {
            $left = (int) $start;
            $right = $left + 1;
            if ($column === $left || $column === $right) {
                return true;
            }
        }

        return false;
    }

    private function getFoodMenu(): array
    {
        return [
            // --- Food Items ---
            'caramel_popcorn' => ['name' => 'Signature Caramel Popcorn', 'price' => 13.50, 'category' => 'food'],
            'original_popcorn' => ['name' => 'Classic Sea Salt Popcorn', 'price' => 12.50, 'category' => 'food'],
            'curly_fries' => ['name' => 'Twister Curly Fries', 'price' => 11.90, 'category' => 'food'],
            'french_fries' => ['name' => 'Crispy Shoestring Fries', 'price' => 9.90, 'category' => 'food'],
            'loaded_nachos' => ['name' => 'Supreme Cheese Nachos Bowl', 'price' => 14.90, 'category' => 'food'],
            'jumbo_hotdog' => ['name' => 'Jumbo Chicken Hot Dog Bun', 'price' => 11.50, 'category' => 'food'],
            'chicken_nuggets' => ['name' => 'Golden Nuggets (6pcs)', 'price' => 12.50, 'category' => 'food'],
            'curry_puff' => ['name' => 'Crispy Potato Curry Puff (3pcs)', 'price' => 9.00, 'category' => 'food'],

            // --- Beverage Items ---
            'coke_classic' => ['name' => 'Coca-Cola (Original)', 'price' => 7.50, 'category' => 'beverage', 'temperature_options' => ['cold'], 'default_temperature' => 'cold'],
            'coke_zero' => ['name' => 'Coca-Cola (Zero Sugar)', 'price' => 7.50, 'category' => 'beverage', 'temperature_options' => ['cold'], 'default_temperature' => 'cold'],
            'milo_ais' => ['name' => 'Milo Kaw Kaw', 'price' => 8.90, 'category' => 'beverage', 'temperature_options' => ['hot', 'cold'], 'default_temperature' => 'cold'], 
            'horkaisee' => ['name' => 'Signature Hor Ka Sai', 'price' => 9.90, 'category' => 'beverage', 'temperature_options' => ['hot', 'cold'], 'default_temperature' => 'cold'], 
            'matcha' => ['name' => 'Matcha Latte', 'price' => 13.90, 'category' => 'beverage', 'temperature_options' => ['hot', 'cold'], 'default_temperature' => 'cold'], 
            'sirap_bandung' => ['name' => 'Sirap Bandung', 'price' => 7.90, 'category' => 'beverage', 'temperature_options' => ['hot', 'cold'], 'default_temperature' => 'cold'],
            'teh_tarik' => ['name' => 'Teh Tarik', 'price' => 8.50, 'category' => 'beverage', 'temperature_options' => ['hot', 'cold'], 'default_temperature' => 'cold'],
            'mineral_water' => ['name' => 'Sky Juice', 'price' => 4.50, 'category' => 'beverage', 'temperature_options' => ['hot', 'cold'], 'default_temperature' => 'cold'], 
        ];
    }

    private function buildAddonLines(Request $request)
    {
        return collect($this->getFoodMenu())->map(function ($item, $key) use ($request) {
            $qty = max((int) $request->input($key . '_qty', 0), 0);

            if ($qty <= 0) {
                return null;
            }

            $line = [
                'key' => $key,
                'name' => $item['name'],
                'price' => (float) $item['price'],
                'qty' => $qty,
                'lineTotal' => $qty * (float) $item['price'],
                'category' => $item['category'],
            ];

            if (($item['category'] ?? null) === 'beverage') {
                $temperatureOptions = $item['temperature_options'] ?? ['cold'];
                $defaultTemperature = $item['default_temperature'] ?? 'cold';
                $selectedTemperature = strtolower((string) $request->input($key . '_temp', $defaultTemperature));

                if (count($temperatureOptions) === 1) {
                    $selectedTemperature = $temperatureOptions[0];
                } elseif (!in_array($selectedTemperature, $temperatureOptions, true)) {
                    $selectedTemperature = $defaultTemperature;
                }

                $line['temperature'] = $selectedTemperature;
                $line['temperatureLabel'] = ucfirst($selectedTemperature);
                $line['temperatureLocked'] = count($temperatureOptions) === 1;
            }

            return $line;
        })->filter()->values();
    }

    private function buildBookingQuery(Request $request): array
    {
        $state = $this->currentBookingState($request);
        $format = strtoupper((string) ($state['format'] ?? '2D'));
        if ($format === 'BONNIE') {
            $format = 'BEANIE';
        }

        $durationMinutes = $this->normalizeDurationMinutes($state['duration'] ?? 120);
        $durationText = intdiv($durationMinutes, 60) . 'h ' . ($durationMinutes % 60) . 'm';

        return [
            'movie_id' => $state['movie_id'] ?? null,
            'showtime_id' => $state['showtime_id'] ?? null,
            'title' => $state['title'] ?? 'Selected Movie',
            'genre' => $state['genre'] ?? 'Movie',
            'duration' => $durationMinutes,
            'durationText' => $durationText,
            'poster' => $state['poster'] ?? null,
            'cinema' => $state['cinema'] ?? 'GSC Mid Valley',
            'hall' => $state['hall'] ?? 'Hall ' . rand(1, 10),
            'format' => $format,
            'date' => $state['date'] ?? Carbon::today()->format('Y-m-d'),
            'time' => $state['time'] ?? '19:10',
            'price' => (float) ($state['price'] ?? 18),
            'seats' => $state['seats'] ?? '',
        ];
    }

    private function calculateSeatTotal(string $seatsRaw, float $basePrice, string $format): float
    {
        $seatConfig = $this->getSeatExperienceConfig($format, $basePrice);
        $coupleExtra = (float) ($seatConfig['coupleExtra'] ?? 0);
        $seats = collect(explode(',', $seatsRaw))->map(fn($seat) => trim($seat))->filter();
        return $seats->reduce(function ($sum, $seat) use ($basePrice, $seatConfig, $coupleExtra) {
            $isCouple = $this->isCoupleSeatId((string) $seat, $seatConfig);
            return $sum + $basePrice + ($isCouple ? $coupleExtra : 0);
        }, 0);
    }

    public function select(Request $request)
    {
        $movie = [
            'id' => $request->query('movie_id'),
            'title' => $request->query('title', 'Selected Movie'),
            'genre' => $request->query('genre', 'Movie'),
            'duration' => $this->normalizeDurationMinutes($request->query('duration', 120)),
            'poster' => $this->resolvePosterUrl($request->query('poster')),
        ];

        $today = Carbon::today();
        $dates = collect(range(0, 4))->map(fn($offset) => $today->copy()->addDays($offset)->format('Y-m-d'));

        $cinemas = collect([
            'GSC Mid Valley', 'TGV Sunway Pyramid', 'Dadi Cinema Pavilion KL', 
            'MBO Atria Shopping Gallery', 'Aurum Theatre Gardens', 
            'GSC IOI City Mall', 'TGV 1 Utama', 'GSC Gurney Plaza'
        ])->map(function($name) {
            return ['name' => $name, 'hall' => 'Hall ' . rand(1, 10)];
        });

        $types = [
            ['label' => '2D', 'price' => 18],
            ['label' => '3D', 'price' => 28],
            ['label' => 'IMAX', 'price' => 45],
            ['label' => 'BEANIE', 'price' => 20],
        ];

        $times = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:45', 
            '12:15', '12:45', '13:20', '13:50', '14:20', '15:00', 
            '15:40', '16:10', '16:50', '17:20', '17:50', '18:30', 
            '19:00', '19:40', '20:15', '20:45', '21:15', '21:50', 
            '22:20', '22:50', '23:15', '23:45', '00:30', '01:00'
        ];

        $durationRaw = (string) ($movie['duration'] ?? 0);
        if (preg_match('/^(\d+)\s*h(?:\s*(\d+)\s*m)?$/i', $durationRaw, $matches)) {
            $durationMinutes = ((int) $matches[1] * 60) + (int) ($matches[2] ?? 0);
        } elseif (preg_match('/^(\d+)\s*m(?:in(?:ute)?s?)?$/i', $durationRaw, $matches)) {
            $durationMinutes = (int) $matches[1];
        } else {
            $durationMinutes = (int) $durationRaw;
        }

        if ($durationMinutes > 0 && $durationMinutes <= 12) {
            $durationMinutes *= 60;
        }

        $durationMinutes = max($durationMinutes, 0);
        $durationText = intdiv($durationMinutes, 60) . 'h ' . ($durationMinutes % 60) . 'm';
        $randomCinemas = collect($cinemas)->random(min(6, count($cinemas)))->values();
        $randomTimes = collect($times)->random(min(10, count($times)))->sort()->values();

        $savedState = (array) $request->session()->get($this->bookingSessionKey(), []);
        $selectedFormat = strtoupper((string) ($savedState['format'] ?? ''));
        if ($selectedFormat === 'BONNIE') {
            $selectedFormat = 'BEANIE';
        }

        $bookingState = [
            'movie_id' => $movie['id'],
            'title' => $movie['title'],
            'genre' => $movie['genre'],
            'duration' => $movie['duration'],
            'poster' => $movie['poster'],
            'cinema' => $savedState['cinema'] ?? null,
            'hall' => $savedState['hall'] ?? null,
            'format' => $selectedFormat ?: null,
            'price' => $savedState['price'] ?? null,
            'date' => $savedState['date'] ?? null,
            'time' => $savedState['time'] ?? null,
        ];
        $bookingSelectData = [
            'auth' => $this->bookingAuthPayload($request),
            'loginUrl' => route('login'),
            'authCheckUrl' => route('booking.auth.status'),
            'storageKey' => 'booking.select.' . ($movie['id'] ?? 'guest'),
            'homeUrl' => route('home'),
            'seatUrl' => route('booking.seat'),
            'bookingState' => $bookingState,
            'movie' => [
                'id' => $movie['id'],
                'title' => $movie['title'],
                'genre' => $movie['genre'],
                'duration' => $movie['duration'],
                'durationText' => $durationText,
                'poster' => $movie['poster'] ?? null,
            ],
            'cinemas' => $randomCinemas->values()->all(),
            'types' => $types,
            'dates' => $dates->values()->all(),
            'times' => $randomTimes->values()->all(),
            'selection' => [
                'cinema' => $bookingState['cinema'] ?? null,
                'hall' => $bookingState['hall'] ?? null,
                'format' => $bookingState['format'] ?? null,
                'price' => $bookingState['price'] ?? null,
                'date' => $bookingState['date'] ?? null,
                'time' => $bookingState['time'] ?? null,
            ],
        ];

        return view('booking.booking', [
            'movie' => $movie,
            'bookingSelectData' => $bookingSelectData,
        ]);
    }

    public function seat(Request $request)
    {
        $bookingQuery = $this->buildBookingQuery($request);
        $seatConfig = $this->getSeatExperienceConfig((string) $bookingQuery['format'], (float) $bookingQuery['price']);
        $bookingQuery['poster'] = $this->resolvePosterUrl($bookingQuery['poster']);
        $this->persistBookingState($request, $bookingQuery);
        $showtime = (object) [
            'id' => crc32($bookingQuery['title'] . $bookingQuery['cinema'] . $bookingQuery['date'] . $bookingQuery['time']),
            'cinema' => $bookingQuery['cinema'],
            'hall' => $bookingQuery['hall'],
            'format' => $bookingQuery['format'],
            'date' => $bookingQuery['date'],
            'time' => $bookingQuery['time'],
            'price' => $bookingQuery['price'],
            'movie' => (object) [
                'id' => $bookingQuery['movie_id'],
                'title' => $bookingQuery['title'],
                'genre' => $bookingQuery['genre'],
                'duration' => $bookingQuery['duration'],
                'durationText' => $bookingQuery['durationText'],
                'poster' => $bookingQuery['poster'],
            ],
        ];

        $rows = $seatConfig['rows'];
        $seatsPerRow = (int) $seatConfig['seatsPerRow'];
        $bookedSeats = collect(range(1, 8))->map(function ($index) use ($showtime, $rows, $seatsPerRow) {
            $seed = crc32($showtime->id . '-' . $index);
            return $rows[$seed % count($rows)] . (($seed % $seatsPerRow) + 1);
        })->unique()->values();

        return view('booking.seat', [
            'showtime' => $showtime,
            'rows' => $rows,
            'seatsPerRow' => $seatsPerRow,
            'bookedSeats' => $bookedSeats,
            'aisleAfterColumns' => $seatConfig['aisleAfterColumns'],
            'coupleRows' => $seatConfig['coupleRows'],
            'couplePairStarts' => $seatConfig['couplePairStarts'],
            'coupleExtra' => (float) $seatConfig['coupleExtra'],
            'hasCoupleSeats' => !empty($seatConfig['coupleRows']) && !empty($seatConfig['couplePairStarts']),
            'bookingQuery' => $bookingQuery,
            'bookingSeatData' => [
                'auth' => $this->bookingAuthPayload($request),
                'loginUrl' => route('login'),
                'authCheckUrl' => route('booking.auth.status'),
                'storageKey' => 'booking.seat.' . $showtime->id,
                'homeUrl' => route('home'),
                'backUrl' => route('booking.select', $bookingQuery),
                'nextUrl' => route('booking.food'),
                'bookingState' => $bookingQuery,
                'showtime' => $showtime,
                'rows' => $rows,
                'seatsPerRow' => $seatsPerRow,
                'bookedSeats' => $bookedSeats->values()->all(),
                'aisleAfterColumns' => $seatConfig['aisleAfterColumns'],
                'coupleRows' => $seatConfig['coupleRows'],
                'couplePairStarts' => $seatConfig['couplePairStarts'],
                'coupleExtra' => (float) $seatConfig['coupleExtra'],
                'hasCoupleSeats' => !empty($seatConfig['coupleRows']) && !empty($seatConfig['couplePairStarts']),
                'basePrice' => (float) $bookingQuery['price'],
                'selectedSeats' => $bookingQuery['seats'],
            ],
        ]);
    }

    public function food(Request $request)
    {
        $bookingQuery = $this->buildBookingQuery($request);
        $seatTotal = $this->calculateSeatTotal((string) $bookingQuery['seats'], (float) $bookingQuery['price'], (string) $bookingQuery['format']);
        $menu = $this->getFoodMenu();
        $foodSelections = $this->buildFoodSelections($request);
        $bookingQuery['poster'] = $this->resolvePosterUrl($bookingQuery['poster']);
        $this->persistBookingState($request, array_merge($bookingQuery, [
            'seat_total' => $seatTotal,
            'food_total' => 0,
        ]));

        return view('booking.food', [
            'bookingQuery' => $bookingQuery,
            'seats' => collect(explode(',', (string) $bookingQuery['seats']))->filter()->values(),
            'seatTotal' => $seatTotal,
            'foodItems' => collect($menu)->filter(fn($item) => $item['category'] === 'food')->all(),
            'beverageItems' => collect($menu)->filter(fn($item) => $item['category'] === 'beverage')->all(),
            'bookingFoodData' => [
                'auth' => $this->bookingAuthPayload($request),
                'loginUrl' => route('login'),
                'authCheckUrl' => route('booking.auth.status'),
                'storageKey' => 'booking.food.' . crc32(($bookingQuery['title'] ?? '') . '|' . ($bookingQuery['date'] ?? '') . '|' . ($bookingQuery['time'] ?? '')),
                'homeUrl' => route('home'),
                'backUrl' => route('booking.seat', $bookingQuery),
                'nextUrl' => route('booking.payment'),
                'bookingState' => $bookingQuery,
                'seats' => collect(explode(',', (string) $bookingQuery['seats']))->filter()->values()->all(),
                'seatTotal' => $seatTotal,
                'foodItems' => collect($menu)->filter(fn($item) => $item['category'] === 'food')->all(),
                'beverageItems' => collect($menu)->filter(fn($item) => $item['category'] === 'beverage')->all(),
                'foodSelections' => $foodSelections,
            ],
        ]);
    }

    public function payment(Request $request)
    {
        $bookingQuery = $this->buildBookingQuery($request);
        $seatTotal = $this->calculateSeatTotal((string) $bookingQuery['seats'], (float) $bookingQuery['price'], (string) $bookingQuery['format']);
        $foodLines = $this->buildAddonLines($request);
        $foodSelections = $this->buildFoodSelections($request);

        $isTuesday = Carbon::parse($bookingQuery['date'])->isTuesday();
        $discountAmount = $isTuesday ? ($seatTotal * 0.5) : 0;
        $grandTotal = ($seatTotal - $discountAmount) + $foodLines->sum('lineTotal');
        $bookingQuery['poster'] = $this->resolvePosterUrl($bookingQuery['poster']);
        $this->persistBookingState($request, array_merge($bookingQuery, [
            'seat_total' => $seatTotal,
            'food_total' => $foodLines->sum('lineTotal'),
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
        ]));

        return view('booking.payment', [
            'bookingQuery' => $bookingQuery,
            'seats' => collect(explode(',', (string) $bookingQuery['seats']))->filter()->values(),
            'seatTotal' => $seatTotal,
            'isTuesday' => $isTuesday,
            'discountAmount' => $discountAmount,
            'discountedSeatTotal' => $seatTotal - $discountAmount,
            'foodLines' => $foodLines,
            'foodTotal' => $foodLines->sum('lineTotal'),
            'grandTotal' => $grandTotal,
            'bookingPaymentData' => [
                'auth' => $this->bookingAuthPayload($request),
                'loginUrl' => route('login'),
                'authCheckUrl' => route('booking.auth.status'),
                'storageKey' => 'booking.payment.' . crc32(($bookingQuery['title'] ?? '') . '|' . ($bookingQuery['date'] ?? '') . '|' . ($bookingQuery['time'] ?? '')),
                'csrfToken' => csrf_token(),
                'homeUrl' => route('home'),
                'backUrl' => route('booking.food', $request->query()),
                'nextUrl' => route('booking.ticket'),
                'bookingState' => $bookingQuery,
                'seats' => collect(explode(',', (string) $bookingQuery['seats']))->filter()->values()->all(),
                'seatTotal' => $seatTotal,
                'isTuesday' => $isTuesday,
                'discountAmount' => $discountAmount,
                'discountedSeatTotal' => $seatTotal - $discountAmount,
                'foodLines' => $foodLines->values()->all(),
                'foodTotal' => $foodLines->sum('lineTotal'),
                'grandTotal' => $grandTotal,
                'foodSelections' => $foodSelections,
                'paymentMethod' => $request->input('payment_method', 'tng'),
            ],
        ]);
    }

    public function ticket(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required', 'cinema' => 'required', 'hall' => 'nullable', 'format' => 'nullable',
            'date' => 'required|date', 'time' => 'required', 'seats' => 'required',
            'seat_total' => 'required|numeric', 'food_total' => 'required|numeric', 'payment_method' => 'required',
        ]);

        $seats = collect(explode(',', (string) $validated['seats']))->map(fn($seat) => trim($seat))->filter()->values();
        $isTuesday = Carbon::parse($validated['date'])->isTuesday();
        $discount = $isTuesday ? ($validated['seat_total'] * 0.5) : 0;
        $total = ($validated['seat_total'] - $discount) + $validated['food_total'];
        $foodLines = $this->buildAddonLines($request);
        $ticketCode = 'MM-' . strtoupper(Str::random(10));
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($ticketCode);

        $ticket = Ticket::create([
            'user_id' => optional($request->user())->id,
            'movie_id' => $request->input('movie_id'),
            'showtime_id' => $request->input('showtime_id'),
            'ticket_code' => $ticketCode,
            'qr_url' => $qrUrl,
            'cinema' => $validated['cinema'],
            'hall' => $validated['hall'] ?? null,
            'format' => $validated['format'] ?? null,
            'date' => $validated['date'],
            'time' => $validated['time'],
            'seats' => $seats->all(),
            'seat_count' => $seats->count(),
            'seat_total' => (float) $validated['seat_total'],
            'food_lines' => $foodLines->values()->all(),
            'food_total' => (float) $validated['food_total'],
            'discount_amount' => $discount,
            'grand_total' => $total,
            'payment_method' => $validated['payment_method'],
            'status' => 'paid',
        ]);

        $this->persistBookingState($request, array_merge($validated, [
            'movie_id' => $request->input('movie_id'),
            'showtime_id' => $request->input('showtime_id'),
            'ticket_code' => $ticketCode,
            'seat_total' => (float) $validated['seat_total'],
            'food_total' => (float) $validated['food_total'],
            'discount_amount' => $discount,
            'grand_total' => $total,
            'payment_method' => $validated['payment_method'],
        ]));

        $ticketPayload = [
            'ticketCode' => $ticketCode,
            'title' => $validated['title'],
            'cinema' => $validated['cinema'],
            'hall' => $validated['hall'] ?? null,
            'format' => $validated['format'] ?? null,
            'date' => Carbon::parse($validated['date'])->format('Y-m-d'),
            'time' => $validated['time'],
            'seats' => $seats->all(),
            'paymentMethod' => $validated['payment_method'],
            'seatTotal' => (float) $validated['seat_total'],
            'foodTotal' => (float) $validated['food_total'],
            'discountAmount' => $discount,
            'isTuesday' => $isTuesday,
            'grandTotal' => $total,
            'foodLines' => $foodLines->values()->all(),
            'qrUrl' => $ticket->qr_url ?: $qrUrl,
        ];

        $request->session()->put('booking.last_ticket', $ticketPayload);

        return view('booking.ticket', [
            'ticketCode' => $ticketCode,
            'title' => $validated['title'],
            'cinema' => $validated['cinema'],
            'hall' => $validated['hall'] ?? null,
            'format' => $validated['format'] ?? null,
            'date' => Carbon::parse($validated['date']),
            'time' => $validated['time'],
            'seats' => $seats->join(', '),
            'paymentMethod' => $validated['payment_method'],
            'seatTotal' => (float) $validated['seat_total'],
            'foodTotal' => (float) $validated['food_total'],
            'discountAmount' => $discount,
            'isTuesday' => $isTuesday,
            'grandTotal' => $total,
            'foodLines' => $foodLines,
            'qrUrl' => $ticket->qr_url ?: $qrUrl,
            'bookingTicketData' => [
                'auth' => $this->bookingAuthPayload($request),
                'loginUrl' => route('login'),
                'authCheckUrl' => route('booking.auth.status'),
                'homeUrl' => route('home'),
                'ticket' => $ticketPayload,
                'ticketModel' => $ticket,
            ],
        ]);
    }

    public function summary(Request $request)
    {
        $bookingQuery = $this->buildBookingQuery($request);
        $seatTotal = $this->calculateSeatTotal((string) $bookingQuery['seats'], (float) $bookingQuery['price'], (string) $bookingQuery['format']);
        $foodLines = $this->buildAddonLines($request);
        $foodTotal = $foodLines->sum('lineTotal');
        $isTuesday = Carbon::parse($bookingQuery['date'])->isTuesday();
        $discount = $isTuesday ? ($seatTotal * 0.5) : 0;

        return view('booking.summary', [
            'movieTitle' => $bookingQuery['title'],
            'cinema' => $bookingQuery['cinema'],
            'hall' => $bookingQuery['hall'],
            'format' => $bookingQuery['format'],
            'date' => Carbon::parse($bookingQuery['date']),
            'time' => $bookingQuery['time'],
            'seats' => collect(explode(',', (string) $bookingQuery['seats']))->filter()->values(),
            'total' => ($seatTotal - $discount) + $foodTotal,
            'bookingSummaryData' => [
                'auth' => $this->bookingAuthPayload($request),
                'loginUrl' => route('login'),
                'authCheckUrl' => route('booking.auth.status'),
                'homeUrl' => route('movies.index'),
                'ticketUrl' => null,
                'summary' => [
                    'movieTitle' => $bookingQuery['title'],
                    'cinema' => $bookingQuery['cinema'],
                    'hall' => $bookingQuery['hall'],
                    'format' => $bookingQuery['format'],
                    'date' => $bookingQuery['date'],
                    'time' => $bookingQuery['time'],
                    'seats' => collect(explode(',', (string) $bookingQuery['seats']))->filter()->values()->all(),
                    'total' => ($seatTotal - $discount) + $foodTotal,
                ],
            ],
        ]);
    }
}