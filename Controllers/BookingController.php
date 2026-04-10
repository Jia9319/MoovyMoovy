<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
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
            $qty = max((int) $request->query($key . '_qty', 0), 0);

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
                $selectedTemperature = strtolower((string) $request->query($key . '_temp', $defaultTemperature));

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
        return [
            'movie_id' => $request->query('movie_id'),
            'title' => $request->query('title', 'Selected Movie'),
            'genre' => $request->query('genre', 'Movie'),
            'duration' => (int) $request->query('duration', 120),
            'poster' => $request->query('poster'),
            'cinema' => $request->query('cinema', 'GSC Mid Valley'),
            'hall' => $request->query('hall', 'Hall ' . rand(1, 10)),
            'format' => $request->query('format', '2D'),
            'date' => $request->query('date', Carbon::today()->format('Y-m-d')),
            'time' => $request->query('time', '19:10'),
            'price' => (float) $request->query('price', 18),
            'seats' => $request->query('seats', ''),
        ];
    }

    private function calculateSeatTotal(string $seatsRaw, float $basePrice, float $vipExtra = 5): float
    {
        $seats = collect(explode(',', $seatsRaw))->map(fn($seat) => trim($seat))->filter();
        return $seats->reduce(function ($sum, $seat) use ($basePrice, $vipExtra) {
            $isVip = str_starts_with($seat, 'D') || str_starts_with($seat, 'E');
            return $sum + $basePrice + ($isVip ? $vipExtra : 0);
        }, 0);
    }

    public function select(Request $request)
    {
        $movie = [
            'id' => $request->query('movie_id'),
            'title' => $request->query('title', 'Selected Movie'),
            'genre' => $request->query('genre', 'Movie'),
            'duration' => (int) $request->query('duration', 120),
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
            ['label' => '3D', 'price' => 24],
            ['label' => 'IMAX', 'price' => 35],
            ['label' => 'Beanie', 'price' => 55],
        ];

        $times = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:45', 
            '12:15', '12:45', '13:20', '13:50', '14:20', '15:00', 
            '15:40', '16:10', '16:50', '17:20', '17:50', '18:30', 
            '19:00', '19:40', '20:15', '20:45', '21:15', '21:50', 
            '22:20', '22:50', '23:15', '23:45', '00:30', '01:00'
        ];

        return view('booking.booking', [
            'movie' => $movie,
            'dates' => $dates,
            'cinemas' => $cinemas,
            'types' => $types,
            'times' => $times,
        ]);
    }

    public function seat(Request $request)
    {
        $bookingQuery = $this->buildBookingQuery($request);
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

        $rows = ['A', 'B', 'C', 'D', 'E'];
        $seatsPerRow = 10;
        $bookedSeats = collect(range(1, 8))->map(function ($index) use ($showtime, $rows, $seatsPerRow) {
            $seed = crc32($showtime->id . '-' . $index);
            return $rows[$seed % count($rows)] . (($seed % $seatsPerRow) + 1);
        })->unique()->values();

        return view('booking.seat', [
            'showtime' => $showtime,
            'rows' => $rows,
            'seatsPerRow' => $seatsPerRow,
            'bookedSeats' => $bookedSeats,
            'vipRows' => ['D', 'E'],
            'vipExtra' => 5,
            'bookingQuery' => $bookingQuery,
        ]);
    }

    public function food(Request $request)
    {
        $bookingQuery = $this->buildBookingQuery($request);
        $seatTotal = $this->calculateSeatTotal((string) $bookingQuery['seats'], (float) $bookingQuery['price']);
        $menu = $this->getFoodMenu();

        return view('booking.food', [
            'bookingQuery' => $bookingQuery,
            'seats' => collect(explode(',', (string) $bookingQuery['seats']))->filter()->values(),
            'seatTotal' => $seatTotal,
            'foodItems' => collect($menu)->filter(fn($item) => $item['category'] === 'food')->all(),
            'beverageItems' => collect($menu)->filter(fn($item) => $item['category'] === 'beverage')->all(),
        ]);
    }

    public function payment(Request $request)
    {
        $bookingQuery = $this->buildBookingQuery($request);
        $seatTotal = $this->calculateSeatTotal((string) $bookingQuery['seats'], (float) $bookingQuery['price']);
        $foodLines = $this->buildAddonLines($request);

        $isTuesday = Carbon::parse($bookingQuery['date'])->isTuesday();
        $discountAmount = $isTuesday ? ($seatTotal * 0.5) : 0;

        return view('booking.payment', [
            'bookingQuery' => $bookingQuery,
            'seats' => collect(explode(',', (string) $bookingQuery['seats']))->filter()->values(),
            'seatTotal' => $seatTotal,
            'isTuesday' => $isTuesday,
            'discountAmount' => $discountAmount,
            'discountedSeatTotal' => $seatTotal - $discountAmount,
            'foodLines' => $foodLines,
            'foodTotal' => $foodLines->sum('lineTotal'),
            'grandTotal' => ($seatTotal - $discountAmount) + $foodLines->sum('lineTotal'),
        ]);
    }

    public function ticket(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required', 'cinema' => 'required', 'hall' => 'nullable', 'format' => 'nullable',
            'date' => 'required|date', 'time' => 'required', 'seats' => 'required',
            'seat_total' => 'required|numeric', 'food_total' => 'required|numeric', 'payment_method' => 'required',
            'promo_code' => 'nullable|string|max:50',
        ]);

        $isTuesday = Carbon::parse($validated['date'])->isTuesday();
        $discount = $isTuesday ? ($validated['seat_total'] * 0.5) : 0;
        $total = ($validated['seat_total'] - $discount) + $validated['food_total'];
        $foodLines = $this->buildAddonLines($request);
        $ticketCode = 'MM-' . strtoupper(substr(md5($validated['title'].$validated['seats'].now()), 0, 10));

        return view('booking.ticket', array_merge($validated, [
            'ticketCode' => $ticketCode,
            'date' => Carbon::parse($validated['date']),
            'discountAmount' => $discount,
            'isTuesday' => $isTuesday,
            'grandTotal' => $total,
            'promoCode' => $validated['promo_code'] ?? null,
            'foodLines' => $foodLines,
            'qrUrl' => 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($ticketCode),
        ]));
    }
}