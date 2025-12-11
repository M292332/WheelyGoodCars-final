<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Elektrisch', 'Hybride', 'Benzine', 'Diesel', 'Automaat', 'Handgeschakeld',
            'SUV', 'Cabrio', 'Station', 'Compact', 'Familie', 'Sport', 'Luxe', 'Budget',
            '4x4', 'Youngtimer', 'Oldtimer', 'Zakelijk', 'Privé', 'Exclusief'
        ];

        $colors = [
            '#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8',
            '#6f42c1', '#fd7e14', '#20c997', '#6610f2', '#e83e8c',
            '#343a40', '#6c757d', '#002a54', '#adb5bd', '#ff6f61',
            '#00b894', '#fdcb6e', '#0984e3', '#d35400', '#636e72'
        ];

        foreach ($names as $i => $name) {
            Tag::create([
                'name' => $name,
                'color' => $colors[$i],
            ]);
        }
    }
}
