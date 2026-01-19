<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create(['name' => 'Sandals', 'price' => 10, 'stock_quantity' => 5]);
        Product::create(['name' => 'RunningShoes', 'price' => 120, 'stock_quantity' => 10]);
        Product::create(['name' => 'WorkBoots', 'price' => 40, 'stock_quantity' => 15]);
        Product::create(['name' => 'HikingShoes', 'price' => 130, 'stock_quantity' => 20]);
        Product::create(['name' => 'FlipFlops', 'price' => 8, 'stock_quantity' => 5]);
        Product::create(['name' => 'Espadrilles', 'price' => 20, 'stock_quantity' => 10]);
        Product::create(['name' => 'SnowBoots', 'price' => 100, 'stock_quantity' => 15]);
        Product::create(['name' => 'Clogs', 'price' => 35, 'stock_quantity' => 20]);        
    }
}
