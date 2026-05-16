<?php

namespace Database\Seeders;

use App\Models\Motorcycle;
use Illuminate\Database\Seeder;

class MotorcycleSeeder extends Seeder
{
    public function run(): void
    {
        $bikes = [
            [
                'make' => 'Honda', 'model' => 'CB500F', 'year' => 2022,
                'color' => 'Pearl Glare White', 'engine_size_cc' => 471, 'mileage_miles' => 5100,
                'price' => 7990.00, 'stock_quantity' => 2, 'is_featured' => true,
                'description' => "The Honda CB500F is the perfect mid-range naked bike for both new and experienced riders. Smooth parallel-twin engine, comfortable upright riding position, and exceptional fuel economy.",
            ],
            [
                'make' => 'Yamaha', 'model' => 'MT-07', 'year' => 2023,
                'color' => 'Cyan Storm', 'engine_size_cc' => 689, 'mileage_miles' => 1900,
                'price' => 10490.00, 'stock_quantity' => 1, 'is_featured' => true,
                'description' => "The Yamaha MT-07 delivers exhilarating torque from its 689cc CP2 engine. Lightweight chassis, aggressive styling, and precise handling make this one of the most popular naked bikes on the market.",
            ],
            [
                'make' => 'Kawasaki', 'model' => 'Ninja 400', 'year' => 2021,
                'color' => 'Lime Green / Ebony', 'engine_size_cc' => 399, 'mileage_miles' => 9000,
                'price' => 6495.00, 'stock_quantity' => 3,
                'description' => "A LAMS-approved sports bike with a punchy 400cc parallel-twin. Lightweight, agile, and great value for money.",
            ],
            [
                'make' => 'Ducati', 'model' => 'Monster 937', 'year' => 2022,
                'color' => 'Ducati Red', 'engine_size_cc' => 937, 'mileage_miles' => 3500,
                'price' => 19990.00, 'stock_quantity' => 1,
                'description' => "The new Monster 937 is lighter, more powerful, and more tech-loaded than ever. Testastretta 11° Desmodromic engine, cornering ABS, traction control.",
            ],
            [
                'make' => 'Suzuki', 'model' => 'V-Strom 650', 'year' => 2020,
                'color' => 'Champion Yellow 2', 'engine_size_cc' => 645, 'mileage_miles' => 13700,
                'price' => 8200.00, 'stock_quantity' => 0,
                'description' => "A versatile adventure tourer with a smooth V-twin engine. Comfortable for long-distance touring while capable on light off-road terrain.",
            ],
            [
                'make' => 'BMW', 'model' => 'R 1250 GS', 'year' => 2023,
                'color' => 'Kalamata Metallic Matt', 'engine_size_cc' => 1254, 'mileage_miles' => 1100,
                'price' => 28500.00, 'stock_quantity' => 1, 'is_featured' => true,
                'description' => "The benchmark adventure bike. ShiftCam technology, adaptive cornering lights, full electronics suite. Ready for any road.",
            ],
        ];

        foreach ($bikes as $data) {
            Motorcycle::create($data);
        }
    }
}
