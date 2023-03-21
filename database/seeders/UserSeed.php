<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'name' =>'admin user',
                'email' => 'admin@devpora.com',
                'password' => bcrypt('HardPass')
            ]
        ];

        foreach ($items as $item) {
            User::create($item);
        }
    }
}
