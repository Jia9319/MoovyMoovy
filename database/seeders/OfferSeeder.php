<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Offer;
class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        Offer::truncate();

        Offer::insert([
            [
                'title'            => 'Student Tuesdays — 50% Off',
                'description'      => 'Flash your student ID every Tuesday and get half-price tickets to any screening. Valid at all MoovyMoovy locations across Malaysia.',
                'code'             => 'STUDENT50',
                'discount_percent' => 50,
                'valid_from'       => '2026-01-01',
                'valid_until'      => '2026-12-31',
                'is_active'        => true,
                'max_uses'         => null,
                'used_count'       => 0,
                'terms'            => 'Valid every Tuesday only. Must present valid student ID at counter. Cannot be combined with other offers.',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'Weekend Family Pack — 20% Off',
                'description'      => 'Bring the whole family on weekends and save 20% on 4 or more tickets.',
                'code'             => 'FAMILY20',
                'discount_percent' => 20,
                'valid_from'       => '2026-01-01',
                'valid_until'      => '2026-12-31',
                'is_active'        => true,
                'max_uses'         => null,
                'used_count'       => 0,
                'terms'            => 'Valid Saturday and Sunday only. Minimum 4 tickets per transaction. One use per family per day.',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'Senior Citizens — 30% Off',
                'description'      => 'Exclusive discount for guests aged 60 and above. Any day, any screening.',
                'code'             => 'SENIOR30',
                'discount_percent' => 30,
                'valid_from'       => '2026-01-01',
                'valid_until'      => '2026-12-31',
                'is_active'        => true,
                'max_uses'         => null,
                'used_count'       => 0,
                'terms'            => 'Valid for guests aged 60 and above. Must present MyKad at counter. Valid every day.',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'Early Bird — 25% Off',
                'description'      => 'Catch a screening before noon and save 25% on your ticket.',
                'code'             => 'EARLYBIRD25',
                'discount_percent' => 25,
                'valid_from'       => '2026-04-01',
                'valid_until'      => '2026-06-30',
                'is_active'        => true,
                'max_uses'         => 500,
                'used_count'       => 0,
                'terms'            => 'Valid for screenings starting before 12:00 PM only. Limited to first 500 redemptions.',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'MoovyMoovy Birthday Month — 15% Off',
                'description'      => 'Celebrate your birthday month with 15% off any ticket!',
                'code'             => 'BDAY15',
                'discount_percent' => 15,
                'valid_from'       => '2026-01-01',
                'valid_until'      => '2026-12-31',
                'is_active'        => true,
                'max_uses'         => null,
                'used_count'       => 0,
                'terms'            => 'Valid during your birth month only. Must present MyKad or passport as proof of birth date.',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }
}
    