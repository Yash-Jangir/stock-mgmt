<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $userId = 1; // change if needed
        $now = now();

        $categories = [
            ['name' => 'Shirt',       'code' => 'SHIRT',       'description' => 'Formal or casual shirts'],
            ['name' => 'T-Shirt',     'code' => 'TSHIRT',      'description' => 'Crew/V-neck tees'],
            ['name' => 'Polo',        'code' => 'POLO',        'description' => 'Collared knit shirts'],
            ['name' => 'Jeans',       'code' => 'JEANS',       'description' => 'Denim bottoms'],
            ['name' => 'Chinos',      'code' => 'CHINOS',      'description' => 'Cotton twill trousers'],
            ['name' => 'Shorts',      'code' => 'SHORTS',      'description' => 'Knee-length bottoms'],
            ['name' => 'Trousers',    'code' => 'TROUSERS',    'description' => 'Formal pants'],
            ['name' => 'Joggers',     'code' => 'JOGGERS',     'description' => 'Elastic cuff pants'],
            ['name' => 'Sweatshirt',  'code' => 'SWEATSHIRT',  'description' => 'Fleece tops'],
            ['name' => 'Hoodie',      'code' => 'HOODIE',      'description' => 'Hooded sweatshirts'],
            ['name' => 'Jacket',      'code' => 'JACKET',      'description' => 'Outerwear jackets'],
            ['name' => 'Blazer',      'code' => 'BLAZER',      'description' => 'Semi-formal blazers'],
            ['name' => 'Coat',        'code' => 'COAT',        'description' => 'Long outerwear'],
            ['name' => 'Kurta',       'code' => 'KURTA',       'description' => 'Traditional long shirt'],
            ['name' => 'Saree',       'code' => 'SAREE',       'description' => 'Traditional drape'],
            ['name' => 'Dress',       'code' => 'DRESS',       'description' => 'One-piece garments'],
            ['name' => 'Skirt',       'code' => 'SKIRT',       'description' => 'Women’s skirts'],
            ['name' => 'Blouse',      'code' => 'BLOUSE',      'description' => 'Women’s tops'],
            ['name' => 'Cardigan',    'code' => 'CARDIGAN',    'description' => 'Knit sweater layers'],
            ['name' => 'Suit',        'code' => 'SUIT',        'description' => 'Two/three-piece suits'],
            ['name' => 'Tracksuit',   'code' => 'TRACKSUIT',   'description' => 'Sport set'],
            ['name' => 'Underwear',   'code' => 'UNDERWEAR',   'description' => 'Innerwear'],
            ['name' => 'Socks',       'code' => 'SOCKS',       'description' => 'Foot garments'],
            ['name' => 'Nightwear',   'code' => 'NIGHTWEAR',   'description' => 'Sleepwear'],
        ];

        foreach ($categories as $c) {
            DB::table('categories')->updateOrInsert(
                ['code' => $c['code']], // unique key
                [
                    'user_id'     => $userId,
                    'name'        => $c['name'],
                    'code'        => $c['code'],
                    'description' => $c['description'],
                    'updated_at'  => $now,
                    'created_at'  => $now,
                ]
            );
        }
    }
}

