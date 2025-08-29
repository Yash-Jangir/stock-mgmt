<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class SizeSeeder extends Seeder
{
    public function run()
    {
        $userId = 1; // change if needed
        $now = now();

        $sizes = [
            // alpha sizes
            ['name' => 'XS',  'code' => 'XS',   'description' => 'Extra Small'],
            ['name' => 'S',   'code' => 'S',    'description' => 'Small'],
            ['name' => 'M',   'code' => 'M',    'description' => 'Medium'],
            ['name' => 'L',   'code' => 'L',    'description' => 'Large'],
            ['name' => 'XL',  'code' => 'XL',   'description' => 'Extra Large'],
            ['name' => 'XXL', 'code' => 'XXL',  'description' => '2X Large'],
            ['name' => '3XL', 'code' => '3XL',  'description' => '3X Large'],

            // numeric waist / chest sizes
            ['name' => '26',  'code' => '26',   'description' => 'Size 26'],
            ['name' => '28',  'code' => '28',   'description' => 'Size 28'],
            ['name' => '30',  'code' => '30',   'description' => 'Size 30'],
            ['name' => '32',  'code' => '32',   'description' => 'Size 32'],
            ['name' => '34',  'code' => '34',   'description' => 'Size 34'],
            ['name' => '36',  'code' => '36',   'description' => 'Size 36'],
            ['name' => '38',  'code' => '38',   'description' => 'Size 38'],
            ['name' => '40',  'code' => '40',   'description' => 'Size 40'],
            ['name' => '42',  'code' => '42',   'description' => 'Size 42'],
            ['name' => '44',  'code' => '44',   'description' => 'Size 44'],
            ['name' => '46',  'code' => '46',   'description' => 'Size 46'],
        ];

        foreach ($sizes as $s) {
            DB::table('sizes')->updateOrInsert(
                ['code' => $s['code']],
                [
                    'user_id'     => $userId,
                    'name'        => $s['name'],
                    'code'        => $s['code'],
                    'description' => $s['description'],
                    'updated_at'  => $now,
                    'created_at'  => $now,
                ]
            );
        }
    }
}
