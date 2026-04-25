<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
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
        $format = strtoupper((string) $request->query('format', '2D'));
        if ($format === 'BONNIE') {
            $format = 'BEANIE';
        }

        return [
            'movie_id' => $request->query('movie_id'),
            'title' => $request->query('title', 'Selected Movie'),
            'genre' => $request->query('genre', 'Movie'),
            'duration' => $this->normalizeDurationMinutes($request->query('duration', 120)),
            'poster' => $request->query('poster'),
            'cinema' => $request->query('cinema', 'GSC Mid Valley'),
            'hall' => $request->query('hall', 'Hall ' . rand(1, 10)),
            'format' => $format,
            'date' => $request->query('date', Carbon::today()->format('Y-m-d')),
            'time' => $request->query('time', '19:10'),
            'price' => (float) $request->query('price', 18),
            'seats' => $request->query('seats', ''),
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
            'poster' => $request->query('poster'),
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

        $timePool = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:45', 
            '12:15', '12:45', '13:20', '13:50', '14:20', '15:00', 
            '15:40', '16:10', '16:50', '17:20', '17:50', '18:30', 
            '19:00', '19:40', '20:15', '20:45', '21:15', '21:50', 
            '22:20', '22:50', '23:15', '23:45', '00:30', '01:00'
        ];

        $times = collect($timePool)
            ->shuffle()
            ->take(10)
            ->sort()
            ->values()
            ->all();

        $bookingSelectData = [
            'movie' => array_merge($movie, [
                'durationText' => $movie['duration'] . ' min',
            ]),
            'cinemas' => $cinemas->values()->all(),
            'types' => $types,
            'dates' => $dates->values()->all(),
            'times' => $times,
            'selection' => [
                'cinema' => $request->query('cinema', ''),
                'hall' => $request->query('hall', ''),
                'format' => strtoupper((string) $request->query('format', '')),
                'price' => (float) $request->query('price', 0),
                'date' => $request->query('date', ''),
                'time' => $request->query('time', ''),
            ],
            'homeUrl' => route('home'),
            'seatUrl' => route('booking.seat'),
            'storageKey' => 'booking.select.' . ($movie['id'] ?: 'default'),
            'isAuthenticated' => auth()->check(),
            'loginUrl' => route('login'),
        ];

        return view('booking.booking', [
            'movie' => $movie,
            'dates' => $dates,
            'cinemas' => $cinemas,
            'types' => $types,
            'times' => $times,
            'bookingSelectData' => $bookingSelectData,
        ]);
    }

    public function seat(Request $request)
    {
        $bookingQuery = $this->buildBookingQuery($request);
        $seatConfig = $this->getSeatExperienceConfig((string) $bookingQuery['format'], (float) $bookingQuery['price']);
        $showtime = (object) [
            'id' => crc32($bookingQuery['title'] . $bookingQuery['cinema'] . $bookingQuery['date'] . $bookingQuery['time']),
            'cinema' => $bookingQuery['cinema'],
            'hall' => $bookingQuery['hall'],
            'format' => $bookingQuery['format'],
            'date' => Carbon::parse($bookingQuery['date']),
            'time' => $bookingQuery['time'],
            'price' => $bookingQuery['price'],
            'movie' => (object) [
                'id' => $bookingQuery['movie_id'], 'title' => $bookingQuery['title'], 
                'genre' => $bookingQuery['genre'], 'duration' => $bookingQuery['duration'], 'poster' => $bookingQuery['poster'],
            ],
        ];

        $rows = $seatConfig['rows'];
        $seatsPerRow = (int) $seatConfig['seatsPerRow'];
        $bookedSeats = collect(range(1, 8))->map(function ($index) use ($showtime, $rows, $seatsPerRow) {
            $seed = crc32($showtime->id . '-' . $index);
            return $rows[$seed % count($rows)] . (($seed % $seatsPerRow) + 1);
        })->unique()->values();

        $bookingSeatData = [
            'storageKey' => 'booking.seat.' . ($bookingQuery['movie_id'] ?: 'default') . '.' . $bookingQuery['date'] . '.' . str_replace(':', '', $bookingQuery['time']),
            'rows' => $rows,
            'seatsPerRow' => $seatsPerRow,
            'aisleAfterColumns' => $seatConfig['aisleAfterColumns'],
            'coupleRows' => $seatConfig['coupleRows'],
            'couplePairStarts' => $seatConfig['couplePairStarts'],
            'bookedSeats' => $bookedSeats->all(),
            'selectedSeats' => (string) ($bookingQuery['seats'] ?? ''),
            'basePrice' => (float) $bookingQuery['price'],
            'coupleExtra' => (float) $seatConfig['coupleExtra'],
            'hasCoupleSeats' => !empty($seatConfig['coupleRows']) && !empty($seatConfig['couplePairStarts']),
            'showtime' => [
                'id' => $showtime->id,
                'cinema' => $bookingQuery['cinema'],
                'hall' => $bookingQuery['hall'],
                'format' => $bookingQuery['format'],
                'date' => $bookingQuery['date'],
                'time' => $bookingQuery['time'],
                'price' => (float) $bookingQuery['price'],
                'movie' => [
                    'id' => $bookingQuery['movie_id'],
                    'title' => $bookingQuery['title'],
                    'genre' => $bookingQuery['genre'],
                    'duration' => $bookingQuery['duration'],
                    'durationText' => $bookingQuery['duration'] . ' min',
                    'poster' => $bookingQuery['poster'],
                ],
            ],
            'bookingState' => array_merge($bookingQuery, [
                'showtime_id' => $request->query('showtime_id', $showtime->id),
            ]),
            'backUrl' => route('booking.select', $bookingQuery),
            'nextUrl' => route('booking.food'),
            'isAuthenticated' => auth()->check(),
            'auth' => [
                'isAuthenticated' => auth()->check(),
                'userId' => auth()->id(),
            ],
            'loginUrl' => route('login'),
        ];

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
            'bookingSeatData' => $bookingSeatData,
        ]);
    }

    public function food(Request $request)
    {
        $bookingQuery = $this->buildBookingQuery($request);
        $seatTotal = $this->calculateSeatTotal((string) $bookingQuery['seats'], (float) $bookingQuery['price'], (string) $bookingQuery['format']);
        $menu = $this->getFoodMenu();
        $seats = collect(explode(',', (string) $bookingQuery['seats']))->filter()->values();
        $foodItems = collect($menu)->filter(fn($item) => $item['category'] === 'food')->all();
        $beverageItems = collect($menu)->filter(fn($item) => $item['category'] === 'beverage')->all();

        $foodSelections = [
            'quantities' => collect($menu)->mapWithKeys(function ($item, $key) use ($request) {
                return [$key => max((int) $request->query($key . '_qty', 0), 0)];
            })->all(),
            'temperatures' => collect($beverageItems)->mapWithKeys(function ($item, $key) use ($request) {
                $options = $item['temperature_options'] ?? ['cold'];
                $default = $item['default_temperature'] ?? ($options[0] ?? 'cold');
                $selected = strtolower((string) $request->query($key . '_temp', $default));

                if (!in_array($selected, $options, true)) {
                    $selected = $default;
                }

                return [$key => $selected];
            })->all(),
        ];

        $bookingFoodData = [
            'storageKey' => 'booking.food.' . ($bookingQuery['movie_id'] ?: 'default') . '.' . $bookingQuery['date'] . '.' . str_replace(':', '', $bookingQuery['time']),
            'bookingState' => $bookingQuery,
            'seats' => $seats->all(),
            'seatTotal' => (float) $seatTotal,
            'foodItems' => $foodItems,
            'beverageItems' => $beverageItems,
            'foodSelections' => $foodSelections,
            'backUrl' => route('booking.seat', $bookingQuery),
            'nextUrl' => route('booking.payment'),
            'isAuthenticated' => auth()->check(),
            'auth' => [
                'isAuthenticated' => auth()->check(),
                'userId' => auth()->id(),
            ],
            'loginUrl' => route('login'),
        ];

        return view('booking.food', [
            'bookingQuery' => $bookingQuery,
            'seats' => $seats,
            'seatTotal' => $seatTotal,
            'foodItems' => $foodItems,
            'beverageItems' => $beverageItems,
            'bookingFoodData' => $bookingFoodData,
        ]);
    }

    public function payment(Request $request)
    {
        $bookingQuery = $this->buildBookingQuery($request);
        $bookingState = array_merge($bookingQuery, [
            'showtime_id' => $request->query('showtime_id'),
        ]);
        $seatTotal = $this->calculateSeatTotal((string) $bookingQuery['seats'], (float) $bookingQuery['price'], (string) $bookingQuery['format']);
        $foodLines = $this->buildAddonLines($request);
        $foodTotal = (float) $foodLines->sum('lineTotal');
        $seats = collect(explode(',', (string) $bookingQuery['seats']))->filter()->values();
        $menu = $this->getFoodMenu();
        $foodItems = collect($menu)->filter(fn($item) => $item['category'] === 'food')->all();
        $beverageItems = collect($menu)->filter(fn($item) => $item['category'] === 'beverage')->all();
        $allEntries = collect(array_merge($foodItems, $beverageItems))
            ->map(function ($item, $key) {
                return [$key, $item];
            })->values()->all();

        $isTuesday = Carbon::parse($bookingQuery['date'])->isTuesday();
        $discountAmount = $isTuesday ? ($seatTotal * 0.5) : 0;
        $discountedSeatTotal = $seatTotal - $discountAmount;
        $grandTotal = $discountedSeatTotal + $foodTotal;

        $foodSelections = [
            'allEntries' => $allEntries,
            'quantities' => collect($menu)->mapWithKeys(function ($item, $key) use ($request) {
                return [$key => max((int) $request->query($key . '_qty', 0), 0)];
            })->all(),
            'temperatures' => collect($beverageItems)->mapWithKeys(function ($item, $key) use ($request) {
                $options = $item['temperature_options'] ?? ['cold'];
                $default = $item['default_temperature'] ?? ($options[0] ?? 'cold');
                $selected = strtolower((string) $request->query($key . '_temp', $default));

                if (!in_array($selected, $options, true)) {
                    $selected = $default;
                }

                return [$key => $selected];
            })->all(),
        ];

        $bookingPaymentData = [
            'storageKey' => 'booking.payment.' . ($bookingQuery['movie_id'] ?: 'default') . '.' . $bookingQuery['date'] . '.' . str_replace(':', '', $bookingQuery['time']),
            'bookingState' => $bookingState,
            'seats' => $seats->all(),
            'seatTotal' => (float) $seatTotal,
            'isTuesday' => $isTuesday,
            'discountAmount' => (float) $discountAmount,
            'discountedSeatTotal' => (float) $discountedSeatTotal,
            'foodLines' => $foodLines->values()->all(),
            'foodTotal' => $foodTotal,
            'grandTotal' => (float) $grandTotal,
            'foodSelections' => $foodSelections,
            'paymentMethod' => (string) $request->query('payment_method', 'tng'),
            'backUrl' => route('booking.food', array_merge($bookingQuery, $request->query())),
            'nextUrl' => route('booking.ticket'),
            'csrfToken' => csrf_token(),
            'isAuthenticated' => auth()->check(),
            'auth' => [
                'isAuthenticated' => auth()->check(),
                'userId' => auth()->id(),
            ],
            'loginUrl' => route('login'),
        ];

        return view('booking.payment', [
            'bookingQuery' => $bookingQuery,
            'seats' => $seats,
            'seatTotal' => $seatTotal,
            'isTuesday' => $isTuesday,
            'discountAmount' => $discountAmount,
            'discountedSeatTotal' => $discountedSeatTotal,
            'foodLines' => $foodLines,
            'foodTotal' => $foodTotal,
            'grandTotal' => $grandTotal,
            'bookingPaymentData' => $bookingPaymentData,
        ]);
    }

    public function ticket(Request $request)
{
    // 1. Validation
    $validated = $request->validate([
        'title'          => 'required',
        'movie_id'       => 'nullable',
        'showtime_id'    => 'nullable',
        'cinema'         => 'required',
        'hall'           => 'nullable',
        'format'         => 'nullable',
        'date'           => 'required',
        'time'           => 'required',
        'seats'          => 'required',
        'seat_total'     => 'required',
        'food_total'     => 'required',
        'payment_method' => 'required',
    ]);

    // 2. Data Preparation
    $seatsArray = collect(explode(',', (string) $validated['seats']))
                    ->map(fn($seat) => trim($seat))
                    ->filter()
                    ->values()
                    ->all();

    $seatTotal = (float)$validated['seat_total'];
    $foodTotal = (float)$validated['food_total'];
    $grandTotal = $seatTotal + $foodTotal;
    $ticketCode = 'MM-' . strtoupper(substr(md5($validated['title'].$validated['seats'].microtime()), 0, 10));
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . $ticketCode;
    
    $foodLines = $this->buildAddonLines($request);

    // 3. Database Insertion
    if (Auth::check()) {
        Ticket::create([
            'user_id'         => Auth::id(),
            'movie_id'        => $request->input('movie_id'),
            'showtime_id'     => $request->input('showtime_id'),
            'ticket_code'     => $ticketCode,
            'qr_url'          => $qrUrl,
            'cinema'          => $validated['cinema'],
            'hall'            => $validated['hall'] ?? 'Hall 1',
            'format'          => $validated['format'] ?? '2D',
            'date'            => $validated['date'],
            'time'            => $validated['time'],
            'seats'           => $seatsArray,
            'seat_count'      => count($seatsArray),
            'seat_total'      => $seatTotal,
            'food_lines'      => $foodLines->all(),
            'food_total'      => $foodTotal,
            'discount_amount' => 0,
            'grand_total'     => $grandTotal,
            'payment_method'  => $validated['payment_method'],
            'promo_code'      => $request->input('promo_code'),
            'status'          => 'paid',
        ]);
    }

    // 4. Prepare Data for React Component (BookingTicket.js)
    $bookingTicketData = [
        'ticket' => [
            'ticketCode'    => $ticketCode,
            'title'         => $validated['title'],
            'cinema'        => $validated['cinema'],
            'hall'          => $validated['hall'] ?? 'Hall 1',
            'format'        => $validated['format'] ?? '2D',
            'date'          => $validated['date'],
            'time'          => $validated['time'],
            'seats'         => $seatsArray,
            'paymentMethod' => $validated['payment_method'],
            'promoCode'     => $request->input('promo_code'),
            'seatTotal'     => $seatTotal,
            'foodTotal'     => $foodTotal,
            'grandTotal'    => $grandTotal,
            'qrUrl'         => $qrUrl,
            'foodLines'     => $foodLines->values()->all(),
            'isTuesday'     => false,
            'discountAmount'=> 0,
        ],
        'homeUrl' => route('home'),
        'isAuthenticated' => auth()->check(),
    ];

    // 5. Return View with both individual vars and the bundled data
    return view('booking.ticket', array_merge($bookingTicketData['ticket'], [
        'bookingTicketData' => $bookingTicketData,
        'date'              => \Carbon\Carbon::parse($validated['date']),
        'foodLines'         => $foodLines,
        'payment_method'    => $validated['payment_method'], 
    ]));
}
    public function history()
{
    $user = auth()->user();

    $tickets = \App\Models\Ticket::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    $stats = [
        'totalTickets' => $tickets->count(),
        'spent' => $tickets->sum('grand_total'),
    ];

    return view('profile.history', compact('tickets', 'stats'));
}

public function showTicket($id)
{
    // 1. 获取该用户的这张票
    $ticket = \App\Models\Ticket::where('user_id', auth()->id())->findOrFail($id);

    return view('booking.ticket', [
        'ticket'         => $ticket, 
        'title'          => $ticket->movie_title ?? 'Movie Ticket', 
        'cinema'         => $ticket->cinema,
        'hall'           => $ticket->hall,
        'format'         => $ticket->format,
        'date'           => \Carbon\Carbon::parse($ticket->date),
        'time'           => $ticket->time,
        'seats'          => $ticket->seats, 
        'seatTotal'      => $ticket->seat_total,
        'foodTotal'      => $ticket->food_total,
        'grandTotal'     => $ticket->grand_total,
        'ticketCode'     => $ticket->ticket_code,
        'foodLines'      => $ticket->food_lines,
        'qrUrl'          => $ticket->qr_url,

        'bookingTicketData' => [
            'ticket' => [
                'ticketCode'    => $ticket->ticket_code,
                'title'         => $ticket->movie_title ?? 'Movie Ticket',
                'cinema'        => $ticket->cinema,
                'hall'          => $ticket->hall,
                'format'        => $ticket->format,
                'date'          => $ticket->date,
                'time'          => $ticket->time,
                'seats'         => is_array($ticket->seats) ? $ticket->seats : json_decode($ticket->seats, true),
                'paymentMethod' => $ticket->payment_method,
                'seatTotal'     => (float)$ticket->seat_total,
                'foodTotal'     => (float)$ticket->food_total,
                'grandTotal'    => (float)$ticket->grand_total,
                'qrUrl'         => $ticket->qr_url,
                'foodLines'     => is_array($ticket->food_lines) ? $ticket->food_lines : json_decode($ticket->food_lines, true),
            ],
            'homeUrl'         => route('home'),
            'isAuthenticated' => true, 
        ]
    ]);
}

public function showDetails($id)
{
    $ticket = \App\Models\Ticket::where('user_id', auth()->id())->findOrFail($id);

    $bookingTicketData = [
        'ticket' => [
            'ticketCode' => $ticket->ticket_code,
            'title'      => $ticket->movie_title ?? 'Movie Ticket',
            'cinema'     => $ticket->cinema,
            'hall'       => $ticket->hall,
            'format'     => $ticket->format,
            'date'       => $ticket->date,
            'time'       => $ticket->time,
            'seats'      => $ticket->seats, 
            'paymentMethod' => $ticket->payment_method,
            'seatTotal'  => (float)$ticket->seat_total,
            'foodTotal'  => (float)$ticket->food_total,
            'grandTotal' => (float)$ticket->grand_total,
            'qrUrl'      => $ticket->qr_url,
            'foodLines'  => $ticket->food_lines,
        ],
        'homeUrl' => route('home'),
        'isAuthenticated' => true, 
    ];

    return view('profile.details', [
        'ticket'            => $ticket,
        'bookingTicketData' => $bookingTicketData,
        'title'             => $bookingTicketData['ticket']['title'],
        'date'              => \Carbon\Carbon::parse($ticket->date),
    ]);
}
}