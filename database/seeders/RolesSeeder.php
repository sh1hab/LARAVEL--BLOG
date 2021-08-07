<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Enums\UserTypes;

class RolesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Role::factory()->create([
            'name' => UserTypes::admin
        ]);

        Role::factory()->create([
            'name' => UserTypes::user
        ]);

        Role::factory()->create([
            'name' => UserTypes::manager
        ]);
    }
}
