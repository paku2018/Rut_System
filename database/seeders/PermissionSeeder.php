<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ['title' => 'Tables', 'name' => 'tables.view'],
            ['title' => 'Categories', 'name' => 'categories.view'],
            ['title' => 'Products', 'name' => 'products.view'],
            ['title' => 'Sales', 'name' => 'sales.view'],
            ['title' => 'Statistics', 'name' => 'statistics.view'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
