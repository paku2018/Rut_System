<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            'restaurant_id' => 0,
            'name' => 'Aggregate',
            'order' => 1
        );

        Category::create($data);
    }
}
