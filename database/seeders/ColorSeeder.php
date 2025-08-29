<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ColorSeeder extends Seeder
{
    public function run()
    {
        $userId = 1; // change if needed
        $now = now();

        $colors = [
            ['name' => 'Black',     'code' => 'BLK', 'color_code' => '#000000', 'description' => 'Pure black'],
            ['name' => 'White',     'code' => 'WHT', 'color_code' => '#FFFFFF', 'description' => 'Pure white'],
            ['name' => 'Red',       'code' => 'RED', 'color_code' => '#FF0000', 'description' => 'Primary red'],
            ['name' => 'Blue',      'code' => 'BLU', 'color_code' => '#0000FF', 'description' => 'Primary blue'],
            ['name' => 'Green',     'code' => 'GRN', 'color_code' => '#008000', 'description' => 'Primary green'],
            ['name' => 'Yellow',    'code' => 'YLW', 'color_code' => '#FFFF00', 'description' => 'Bright yellow'],
            ['name' => 'Orange',    'code' => 'ORG', 'color_code' => '#FFA500', 'description' => 'Orange hue'],
            ['name' => 'Purple',    'code' => 'PPL', 'color_code' => '#800080', 'description' => 'Purple hue'],
            ['name' => 'Pink',      'code' => 'PNK', 'color_code' => '#FFC0CB', 'description' => 'Pink hue'],
            ['name' => 'Brown',     'code' => 'BRN', 'color_code' => '#A52A2A', 'description' => 'Brown hue'],
            ['name' => 'Beige',     'code' => 'BEI', 'color_code' => '#F5F5DC', 'description' => 'Beige tone'],
            ['name' => 'Grey',      'code' => 'GRY', 'color_code' => '#808080', 'description' => 'Grey tone'],
            ['name' => 'Navy',      'code' => 'NAV', 'color_code' => '#000080', 'description' => 'Dark blue'],
            ['name' => 'Maroon',    'code' => 'MRN', 'color_code' => '#800000', 'description' => 'Dark red'],
            ['name' => 'Olive',     'code' => 'OLV', 'color_code' => '#808000', 'description' => 'Olive green'],
            ['name' => 'Teal',      'code' => 'TEL', 'color_code' => '#008080', 'description' => 'Blue-green'],
            ['name' => 'Cyan',      'code' => 'CYN', 'color_code' => '#00FFFF', 'description' => 'Cyan hue'],
            ['name' => 'Magenta',   'code' => 'MGT', 'color_code' => '#FF00FF', 'description' => 'Magenta hue'],
            ['name' => 'Lime',      'code' => 'LIM', 'color_code' => '#00FF00', 'description' => 'Lime green'],
            ['name' => 'Khaki',     'code' => 'KHK', 'color_code' => '#F0E68C', 'description' => 'Khaki tone'],
            ['name' => 'Mustard',   'code' => 'MST', 'color_code' => '#FFDB58', 'description' => 'Mustard yellow'],
            ['name' => 'Cream',     'code' => 'CRM', 'color_code' => '#FFFDD0', 'description' => 'Cream tone'],
            ['name' => 'Gold',      'code' => 'GLD', 'color_code' => '#FFD700', 'description' => 'Metallic gold'],
            ['name' => 'Silver',    'code' => 'SLV', 'color_code' => '#C0C0C0', 'description' => 'Metallic silver'],
            ['name' => 'Charcoal',  'code' => 'CHR', 'color_code' => '#36454F', 'description' => 'Dark grey'],
            ['name' => 'Turquoise', 'code' => 'TRQ', 'color_code' => '#40E0D0', 'description' => 'Greenish-blue'],
            ['name' => 'Lavender',  'code' => 'LAV', 'color_code' => '#E6E6FA', 'description' => 'Pale purple'],
            ['name' => 'Indigo',    'code' => 'IND', 'color_code' => '#4B0082', 'description' => 'Deep blue-violet'],
            ['name' => 'Fuchsia',   'code' => 'FCH', 'color_code' => '#FF00FF', 'description' => 'Vivid pink-purple'],
            ['name' => 'Ivory',     'code' => 'IVY', 'color_code' => '#FFFFF0', 'description' => 'Off-white'],
        ];

        foreach ($colors as $c) {
            DB::table('colors')->updateOrInsert(
                ['code' => $c['code']], // unique key
                [
                    'user_id'     => $userId,
                    'name'        => $c['name'],
                    'code'        => $c['code'],
                    'color_code'  => $c['color_code'],
                    'description' => $c['description'],
                    'updated_at'  => $now,
                    'created_at'  => $now,
                ]
            );
        }
    }
}

