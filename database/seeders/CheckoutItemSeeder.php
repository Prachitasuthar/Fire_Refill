<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CheckoutItem;

class CheckoutItemSeeder extends Seeder
{
    public function run(): void
    {
        CheckoutItem::factory(10)->create();
    }
}
